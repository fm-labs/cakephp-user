<?php
return [
    'User.Email' => [
        'userRegistration' => [
            'subject' => 'Your registration',
            'template' => 'User.user_registration',
            '_localized' => [
                'de' => [
                    'subject' => 'Ihre Registrierung',
                    'template' => 'User.user_registration_de',
                ],
            ],
        ],
        'passwordForgotten' => [
            'subject' => 'Password forgotten',
            'template' => 'User.password_forgotten',
            '_localized' => [
                'de' => [
                    'subject' => 'Password vergessen',
                    'template' => 'User.password_forgotten_de',
                ],
            ],
        ],
        'passwordReset' => [
            'subject' => 'Your password has been changed',
            'template' => 'User.password_reset',
            '_localized' => [
                'de' => [
                    'subject' => 'Ihr Passwort wurde geaendert!',
                    'template' => 'User.password_reset_de',
                ],
            ],
        ],
    ],
];
