<?php
$this->extend('User./base');
?>
<div id="user-login-form" class="user-form">
    <?= $this->Form->create(null); ?>
    <?= $this->Form->control('username', [
        'placeholder' => __d('user', 'Type your email here'),
        'label' => __d('user', 'Email')]); ?>
    <?= $this->Form->control('password', [
        'type' => 'password',
        'placeholder' => __d('user', 'Type your password here'),
        'label' => __d('user', 'Password')]); ?>
    <?= $this->Form->button(__d('user', 'Login'), [
        'class' => 'btn btn-primary',
    ]); ?>
    <?php if (\Cake\Core\Configure::read('User.Signup.enabled')) : ?>
        <?= $this->Html->link(__d('user', 'Signup'), ['_name' => 'user:register'], ['class' => 'btn btn-default']); ?>
    <?php endif; ?>
    <?= $this->Form->end(); ?>

    <hr/>
    <?php if (\Cake\Core\Configure::read('User.Signup.verifyEmail')) : ?>
        <?= $this->Html->link(__d('user', 'Activate account'), ['_name' => 'user:activate']); ?>
        <br/>
    <?php endif; ?>
    <?= $this->Html->link(__d('user', 'Forgot password?'), ['_name' => 'user:passwordforgotten']); ?>
    <br />
    <hr />
    <?= $this->Html->link(__d('user', 'Back to the website'), '/'); ?>
</div>
