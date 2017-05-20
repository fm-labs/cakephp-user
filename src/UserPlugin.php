<?php

namespace User;


use Banana\Plugin\PluginInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use User\Event\UserEventListener;
use User\Mailer\UserMailer;

class UserPlugin implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Backend.Menu.get' => 'getBackendMenu'
        ];
    }

    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Users',
            'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
            'data-icon' => 'user',
        ]);
    }

    public function __invoke()
    {
        \Cake\Event\EventManager::instance()->on(new \User\Event\UserEventListener());
    }
}