<?php
declare(strict_types=1);

namespace User\Service;

use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\I18n\FrozenTime;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class UserAuthService
 *
 * @package User\Event
 */
class UserAuthService implements EventListenerInterface
{

//    public function onViewInitialize(EventInterface $event)
//    {
//        /** @var \Cake\View\View $view */
//        $view = $event->getSubject();
//        $view->loadHelper('User.Auth');
//    }

    /**
     * @param \Cake\Event\EventInterface $event The event object
     * @return void
    // @todo Refactor with separate Fail2ban-like service
     */
    public function onLoginError(EventInterface $event)
    {
        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();
        $request = $controller->getRequest();
        $username = $request->getData('username');
        $clientIp = $request->clientIp();

        if ($username) {
            Log::warning(sprintf('User %s failed to login', $username), ['user']);
            $Users = TableRegistry::getTableLocator()->get('User.Users');
            $user = $Users->findByUsername($username)->first();
            if ($user) {
                $user->login_failure_count++;
                $user->login_failure_datetime = FrozenTime::now();

                if (!$Users->save($user)) {
                    Log::error('Failed to update user with login info', ['user']);
                }

                //if ($user->login_failure_count > MAX_LOGIN_FAILURES) {
                //    @todo Block login for clientip and username for X minutes (e.g. via NetFilter plugin)
                //}
            }
        }
    }

    public function afterIdentify(EventInterface $event): void
    {
        /** @var \User\Model\Entity\User|\Authentication\IdentityInterface $user */
        $user = $event->getData('identity');
        /** @var \Authentication\AuthenticationServiceProviderInterface $provider */
        $provider = $event->getData('provider');
        ///** @var \Authentication\AuthenticationServiceInterface $service */
        //$service = $event->getData('service');
        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();

        if (!$user || !$provider) {
            return;
        }

        $isSuperUser = $user->get('is_superuser');
        $role = $isSuperUser ? 'admin' : 'normal';
        Log::info(sprintf('User[%s] %s is logging in', $role, $user->id), ['user']);

        // store login attempt in users db table
        // @todo Refactor with separate LoginHistory/SessionHistory service
        try {
            $request = $controller->getRequest();
            $clientIp = $clientHostname = null;
            if ($request instanceof \Cake\Http\ServerRequest) {
                $clientIp = $request->clientIp();
                // @todo Lookup client hostname
                //$clientHostname = null;
            }
            $data = [
                'login_last_login_ip' => $clientIp,
                'login_last_login_host' => $clientHostname,
                'login_last_login_datetime' => FrozenTime::now(),
                'login_failure_count' => 0, // reset login failure counter
            ];

            $Users = TableRegistry::getTableLocator()->get('User.Users');
            /** @var \User\Model\Entity\User $entity */
            $entity = $Users->get($user->getIdentifier(), ['contain' => []]);
            $entity->setAccess(array_keys($data), true);
            $entity = $Users->patchEntity($entity, $data);
            if (!$Users->save($entity)) {
                Log::error('Failed to update user login info', ['user']);
            }
        } catch (\Exception $ex) {
            Log::critical(sprintf('User %s: Failed to save login attempt', $user->getIdentifier()), ['user']);
        }

        // skip additional identification checks for super-users
        if ($isSuperUser) {
            return;
        }

        // logout helper
        $doLogout = function (?string $errMsg = null) use ($controller) {
            if ($errMsg) {
                $controller->components()->get('Flash')->error($errMsg, ['key' => 'auth']);
            }
            $controller->components()->get('Authentication')->logout();
        };
        // redirect helper
        $doRedirect = function ($url, ?string $errMsg = null) use ($controller) {
            if ($errMsg) {
                $controller->components()->get('Flash')->error($errMsg, ['key' => 'auth']);
            }
            $controller->redirect($url);
        };

        if ($user->get('is_deleted')) {
            $doLogout(__d('user', 'This account has been deleted'));
        }
        if ($user->get('block_enabled')) {
            $doLogout(__d('user', 'This account has been blocked'));
        }
        if (!$user->get('login_enabled')) {
            $doLogout(__d('user', 'Login to this account is not enabled'));
        }
        //@todo Move to separate EmailVerification service
        if ($user->get('email_verification_required') == true && $user->get('email_verified') == false) {
            $doRedirect(['_name' => 'user:activate'], __d('user', 'Your account has not been verified yet'));
        }
    }

    /**
     * @param \Cake\Event\EventInterface $event Event object
     * @return void
     */
    public function onLogout(EventInterface $event): void
    {
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Authentication.afterIdentify' => 'afterIdentify',
            'Authentication.logout' => 'onLogout',
            'User.Auth.error' => 'onLoginError',
            //'View.initialize' => 'onViewInitialize'
        ];
    }
}
