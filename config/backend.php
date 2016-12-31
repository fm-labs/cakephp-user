<?php
return [
    'Backend.Plugin.User.Menu' => [

        'system' => [
            [
                'title' => 'Users',
                'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                'data-icon' => 'user',
            ]
        ]

    ],
];