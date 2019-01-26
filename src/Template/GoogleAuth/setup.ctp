<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','2-Factor Authentication'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Setup 2-Factor Authentication'));
?>
<div class="user-gauth-form user-form form">

    <p>
        1. <?= __d('user','Download Google Authenticator app for your smartphone'); ?>
    </p>
    <p>
        2. <?= __d('user','Open Google Authenticator app and scan QR code'); ?>
    </p>
    <div class="image text-center" style="margin-bottom: 1em;">
        <?php
        if ($imgUri) {
            echo $this->Html->image($imgUri);
        } else {
            echo __d('user', "Failed to render QR image");
        }
        ?>
    </div>
    <p>
        3. <?= __d('user','Enter the code shown in the Google Authenticator app'); ?>
    </p>

    <?= $this->Form->create(); ?>
    <?= $this->Form->hidden('user_id', ['value' => $user->id]); ?>
    <?= $this->Form->input('code', ['placeholder' => __d('user','Enter code here'), 'autocomplete' => 'off']); ?>
    <?= $this->Form->submit(__d('user', 'Enable'), ['class' => 'btn btn-primary btn-block']); ?>
    <?= $this->Form->end(); ?>

</div>

