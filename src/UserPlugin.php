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
use Settings\SettingsManager;
use User\Service\GoogleAuthenticatorService;
use User\Service\UserEventLoggerService;
use User\Service\UserLoginService;
use User\Service\UserMailerService;

/**
 * Class UserPlugin
 *
 * @package User
 */
class UserPlugin implements PluginInterface, BackendPluginInterface, EventListenerInterface
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
            'Settings.build' => 'buildSettings',
            'Backend.Sidebar.build' => ['callable' => 'buildBackendSidebarMenu', 'priority' => 99 ],
        ];
    }

    /**
     * @param Event $event
     */
    public function buildSettings(Event $event)
    {
        if ($event->subject() instanceof SettingsManager) {
            $event->subject()->add('User', [
                'Login.disabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Signup.disabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Recaptcha.enabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'EventLogger.enabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                /*
                'Signup.groupAuth' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Signup.verifyEmail' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                */
            ]);
        }
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

    public function bootstrap(Application $app)
    {
        EventManager::instance()->on($this);
        EventManager::instance()->on(new UserLoginService());

        if (Configure::read('User.EventLogger.enabled') == true) {
            EventManager::instance()->on(new UserEventLoggerService());
        }

        if (Configure::read('User.Mailer.enabled') == true) {
            EventManager::instance()->on(new UserMailerService([
                'mailerClass' => Configure::read('User.Mailer.className')
            ]));
        }

        if (Plugin::loaded('GoogleAuthenticator')) {
            EventManager::instance()->on(new GoogleAuthenticatorService());
        }
    }

    public function routes(RouteBuilder $routes)
    {
    }

    public function middleware(MiddlewareQueue $middleware)
    {

    }

    public function backendBootstrap(Backend $backend)
    {

    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');

        return $routes;
    }
}
