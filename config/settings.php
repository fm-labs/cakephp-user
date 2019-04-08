<?php
return [
    'Settings' => [
        /*
        'auth' => [
            'settings' => []
        ],
        */
        'user_signup' => [
            'settings' => [
                'User.Signup.groupAuth' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'User.Signup.verifyEmail' => [
                    'type' => 'boolean',
                    'default' => false
                ]
            ]
        ],
        'user_recaptcha' => [
            'settings' => [
                'User.Recaptcha.enabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
            ]
        ]
    ]
];
