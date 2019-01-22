<?php
return [
    'User.model' => 'User.Users',
    'User.controller' => 'User.User',

    'User.EventLogger.enabled' => false,

    'User.Mailer.enabled' => false,
    'User.Mailer.profile' => 'default',
    'User.Mailer.className' => 'User.User',
    //'User.layout' => 'User.auth',
    //'User.Login.disabled' => false,
    //'User.Login.layout' => null,
    //'User.Logout.layout' => null,
    //'User.Signup.disable' => false,
    //'User.Signup.disableEmailVerification' => false,
    //'User.PasswordReset.disable' => false,
    //'User.PasswordReset.redirectUrl' => null,
    //'User.Blacklist.enabled' => false,

    'User.Recaptcha.enabled' => false,
];
