<?php
declare(strict_types=1);

namespace User\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use User\Event\AuthEvent;
use User\Form\UserForm;

/**
 * Class UserDebugListener
 *
 * @package User\Event
 */
class UserDebugListener implements EventListenerInterface
{
    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function logEvent(Event $event): void
    {
        $eventName = $event->getName();

        if ($event instanceof AuthEvent) {
            $user = $event->getUser();
        } elseif ($event->getSubject() instanceof UserForm) {
            $user = $event->getSubject()->getUser();
        } elseif ($event->getData('identity')) {
            $user = $event->getData('identity');
        } else {
            $user = $event->getData('user');
        }

        $userId = $user['id'] ?? '';
        $userName = $user['username'] ?? '';
        $userEmail = $user['email'] ?? '';
        Log::debug(sprintf('[UserDebugListener][%s] %s:%s (ID:%s)', $eventName, $userName, $userEmail, $userId), ['user']);
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Authentication.afterIdentify' => 'logEvent',
            'Authentication.logout' => 'logEvent',
            'User.Password.forgotten' => 'logEvent',
            'User.Password.reset' => 'logEvent',
            'User.Model.User.register' => 'logEvent',
            'User.Signup.afterActivate' => 'logEvent',
            'User.Signup.registrationResend' => 'logEvent',
            'User.Auth.beforeLogin' => 'logEvent',
            'User.Auth.login' => 'logEvent',
            'User.Auth.error' => 'logEvent',
            'User.Auth.logout' => 'logEvent',
        ];
    }
}
