<?php
use Cake\Routing\Router;

// User auth routes
Router::connect('/login', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'login']);
Router::connect('/logout', ['plugin' => 'User', 'controller' => 'Auth', 'action' => 'logout']);

// User registration routes
Router::connect('/register', ['plugin' => 'User', 'controller' => 'User', 'action' => 'register']);

// User routes
Router::connect('/user/:action/*', ['plugin' => 'User', 'controller' => 'User']);
Router::connect('/user/:action', ['plugin' => 'User', 'controller' => 'User']);
Router::connect('/user', ['plugin' => 'User', 'controller' => 'User', 'action' => 'index']);
