<?php
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Utility\Hash;

$userConfig = (array) Configure::read('User');

/*
if (Hash::get($userConfig, 'Router.rootScope') === true) {
    Router::connect('/login',
        $base + ['action' => 'login'],
        ['_name' => 'user:login']
    );
    Router::connect('/logout',
        $base + ['action' => 'logout'],
        ['_name' => 'user:logout']
    );
    Router::connect('/register',
        $base + ['action' => 'register'],
        ['_name' => 'user:register']
    );
}
*/

// User plugin routes
Router::scope('/user', ['_namePrefix' => 'user:'], function ($routes) {

    $userController = (Configure::read('User.controller')) ?: 'User.User';
    list($plugin, $controller) = pluginSplit($userController);
    $base = compact('plugin', 'controller');
    
    $routes->connect('/login',
        $base + ['action' => 'login'],
        ['_name' => 'login']
    );
    $routes->connect('/logout',
        $base + ['action' => 'logout'],
        ['_name' => 'logout']
    );
    $routes->connect('/register',
        $base + ['action' => 'register'],
        ['_name' => 'register']
    );
    $routes->connect('/activate',
        $base + ['action' => 'activate'],
        ['_name' => 'activate']
    );
    $routes->connect('/password-forgotten',
        $base + ['action' => 'passwordForgotten'],
        ['_name' => 'passwordforgotten']
    );
    $routes->connect('/password-reset',
        $base + ['action' => 'passwordReset'],
        ['_name' => 'passwordreset']
    );
    $routes->connect('/password-change',
        $base + ['action' => 'passwordChange'],
        ['_name' => 'passwordchange']
    );
    $routes->connect('/:action',
        ['plugin' => 'User', 'controller' => 'User']
    );
    $routes->connect('/',
        $base + ['action' => 'index'],
        ['_name' => 'profile']
    );

    //$routes->connect('/:controller');
    //$routes->fallbacks('DashedRoute');
});

unset($userConfig);