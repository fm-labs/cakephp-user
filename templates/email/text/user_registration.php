Hallo <?= $user->displayName; ?>,


Ihre Registrierung war erfolgreich.

<?php if ($user->email_verification_required): ?>
Bitte nutzen Sie folgenden Link um Ihren Account freizuschalten!

    <?php echo $verificationUrl; ?>

<?php endif; ?>
