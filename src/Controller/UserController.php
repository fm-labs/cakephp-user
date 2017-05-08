<?php
namespace User\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use User\Model\Table\GroupsTable;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 * @package User\Controller
 *
 * @property UsersTable $Users
 * @property GroupsTable $Groups
 */
class UserController extends AppController
{

    public $modelClass = "User.Users";

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['login', 'register', 'registerGroup', 'passwordForgotten', 'passwordReset']);

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
                //$this->Flash->success('Login. Redirect to '. $redirectUrl);
                $this->redirect($redirectUrl);
            }
        } elseif ($this->request->is(['post'])) {
            $this->Flash->error(__d('user','Sorry, but login is currently disabled.'), ['key' => 'auth']);
        }


        $user = $this->Users->newEntity();
        $this->set('user', $user);

        if (Configure::read('User.loginLayout')) {
            $this->viewBuilder()->layout(Configure::read('User.loginLayout'));
        }
    }

    /**
     * Logout method
     */
    public function logout()
    {
        //$this->Flash->success(__d('user', 'You are logged out now!'), ['key' => 'auth']);
        $this->redirect($this->Auth->logout());
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
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        //@TODO Make user registration configurable
        //@TODO Dispatch 'User.register' event

        if ($this->Auth->user('id')) {
            $this->redirect('/');
            return;
        }

        if (Configure::read('User.Signup.groupAuth') === true) {
            if (!$this->request->session()->read('User.Signup.group_id')) {
                $this->redirect(['action' => 'registerGroup']);
                return;
            }
        }

        $user = null;
        if (Configure::read('User.Signup.disabled') != true) {

            if ($this->request->is('post')) {
                $data = $this->request->data;
                if (Configure::read('User.Signup.groupAuth') === true) {
                    $data['group_id'] = $this->request->session()->read('User.Signup.group_id');
                }

                $user = $this->Users->register($data);
                debug($user->errors());
                if ($user && $user->id) {
                    //$this->request->session()->delete('User.Signup');
                    $this->Flash->success(__d('user','An activation email has been sent to your email address!'), ['key' => 'auth']);
                    $this->redirect(['_name' => 'user:login']);
                    return;
                } else {
                    $this->Flash->error(__d('user','Please fill all required fields'), ['key' => 'auth']);
                }
            } else {
                $user = $this->Users->register([]);
            }

        } else {
            $this->Flash->error(__d('user','Sorry, but user registration is currently disabled.'), ['key' => 'auth']);
        }


        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    public function registerGroup()
    {
        if ($this->request->is(['put', 'post'])) {

            $grpPass = $this->request->data('group_pass');
            $grpPass = trim($grpPass);
            if (!$grpPass) {
                $this->Flash->error('Es wurde kein Passwort eingegeben', ['key' => 'auth']);
                return;
            }

            // find user group with that password
            $this->loadModel('User.Groups');
            $userGroup = $this->Groups->find()->where(['password' => $grpPass])->first();

            if (!$userGroup) {
                $this->request->session()->delete('User.Signup.group_id');
                $this->Flash->error('Ungültiges Passwort', ['key' => 'auth']);
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
     * Password forgotten method
     * Creates a new password reset code and sends email with password reset link
     * No authentication required
     */
    public function passwordforgotten() {
        if ($this->Auth->user()) {
            $this->redirect(array('action' => 'index'));
            return;
        }
        $user = $this->Users->newEntity();
        $user->username = ($this->request->query('u')) ? base64_decode($this->request->query('u')) : null;
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->forgotPassword($user, $this->request->data) && !$user->errors()) {
                $this->Flash->success('A password reset link has been sent to you via email. Please check your inbox.', ['key' => 'auth']);
                $this->redirect(['action' => 'passwordreset', 'u' => base64_encode($user->username),]);
            } else {
                $this->Flash->error(__d('user','Something went wrong'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }

    /**
     * Password reset method
     * User can assign new password with username and a password reset code
     * No authentication required
     */
    public function passwordreset() {
        if ($this->Auth->user()) {
            $this->redirect(array('action' => 'index'));
            return;
        }

        $user = $this->Users->newEntity();
        $user->username = ($this->request->query('u')) ? base64_decode($this->request->query('u')) : null;
        $user->password_reset_code = ($this->request->query('c')) ? base64_decode($this->request->query('c')) : null;

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->resetPassword($user, $this->request->data)) {

                $event = $this->eventManager()->dispatch(new Event('User.Model.User.passwordReset', $user));

                $this->Flash->success(__d('user','You can now login with your new password'), ['key' => 'auth']);
                $this->redirect(['_name' => 'user:login', 'u' => base64_encode($user->username)]);
            } else {
                //@todo check if link has expired -> Document expired
                debug($user->errors());
                $this->Flash->error(__d('user','Failed to reset password'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }

    /**
     * Passsword change method
     */
    public function passwordchange()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__d('user','Your password has been changed.'), ['key' => 'auth']);
                $this->redirect(['_name' => 'user:profile']);
            } else {
                $this->Flash->error(__d('user','Ups, something went wrong'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }
}
