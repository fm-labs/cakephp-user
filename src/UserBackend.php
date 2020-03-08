<?php

namespace User;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class UserBackend implements EventListenerInterface
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
            'Settings.build' => 'settings',
            'Backend.Menu.build.admin_primary' => ['callable' => 'buildMenu', 'priority' => 99 ],
        ];
    }

    /**
     * @param Event $event The event object
     * @param \Settings\SettingsManager $settings The settings manager object
     * @return void
     */
    public function settings(Event $event, $settings)
    {
        $settings->load('User.settings');
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function buildMenu(Event $event, \Banana\Menu\Menu $menu)
    {
        $menu->addItem([
            'title' => __d('user', 'Users'),
            'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
            'data-icon' => 'users',
            'children' => [
                'users' => [
                    'title' => __d('user', 'Users'),
                    'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                    'data-icon' => 'users',
                ],
                'user_groups' => [
                    'title' => __d('user', 'User Groups'),
                    'url' => ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'index'],
                    'data-icon' => 'users',
                ],
            ],
        ]);
    }
}
