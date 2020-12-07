<?php
return [
    'Settings' => [
        'User' => [
            'groups' => [
                'User.Services' => [],
                'User.Auth' => [],
                'User.Signup' => [],
                'User.Captcha' => [],
            ],
            'schema' => [
                'User.Login.disabled' => [
                    'group' => 'User.Auth',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.EventLogger.enabled' => [
                    'group' => 'User.Auth',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Signup.disabled' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Signup.groupAuth' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Signup.verifyEmail' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Recaptcha.enabled' => [
                    'group' => 'User.Captcha',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Logging.enabled' => [
                    'group' => 'User.Services',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Mailer.enabled' => [
                    'group' => 'User.Services',
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
        ],
    ],
];
