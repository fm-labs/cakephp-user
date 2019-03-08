<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;

/**
 * Class UserPasswordService
 *
 * @package User\Event
 */
class UserPasswordService implements EventListenerInterface
{
    /**
     * @param Event $event The event object
     * @return void
     */
    public function onLogin(Event $event)
    {
        // rehash password, if needed
        /* @var \User\Controller\Component\AuthComponent $Auth */
        $Auth = $event->subject();
        if ($Auth->user() && $Auth->authenticationProvider()->needsPasswordRehash()) {
            $user = $Auth->Users->get($Auth->user('id'));
            $user->password = $Auth->request->data('password');
            $Auth->Users->save($user);

            Log::info(sprintf("AuthComponent: User %s (%s): Password rehashed", $Auth->user('id'), $Auth->user('username')), ['user']);
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Auth.login' => 'onLogin',
        ];
    }
}
