<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/7/15
 * Time: 8:04 PM
 */

namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as CakeAuthComponent;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\FlashComponent;
use User\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;

/**
 * Plugin User
 * Class AuthComponent
 *
 * @package User\Controller\Component
 *
 * @property FlashComponent $Flash
 *
 * @TODO Localize User AuthComponent
 */
class AuthComponent extends CakeAuthComponent
{
    protected $_userModel;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_userModel = Configure::read('User.userModel') ?: 'User.Users';

        // default login action
        if (!$this->config('loginAction')) {
            $this->config('loginAction', [
                'controller' => 'Auth',
                'action' => 'login',
                'plugin' => 'User'
            ]);
        }

        // default authenticate
        if (!$this->config('authenticate')) {
            $this->config('authenticate', [
                self::ALL => ['userModel' => $this->_userModel],
                'Form' => ['userModel' => $this->_userModel]
            ]);
        }

        // default authorize
        if (!$this->config('authorize')) {
            //$this->config('authorize', [
            //    'Controller'
            //]);
        }
    }

    public function startup(Event $event)
    {
        parent::startup($event);
    }

    /**
     * Login method
     */
    public function userLogin()
    {
        // check if user is already authenticated
        if ($this->user()) {
            $this->redirect($this->redirectUrl());
        }

        // attempt to identify user (any request method)
        $user = $this->identify();
        if ($user) {
            $this->Flash->success(__('You are logged in now!'), ['key' => 'auth']);

            // dispatch 'User.login' event
            $event = new Event('User.login', $this, [
                'user' => $user
            ]);
            $this->eventManager()->dispatch($event);

            // authenticate user
            $this->setUser($event->data['user']);

            // redirect to originally requested url (or login redirect url)
            return $this->redirect($this->redirectUrl());

            // form login obviously failed
        } elseif ($this->request->is('post')) {
            $this->flash(__('Login failed'));

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
    public function userLogout()
    {
        $this->Flash->success(__('You are logged out now!'), ['key' => 'auth']);
        $this->redirect($this->logout());
    }

    /**
     * @return UsersTable
     */
    public function userModel()
    {
        return TableRegistry::get($this->_userModel);
    }

    protected function redirect($url)
    {
        $this->_registry->getController()->redirect($url);
    }
}
