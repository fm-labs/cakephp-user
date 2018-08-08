<?php
return [
    'User.Email' => [
        'userRegistration' => [
            'subject' => 'Your registration',
            'template' => 'User.user_registration'
        ],
        'passwordForgotten' => [
            'subject' => 'Password forgotten',
            'template' => 'User.password_forgotten'
        ],
        'passwordReset' => [
            'subject' => 'Your password has been changed',
            'template' => 'User.password_reset'
        ]
    ],

    'User.EmailTranslation.de' => [
        'userRegistration' => [
            'subject' => 'Ihre Registrierung',
            'template' => 'User.user_registration'
        ],
        'passwordForgotten' => [
            'subject' => 'Password vergessen',
            'template' => 'User.password_forgotten'
        ],
        'passwordReset' => [
            'subject' => 'Ihr Passwort wurde geaendert!',
            'template' => 'User.password_reset'
        ]
    ]
];
