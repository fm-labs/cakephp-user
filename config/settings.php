<?php

use User\Model\Table\UsersTable;

return [
    'Settings' => [
        'User' => [
            'groups' => [
                'User.Auth' => [],
                'User.Signup' => [],
                'User.Captcha' => [],
                'User.Mailer' => [],
                'User.Password.Security' => [],
                'User.Password.Recovery' => [],
                'User.Debug' => [],
                //'User.Services' => [],
            ],
            'schema' => [
                'User.Login.disabled' => [
                    'group' => 'User.Auth',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('user', 'Disable the login form'),
                ],

                // Signup
                'User.Signup.disabled' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('user', 'Disable the signup form'),
                ],
                'User.Signup.groupAuth' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('user', 'Enable group authentication feature'),
                ],
                'User.Signup.verifyEmail' => [
                    'group' => 'User.Signup',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('user', 'Require email verification'),
                ],

                // Captcha
                'User.Recaptcha.enabled' => [
                    'group' => 'User.Captcha',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('user', 'Enable Google reCAPTCHA feature'),
                ],

                // Mailer
                'User.Mailer.enabled' => [
                    'group' => 'User.Mailer',
                    'type' => 'boolean',
                    'default' => false,
                ],
                'User.Mailer.defaultProfile' => [
                    'group' => 'User.Mailer',
                    'type' => 'string',
                    'default' => null,
                ],

                // Password Security
                'User.Password.minLength' => [
                    'help' => __d('user', 'Minimum length of the password'),
                    'group' => 'User.Password.Security',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinLength,
                ],
                'User.Password.minLowercase' => [
                    'help' => __d('user', 'Minimum number of lowercase characters'),
                    'group' => 'User.Password.Security',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinLowercase,
                ],
                'User.Password.minUppercase' => [
                    'help' => __d('user', 'Minimum number of uppercase characters'),
                    'group' => 'User.Password.Security',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinUppercase,
                ],
                'User.Password.minSpecialChars' => [
                    'help' => __d('user', 'Minimum number of special characters'),
                    'group' => 'User.Password.Security',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinSpecialChars,
                ],
                'User.Password.minNumbers' => [
                    'help' => __d('user', 'Minimum number of numbers'),
                    'group' => 'User.Password.Security',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinNumbers,
                ],
                'User.Password.specialChars' => [
                    'help' => __d('user', 'Special characters allowed in the password'),
                    'group' => 'User.Password.Security',
                    'type' => 'string',
                    'default' => UsersTable::$passwordSpecialChars,
                ],

                // Password recovery
                'User.Password.recoveryEnabled' => [
                    'group' => 'User.Password.Recovery',
                    'type' => 'boolean',
                    'default' => true,
                    'help' => __d('user', 'Enable password recovery feature')
                ],

                'User.Password.resetCodeLength' => [
                    'group' => 'User.Password.Recovery',
                    'type' => 'number',
                    'default' => UsersTable::$passwordResetCodeLength,
                    'help' => __d('user', 'Length of the password reset code (Deprecated)')
                ],
                'User.Password.verificationCodeLength' => [
                    'group' => 'User.Password.Recovery',
                    'type' => 'number',
                    'default' => UsersTable::$verificationCodeLength,
                    'help' => __d('user', 'Length of the password reset code')
                ],
                'User.Password.resetExpiry' => [
                    'group' => 'User.Password.Recovery',
                    'type' => 'number',
                    'default' => UsersTable::$passwordResetExpiry,
                    'help' => __d('user', 'Number of seconds after the password reset code becomes invalid')
                ],

                // Debugging
                'User.Debug.enabled' => [
                    'group' => 'User.Debug',
                    'type' => 'boolean',
                    'default' => false,
                ],

            ],
        ],
    ],
];
