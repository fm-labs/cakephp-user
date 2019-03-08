<?php
use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$userConfig = (array)Configure::read('User');

// User plugin routes
//Router::scope('/user', ['_namePrefix' => 'user:'], function ($routes) {
Router::plugin('User', ['_namePrefix' => 'user:'], function (RouteBuilder $routes) {

    $userController = (Configure::read('User.controller')) ?: 'User.User';
    list($plugin, $controller) = pluginSplit($userController);
    $base = compact('plugin', 'controller');

    $routes->connect(
        '/login',
        $base + ['action' => 'login'],
        ['_name' => 'login']
    );
    $routes->connect(
        '/logout',
        $base + ['action' => 'logout'],
        ['_name' => 'logout']
    );
    $routes->connect(
        '/register',
        $base + ['action' => 'register'],
        ['_name' => 'register']
    );
    $routes->connect(
        '/activate',
        $base + ['action' => 'activate'],
        ['_name' => 'activate']
    );
    $routes->connect(
        '/password-forgotten',
        $base + ['action' => 'passwordForgotten'],
        ['_name' => 'passwordforgotten']
    );
    $routes->connect(
        '/password-reset',
        $base + ['action' => 'passwordReset'],
        ['_name' => 'passwordreset']
    );
    $routes->connect(
        '/password-change',
        $base + ['action' => 'passwordChange'],
        ['_name' => 'passwordchange']
    );
    $routes->connect(
        '/session',
        $base + ['action' => 'session'],
        ['_name' => 'checkauth']
    );
    //$routes->connect('/:action',
    //    $base
    //);
    $routes->connect(
        '/',
        $base + ['action' => 'index'],
        ['_name' => 'profile']
    );

    //$routes->connect('/:controller');
    $routes->fallbacks('DashedRoute');
});

unset($userConfig);
