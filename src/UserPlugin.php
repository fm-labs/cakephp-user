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
use Cake\Log\Engine\FileLog;
use Cake\Log\Log;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\RouteBuilder;
use Psr\Http\Message\ServerRequestInterface;
use User\Listener\AuthenticationListener;
use User\Listener\GoogleAuthenticatorListener;
use User\Listener\UserDebugListener;
use User\Mailer\UserMailer;

/**
 * Class UserPlugin
 *
 * @package User
 */
class UserPlugin extends BasePlugin implements AuthenticationServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        /**
         * Configuration / Settings
         */
        Configure::load('User.user');
        Configure::load('User.emails');
        if (\Cake\Core\Plugin::isLoaded('Settings')) {
            Configure::load('User', 'settings');
        }

        /**
         * Logs
         */
        if (!Log::getConfig('user')) {
            Log::setConfig('user', [
                'className' => FileLog::class,
                'path' => LOGS,
                'file' => 'user',
                //'levels' => ['info'],
                'scopes' => ['user', 'auth'],
            ]);
        }

        /**
         * Authentication
         */
        $app->addPlugin('Authentication');
        EventManager::instance()->on(new AuthenticationListener());
        //EventManager::instance()->on(new UserSessionService());


        /**
         * Mailer support
         */
        if (Configure::read('User.Mailer.enabled')) {
            if (!Configure::check('User.Email')) {
                Configure::load('User.emails');
            }
            // Make sure Mailman plugin is loaded before UserMailer is instantiated
            $app->addOptionalPlugin("Mailman");
            //EventManager::instance()->on(new UserMailerService(Configure::read('User.Mailer')));
            EventManager::instance()->on(new UserMailer());
        }

        /**
         * Logging
         */
        if (Configure::read('User.Debug.enabled')) {
            EventManager::instance()->on(new UserDebugListener());
        }

//        /**
//         * Activity integration
//         */
//        if (\Cake\Core\Plugin::isLoaded('Activity') && Configure::read('User.Activity.enabled')) {
//            EventManager::instance()->on(new UserActivityListener());
//        }

        /**
         * Google Recaptcha integration
         */
        if (Configure::read('User.Captcha.enabled')) {
            $app->addOptionalPlugin('GoogleRecaptcha');
            //if (\Cake\Core\Plugin::isLoaded('GoogleRecaptcha')) {
            //    EventManager::instance()->on(new GoogleRecaptchaListener());
            //}
        }

        /**
         * OTP / Google Authenticator integration
         */
        if (Configure::read('User.Otp.enabled')) {
            EventManager::instance()->on(new GoogleAuthenticatorListener());
        }

        /**
         * Administration
         */
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \User\UserAdmin());
        }
    }

    /**
     * @inheritDoc
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->plugin('User', [], function ($routes) {
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
                ['controller' => 'Signup', 'action' => 'register'],
                ['_name' => 'user:register']
            );
            $routes->connect(
                '/activate',
                ['controller' => 'Signup', 'action' => 'activate'],
                ['_name' => 'user:activate']
            );
            $routes->connect(
                '/password-forgotten',
                ['controller' => 'Password', 'action' => 'passwordForgotten'],
                ['_name' => 'user:passwordforgotten']
            );
            $routes->connect(
                '/password-reset',
                ['controller' => 'Password', 'action' => 'passwordReset'],
                ['_name' => 'user:passwordreset']
            );
            $routes->connect(
                '/password-change',
                ['controller' => 'Password', 'action' => 'passwordChange'],
                ['_name' => 'user:passwordchange']
            );
            $routes->connect(
                '/session',
                ['controller' => 'Auth', 'action' => 'session'],
                ['_name' => 'user:checkauth']
            );
            $routes->connect(
                '/',
                ['controller' => 'User', 'action' => 'index'],
                ['_name' => 'user:profile']
            );
            $routes->fallbacks('DashedRoute');
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
        $authentication = new AuthenticationMiddleware($this);
        $middlewareQueue->insertBefore(RoutingMiddleware::class, $authentication);

        return $middlewareQueue;
    }

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

        if (!Configure::read('User.Login.disabled')) {
            $service->loadAuthenticator('Authentication.Form', [
                'fields' => $fields,
                //'loginUrl' => '/user/login',
            ]);

            // Load identifiers
            $service->loadIdentifier('Authentication.Password', [
                'resolver' => [
                    'className' => 'Authentication.Orm',
                    'userModel' => 'User.Users',
                ],
                'fields' => $fields,
            ]);
        }

        return $service;
    }
}
