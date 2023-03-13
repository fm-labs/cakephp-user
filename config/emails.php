<?php
return [
    'User.Email' => [
        'userRegistration' => [
            'emailFormat' => 'text',
            'subject' => 'Your registration',
            'template' => 'User.user_registration',
            'localized' => [
                'de' => [
                    'subject' => 'Ihre Registrierung',
                    'template' => 'User.user_registration_de',
                ],
            ],
        ],
        'userActivation' => [
            'emailFormat' => 'text',
            'subject' => 'Your account is verified now',
            'template' => 'User.user_activation',
            'localized' => [
                'de' => [
                    'subject' => 'Ihre Konto ist nun verfiziert',
                    'template' => 'User.user_activation_de',
                ],
            ],
        ],
        'newLogin' => [
            'emailFormat' => 'text',
            'subject' => 'New login',
            'template' => 'User.new_login',
            'localized' => [
                'de' => [
                    'subject' => 'Neue Anmeldung',
                    'template' => 'User.new_login_de',
                ],
            ],
        ],
        'passwordForgotten' => [
            'subject' => 'Password forgotten',
            'template' => 'User.password_forgotten',
            'localized' => [
                'de' => [
                    'subject' => 'Password vergessen',
                    'template' => 'User.password_forgotten_de',
                ],
            ],
        ],
        'passwordReset' => [
            'subject' => 'Your password has been changed',
            'template' => 'User.password_reset',
            'localized' => [
                'de' => [
                    'subject' => 'Ihr Passwort wurde geaendert!',
                    'template' => 'User.password_reset_de',
                ],
            ],
        ],
    ],
];
