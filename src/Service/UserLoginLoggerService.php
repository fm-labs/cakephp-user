<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Network\Request;

/**
 * Class UserLoginLoggerService
 *
 * @package User\Event
 */
class UserLoginLoggerService implements EventListenerInterface
{

    /**
     * @param Event $event
     * @return void
     */
    public function onLogin(Event $event)
    {
        $request = (isset($event->data['request'])) ? $event->data['request'] : null;
        $user = (isset($event->data['user'])) ? $event->data['user'] : [];
        $clientIp = $clientHostname = null;

        if ($request instanceof Request) {
            $clientIp = $request->clientIp();
            $clientHostname = null;
        }

        $loginData = [
            'login_last_login_ip' => $clientIp,
            'login_last_login_host' => $clientHostname,
            'login_last_login_datetime' => new Time(),
            'login_failure_count' => 0, // reset login failure counter
        ];
        //$event->data['user'] = array_merge($user, $loginData);

        // update user table
        if ($user && isset($user['id'])) {
            $user = $event->subject()->Users->get($user['id']);
            $user->accessible(array_keys($loginData), true);
            $user = $event->subject()->Users->patchEntity($user, $loginData);
            if (!$event->subject()->Users->save($user)) {
                Log::error("Failed to update user with login info", ['user']);
            }
        }

        EventManager::instance()->dispatch(new Event('User.Model.User.newLogin', $user, $loginData));
    }

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
            } else {
                //$event->subject()->flash('FUCK IT');
            }
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.login'                        => 'onLogin',
            'User.loginError'                   => 'onLoginError',

            //'User.Model.User.passwordForgotten' => 'logEvent',
            //'User.Model.User.passwordReset'     => 'logEvent',
            //'User.Model.User.register'          => 'logEvent',
        ];
    }
}
