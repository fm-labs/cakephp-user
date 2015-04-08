<?php
use Cake\Routing\Router;

Router::connect('/login', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'login']);
Router::connect('/logout', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'logout']);

Router::plugin('User', function (\Cake\Routing\RouteBuilder $routes) {

    $routes->prefix('admin', function (\Cake\Routing\RouteBuilder $routes) {
        $routes->connect('/:controller', ['action' => 'index', 'prefix' => null], ['routeClass' => 'InflectedRoute']);
        $routes->connect('/:controller/:action/*', ['prefix' => null], ['routeClass' => 'InflectedRoute']);
        $routes->fallbacks('InflectedRoute');
    });

    $routes->fallbacks('InflectedRoute');
});
