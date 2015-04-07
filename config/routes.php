<?php
use Cake\Routing\Router;

Router::connect('/login', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'login']);
Router::connect('/logout', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'logout']);

Router::plugin('User', function ($routes) {
    $routes->fallbacks();
});
