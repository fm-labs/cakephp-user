<?php
return [
    'Settings' => [
        'User.Auth' => [
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
        'User.Signup' => [
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
        'User.Captcha' => [
            'settings' => [
                'User.Recaptcha.enabled' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
    ],
];
