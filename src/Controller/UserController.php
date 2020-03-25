<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Log;
use Cake\Routing\Router;
use User\Exception\AuthException;
use User\Exception\PasswordResetException;
use User\Form\PasswordForgottenForm;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 *
 * @package User\Controller
 * @property \User\Model\Table\UsersTable $Users
 */
class UserController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = "User.Users";

    public $captchaActions = ['login', 'register'];

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow([
            'login', 'register', 'registerGroup', 'activate', 'activateResend',
            'passwordForgotten', 'passwordSent', 'passwordReset', 'passwordChanged',
        ]);

        if ($this->components()->has('UserSession')) {
            $this->UserSession->ignoreActions(['checkAuth']);
        }

        $layout = Configure::read('User.layout') ?: null; //'User.user';
        $this->viewBuilder()->setLayout($layout);
    }

    /**
     * Login method
     * No authentication required
     *
     * @return void
     */
    public function login()
    {
        if (Configure::read('User.Login.layout')) {
            $this->viewBuilder()->setLayout(Configure::read('User.Login.layout'));
        }

        if ($this->request->getQuery('goto')) {
            //@TODO Check if goto URL is within app scope and/or use a token
            $this->request->getSession()->write('Auth.redirect', urldecode($this->request->getQuery('goto')));
        } elseif (!$this->request->getSession()->check('Auth.redirect')) {
            $referer = $this->referer();
            if ($referer && Router::normalize($referer) != Router::normalize(['action' => __FUNCTION__])) {
                //debug("set referer to " . Router::normalize($referer));
                //$this->request->getSession()->write('Auth.redirect', $referer);
            }
        }

        try {
            if (Configure::read('User.Login.disabled') == true) {
                throw new AuthException(__d('user', 'Sorry, but login is currently disabled.'));
            }

            $authUser = $this->Auth->login();
            if ($authUser) {
                $redirectUrl = $this->Auth->redirectUrl();
                if ($redirectUrl) {
                    $this->redirect($redirectUrl);
                }
            }
        } catch (AuthException $ex) {
            $this->Auth->flash($ex->getMessage());
        } catch (\Exception $ex) {
            debug($ex->getMessage());
            $this->Auth->flash(__('Login unavailable'));
        }

        $user = $this->Users->newEntity();
        $this->set('user', $user);
    }

    /**
     * Logout method
     *
     * @return void
     */
    public function logout()
    {
        $this->Flash->success(__d('user', 'You are logged out now!'), ['key' => 'auth']);
        $redirectUrl = $this->Auth->logout();
        $this->redirect($redirectUrl);
    }

    /**
     * Index method
     * Show user profile
     *
     * @return void
     */
    public function index()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        $this->set('user', $user);
    }

    /**
     * Register method
     * No authentication required
     *
     * @return void|null|\Cake\Http\Response Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        if ($this->Auth->user('id')) {
            return $this->redirect('/');
        }

        // force group auth
        if (Configure::read('User.Signup.groupAuth') == true) {
            if (!$this->request->getSession()->read('User.Signup.group_id')) {
                return $this->redirect(['action' => 'registerGroup']);
            }
        }

        $formClass = '\\User\\Form\\UserRegisterForm';
        if (Configure::read('User.Signup.formClass')) {
            $formClass = Configure::read('User.Signup.formClass');
        }
        if (!class_exists($formClass)) {
            throw new InternalErrorException("Class not found: $formClass");
        }
        $form = new $formClass();
        if (!($form instanceof Form)) {
            throw new InternalErrorException("Object is not an instance of \\Cake\\Form\\Form");
        }

        if (Configure::read('User.Signup.disabled') != true) {
            if ($this->request->is('post')) {
                $data = $this->request->getData();
                if (Configure::read('User.Signup.groupAuth') == true) {
                    $data['group_id'] = $this->request->getSession()->read('User.Signup.group_id');
                }

                //$user = $this->Users->register($data);
                $user = $form->execute($data);
                if ($user && $user->id) {
                    //$this->request->getSession()->delete('User.Signup');
                    $this->Flash->success(__d('user', 'An activation email has been sent to your email address!'), ['key' => 'auth']);
                    $redirect = $this->Auth->getConfig('registerRedirect');
                    $redirect = $redirect ?: ['_name' => 'user:login'];
                    $this->redirect($redirect);
                } else {
                    $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
                }
            }
        } else {
            $this->Flash->error(__d('user', 'Sorry, but user registration is currently disabled.'), ['key' => 'auth']);
        }

        $this->set(compact('user', 'form'));
    }

    /**
     * Group registration
     *
     * @return void
     */
    public function registerGroup()
    {
        if ($this->request->is(['put', 'post'])) {
            $grpPass = $this->request->getData('group_pass');
            $grpPass = trim($grpPass);
            if (!$grpPass) {
                $this->Flash->error(__d('user', 'No password entered'), ['key' => 'auth']);

                return;
            }

            // find user group with that password
            //$this->loadModel('User.Groups');
            $userGroup = $this->Users->UserGroups->find()->where(['password' => $grpPass])->first();

            if (!$userGroup) {
                $this->request->getSession()->delete('User.Signup.group_id');
                $this->Flash->error(__d('user', 'Invalid password'), ['key' => 'auth']);

                return;
            }

            // store group auth info in session
            $this->request->getSession()->write('User.Signup.group_id', $userGroup->id);
            $this->request->getSession()->write('User.Signup.group_pass', $grpPass);

            // continue registration
            $this->redirect(['action' => 'register']);
        } elseif ($this->request->getSession()->read('User.Signup.group_id')) {
            // continue registration
            //$this->redirect(['action' => 'register']);
        }
    }

    /**
     * Activate
     *
     * @return void
     */
    public function activate()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }

        /** @var \User\Model\Entity\User $user */
        $user = $this->Users->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->activate($this->request->getData())) {
                $this->Flash->success(__d('user', 'Your account has been activated. You can login now.'), ['key' => 'auth']);
                $this->redirect(['action' => 'login', 'm' => base64_encode($user->email) ]);
            } else {
                $this->Flash->error(__d('user', 'Account activation failed'), ['key' => 'auth']);
            }
        } else {
            $user->email = $this->request->getQuery('m')
                ? base64_decode($this->request->getQuery('m')) : null;
            $user->email_verification_code = $this->request->getQuery('c')
                ? base64_decode($this->request->getQuery('c')) : null;

            // auto-activation
            if ($user->email && $user->email_verification_code) {
                if (
                    $this->Users->activate([
                    'email' => $user->email,
                    'email_verification_code' => $user->email_verification_code,
                    ])
                ) {
                    $this->Flash->success(__d('user', 'Your account has been activated. You can login now.'), ['key' => 'auth']);
                    $this->redirect(['action' => 'login', 'm' => base64_encode($user->email) ]);
                } else {
                    $this->Flash->error(__d('user', 'Account activation failed'), ['key' => 'auth']);
                }
            }
        }
        $this->set('user', $user);
    }

    /**
     * Resend email verification email
     *
     * @return void
     */
    public function activateResend()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }

        /** @var \User\Model\Entity\User $user */
        $user = $this->Users->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            $email = trim($this->request->getData('email'));
            if (!$email) {
                $this->Flash->error(__d('user', 'Please enter an email address'), ['key' => 'auth']);

                return;
            }

            $user = $this->Users->find()->where(['email' => $email])->contain([])->first();
            if (!$user) {
                $this->Flash->error(__d('user', 'No user with such email address'), ['key' => 'auth']);

                return;
            }

            $user = $this->Users->resendVerificationCode($user);
            if ($user && !$user->getErrors()) {
                $this->Flash->success(__d('user', 'An activation email has been sent to {0}', $user->email), ['key' => 'auth']);
                $this->redirect(['action' => 'activate', 'm' => base64_encode($user->email)]);
            } else {
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            }
        } else {
            $user->email = $this->request->getQuery('m')
                ? base64_decode($this->request->getQuery('m')) : null;
        }
        $this->set('user', $user);
    }

    /**
     * Password forgotten method
     * Creates a new password reset code and sends email with password reset link
     * No authentication required
     *
     * @return void
     */
    public function passwordForgotten()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }

        $form = new PasswordForgottenForm();

        if ($this->request->is('post') || $this->request->is('put')) {
            $user = $form->execute($this->request->getData());
            if ($user) {
                $this->Flash->success(__d('user', 'Password recovery info has been sent to you via email. Please check your inbox.'), ['key' => 'auth']);

                if (Configure::read('debug')) {
                    $this->Flash->set(UsersTable::buildPasswordResetUrl($user), ['key' => 'auth']);
                }

                $this->redirect(['action' => 'passwordSent']);
            } else {
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            }
        }

        $this->set('form', $form);
    }

    /**
     * Password forgotten default success action
     *
     * @return void
     */
    public function passwordSent()
    {
    }

    /**
     * Password reset method
     * User can assign new password with username and a password reset code
     * No authentication required
     *
     * @return void|\Cake\Http\Response
     */
    public function passwordReset()
    {
        if ($this->Auth->user()) {
            return $this->redirect(['action' => 'index']);
        }

        $user = null;
        try {
            $query = [];
            if ($this->request->getQuery('u')) {
                $query['username'] = base64_decode($this->request->getQuery('u'));
            }
            if ($this->request->getQuery('c')) {
                $query['password_reset_code'] = base64_decode($this->request->getQuery('c'));
            }

            if (!isset($query['password_reset_code'])) {
                throw new PasswordResetException(__d('user', "Password reset code missing"));
            }

            /** @var \User\Model\Entity\User $user */
            $user = $this->Users->find()
                ->where($query)
                ->first();
            if (!$user) {
                throw new PasswordResetException("Invalid request");
            }

            if ($this->request->is('post') || $this->request->is('put')) {
                $user = $this->Users->resetPassword($user, $this->request->getData());
                if ($user && !$user->getErrors()) {
                    $this->Flash->success(__d('user', 'You can now login with your new password'), ['key' => 'auth']);
                    $this->redirect(['_name' => 'user:login', 'u' => base64_encode($user->username)]);
                } else {
                    $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
                }
            }
        } catch (PasswordResetException $ex) {
            $this->Flash->error($ex->getMessage(), ['key' => 'auth']);
            $this->redirect(['_name' => 'user:login']);
        } catch (\Exception $ex) {
            Log::error("UsersController::resetPassword: " . $ex->getMessage(), ['user']);
            $this->Flash->error(__d('user', 'Something went wrong. Please try again.'), ['key' => 'auth']);
            $this->redirect(['_name' => 'user:login']);
        }

        $this->set('user', $user);
    }

    /**
     * Passsword change method
     *
     * @return void
     */
    public function passwordChange()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->getData())) {
                $this->Flash->success(__d('user', 'Your password has been changed.'), ['key' => 'auth']);
                $this->redirect(['action' => 'passwordChanged']);
            } else {
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }

    /**
     * Password forgotten default success action
     *
     * @return void
     */
    public function passwordChanged()
    {
    }

    /**
     * Return client session info in JSON format
     *
     * @return void
     */
    public function session()
    {
        $this->viewBuilder()->setClassName('Json');
        $data = $this->UserSession->extractSessionInfo();
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
