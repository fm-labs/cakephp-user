<?php
declare(strict_types=1);

namespace User;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cupcake\Menu\MenuItemCollection;

class UserAdmin extends BaseAdminPlugin implements EventListenerInterface
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
            //'Controller.initialize' => ['callable' => 'controllerInitialize'],
            'Admin.Menu.build.admin_system' => ['callable' => 'buildAdminSystemMenu', 'priority' => 999 ],
        ];
    }

    /**
     * @param \Cake\Event\EventInterface $event
     * @return void
     * @throws \Exception
     */
    public function controllerInitialize(EventInterface $event): void
    {
        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();
        $controller->components()->load('User.UserSession');
    }

    /**
     * @param \Cake\Event\Event $event The event.
     * @param \Cupcake\Menu\MenuItemCollection $menu The menu.
     * @return void
     */
    public function buildAdminSystemMenu(Event $event, MenuItemCollection $menu): void
    {
        $menu->addItem([
            'title' => __d('user', 'Users'),
            'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
            'data-icon' => 'users',
            'children' => [
                'users' => [
                    'title' => __d('user', 'Users'),
                    'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                    'data-icon' => 'user',
                ],
                'user_groups' => [
                    'title' => __d('user', 'User Groups'),
                    'url' => ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'index'],
                    'data-icon' => 'users',
                ],
                'user_sessions' => [
                    'title' => __d('user', 'User Sessions'),
                    'url' => ['plugin' => 'User', 'controller' => 'UserSessions', 'action' => 'index'],
                    'data-icon' => 'user-secret',
                ],
            ],
        ]);
    }
}
