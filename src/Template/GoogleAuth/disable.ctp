<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Account Verification'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Disable 2-Factor Auth'));
?>
<div class="user-gauth-form user-form form">

    <p>
        Please confirm that you want to DISABLE the 2-Factor-Authentication.
    </p>

    <?= $this->Form->create(); ?>
    <?= $this->Form->hidden('user_id', ['value' => $user->id]); ?>
    <?= $this->Form->input('code', ['placeholder' => 'Enter code here', 'autocomplete' => 'off']); ?>
    <?= $this->Form->submit(__('Yes, I want to disable 2-Factor-Auth'), ['class' => 'btn btn-danger btn-block']); ?>
    <?= $this->Form->end(); ?>

</div>

