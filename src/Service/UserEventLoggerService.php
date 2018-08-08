<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;

/**
 * Class UserEventLoggerService
 *
 * @package User\Event
 */
class UserEventLoggerService implements EventListenerInterface
{
    /**
     * @param Event $event
     * @param array $context
     * @return void
     */
    public function logEvent(Event $event)
    {
        $user = null;
        if (isset($event->data['user'])) {
            Log::info(sprintf("[User:%s] %s", $event->name(), $event->data['user']['username']), ['user']);
        } else {
            Log::info(sprintf("[User:%s]", $event->name()), ['user']);
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Model.User.passwordForgotten' => 'logEvent',
            'User.Model.User.passwordReset'     => 'logEvent',
            'User.Model.User.register'          => 'logEvent',
            'User.Model.User.activate'          => 'logEvent',
            'User.login'                        => 'logEvent',
            'User.loginError'                   => 'logEvent',
            'User.logout'                       => 'logEvent',
        ];
    }
}
