<?php
return [
    'Backend.Plugin.User.Menu' => [
        'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
        'icon' => 'lock',
        'requireRoot' => true, // temporary access control workaround

        '_children' => [
        ]
    ],
];