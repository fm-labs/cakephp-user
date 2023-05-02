<?php
return [
    'User.Email' => [
        'userRegistration' => [
            'emailFormat' => 'text',
            'subject' => 'Your registration',
            'template' => 'User.user_registration',
            'localized' => [
                'de' => [
                    'subject' => 'Ihre Registrierung war erfolgreich. Bitte aktivieren Sie Ihr Konto.',
                    'template' => 'User.user_registration_de',
                ],
            ],
        ],
        'userActivation' => [
            'emailFormat' => 'text',
            'subject' => 'Your account is now verified',
            'template' => 'User.user_activation',
            'localized' => [
                'de' => [
                    'subject' => 'Ihre Konto ist nun verifiziert',
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
                    'subject' => 'Ihr Passwort wurde geändert!',
                    'template' => 'User.password_reset_de',
                ],
            ],
        ],
    ],
];
