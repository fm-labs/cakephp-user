<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('My account'), ['_name' => 'user:profile']);
$this->Breadcrumbs->add(__('Change password'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('Set a new password'));
?>
<div id="user-password-change-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->input('password0', [
        'label' => __('Current password'),
        'type' => 'password',
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
    <?= $this->Form->button(__('Update my password'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__('Cancel'), ['_name' => 'user:profile'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>
