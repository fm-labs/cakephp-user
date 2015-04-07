<?php
return [
    'User' => [
        'model' => 'User.Users',
        'authLayout' => 'User.auth',
        'userLayout' => 'User.user'
    ],
    'User.Auth' => [
        'loginAction' => '/login',
        'logoutAction' => '/logout',
        'logoutRedirect' => '/login?logout=1',
        'authenticate' => [
            'Form' => ['userModel' => 'User.Users'],
        ],
        'authorize' => false,
    ]
];