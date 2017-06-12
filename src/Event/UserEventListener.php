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
     */
    protected function _logEvent(Event $event, array $context = ['user'])
    {
        Log::info(sprintf("%s [User:%s]", $event->name(), $event->subject()->id), $context);
    }

    /**
     * @param Event $event
     */
    public function onEvent(Event $event)
    {
        $this->_logEvent($event);
    }

    /**
     * @param Event $event
     */
    public function onLogin(Event $event)
    {
        $this->_logEvent($event);
        Log::info(sprintf("[login][user:%s]", $event->subject()->id), ['auth']);
    }

    /**
     * @param Event $event
     */
    public function onLogout(Event $event)
    {
        $this->_logEvent($event);
        Log::info(sprintf("[logout][user:%s]", $event->subject()->id), ['auth']);
    }

    /**
     * @param Event $event
     */
    public function onPasswordForgotten(Event $event)
    {
        $this->_logEvent($event);

        $mailer = new UserMailer();
        $mailer->send('passwordForgotten', [$event->subject()]);
    }

    /**
     * @param Event $event
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
            'User.Model.User.register'          => 'onEvent',
            'User.login'                        => 'onLogin',
            'User.logout'                       => 'onLogout',
        ];
    }
}
