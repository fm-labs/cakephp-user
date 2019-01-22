<?php
namespace User\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Form\Form;
use Cake\Log\Log;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Response;
use Cake\Routing\Router;
use User\Exception\PasswordResetException;
use User\Form\PasswordForgottenForm;
//use User\Model\Table\GroupsTable;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 *
 * @package User\Controller
 * @property UsersTable $Users
 * @property GroupsTable $Groups
 */
class UserController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = "User.Users";

    public $captchaActions = ['login', 'register'];

    public function initialize()
    {
        parent::initialize();

        if (!Configure::read('User')) {
            throw new \RuntimeException("UserPlugin: Configuration not loaded!");
        }
    }
    
    /**
     * @param Event $event
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow([
            'login', 'register', 'registerGroup', 'activate', 'activateResend',
            'passwordForgotten', 'passwordSent', 'passwordReset', 'passwordChanged'
        ]);

        if ($this->components()->has('UserSession')) {
            $this->UserSession->ignoreActions(['checkAuth']);
        }


        if (Configure::read('User.layout')) {
            $this->viewBuilder()->layout(Configure::read('User.layout'));
        }
    }

    /**
     * Login method
     * No authentication required
     */
    public function login()
    {
        if ($this->request->query('goto')) {
            //@TODO Check if goto URL is within app scope and/or use a token
            $this->request->session()->write('Auth.redirect', urldecode($this->request->query('goto')));
        } elseif (!$this->request->session()->check('Auth.redirect')) {
            $referer = $this->referer();
            if ($referer && Router::normalize($referer) != Router::normalize(['action' => __FUNCTION__])) {
                //debug("set referer to " . Router::normalize($referer));
                //$this->request->session()->write('Auth.redirect', $referer);
            }
        }

        if (Configure::read('User.Login.disabled') != true) {
            $redirectUrl = $this->Auth->login();
            if ($redirectUrl) {
                $this->redirect($redirectUrl);
            }
        } elseif ($this->request->is(['post'])) {
            $this->Flash->error(__d('user', 'Sorry, but login is currently disabled.'), ['key' => 'auth']);
        }

        $user = $this->Users->newEntity();
        $this->set('user', $user);

        if (Configure::read('User.Login.layout')) {
            $this->viewBuilder()->layout(Configure::read('User.Login.layout'));
        }
    }

    /**
     * Logout method
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
     * @return void|null|Response Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        if ($this->Auth->user('id')) {
            return $this->redirect('/');
        }

        // force group auth
        if (Configure::read('User.Signup.groupAuth') == true) {
            if (!$this->request->session()->read('User.Signup.group_id')) {

                return $this->redirect(['action' => 'registerGroup']);
            }
        }

        $formClass = '\\User\\Form\\UserRegisterForm';
        if (Configure::read('User.Form.register')) {
            $formClass = Configure::read('User.Form.register');
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
                $data = $this->request->data;
                if (Configure::read('User.Signup.groupAuth') == true) {
                    $data['group_id'] = $this->request->session()->read('User.Signup.group_id');
                }

                //$user = $this->Users->register($data);
                $user = $form->execute($data);
                if ($user && $user->id) {
                    //$this->request->session()->delete('User.Signup');
                    $this->Flash->success(__d('user', 'An activation email has been sent to your email address!'), ['key' => 'auth']);
                    $redirect = $this->Auth->config('registerRedirect');
                    $redirect = ($redirect) ?: ['_name' => 'user:login'];
                    $this->redirect($redirect);
                } else {
                    $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
                }
            }
        } else {
            $this->Flash->error(__d('user', 'Sorry, but user registration is currently disabled.'), ['key' => 'auth']);
        }

        $this->set(compact('user', 'form'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Group registration
     */
    public function registerGroup()
    {
        if ($this->request->is(['put', 'post'])) {
            $grpPass = $this->request->data('group_pass');
            $grpPass = trim($grpPass);
            if (!$grpPass) {
                $this->Flash->error(__d('user', 'No password entered'), ['key' => 'auth']);

                return;
            }

            // find user group with that password
            $this->loadModel('User.Groups');
            $userGroup = $this->UserGroups->find()->where(['password' => $grpPass])->first();

            if (!$userGroup) {
                $this->request->session()->delete('User.Signup.group_id');
                $this->Flash->error(__d('user', 'Invalid password'), ['key' => 'auth']);

                return;
            }

            // store group auth info in session
            $this->request->session()->write('User.Signup.group_id', $userGroup->id);
            $this->request->session()->write('User.Signup.group_pass', $grpPass);

            // continue registration
            $this->redirect(['action' => 'register']);
        } elseif ($this->request->session()->read('User.Signup.group_id')) {
            // continue registration
            //$this->redirect(['action' => 'register']);
        }
    }

    /**
     * Activate
     */
    public function activate()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }
        $user = $this->Users->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->activate($this->request->data)) {
                $this->Flash->success(__d('user', 'Your account has been activated. You can login now.'), ['key' => 'auth']);
                $this->redirect(['action' => 'login', 'm' => base64_encode($user->email) ]);
            } else {
                $this->Flash->error(__d('user', 'Account activation failed'), ['key' => 'auth']);
            }
        } else {
            $user->email = ($this->request->query('m'))
                ? base64_decode($this->request->query('m')) : null;
            $user->email_verification_code = ($this->request->query('c'))
                ? base64_decode($this->request->query('c')) : null;
        }
        $this->set('user', $user);
    }

    /**
     * Resend email verification email
     */
    public function activateResend()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }

        $user = $this->Users->newEntity();
        if ($this->request->is('post') || $this->request->is('put')) {

            $email = trim($this->request->data('email'));
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
            if ($user && !$user->errors()) {
                $this->Flash->success(__d('user', 'An activation email has been sent to {0}', $user->email), ['key' => 'auth']);
                $this->redirect(['action' => 'activate', 'm' => base64_encode($user->email)]);
            } else {
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            }
        } else {
            $user->email = ($this->request->query('m'))
                ? base64_decode($this->request->query('m')) : null;
        }
        $this->set('user', $user);
    }

    /**
     * Password forgotten method
     * Creates a new password reset code and sends email with password reset link
     * No authentication required
     */
    public function passwordForgotten()
    {
        if ($this->Auth->user()) {
            $this->redirect(['action' => 'index']);

            return;
        }

        $form = new PasswordForgottenForm();

        if ($this->request->is('post') || $this->request->is('put')) {
            $user = $form->execute($this->request->data);
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
     */
    public function passwordSent()
    {
    }

    /**
     * Password reset method
     * User can assign new password with username and a password reset code
     * No authentication required
     */
    public function passwordReset()
    {
        if ($this->Auth->user()) {
            return $this->redirect(['action' => 'index']);
        }

        $user = null;
        try {

            $query = [];
            if ($this->request->query('u')) {
                $query['username'] = base64_decode($this->request->query('u'));
            }
            if ($this->request->query('c')) {
                $query['password_reset_code'] = base64_decode($this->request->query('c'));
            }

            if (!isset($query['password_reset_code'])) {
                throw new PasswordResetException(__d('user', "Password reset code missing"));
            }

            $user = $this->Users->find()
                ->where($query)
                ->first();
            if (!$user) {
                throw new PasswordResetException("Invalid request");
            }

            if ($this->request->is('post') || $this->request->is('put')) {
                $user = $this->Users->resetPassword($user, $this->request->data);
                if ($user && !$user->errors()) {
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
     */
    public function passwordChange()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
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
     */
    public function passwordChanged()
    {
    }

    /**
     * Return login status info in JSON format
     */
    public function checkAuth()
    {
        $this->viewBuilder()->className('Json');

        $data = [
            'l' => ($this->Auth->user('id')) ? 1 : 0,
            'e' => ($this->request->session()->read('Auth.UserSession.expires')) ?: 0,
            'efmt' => ($this->request->session()->read('Auth.UserSession.expires'))
                ? date(DATE_ATOM, $this->request->session()->read('Auth.UserSession.expires'))
                : 0
        ];

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
