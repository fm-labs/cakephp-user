<?php
declare(strict_types=1);

namespace User;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * @inheritDoc
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->fallbacks(DashedRoute::class);
    }

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_system' => ['callable' => 'buildAdminSystemMenu', 'priority' => 999 ],
        ];
    }

    /**
     * @param \Cake\Event\Event $event The event.
     * @param \Cupcake\Menu\MenuItemCollection $menu The menu.
     * @return void
     */
    public function buildAdminSystemMenu(Event $event, \Cupcake\Menu\MenuItemCollection $menu): void
    {
        $menu->addItem([
            'title' => __d('user', 'Users'),
            'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
            'data-icon' => 'users',
//            'children' => [
//                'users' => [
//                    'title' => __d('user', 'Users'),
//                    'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
//                    'data-icon' => 'users',
//                ],
//                'user_groups' => [
//                    'title' => __d('user', 'User Groups'),
//                    'url' => ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'index'],
//                    'data-icon' => 'users',
//                ],
//            ],
        ]);
    }
}
