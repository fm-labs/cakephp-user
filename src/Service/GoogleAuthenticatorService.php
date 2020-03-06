<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Http\ServerRequest as Request;

/**
 * Class GoogleAuthenticatorService
 *
 * @package User\Event
 */
class GoogleAuthenticatorService implements EventListenerInterface
{

    /**
     * @param Event $event The event object
     * @return void
     */
    public function onLogout(Event $event)
    {
        $event->getSubject()->request->getSession()->delete('Auth.GoogleAuth');
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Auth.logout' => 'onLogout',
        ];
    }
}
