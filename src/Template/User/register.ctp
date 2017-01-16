<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('Account Registration'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('Registration'));
?>
<div id="user-registration-form">
    <?= $this->Form->create($user, ['novalidate']); ?>
    <?= $this->Form->input('username'); ?>
    <?= $this->Form->input('password1', ['type' => 'password', 'required' => true, 'label' => __('Password')]); ?>
    <?= $this->Form->input('password2', ['type' => 'password', 'required' => true, 'label' => __('Repat password')]); ?>
    <?= $this->Form->button(__('Signup'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__('I\'m already registered'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
    <hr />
</div>