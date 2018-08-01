<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
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
            'login_last_login_datetime' => new Time()
        ];
        //$event->data['user'] = array_merge($user, $loginData);

        // update user table
        if ($user && isset($user['id'])) {
            $entity = $event->subject()->Users->get($user['id']);
            $entity->accessible(array_keys($loginData), true);
            $entity = $event->subject()->Users->patchEntity($entity, $loginData);
            if (!$event->subject()->Users->save($entity)) {
                Log::error("Failed to update user with login info", ['user']);
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
        ];
    }
}
