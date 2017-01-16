<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__('Password recovery'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('Password forgotten?'));
?>
<div id="user-password-forgotten-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->input('username', [
        'label' => __('Your username'),
        'placeholder' => __('Enter username or email address'),
        'required' => true
    ]); ?>
    <?= $this->Form->button(__('Send password recovery instructions'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__('Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>