<?php
namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as CakeAuthComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\FlashComponent;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use User\Model\Table\UsersTable;

/**
 * Class AuthComponent
 *
 * @package User\Controller\Component
 * @property FlashComponent $Flash
 */
class AuthComponent extends CakeAuthComponent
{
    /**
     * @var UsersTable
     */
    public $Users;

    static public function url($url)
    {
        if (is_array($url)) {
            list($plugin,$controller) = Configure::read('User.controller');
            $url = array_merge(compact('plugin','controller'), $url);
        }

        return Router::url($url, true);
    }

    /**
     * @param ComponentRegistry $registry
     * @param array $config
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        // Inject additional config values
        $this->_defaultConfig['userModel'] = 'User.Users';
        //$this->_defaultConfig['registerRedirect'] = null;
        $this->_defaultConfig['loginAction'] = ['plugin' => 'User', 'controller' => 'User', 'action' => 'login'];

        $config += (array)Configure::read('User.Auth');

        parent::__construct($registry, $config);
    }

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // default login action
        //if (!$this->config('loginAction')) {
        //    $this->config('loginAction', ['plugin' => 'User', 'controller' => 'User', 'action' => 'login']);
        //}

        // default authenticate
        if (!$this->config('authenticate')) {
            $this->config('authenticate', [
                self::ALL => ['userModel' => $this->config('userModel'), 'finder' => 'authUser'],
                'Form' => [/*'className' => 'User.Form'*/]
            ]);
        }

        // default authorize
        if (!$this->config('authorize')) {
            //$this->config('authorize', [
            //    'Controller'
            //]);
        }

        $this->Users = $this->_registry->getController()->loadModel($this->config('userModel'));
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
            // dispatch 'User.Auth.login' event
            $event = new Event('User.Auth.login', $this, [
                'user' => $user,
                'request' => $this->request
            ]);
            $event = $this->eventManager()->dispatch($event);

            // authenticate user
            $this->setUser($event->data['user']);

            if ($event->result !== null) {
                if (isset($event->result['redirect'])) {
                    $this->storage()->redirectUrl($event->result['redirect']);
                }

                if (isset($event->result['error'])) {
                    $this->flash($event->result['error']);
                }
            }

            if ($event->isStopped() || !$this->user()) {
                return $this->redirectUrl();
            }

            // rehash, if required
            if ($this->authenticationProvider()->needsPasswordRehash()) {
                $user = $this->Users->get($this->user('id'));
                $user->password = $this->request->data('password');
                $this->Users->save($user);

                Log::info(sprintf("AuthComponent: User %s (%s): Password rehashed", $this->user('id'), $this->user('username')), ['user']);
            }

            // redirect to originally requested url (or login redirect url)
            return $this->redirectUrl();

            // form login obviously failed
        } elseif ($this->request->is('post')) {
            $this->flash(__d('user', 'Login failed'));

            // dispatch 'User.Auth.loginFailed' event
            $event = new Event('User.Auth.loginError', $this, [
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
     *
     * @return string Redirect url
     */
    public function logout()
    {
        // dispatch 'User.Auth.logout' event
        $event = new Event('User.Auth.logout', $this, [
            'user' => false,
            'request' => $this->request // @TODO This is redundant, as the request object can be accessed from the event subject
        ]);
        $this->eventManager()->dispatch($event);

        return parent::logout();
    }

    /**
     * @return UsersTable
     * @deprecated
     */
    public function userModel()
    {
        return TableRegistry::get($this->config('userModel'));
    }


    protected function _unauthenticated(Controller $controller)
    {
        $response = parent::_unauthenticated($controller);

        // do not store redirectUrl for json/xml/flash/requested/ajax requests
        // this extends the core behaviour, where this applies only to ajax requests
        if ($this->request->is(['ajax', 'json', 'xml', 'flash', 'requested'])) {
            if ($response->location() == null) {
                //$response->statusCode(403);
                $this->storage()->redirectUrl(false);
            }
        }

        return $response;
    }
}
