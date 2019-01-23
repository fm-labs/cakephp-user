<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Account Verification'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Setup 2-Factor Auth'));
?>
<div class="user-gauth-form user-form form">

    <p>
        1. Download Google Authenticator app for your smartphone
    </p>
    <p>
        2. Open Google Authenticator app and scan QR code
    </p>
    <div class="image" style="margin-bottom: 1em;">
        <?php
        if ($imgUri) {
            echo $this->Html->image($imgUri);
        } else {
            echo __d('user', "Failed to render QR image");
        }
        ?>
    </div>

    <p>
        3. Enter the code shown in the Google Authenticator app
    </p>

    <?= $this->Form->create(); ?>
    <?= $this->Form->hidden('user_id', ['value' => $user->id]); ?>
    <?= $this->Form->input('code', ['placeholder' => 'Enter code here', 'autocomplete' => 'off']); ?>
    <?= $this->Form->submit(__d('user', 'Submit'), ['class' => 'btn btn-primary btn-block']); ?>
    <?= $this->Form->end(); ?>

</div>

