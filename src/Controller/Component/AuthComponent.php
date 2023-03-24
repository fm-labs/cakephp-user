<?php
declare(strict_types=1);

namespace User\Controller\Component;

use Authentication\Controller\Component\AuthenticationComponent;
use Cake\Controller\Component;
use Cake\Controller\Component\FlashComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventManagerInterface;
use Cake\Log\Log;
use User\Event\AuthEvent;
use User\Exception\AuthException;

/**
 * Class AuthComponent
 *
 * A shim component for mapping the old CakePHP AuthComponent to the new standalone AuthenticationComponent
 *
 * @package User\Controller\Component
 * @property AuthenticationComponent $Authentication
 * @property FlashComponent $Flash
 * @todo Add backward-compatibility to CakePHP's legacy AuthComponent
 */
class AuthComponent extends Component
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public $Users;

    //public $components = ['Flash'];

    protected $_defaultConfig = [
        'logoutRedirect' => false,
    ];

    /**
     * @var FlashComponent|null
     */
    public ?FlashComponent $Flash = null;

    /**
     * @var AuthenticationComponent|null
     */
    public ?AuthenticationComponent $Authentication = null;

    /**
     * @inheritDoc
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    protected function _deprecated(string $functionName)
    {
        deprecationWarning(sprintf("(User)AuthComponent::%s() is deprecated. Use AuthenticationComponent from cakephp/authentication package instead!", $functionName));
    }

    /**
     * @param array $config Component config
     * @return void
     * @throws \Exception
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->Flash = $this->getController()->components()->load('Flash');
        $this->Authentication = $this->getController()->components()->load('Authentication.Authentication', [
            'logoutRedirect' => $this->getConfig('logoutRedirect'),
        ]);

        // auto-configure Authentication component
        if (isset($this->getController()->allowUnauthenticated)) {
            $this->Authentication->allowUnauthenticated((array)$this->getController()->allowUnauthenticated);
        }
        // @deprecated
        if (isset($this->getController()->allowedActions)) {
            $this->Authentication->allowUnauthenticated((array)$this->getController()->allowedActions);
        }
    }

    public function beforeFilter()
    {

    }

    /**
     * @param string[] $actions List of allowed actions
     * @return void
     */
    public function allow(array $actions = [], $merge = true): void
    {
        if ($merge) {
            $this->Authentication->addUnauthenticatedActions($actions);
        } else {
            $this->Authentication->allowUnauthenticated($actions);
        }
    }

    /**
     * @param string|false $msg Flash message
     * @return void
     * @deprecated Use Flash::error instead.
     */
    public function flash($msg): void
    {
        $this->_deprecated(__FUNCTION__);
        $this->Flash->error($msg, ['key' => 'auth']);
    }

    /**
     * @param null|string $key Identity data key
     * @return \Authentication\IdentityInterface|mixed|null
     */
    public function user(?string $key = null)
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
     * @param \ArrayAccess|null $user The identity data.
     * @return void
     * @deprecated Use Authentication::setIdentity instead.
     */
    public function setUser(?\ArrayAccess $user): void
    {
        $this->Authentication->setIdentity($user);
        /*
        if ($user === null) {
            $this->logout();
            return;
        }
        */
    }

    /**
     * Logout method.
     * Dispatches event 'User.Auth.logout'.
     * Delegates logout to AuthenticationComponent
     *
     * @return null|string Logout redirect url
     */
    public function logout(): ?string
    {
        $event = new AuthEvent('User.Auth.logout', $this, [
            'user' => $this->user(),
        ]);
        $this->getController()->getEventManager()->dispatch($event);

        return $this->Authentication->logout();
    }

    /**
     * @return null|string Login redirect url
     */
    public function redirectUrl(): ?string
    {
        return $this->Authentication->getLoginRedirect();
    }

    /**
     * Login method.
     * Dispatches event 'User.Auth.beforeLogin' after authentication, but before user is logged in.
     * Dispatches event 'User.Auth.login' after the user has been authenticated and logged in.
     * Dispatches event 'User.Auth.error' after the user has been authenticated, but login failed.
     *
     * @throws \Exception
     * @return void User data or NULL if login failed
     */
    public function login()
    {
//        // check if user is already authenticated
//        if ($this->user()) {
//            return $this->user();
//        }

        $controller = $this->getController();
        $request = $this->getController()->getRequest();
        $user = null;
        try {
            $result = $this->Authentication->getResult();
            if ($controller->getRequest()->is('post') && !$result->isValid()) {
                throw new AuthException('Invalid username or password');
            }

            // authentication successful
            // dispatch 'User.Auth.beforeLogin' event
            if ($result->isValid()) {

                //$user = $this->Authentication->getIdentity();
                $user = $this->user();
                if ($user) {
                    $event = new AuthEvent('User.Auth.beforeLogin', $this, [
                        'user' => $user,
                    ]);
                    $event = $this->getEventManager()->dispatch($event);
//                if ($event->getData('redirect')) {
//                    $this->storage()->redirectUrl($event->getData('redirect'));
//                }

                    // login fails if beforeLogin event has error data set
                    if ($event->getData('error')) {
                        throw new AuthException($event->getData('error'), $event->getData('user'));
                    }

                    // login fails if beforeLogin event is stopped or result is FALSE
                    if ($event->getResult() === false || $event->isStopped()) {
                        throw new AuthException(__d('user', 'Login aborted'), $event->getData('user'));
                    }

                    // login successful
                    // dispatch 'User.Auth.login' event
                    $user = $event->getData('user');
                    $this->setUser($user);

                    $event = new AuthEvent('User.Auth.login', $this, [
                        'user' => $user,
                    ]);
                    $this->getEventManager()->dispatch($event);


//                    // login redirect
//                    //print_r($result->getData());
//                    $defaultRedirect = $controller->config['loginRedirectUrl'] ?? '/';
//                    $target = $this->redirectUrl() ?? $defaultRedirect;
//                    $controller->Flash->success(__d('user', 'Login successful'), ['key' => 'auth']);
//                    $controller->redirect($target);

                } elseif ($request->is('post')) {
                    throw new AuthException(__d('user', 'Login failed'), $user);
                }
            }

        } catch (AuthException $ex) {
            //$this->setUser(null);
            //$this->flash($ex->getMessage());
            Log::error('AuthComponent: ' . $ex->getMessage(), ['user']);

            $event = new AuthEvent('User.Auth.error', $this, [
                'user' => $ex->getUser(),
                'error' => $ex,
            ]);
            $this->getEventManager()->dispatch($event);
            throw $ex;
        } catch (\Exception $ex) {
            debug($ex->getMessage());
            //$this->setUser(null);
            //$this->flash($ex->getMessage());
            Log::error('AuthComponent: ' . $ex->getMessage(), ['user']);
            throw $ex;
        }
    }

    /**
     * @return \Cake\Event\EventManagerInterface
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->getController()->getEventManager();
    }
}
