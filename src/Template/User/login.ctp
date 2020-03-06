<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Login'));
?>
<div id="user-login-form" class="user-form">
    <?= $this->Form->create($user); ?>
    <?= $this->Form->control('username', ['placeholder' => __d('user', 'Type your email here'), 'label' => __d('user', 'Email')]); ?>
    <?= $this->Form->control('password', ['type' => 'password', 'placeholder' => __d('user', 'Type your password here'), 'label' => __d('user', 'Password')]); ?>
    <?= $this->Form->button(__d('user', 'Login'), ['class' => 'btn btn-primary']); ?>
    <?php if (\Cake\Core\Configure::read('User.Signup.enabled')) : ?>
        <?= $this->Html->link(__d('user', 'Signup'), ['_name' => 'user:register'], ['class' => 'btn btn-default']); ?>
    <?php endif; ?>
    <?= $this->Form->end(); ?>

    <hr/>

    <?php if (\Cake\Core\Configure::read('User.Signup.verifyEmail')) : ?>
        <?= $this->Html->link(__d('user', 'Activate account'), ['action' => 'activate']); ?>
        <br/>
    <?php endif; ?>
    <?= $this->Html->link(__d('user', 'Forgot password?'), ['_name' => 'user:passwordforgotten']); ?>
</div>
