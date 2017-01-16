<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('Login'), ['_name' => 'user:login']);

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('Login'));
?>
<div id="user-login-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->input('username', ['placeholder' => 'Type your email here']); ?>
    <?= $this->Form->input('password', ['type' => 'password', 'placeholder' => 'Type your password here']); ?>
    <?= $this->Form->button(__('Login'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Html->link(__('Signup'), ['_name' => 'user:register'], ['class' => 'btn btn-default']); ?>
    <?= $this->Form->end(); ?>

    <hr />
    <?= $this->Html->link(__('Forgot password?'), ['_name' => 'user:passwordforgotten']); ?><br />
    <!--
        <?= $this->Html->link(__('Activate account'), ['action' => 'activate']); ?>
        -->
</div>