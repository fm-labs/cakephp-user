<?php

namespace User\Event;


use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use User\Mailer\UserMailer;

class UserEventListener implements EventListenerInterface
{

    protected function _logEvent(Event $event, array $context = ['user'])
    {
        Log::info(sprintf("%s [User:%s]", $event->name(), $event->subject()->id), $context);
    }

    public function onEvent(Event $event)
    {
        $this->_logEvent($event);
    }

    public function onLogin(Event $event)
    {
        $this->_logEvent($event);
        Log::info(sprintf("[login][user:%s]", $event->subject()->id), ['auth']);
    }

    public function onLogout(Event $event)
    {
        $this->_logEvent($event);
        Log::info(sprintf("[logout][user:%s]", $event->subject()->id), ['auth']);
    }

    public function onPasswordForgotten(Event $event)
    {
        $this->_logEvent($event);

        $mailer = new UserMailer();
        $mailer->send('passwordForgotten', [$event->subject()]);
    }

    public function onPasswordReset(Event $event)
    {
        $this->_logEvent($event);

        $mailer = new UserMailer();
        $mailer->send('passwordReset', [$event->subject()]);
    }

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * ### Example:
     *
     * ```
     *  public function implementedEvents()
     *  {
     *      return [
     *          'Order.complete' => 'sendEmail',
     *          'Article.afterBuy' => 'decrementInventory',
     *          'User.onRegister' => ['callable' => 'logRegistration', 'priority' => 20, 'passParams' => true]
     *      ];
     *  }
     * ```
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'User.Model.User.passwordForgotten' => 'onPasswordForgotten',
            'User.Model.User.passwordReset' => 'onPasswordReset',
            'User.Model.User.register' => 'onEvent',
            'User.login' => 'onLogin',
            'User.logout' => 'onLogout',
        ];
    }
}