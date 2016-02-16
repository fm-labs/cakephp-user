<?php
use Cake\Routing\Router;

// User auth routes
Router::connect('/login', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'login']);
Router::connect('/logout', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'logout']);

// User registration routes
Router::connect('/register', ['plugin' => 'User', 'controller' => 'User', 'action' => 'register']);

//Plugin routes
Router::plugin('User', function ($routes) {

    $routes->prefix('admin', function ($routes) {
        $routes->connect('/:controller');
        $routes->fallbacks('DashedRoute');
    });

    $routes->connect('/:controller');
    $routes->fallbacks('DashedRoute');
});