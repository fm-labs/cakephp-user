# User plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require fm-labs/cakephp3-user
```

## Features

* Extended AuthComponent for handling auth procedure
* User Registration
* User Registration with Group password
* User Email verification
* User Password reset
* User Password forgotten (by sending a reset link)
* User Password change
* User Password policy
* Email Domain black-/whitelisting
* Email as Username
* Email service to send templated emails on model- and auth events
* Localized User Emails
* Login failure counter
* User Logger
* GoogleAuthenticator support as Authorization provider (2FA)
* GoogleRecaptcha support for registration form

## Configuration

Key                                     | Default       | Overrideable by Settings
---                                     | ---           | --- 
User.model                              | 'User.Users'  | no
User.layout                             | null          | no
User.EventLogger.enabled                | false         | yes
User.Mailer.enabled                     | false         | no
User.Mailer.className                   | null          | no
User.Login.layout                       | null          | no
User.Login.disabled                     | false         | yes
User.Signup.groupAuth                   | false         | no
User.Signup.disabled                    | false         | yes
User.Signup.verifyEmail                 | false         | no
User.Signup.disableEmailVerification    | false         | no
User.Form.register                      | null          | no
User.Mailer.profile                     | ''            | no
User.Blacklist                          | []            | no
User.Recaptcha.enabled                  | false         | yes
GoogleAuthenticator.issuer              | ''            | no
GoogleRecaptcha.secretKey               | false         | no
Session.timeout                         | ?             | no
