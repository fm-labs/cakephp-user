<?php
return [
    'Backend.Plugin.User.Menu' => [

        'system' => [
            [
                'title' => 'User',
                'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                'data-icon' => 'users',
            ]
        ]

    ],
];