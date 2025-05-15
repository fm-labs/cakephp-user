<?php
declare(strict_types=1);

namespace User\Listener;

use Authentication\IdentityInterface;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use Cake\I18n\DateTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Exception;
use User\Event\AuthEvent;
use User\Exception\AuthException;

/**
 * Class UserAuthListener
 *
 * @package User\Event
 */
class AuthenticationListener implements EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Authentication.afterIdentify' => 'afterAuthIdentify',
            'Authentication.logout' => ['callable' => 'onAuthLogout'],

            'User.Auth.beforeLogin' => 'beforeLogin',
            'User.Auth.login' => 'onLogin',
            'User.Auth.logout' => 'onLogout',
            'User.Auth.error' => 'onLoginError',

            //'View.initialize' => 'onViewInitialize'
        ];
    }

    /**
     * @param \User\Event\AuthEvent $event The event object
     * @return void
     */
    public function beforeLogin(AuthEvent $event): ?bool
    {
        // put any logic here, which aborts login attempt early
        //$event->stopPropagation();
        //return false

        //$event->setData('error', 'nope. you are not getting in here');
        //$event->stopPropagation();
        //return false;
        return null;
    }

    /**
     * @param \User\Event\AuthEvent $event The event object
     * @return void
     */
    public function onLogin(AuthEvent $event): void
    {
        $user = $event->getUser();
        $controller = $event->getController();

        if (!$user) {
            return;
        }

        Log::info(sprintf(
            '[login] User %s:%s (ID %s) just logged in',
            $user->get('username'),
            $user->get('email'),
            $user->getIdentifier()
        ), ['user']);

        // store login attempt in users db table
        try {
            $request = $controller->getRequest();
            $this->storeLoginAttempt($user, $request);
        } catch (Exception $ex) {
            Log::error(sprintf('User %s: Failed to save login attempt', $user->getIdentifier()), ['user']);
        }

        // post login checks
        try {
            $this->validateLogin($user);
        } catch (AuthException $ex) {
            Log::error(sprintf('User %s: Failed to validate login attempt', $user->getIdentifier()), ['user']);

            $controller->components()->get('Flash')->error($ex->getMessage(), ['key' => 'auth']);

            $redirectUrl = $ex->getRedirectUrl();
            if ($redirectUrl) {
                $controller->redirect($redirectUrl);

                return;
            }

            // Attempt to logout via AuthComponent
            /** @var \User\Controller\Component\AuthComponent|null $Auth */
            $Auth = $controller->components()->get('Auth');
            if ($Auth) {
                $Auth->logout();

                return;
            }

            // Fallback to logout via AuthenticationComponent
            /** @var \Authentication\Controller\Component\AuthenticationComponent $Authentication */
            $Authentication = $controller->components()->get('Authentication');
            $Authentication->logout();

            return;
        }

        // @todo rehash password if needed
        try {
            $this->rehashPassword();
        } catch (Exception $ex) {
            Log::error(sprintf('User %s: Failed to rehash password', $user->getIdentifier()), ['user']);
        }
    }

    /**
     * @param \User\Event\AuthEvent $event The event object
     * @return void
     */
    public function onLoginError(Event $event): void
    {
        if (!($event instanceof AuthEvent)) {
            return;
        }

        $controller = $event->getController();
        $request = $controller->getRequest();
        $user = $event->getUser();
        $error = $event->getData('error');

        $userId = $user['id'] ?? '';
        $userName = $user['username'] ?? '';
        $userEmail = $user['email'] ?? '';
        $clientIp = $request->clientIp();

        Log::warning(sprintf(
            '[login:error] User %s:%s (ID:%s) failed to login from IP %s: %s',
            $userName,
            $userEmail,
            $userId,
            $clientIp,
            $error
        ), ['user']);

        if ($userName) {
            //Log::warning(sprintf('User %s failed to login', $userName), ['user']);
            $Users = TableRegistry::getTableLocator()->get('User.Users');
            $user = $Users->findByUsername($userName)->first();
            if ($user) {
                $user->login_failure_count++;
                $user->login_failure_datetime = DateTime::now();

                if (!$Users->save($user)) {
                    Log::error('Failed to update user with login info', ['user']);
                }

                //if ($user->login_failure_count > MAX_LOGIN_FAILURES) {
                //    @todo Block login for clientip and username for X minutes (e.g. via NetFilter plugin)
                //}
            }
        }
    }

    /**
     * @param \User\Event\AuthEvent $event Event object
     * @return void
     */
    public function onLogout(AuthEvent $event): void
    {
        $user = $event->getUser();
        Log::info(sprintf(
            '[logout] User %s:%s (ID %s) is logging out',
            $user->get('username'),
            $user->get('email'),
            $user->getIdentifier()
        ), ['user']);
    }

    /**
     * @param \Cake\Event\EventInterface $event The event object
     * @return void
     */
    public function afterAuthIdentify(EventInterface $event): void
    {
        /** @var \Authentication\IdentityInterface $user */
        $user = $event->getData('identity');
        /** @var \Authentication\AuthenticationServiceProviderInterface $provider */
        $provider = $event->getData('provider');
        ///** @var \Authentication\AuthenticationServiceInterface $service */
        //$service = $event->getData('service');
        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();

        Log::info(sprintf('[auth:login] Identification successful'));
    }

    /**
     * @param \Cake\Event\EventInterface $event The event object
     * @return void
     */
    public function onAuthLogout(EventInterface $event): void
    {
        Log::info(sprintf('[auth:logout] Logout successful'));
    }

    /**
     * @param \Authentication\IdentityInterface $user
     * @param \Cake\Http\ServerRequest $request
     * @return void
     */
    protected function storeLoginAttempt(IdentityInterface $user, ServerRequest $request): void
    {
        // @todo Lookup client hostname
        $clientIp = $clientHostname = null;
        $clientIp = $request->clientIp();
        $data = [
            'login_last_login_ip' => $clientIp,
            'login_last_login_host' => $clientHostname,
            'login_last_login_datetime' => DateTime::now(),
            'login_failure_count' => 0, // reset login failure counter
        ];

        $Users = TableRegistry::getTableLocator()->get('User.Users');
        /** @var \User\Model\Entity\User $entity */
        $entity = $Users->get($user->getIdentifier(), contain: []);
        $entity->setAccess(array_keys($data), true);
        $entity = $Users->patchEntity($entity, $data);
        if (!$Users->save($entity)) {
            Log::error('Failed to update user login info', ['user']);
        }
    }

    /**
     * @throws \User\Exception\AuthException
     */
    protected function validateLogin(IdentityInterface $user): void
    {
        // skip additional identification checks for super-users
        $isSuperUser = $user->get('is_superuser');
        if ($isSuperUser) {
            return;
        }

        if ($user->get('is_deleted')) {
            throw new AuthException(__d('user', 'This account has been deleted'), $user);
        }
        if ($user->get('block_enabled')) {
            throw new AuthException(__d('user', 'This account has been blocked'), $user);
        }
        if (!$user->get('login_enabled')) {
            throw new AuthException(__d('user', 'Login to this account is not enabled'), $user);
        }
        //@todo Move to separate EmailVerification service
        if ($user->get('email_verification_required') && !$user->get('email_verified')) {
            throw new AuthException(__d('user', 'Your account has not been verified yet'), $user, ['_name' => 'user:activate']);
        }
    }

    protected function rehashPassword(): void
    {
        //@todo Implement rehashPassword() method
//        // Assuming you are using the `Password` identifier.
//        if ($authentication->identifiers()->get('Password')->needsPasswordRehash()) {
//            // Rehash happens on save.
//            $user = $this->Users->get($authentication->getIdentity()->getIdentifier());
//            $user->password = $this->request->getData('password');
//            $this->Users->save($user);
//        }
    }

//    public function onViewInitialize(EventInterface $event)
//    {
//        /** @var \Cake\View\View $view */
//        $view = $event->getSubject();
//        $view->loadHelper('User.Auth');
//    }
}
