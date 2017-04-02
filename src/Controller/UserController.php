<?php
namespace User\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 * @package User\Controller
 *
 * @property UsersTable $Users
 */
class UserController extends AppController
{

    public $modelClass = "User.Users";

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['login', 'register', 'passwordForgotten', 'passwordReset']);

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
        }

        $redirectUrl = $this->Auth->login();
        if ($redirectUrl) {
            // Use Authcomponents User.login event instead
            //$event = $this->eventManager()->dispatch(new Event('User.Model.User.login', $this->Auth->user()));
            $this->redirect($redirectUrl);
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
        // Use Authcomponents User.logout event instead
        //$event = $this->eventManager()->dispatch(new Event('User.Model.User.logout', $this->Auth->user()));

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

        if ($this->request->is('post')) {
            $user = $this->Users->register($this->request->data);
            if ($user && $user->id) {
                $this->Flash->success(__('Your registration was successful!'), ['key' => 'auth']);
                $this->redirect($this->Auth->redirectUrl());
                return;
            } else {
                $this->Flash->error(__('Ups, something went wrong. Please check the form.'), ['key' => 'auth']);
            }
        } else {
            $user = $this->Users->register(null);
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
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
                $this->Flash->error(__('Something went wrong'));
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

                $this->Flash->success(__('You can now login with your new password'), ['key' => 'auth']);
                $this->redirect(['_name' => 'user:login', 'u' => base64_encode($user->username)]);
            } else {
                //@todo check if link has expired -> Document expired
                debug($user->errors());
                $this->Flash->error(__('Failed to reset password'), ['key' => 'auth']);
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
                $this->Flash->success(__('Your password has been changed.'), ['key' => 'auth']);
                $this->redirect(['_name' => 'user:profile']);
            } else {
                $this->Flash->error(__('Ups, something went wrong'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }
}
