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
        Log::info(sprintf("[User:%s] %s", get_class($event->subject()), $event->name()), ['user']);
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
            'User.login'                        => 'logEvent',
            'User.logout'                       => 'logEvent',
        ];
    }
}
