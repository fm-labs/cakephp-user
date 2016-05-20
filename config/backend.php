<?php
return [
    'Backend.Plugin.User.Menu' => [
        'title' => 'User',
        'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
        'icon' => 'lock',
        'requireRoot' => true, // temporary access control workaround

        '_children' => [
        ]
    ],
];