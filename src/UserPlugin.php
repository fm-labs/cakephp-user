<?php

namespace User;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Banana\Application;
use Banana\Plugin\PluginInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use User\Service\GoogleAuthenticatorService;
use User\Service\UserActivityService;
use User\Service\UserAuthService;
use User\Service\UserLoggingService;
use User\Service\UserMailerService;
use User\Service\UserPasswordService;
use User\Service\UserSessionService;

/**
 * Class UserPlugin
 *
 * @package User
 */
class UserPlugin implements PluginInterface, /*BackendPluginInterface,*/ EventListenerInterface
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
            'Backend.Sidebar.build' => ['callable' => 'buildBackendSidebarMenu', 'priority' => 99 ],
        ];
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function buildBackendSidebarMenu(Event $event)
    {
        if ($event->subject() instanceof \Banana\Menu\Menu) {
            //$settingsMenu = new Menu();
            //$this->eventManager()->dispatch(new Event('Backend.SysMenu.build', $settingsMenu));
            $event->subject()->addItem([
                'title' => __d('user', 'Users'),
                'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                'data-icon' => 'users',
                'children' => [
                    'user_groups' => [
                        'title' => __d('user', 'User Groups'),
                        'url' => ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'index'],
                        'data-icon' => 'users',
                    ]
                ],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        EventManager::instance()->on($this);
        EventManager::instance()->on(new UserAuthService());
        EventManager::instance()->on(new UserSessionService());
        EventManager::instance()->on(new UserPasswordService());

        if (Configure::read('User.Logging.enabled') == true) {
            EventManager::instance()->on(new UserLoggingService(Configure::read('User.Logging')));
        }

        if (Configure::read('User.Mailer.enabled') == true) {
            EventManager::instance()->on(new UserMailerService(Configure::read('User.Mailer')));
        }

        if (Plugin::loaded('Activity')) {
            EventManager::instance()->on(new UserActivityService());
        }

        if (Plugin::loaded('GoogleAuthenticator')) {
            EventManager::instance()->on(new GoogleAuthenticatorService());
        }
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
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function middleware(MiddlewareQueue $middleware)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function backendBootstrap(Backend $backend)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');

        return $routes;
    }
}
