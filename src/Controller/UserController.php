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

        //$this->viewBuilder()->layout('User.auth');

    }

    /**
     * Login method
     * No authentication required
     */
    public function login()
    {
        $redirect = $this->Auth->login();
        if ($redirect) {
            $this->redirect($redirect);
        }

        $user = $this->Users->newEntity();
        //debug($user->errors());
        $this->set('user', $user);
    }

    /**
     * Logout method
     */
    public function logout()
    {
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
            //@TODO Set accessible fields
            //$user = $this->Users->patchEntity($user, $this->request->data, ['validate' => 'passwordForgotten']);
            if ($this->Users->forgotPassword($user, $this->request->data)) {
                //Log::info(sprintf("User %s requested new password", $this->request->data('email')), 'activity');
                $resetUrl = Router::url(['action' => 'passwordReset', 'c' => $user->password_reset_code, 'm' => base64_encode($user->username)], true);
                $this->Flash->success('Ein Link zum Zurücksetzen deines Passworts wurde dir soeben per Email zugesandt! ' . $resetUrl, ['key' => 'auth']);
                $this->redirect(['_name' => 'user:login']);
            } else {
                debug($user->errors());
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
        $user->password_reset_code = $this->request->query('c');

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->resetPassword($user, $this->request->data)) {
                //Log::info(sprintf("User %s reseted password", $this->request->data('User.email')), 'activity');
                $this->Flash->success(__('Your password has been changed'), ['key' => 'auth']);
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
                //@todo make configurable 'user password change' success redirect url
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Ups, something went wrong'), ['key' => 'auth']);
            }
        }
        $this->set('user', $user);
    }
}
