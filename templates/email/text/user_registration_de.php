

Ihre Registrierung war erfolgreich.

<?php if ($user->email_verification_required): ?>
    Bitte nutzen Sie folgenden Link um Ihren Account freizuschalten!

    <?php echo $verificationUrl; ?>


    Ihr Verifizierungs-Code lautet:

    <?php echo $user->email_verification_code; ?>


<?php else: ?>
    Ihr Konto wurde freigeschalten und sie können sich hier anmelden:

    <?php echo $loginUrl; ?>
<?php endif; ?>
