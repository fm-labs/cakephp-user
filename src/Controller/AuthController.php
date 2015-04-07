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

class AuthController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->layout = (Configure::read('User.authLayout')) ?: 'User.auth';

        $this->Auth->allow(['login']);
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
                $this->Flash->set(__('USER_AUTH_LOGIN_SUCCESS'));
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Auth->flash(__('USER_AUTH_LOGIN_FAILURE'));
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
        $this->Flash->success(__('USER_AUTH_LOGOUT_SUCCESS'));
        $this->redirect($this->Auth->logout());
    }
}
