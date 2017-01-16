<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__('Reset account password'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('Password forgotten?'));
?>
<div id="user-password-reset-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->input('password_reset_code', [
        'label' => __('Password reset code'),
        'placeholder' => __('e.g. A8CD3F'),
        'required' => true
    ]); ?>
    <?= $this->Form->input('username', [
        'label' => __('Username'),
        'placeholder' => __('Enter username or email'),
        'required' => true
    ]); ?>
    <?= $this->Form->input('password1', [
        'label' => __('New password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->input('password2', [
        'label' => __('Repeat password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->submit(__('Reset password'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__('Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>