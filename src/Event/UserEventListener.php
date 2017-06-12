<?php

namespace User\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use User\Mailer\UserMailer;

/**
 * Class UserEventListener
 *
 * @package User\Event
 */
class UserEventListener implements EventListenerInterface
{
    /**
     * @param Event $event
     * @param array $context
     * @return void
     */
    protected function _logEvent(Event $event, array $context = ['user'])
    {
        Log::info(sprintf("[User:%s] %s", $event->subject()->id, $event->name()), $context);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onRegister(Event $event)
    {
        $this->_logEvent($event);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onLogin(Event $event)
    {
        $this->_logEvent($event);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onLogout(Event $event)
    {
        $this->_logEvent($event);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onPasswordForgotten(Event $event)
    {
        $this->_logEvent($event);

        $mailer = new UserMailer();
        $mailer->send('passwordForgotten', [$event->subject()]);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function onPasswordReset(Event $event)
    {
        $this->_logEvent($event);

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
            'User.login'                        => 'onLogin',
            'User.logout'                       => 'onLogout',
        ];
    }
}
