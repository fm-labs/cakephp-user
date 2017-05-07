<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Account Registration'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Registration'));
?>
<div id="user-registration-form">
    <?= $this->Form->create($user, ['novalidate']); ?>
    <?= $this->Form->input('name', ['label' => __d('user','Your name'), 'placeholder' => __('Firstname Lastname')]); ?>
    <?= $this->Form->input('username', ['label' => __d('user','Email'), 'placeholder' => 'email@example.com']); ?>
    <?= $this->Form->input('password1', ['type' => 'password', 'required' => true, 'label' => __d('user','Password'), 'placeholder' => __('Min 8 characters')]); ?>
    <?= $this->Form->input('password2', ['type' => 'password', 'required' => true, 'label' => __d('user','Repat password')]); ?>
    <?= $this->Form->button(__d('user','Signup'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__d('user','I\'m already registered'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
    <hr />
</div>