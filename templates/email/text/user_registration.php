

Your registration was successful.


<?php if ($user->email_verification_required): ?>
Please click the following link to verify your email address and activate your account:

    <?php echo $verificationUrl; ?>



Your verification code is:

    <?php echo $user->email_verification_code; ?>


<?php else: ?>
Your account has been activated and you can login here:

    <?php echo $loginUrl; ?>

<?php endif; ?>
