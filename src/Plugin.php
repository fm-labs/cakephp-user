<?php
declare(strict_types=1);

namespace User;

use Banana\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Routing\Route\DashedRoute;
use User\Service\UserAuthService;
use User\Service\UserLoggingService;
use User\Service\UserMailerService;
use User\Service\UserPasswordService;

/**
 * Class UserPlugin
 *
 * @package User
 */
class Plugin extends BasePlugin
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

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
            EventManager::instance()->on(new UserBackend());
            //\Backend\Backend::register($this->getName, UserBackend::class);
        }

        /**
         * Settings
         */
        if (\Cake\Core\Plugin::isLoaded('Settings')) {
            //EventManager::instance()->on(new Settings());
            \Settings\SettingsManager::register($this->getName(), function ($settings) {
                /** @var \Settings\SettingsManager $settings */
                $settings->load('User.settings');
            });

            /*
            Hook::addCallback('settings_build', function ($settings, $options) {
                $settings['foo'] = [
                    'type' => 'string'
                ];
                return $settings;
            });
            $settings = Hook::callback('settings_build', [], ['some' => 'option']);

            Hook::addAction('settings_notify', function ($xy) {
                // do something without returning anything
            });
            Hook::action('settings_notify', '1234');
            */
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
        $routes->plugin('User', [], function ($routes) {
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
        });

        $routes->scope('/admin/user', ['prefix' => 'admin', 'plugin' => 'User'], function ($routes) {
            $routes->fallbacks(DashedRoute::class);
        });
    }
}
