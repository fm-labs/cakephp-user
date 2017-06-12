<?php

namespace User;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use User\Event\UserEventListener;

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
            'Settings.get' => 'getSettings',
            'Backend.Menu.get' => ['callable' => 'getBackendMenu', 'priority' => 99],
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    /**
     * @param Event $event
     */
    public function getSettings(Event $event)
    {
        $event->result['User'] = [
            'layout' => [
                'type' => 'string',
            ],
            'Login.disabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'Signup.disabled' => [
                'type' => 'boolean',
                'default' => false
            ],
            'Signup.groupAuth' => [
                'type' => 'boolean',
                'default' => false
            ],
        ];
    }

    /**
     * @param Event $event
     */
    public function buildBackendRoutes(Event $event)
    {
        Router::scope('/user/admin', ['plugin' => 'User', 'prefix' => 'admin', '_namePrefix' => 'user:admin:'],
            function ($routes) {
                //$routes->connect('/:controller');
                $routes->fallbacks('DashedRoute');
            });
    }

    /**
     * @param Event $event
     */
    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Users',
            'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
            'data-icon' => 'user',
        ]);
    }

    /**
     *
     */
    public function __invoke()
    {
        EventManager::instance()->on(new UserEventListener());
    }
}
