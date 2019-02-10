<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Network\Request;

/**
 * Class UserLoginService
 *
 * @package User\Event
 */
class UserLoginService implements EventListenerInterface
{

    /**
     * @param Event $event
     * @return null|array
     */
    public function onLogin(Event $event)
    {
        $request = (isset($event->data['request'])) ? $event->data['request'] : null;
        $user = (isset($event->data['user'])) ? $event->data['user'] : [];

        if (empty($user)) {
            $event->stopPropagation();

            return [
                'redirect' => ['_name' => 'user:login']
            ];
        }

        if (isset($user['is_deleted']) && $user['is_deleted'] == true) {
            $event->data['user'] = null;
            $event->stopPropagation();

            return [
                'error' => __d('user', 'This account has been deleted'),
                'redirect' => ['_name' => 'user:login']
            ];
        }

        if (isset($user['block_enabled']) && $user['block_enabled'] == true) {
            $event->data['user'] = null;
            $event->stopPropagation();

            return [
                'error' => __d('user', 'This account has been blocked'),
                'redirect' => ['_name' => 'user:login']
            ];
        }

        if (isset($user['login_enabled']) && $user['login_enabled'] != true) {
            $event->data['user'] = null;
            $event->stopPropagation();

            return [
                'error' => __d('user', 'Login to this account is not enabled'),
                'redirect' => ['_name' => 'user:login']
            ];
        }

        if ($user['email_verification_required'] && !$user['email_verified']) {
            $event->data['user'] = null;
            $event->stopPropagation();

            return [
                'error' => __d('user', 'Your account has not been verified yet'),
                'redirect' => ['_name' => 'user:activate']
            ];
        }

        // update user table
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
        //$event->data['user'] = array_merge($user, $loginData);
        if ($user && isset($user['id'])) {
            $user = $event->subject()->Users->get($user['id']);
            $user->accessible(array_keys($data), true);
            $user = $event->subject()->Users->patchEntity($user, $data);
            if (!$event->subject()->Users->save($user)) {
                Log::error("Failed to update user login info", ['user']);
            }
        }

        EventManager::instance()->dispatch(new Event('User.Model.User.newLogin', $this, compact('user', 'data')));
    }

    /**
     * @param Event $event
     */
    public function onLoginError(Event $event)
    {
        //debug($event->data);

        $request = $event->data['request'];
        $data = $request->data;

        if (isset($data['username'])) {
            $user = $event->subject()->Users->findByUsername($data['username'])->first();
            if ($user) {
                $user->login_failure_count++;
                $user->login_failure_datetime = new Time();

                if (!$event->subject()->Users->save($user)) {
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
            'User.Auth.login' => 'onLogin',
            'User.Auth.loginError' => 'onLoginError',
        ];
    }
}
