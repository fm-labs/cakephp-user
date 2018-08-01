<?php
use Cake\Routing\Router;
?>

Hallo <?= $user->displayName; ?>,


Ihre Registrierung war erfolgreich.

<?php if ($user->email_verification_required): ?>
Bitte nutzen Sie folgenden Link um Ihren Account freizuschalten!

<?php echo Router::url(array('plugin' => 'User', 'controller' => 'User', 'action'=>'activate',
    'c'=> base64_encode($user->email_verification_code), 'm'=>base64_encode($user->email)), true); ?>


oder kopieren Sie folgende URL in Ihren Webbrowser ein:

<?php echo Router::url(array('plugin' => 'User', 'controller' => 'User', 'action'=>'activate'), true); ?>


und aktivieren Ihren Account mit folgendem Aktivierungs-Code: <?php echo $user->email_verification_code; ?>

<?php endif; ?>
