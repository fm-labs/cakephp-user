<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use User\Mailer\UserMailer;

/**
 * Class UserMailerService
 *
 * @package User\Event
 * @todo error handling: catch mailer exceptions.
 */
class UserMailerService implements EventListenerInterface
{
    /**
     * @param Event $event
     * @return void
     */
    public function onRegister(Event $event)
    {
        $mailer = new UserMailer();
        $mailer->send('userRegistration', [$event->subject()]);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onPasswordForgotten(Event $event)
    {
        $mailer = new UserMailer();
        $mailer->send('passwordForgotten', [$event->subject()]);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onPasswordReset(Event $event)
    {
        $mailer = new UserMailer();
        $mailer->send('passwordReset', [$event->subject()]);
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Model.User.passwordForgotten' => 'onPasswordForgotten',
            'User.Model.User.passwordReset'     => 'onPasswordReset',
            'User.Model.User.register'          => 'onRegister',
        ];
    }
}
