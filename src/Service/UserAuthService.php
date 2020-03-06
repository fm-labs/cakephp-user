<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Http\ServerRequest as Request;

/**
 * Class UserAuthService
 *
 * @package User\Event
 */
class UserAuthService implements EventListenerInterface
{

    /**
     * @param Event $event The event object
     * @return array|void
     */
    public function beforeLogin(Event $event)
    {
        $user = ($event->getData('user')) ?: [];

        if (empty($user)) {
            $event->setData([
                'redirect' => ['_name' => 'user:login']
            ]);

            return false;
        }

        if (isset($user['is_deleted']) && $user['is_deleted'] == true) {
            $event->setData([
                'user' => null,
                'error' => __d('user', 'This account has been deleted'),
                'redirect' => ['_name' => 'user:login']
            ]);

            return false;
        }

        if (isset($user['block_enabled']) && $user['block_enabled'] == true) {
            $event->setData([
                'error' => __d('user', 'This account has been blocked'),
                'redirect' => ['_name' => 'user:login']
            ]);

            return false;
        }

        if (isset($user['login_enabled']) && $user['login_enabled'] != true) {
            $event->setData([
                'error' => __d('user', 'Login to this account is not enabled'),
                'redirect' => ['_name' => 'user:login']
            ]);

            return false;
        }

        if ($user['email_verification_required'] && !$user['email_verified']) {
            $event->setData([
                'error' => __d('user', 'Your account has not been verified yet'),
                'redirect' => ['_name' => 'user:activate']
            ]);

            return false;
        }
    }

    /**
     * @param Event $event The event object
     * @return array|void
     */
    public function afterLogin(Event $event)
    {
        $request = $event->getData('request');
        $user = $event->getData('user');
        if ($user && isset($user['id'])) {
            $clientIp = $clientHostname = null;
            if ($request instanceof Request) {
                $clientIp = $request->clientIp();
                //$clientHostname = null;
            }
            $data = [
                'login_last_login_ip' => $clientIp,
                'login_last_login_host' => $clientHostname,
                'login_last_login_datetime' => new Time(),
                'login_failure_count' => 0, // reset login failure counter
            ];

            /* @var \User\Model\Entity\User $entity */
            $entity = $event->getSubject()->Users->get($user['id']);
            $entity->setAccess(array_keys($data), true);
            $entity = $event->getSubject()->Users->patchEntity($entity, $data);
            if (!$event->getSubject()->Users->save($entity)) {
                Log::error("Failed to update user login info", ['user']);
            }

            EventManager::instance()->dispatch(new Event('User.Model.User.newLogin', $event->getSubject()->Users, [
                'user' => $entity,
                'data' => $data
            ]));
        }
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function onLoginError(Event $event)
    {
        $request = $event->getData('request');
        $data = $request->getData();

        if (isset($data['username'])) {
            $user = $event->getSubject()->Users->findByUsername($data['username'])->first();
            if ($user) {
                $user->login_failure_count++;
                $user->login_failure_datetime = new Time();

                if (!$event->getSubject()->Users->save($user)) {
                    Log::error("Failed to update user with login info", ['user']);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Auth.beforeLogin' => 'beforeLogin',
            'User.Auth.login' => 'afterLogin',
            'User.Auth.error' => 'onLoginError',
        ];
    }
}
