<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Password recovery'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Password forgotten?'));
?>
<div id="user-password-forgotten-form" class="user-form">
    <?= $this->Form->create($form); ?>
    <?= $this->Form->control('username', [
        'label' => __d('user', 'Email'),
        //'placeholder' => __d('user','Enter username or email address'),
        'required' => true
    ]); ?>
    <?= $this->Form->button(__d('user','Send password recovery instructions'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__d('user','Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>