<?php
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
 */
class AuthComponent extends CakeAuthComponent
{
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        // Inject additional config values
        $this->_defaultConfig['userModel'] = 'User.Users';

        parent::__construct($registry, $config);
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

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
                self::ALL => ['userModel' => $this->config('userModel'), 'finder' => 'authUser'],
                'Form' => ['userModel' => $this->config('userModel')]
            ]);
        }

        // default authorize
        if (!$this->config('authorize')) {
            //$this->config('authorize', [
            //    'Controller'
            //]);
        }
    }

    /**
     * Login method
     *
     * @return string|array Redirect url
     */
    public function login()
    {
        // check if user is already authenticated
        if ($this->user()) {
            return $this->redirectUrl();
        }

        // attempt to identify user (any request method)
        $user = $this->identify();
        if ($user) {

            // dispatch 'User.login' event
            $event = new Event('User.login', $this, [
                'user' => $user,
                'request' => $this->request
            ]);
            $this->eventManager()->dispatch($event);

            // authenticate user
            $this->setUser($event->data['user']);

            // redirect to originally requested url (or login redirect url)
            return $this->redirectUrl();

            // form login obviously failed
        } elseif ($this->request->is('post')) {
            $this->flash(__d('user', 'Login failed'));

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
        $this->Flash->success(__d('user', 'You are logged out now!'), ['key' => 'auth']);

        // dispatch 'User.login' event
        $event = new Event('User.logout', $this, [
            'user' => false,
            'request' => $this->request
        ]);
        $this->eventManager()->dispatch($event);
        return parent::logout();
    }

    /**
     * @return UsersTable
     */
    public function userModel()
    {
        return TableRegistry::get($this->_userModel);
    }

    /**
     * @deprecated Use login() method instead
     */
    public function userLogin()
    {
        $this->login();
    }

    /**
     * @deprecated Use logout() method instead
     */
    public function userLogout()
    {
        $this->logout();
    }
}
