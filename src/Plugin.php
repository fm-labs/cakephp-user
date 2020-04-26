<?php
declare(strict_types=1);

namespace User;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Log\Log;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;
use User\Service\UserAuthService;
use User\Service\UserLoggingService;
use User\Service\UserMailerService;
use User\Service\UserPasswordService;

/**
 * Class UserPlugin
 *
 * @package User
 */
class Plugin extends BasePlugin implements AuthenticationServiceProviderInterface
{
    public $bootstrapEnabled = true;

    public $routesEnabled = true;

    public $middlewareEnabled = true;

    public $consoleEnabled = true;

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();
        $service->setConfig([
            'unauthenticatedRedirect' => '/user/login',
            'queryParam' => 'redirect',
        ]);

        $fields = [
            'username' => 'username',
            'password' => 'password',
        ];

        // Load the authenticators, you want session first
        $service->loadAuthenticator('Authentication.Session');
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => '/user/login',
        ]);

        // Load identifiers
        $service->loadIdentifier('Authentication.Password', [
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => 'User.Users',
            ],
            'fields' => $fields,
        ]);

        return $service;
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        $app->addPlugin('Authentication');

        include 'functions.php';

        /**
         * Logs
         */
        if (!Log::getConfig('user')) {
            Log::setConfig('user', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'user',
                //'levels' => ['info'],
                'scopes' => ['user', 'auth'],
            ]);
        }

        /**
         * Authentication
         */
        EventManager::instance()->on(new UserAuthService());
        EventManager::instance()->on(new UserPasswordService());
        //EventManager::instance()->on(new UserSessionService());


        /**
         * Mailer support
         */
        if (Configure::read('User.Mailer.enabled') == true) {
            if (!Configure::check('User.Email')) {
                Configure::load('User.emails');
            }
            EventManager::instance()->on(new UserMailerService(Configure::read('User.Mailer')));
        }

        /**
         * Logging
         */
        if (Configure::read('User.Logging.enabled') == true) {
            EventManager::instance()->on(new UserLoggingService(Configure::read('User.Logging')));
        }

        /**
         * Administration
         */
        if (\Cake\Core\Plugin::isLoaded('Backend')) {
            EventManager::instance()->on(new Admin());
            //\Backend\Backend::register($this->getName, UserBackend::class);
        }

        /**
         * Settings
         */
        if (\Cake\Core\Plugin::isLoaded('Settings')) {
            //EventManager::instance()->on(new Settings());
            \Settings\SettingsManager::register($this->getName(), function (\Settings\SettingsManager $settings) {
                $settings->load('User.settings');
            });
        }

        /*
        if ($app->getPlugins()->has('Activity')) {
            EventManager::instance()->on(new UserActivityService());
        }

        if ($app->getPlugins()->has('GoogleAuthenticator')) {
            EventManager::instance()->on(new GoogleAuthenticatorService());
        }
        */
    }

    /**
     * {@inheritDoc}
     */
    public function routes(\Cake\Routing\RouteBuilder $routes): void
    {
        Router::plugin('User', [], function ($routes) {
            $routes->connect(
                '/login',
                ['controller' => 'Auth', 'action' => 'login'],
                ['_name' => 'user:login']
            );
            $routes->connect(
                '/logout',
                ['controller' => 'Auth', 'action' => 'logout'],
                ['_name' => 'user:logout']
            );
            $routes->connect(
                '/register',
                ['controller' => 'User', 'action' => 'register'],
                ['_name' => 'user:register']
            );
            $routes->connect(
                '/activate',
                ['controller' => 'User', 'action' => 'activate'],
                ['_name' => 'user:activate']
            );
            $routes->connect(
                '/password-forgotten',
                ['controller' => 'User', 'action' => 'passwordForgotten'],
                ['_name' => 'user:passwordforgotten']
            );
            $routes->connect(
                '/password-reset',
                ['controller' => 'User', 'action' => 'passwordReset'],
                ['_name' => 'user:passwordreset']
            );
            $routes->connect(
                '/password-change',
                ['controller' => 'User', 'action' => 'passwordChange'],
                ['_name' => 'user:passwordchange']
            );
            $routes->connect(
                '/session',
                ['controller' => 'User', 'action' => 'session'],
                ['_name' => 'user:checkauth']
            );
            //$routes->connect('/:action',
            //    $base
            //);
            $routes->connect(
                '/',
                ['controller' => 'User', 'action' => 'index'],
                ['_name' => 'user:profile']
            );

            // LEGACY ROUTES
            $routes->connect(
                '/xlogin',
                ['controller' => 'User', 'action' => 'login'],
                ['_name' => 'user:x:login']
            );
            $routes->connect(
                '/xlogout',
                ['controller' => 'User', 'action' => 'logout'],
                ['_name' => 'user:x:logout']
            );

            //$routes->connect('/:controller');
            $routes->fallbacks('DashedRoute');
        });

        $routes->scope('/admin/user', ['prefix' => 'Admin', 'plugin' => 'User'], function ($routes) {
            $routes->fallbacks(DashedRoute::class);
        });
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        // Various other middlewares for error handling, routing etc. added here.

        // Create an authentication middleware object
        $authentication = new AuthenticationMiddleware($this);

        // Add the middleware to the middleware queue.
        // Authentication should be added *after* RoutingMiddleware.
        // So that subdirectory information and routes are loaded.
        $middlewareQueue->add($authentication);

        return $middlewareQueue;
    }
}
