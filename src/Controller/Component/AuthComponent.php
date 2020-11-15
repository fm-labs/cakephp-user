<?php
declare(strict_types=1);

namespace User\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Event\EventManagerInterface;
use Cake\Log\Log;
use User\Exception\AuthException;

/**
 * Class AuthComponent
 *
 * A shim component for mapping the old CakePHP AuthComponent to the new standalone AuthenticationComponent
 *
 * @package User\Controller\Component
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @deprecated Use the cakephp/authentication and cakephp/authorization plugins instead.
 */
class AuthComponent extends Component
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public $Users;

    public $components = ['Authentication.Authentication', 'Flash'];

    protected $_defaultConfig = [
        'loginAction' => null,
    ];

    /**
     * @inheritDoc
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * @param array $config Component config
     * @return void
     * @throws \Exception
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->getController()->loadComponent('Authentication.Authentication');
        $this->getController()->loadComponent('Flash');

        // default login action
        if (!$this->getConfig('loginAction')) {
            $this->setConfig('loginAction', ['plugin' => 'User', 'controller' => 'User', 'action' => 'login']);
        }

    }

    /**
     * @return \Cake\Event\EventManagerInterface
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->getController()->getEventManager();
    }

    /**
     * @param string[] $actions List of allowed actions
     * @return void
     */
    public function allow($actions = []): void
    {
        $this->Authentication->addUnauthenticatedActions($actions);
    }

    /**
     * @param string $msg Flash message
     * @return void
     */
    public function flash($msg): void
    {
        $this->Flash->error($msg, ['key' => 'auth']);
    }

    /**
     * @param null|string $key Identity data key
     * @return \Authentication\IdentityInterface|mixed|null
     */
    public function user($key = null)
    {
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return null;
        }

        if ($key !== null) {
            return $this->Authentication->getIdentityData($key);
        }

        return $identity;
    }

    /**
     * @param \ArrayAccess $user The identity data.
     * @return void
     */
    public function setUser($user): void
    {
        if ($user === null) {
            $this->Authentication->logout();

            return;
        }
        $this->Authentication->setIdentity($user);
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
            $result = $this->Authentication->getResult();
            if ($this->getController()->getRequest()->is('post') && !$result->isValid()) {
                throw new AuthException('Invalid username or password');
            }

            $user = $this->Authentication->getIdentity();
            if ($user) {
                $event = new Event('User.Auth.beforeLogin', $this, [
                    'user' => $user,
                    'request' => $request,
                ]);
                $event = $this->getEventManager()->dispatch($event);
//                if ($event->getData('redirect')) {
//                    $this->storage()->redirectUrl($event->getData('redirect'));
//                }
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

        return $this->Authentication->logout();
    }

    /**
     * @return string|null
     */
    public function redirectUrl()
    {
        return $this->Authentication->getLoginRedirect();
    }
}
