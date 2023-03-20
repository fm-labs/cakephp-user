<?php
declare(strict_types=1);

namespace User\Listener;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Class GoogleAuthenticatorService
 *
 * @package User\Event
 */
class GoogleAuthenticatorListener implements EventListenerInterface
{
    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onLogout(Event $event)
    {
        $event->getSubject()->request->getSession()->delete('Auth.GoogleAuth');
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'User.Auth.logout' => 'onLogout',
        ];
    }
}
