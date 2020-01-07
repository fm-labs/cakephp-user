<?php

namespace User;

use Banana\Application;
use Banana\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\EventManager;
use Cake\Log\Log;
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
class UserPlugin extends BasePlugin
{
    protected $_name = "User";

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);

        include 'functions.php';

        /**
         * Logs
         */
        Log::config('user', [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'user',
            //'levels' => ['info'],
            'scopes' => ['user', 'auth']
        ]);

        /**
         * Mailer support
         */
        if (Configure::read('User.Mailer.enabled') == true && !Configure::check('User.Email')) {
            Configure::load('User.emails');
        }

        EventManager::instance()->on(new UserBackend());
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
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes)
    {
        $routes->connect(
            '/login',
            ['controller' => 'User', 'action' => 'login'],
            ['_name' => 'user:login']
        );
        $routes->connect(
            '/logout',
            ['controller' => 'User', 'action' => 'logout'],
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

        //$routes->connect('/:controller');
        $routes->fallbacks('DashedRoute');
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');

        return $routes;
    }
}
