<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;

/**
 * Class UserLoggingService
 *
 * @package User\Event
 */
class UserLoggingService implements EventListenerInterface
{
    /**
     * @param Event $event The event object
     * @return void
     */
    public function logEvent(Event $event)
    {
        $user = null;
        if ($event->getData('user')) {
            Log::info(sprintf("[User:%s] %s", $event->getName(), $event->getData('user')['username']), ['user']);
        } else {
            Log::info(sprintf("[User:%s]", $event->getName()), ['user']);
        }
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'User.Model.User.passwordForgotten' => 'logEvent',
            'User.Model.User.passwordReset' => 'logEvent',
            'User.Model.User.register' => 'logEvent',
            'User.Model.User.activate' => 'logEvent',
            'User.Model.User.activationResend' => 'logEvent',
            'User.Auth.login' => 'logEvent',
            'User.Auth.error' => 'logEvent',
            'User.Auth.logout' => 'logEvent',
        ];
    }
}
