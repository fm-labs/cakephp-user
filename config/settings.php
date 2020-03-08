<?php
return [
    'Settings' => [
        'user_auth' => [
            'settings' => [
                'User.Login.disabled' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.EventLogger.enabled' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
        'user_signup' => [
            'settings' => [
                'User.Signup.disabled' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Signup.groupAuth' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Signup.verifyEmail' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
        'user_recaptcha' => [
            'settings' => [
                'User.Recaptcha.enabled' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
    ],
];
