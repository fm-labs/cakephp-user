<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/6/15
 * Time: 10:39 PM
 */

namespace User\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use User\Model\Table\UsersTable;

/**
 * Class AuthController
 *
 * @package User\Controller
 *
 * @property UsersTable $Users
 */
class AuthController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->layout = (Configure::read('User.authLayout')) ?: 'User.auth';

        $this->Auth->allow(['login']);

        $this->Users = TableRegistry::get(Configure::read('User.userModel') ?: 'User.Users');
    }

    /**
     * Login method
     */
    public function login()
    {
        // authentication via form post
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Flash->set(__('You are logged in now!'));
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Auth->flash(__('Login failed'));
            }
        // already authenticated
        } elseif ($this->Auth->user()) {
            $this->redirect($this->Auth->redirectUrl());
        }
    }

    /**
     * Logout method
     */
    public function logout()
    {
        $this->Flash->success(__('You are logged out now!'));
        $this->redirect($this->Auth->logout());
    }

    public function password_change()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__('Your password has been changed.'));
                //@todo make configurable 'user password change' success redirect url
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }
}
