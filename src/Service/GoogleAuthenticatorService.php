<?php
declare(strict_types=1);

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Class GoogleAuthenticatorService
 *
 * @package User\Event
 */
class GoogleAuthenticatorService implements EventListenerInterface
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
