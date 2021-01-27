<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Reset account password'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Reset your password'));
?>
<div id="user-password-reset-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->control('username', [
        'label' => __d('user','Username'),
        'placeholder' => __d('user','Enter username or email'),
        'required' => true
    ]); ?>
    <?= $this->Form->control('password_reset_code', [
        'label' => __d('user','Password reset code received via email'),
        'placeholder' => '',
        'required' => true,
        'autocomplete' => 'off'
    ]); ?>
    <?= $this->Form->control('password1', [
        'label' => __d('user','New password'),
        'type' => 'password',
        'required' => true,
        'autocomplete' => 'off'
    ]); ?>
    <?= $this->Form->control('password2', [
        'label' => __d('user','Repeat password'),
        'type' => 'password',
        'required' => true,
        'autocomplete' => 'off'
    ]); ?>
    <?= $this->Form->submit(__d('user','Reset password'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__d('user','Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>