<?php
declare(strict_types=1);

namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as CakeAuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Log\Log;
use User\Exception\AuthException;

/**
 * Class AuthComponent
 *
 * @package User\Controller\Component
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @deprecated Use the cakephp/authentication and cakephp/authorization plugins instead.
 */
class AuthComponent extends CakeAuthComponent
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public $Users;

    /**
     * {@inheritDoc}
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        // Inject additional config values
        $this->_defaultConfig += ['userModel' => null];

        parent::__construct($registry, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // user model
        if (!$this->getConfig('userModel')) {
            $this->setConfig('userModel', 'User.Users');
        }

        // default login action
        if (!$this->getConfig('loginAction')) {
            $this->setConfig('loginAction', ['plugin' => 'User', 'controller' => 'User', 'action' => 'login']);
        }

        // default authenticate
        if (!$this->getConfig('authenticate')) {
            $this->setConfig('authenticate', [
                self::ALL => ['userModel' => $this->getConfig('userModel'), 'finder' => 'authUser'],
                'Form' => ['userModel' => $this->getConfig('userModel')],
            ]);
        }

        // default authorize
        if (!$this->getConfig('authorize')) {
            //$this->setConfig('authorize', [
            //    'Controller'
            //]);
        }

        // load user model
        $this->table();
    }

    /**
     * Login method.
     * Dispatches event 'User.Auth.beforeLogin' after authentication, but before user is logged in.
     * Dispatches event 'User.Auth.login' after the user has been authenticated and logged in.
     * Dispatches event 'User.Auth.error' after the user has been authenticated, but login failed.
     *
     * @throws \Exception
     * @return array|null User data or NULL if login failed
     */
    public function login()
    {
        // check if user is already authenticated
        if ($this->user()) {
            return $this->user();
        }

        $request = $this->getController()->getRequest();
        $user = null;
        try {
            // attempt to identify user (any request method)
            $user = $this->identify();
            if ($user) {
                $event = new Event('User.Auth.beforeLogin', $this, [
                    'user' => $user,
                    'request' => $request,
                ]);
                $event = $this->getEventManager()->dispatch($event);
                if ($event->getData('redirect')) {
                    $this->storage()->redirectUrl($event->getData('redirect'));
                }
                if ($event->getData('error')) {
                    throw new AuthException($event->getData('error'), $event->getData('user'));
                }

                if ($event->getResult() === false || $event->isStopped()) {
                    throw new AuthException(__d('user', 'Login failed'), $event->getData('user'));
                }

                // set user in session
                $user = $event->getData('user');
                $this->setUser($user);

                $event = new Event('User.Auth.login', $this, [
                    'user' => $user,
                    'request' => $request,
                ]);
                $this->getEventManager()->dispatch($event);

                return $user;
            } elseif ($request->is('post')) {
                throw new AuthException(__d('user', 'Login failed'), $request->getData());
            }
        } catch (AuthException $ex) {
            $this->setUser(null);
            $this->flash($ex->getMessage());

            $event = new Event('User.Auth.error', $this, [
                'request' => $request,
                'error' => $ex,
            ]);
            $this->getEventManager()->dispatch($event);
        } catch (\Exception $ex) {
            debug($ex->getMessage());
            $this->setUser(null);
            Log::error('AuthComponent: ' . $ex->getMessage(), ['user']);
            throw $ex;
        } finally {
        }

        return $user;
    }

    /**
     * Logout method.
     * Dispatches event 'User.Auth.logout'.
     *
     * @return string Redirect url
     */
    public function logout(): string
    {
        $event = new Event('User.Auth.logout', $this, [
            'user' => $this->user(),
            'request' => $this->getController()->getRequest(), // @deprecated This is redundant, as the request object can be accessed from the event subject
        ]);
        $this->getEventManager()->dispatch($event);

        return parent::logout();
    }

    /**
     * Get user table instance
     *
     * @return \User\Model\Table\UsersTable
     */
    public function table()
    {
        if (!$this->Users) {
            $this->Users = $this->getController()->loadModel($this->getConfig('userModel'));
        }

        return $this->Users;
    }

    /**
     * {@inheritDoc}
     */
    protected function _unauthenticated(Controller $controller): ?Response
    {
        $response = parent::_unauthenticated($controller);

        // do not store redirectUrl for json/xml/flash/requested/ajax requests
        // this extends the core behaviour, where this applies only to ajax requests
        if ($this->getController()->getRequest()->is(['ajax', 'json', 'xml', 'flash', 'requested'])) {
            if ($response->getHeaderLine('Location') == null) {
                //$response->statusCode(403);
                $this->storage()->redirectUrl(false);
            }
        }

        return $response;
    }
}
