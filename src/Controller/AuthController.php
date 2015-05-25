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
    /**
     * @var string Name of auth layout
     */
    public $layout = 'User.auth';

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // allow login method to pass authentication
        $this->Auth->allow(['login']);
    }

    /**
     * Login method
     */
    public function login()
    {
        // check if user is already authenticated
        if ($this->Auth->user()) {
            $this->redirect($this->Auth->redirectUrl());
        }

        // attempt to identify user (any request method)
        $user = $this->Auth->identify();
        if ($user) {
            $this->Flash->set(__('You are logged in now!'));

            // dispatch 'User.login' event
            $event = new Event('User.login', $this, [
                'user' => $user
            ]);
            $this->eventManager()->dispatch($event);

            // authenticate user
            $this->Auth->setUser($event->data['user']);

            // redirect to originally requested url (or login redirect url)
            return $this->redirect($this->Auth->redirectUrl());

        // form login obviously failed
        } elseif ($this->request->is('post')) {
            $this->Auth->flash(__('Login failed'));

            // dispatch 'User.login' event
            $event = new Event('User.login', $this, [
                'user' => false,
                'request' => $this->request
            ]);
            $this->eventManager()->dispatch($event);

        // all other authentication providers also failed to authenticate
        // or no further authentication has occured
        } else {
            // show login form
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
}
