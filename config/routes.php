<?php
use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

// User plugin routes
Router::plugin('User', ['_namePrefix' => 'user:'], function (RouteBuilder $routes) {
    $routes->connect(
        '/login',
        ['controller' => 'User', 'action' => 'login'],
        ['_name' => 'login']
    );
    $routes->connect(
        '/logout',
        ['controller' => 'User', 'action' => 'logout'],
        ['_name' => 'logout']
    );
    $routes->connect(
        '/register',
        ['controller' => 'User', 'action' => 'register'],
        ['_name' => 'register']
    );
    $routes->connect(
        '/activate',
        ['controller' => 'User', 'action' => 'activate'],
        ['_name' => 'activate']
    );
    $routes->connect(
        '/password-forgotten',
        ['controller' => 'User', 'action' => 'passwordForgotten'],
        ['_name' => 'passwordforgotten']
    );
    $routes->connect(
        '/password-reset',
        ['controller' => 'User', 'action' => 'passwordReset'],
        ['_name' => 'passwordreset']
    );
    $routes->connect(
        '/password-change',
        ['controller' => 'User', 'action' => 'passwordChange'],
        ['_name' => 'passwordchange']
    );
    $routes->connect(
        '/session',
        ['controller' => 'User', 'action' => 'session'],
        ['_name' => 'checkauth']
    );
    //$routes->connect('/:action',
    //    $base
    //);
    $routes->connect(
        '/',
        ['controller' => 'User', 'action' => 'index'],
        ['_name' => 'profile']
    );

    //$routes->connect('/:controller');
    $routes->fallbacks('DashedRoute');
});
