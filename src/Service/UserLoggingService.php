<?php
declare(strict_types=1);

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
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function logEvent(Event $event)
    {
        $eventName = $event->getName();
        $user = $event->getData('user');
        $userName = $user ? $user['username'] : '';
        Log::info(sprintf('[User:%s] %s', $eventName, $userName), ['user']);
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Authentication.afterIdentify' => 'logEvent',
            'Authentication.logout' => 'logEvent',
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
