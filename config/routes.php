<?php
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Utility\Hash;

$userConfig = (array) Configure::read('User');

/*
if (Hash::get($userConfig, 'Router.rootScope') === true) {
    Router::connect('/login',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'login'],
        ['_name' => 'user:login']
    );
    Router::connect('/logout',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'logout'],
        ['_name' => 'user:logout']
    );
    Router::connect('/register',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'register'],
        ['_name' => 'user:register']
    );
}
*/

// User plugin routes
Router::plugin('User', ['_namePrefix' => 'user:'], function ($routes) {

    $routes->connect('/login',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'login'],
        ['_name' => 'login']
    );
    $routes->connect('/logout',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'logout'],
        ['_name' => 'logout']
    );
    $routes->connect('/register',
        ['plugin' => 'User', 'controller' => 'User', 'action' => 'register'],
        ['_name' => 'register']
    );

    $routes->connect('/:controller');
    $routes->fallbacks('DashedRoute');


    // Admin routes
    $routes->prefix('admin', function ($routes) {
        $routes->connect('/:controller');
        $routes->fallbacks('DashedRoute');
    });
});

unset($userConfig);