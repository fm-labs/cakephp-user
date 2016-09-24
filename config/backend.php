<?php
return [
    'Backend.Plugin.User.Menu' => [
        'title' => 'User',
        'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
        'data-icon' => 'lock',
        'requireRoot' => true, // temporary access control workaround

        '_children' => [
        ]
    ],
];