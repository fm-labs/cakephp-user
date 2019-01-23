<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Account Verification'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', '2-Factor-Auth'));
?>
<div class="user-gauth-form" class="form">

    <?php if ($user->gauth_enabled): ?>
        <p class="text-success">
            <?= __d('user', 'You have 2-Factor-Authentication enabled!'); ?>
        </p>

        <p style="margin-top: 1em;">
            <?= $this->Html->link("Disable", ['action' => 'disable'], ['class' => 'btn btn-danger btn-block']); ?>
        </p>

    <?php else: ?>
        <p class="text-success">
            <?= __d('user', '2-Factor-Authentication is currently DISABLED'); ?>
        </p>
        <p style="margin-top: 1em;">
            <?= $this->Html->link("Setup 2-Factor-Auth", ['action' => 'setup'], ['class' => 'btn btn-primary btn-block']); ?>
        </p>
    <?php endif; ?>

</div>
