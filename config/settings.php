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
                'User.Password' => [],
                'User.PasswordRecovery' => [],
                'User.Debug' => [],
                //'User.Services' => [],
            ],
            'schema' => [
                'User.Login.disabled' => [
                    'group' => 'User.Auth',
                    'type' => 'boolean',
                    'default' => false,
                ],

                // Signup
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

                // Captcha
                'User.Recaptcha.enabled' => [
                    'group' => 'User.Captcha',
                    'type' => 'boolean',
                    'default' => false,
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

                // Password
                'User.Password.minLength' => [
                    'group' => 'User.Password',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinLength,
                ],
                'User.Password.minLowercase' => [
                    'group' => 'User.Password',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinLowercase,
                ],
                'User.Password.minUppercase' => [
                    'group' => 'User.Password',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinUppercase,
                ],
                'User.Password.minSpecialChars' => [
                    'group' => 'User.Password',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinSpecialChars,
                ],
                'User.Password.minNumbers' => [
                    'group' => 'User.Password',
                    'type' => 'number',
                    'default' => UsersTable::$passwordMinNumbers,
                ],
                'User.Password.specialChars' => [
                    'group' => 'User.Password',
                    'type' => 'string',
                    'default' => UsersTable::$passwordSpecialChars,
                ],
                'User.Password.resetCodeLength' => [
                    'group' => 'User.PasswordRecovery',
                    'type' => 'number',
                    'default' => UsersTable::$passwordResetCodeLength,
                    'help' => __d('user', 'Length of the password reset code (Deprecated)')
                ],
                'User.Password.verificationCodeLength' => [
                    'group' => 'User.PasswordRecovery',
                    'type' => 'number',
                    'default' => UsersTable::$verificationCodeLength,
                    'help' => __d('user', 'Length of the password reset code')
                ],
                'User.Password.resetExpiry' => [
                    'group' => 'User.PasswordRecovery',
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
