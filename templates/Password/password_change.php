<?php
//$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','My account'), ['_name' => 'user:profile']);
$this->Breadcrumbs->add(__d('user','Change password'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Set a new password'));
?>
<div id="user-password-change-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->control('password0', [
        'label' => __d('user','Current password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->control('password1', [
        'label' => __d('user','New password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->control('password2', [
        'label' => __d('user','Repeat password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->button(__d('user','Update my password'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__d('user','Cancel'), ['_name' => 'user:profile'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>
