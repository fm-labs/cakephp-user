<?php

use Cake\Core\Configure;

$this->extend('User./base');

$this->assign('title', __('Login'));
$this->assign('heading', __d('user', 'Please sign in'));
?>
<div class="form-signin ms-auto me-auto">
    <?= $this->Form->create($this->get('form'), [
        'type' => 'post',
        'url' => ['_name' => 'user:login'],
        'novalidate' => false,
    ]); ?>

    <div class="form-floating">
        <?= $this->Form->text('username', [
            'class' => 'form-control',
            'type' => "text",
            'required' => true,
            'placeholder' => __d('user',  'yourname@example.org'),
        ]); ?>
        <?= $this->Form->label('username', __d('user', 'Email')); ?>
        <?= $this->Form->error('username'); ?>
    </div>
    <div class="form-floating">
        <?= $this->Form->password('password', [
            'class' => 'form-control',
            'type' => 'password',
            'required' => true,
            'placeholder' => __d('user',  'Type your password here'),
            'label' => __d('user',  'Password')
        ]); ?>
        <?= $this->Form->label('password'); ?>
        <?= $this->Form->error('password'); ?>
    </div>

    <div class="checkbox mb-3">
        <label>
            <input type="checkbox" value="remember-me">
            <?= __d('user', 'Remember me'); ?>
        </label>
    </div>

    <div class="mb-3">

        <?php if (Configure::read('User.Recaptcha.enabled')) : ?>
            <?php
            $this->loadHelper("GoogleRecaptcha.Recaptcha");
            echo $this->Form->control('captcha', [
                'type' => 'recaptcha2',
                'label' => false,
            ]);
            ?>
        <?php endif; ?>
    </div>

    <?= $this->Form->button(__d('user',  'Sign in'), [
        'class' => 'w-100 btn btn-lg btn-primary',
    ]); ?>
    <?= $this->Form->end(); ?>

    <p class="my-2 text-muted">
        <?php if (true || Configure::read('User.Signup.enabled')) : ?>
            <?= $this->Html->link(
                    __d('user',  'Create account'),
                    ['_name' => 'user:register'],
                    ['class' => 'w-100 btn btn-lg btn-outline-secondary']); ?>
        <?php endif; ?>
    </p>


    <hr/>
    <?php if (Configure::read('User.Signup.verifyEmail')) : ?>
        <?= $this->Html->link(__d('user',  'Activate account'), ['_name' => 'user:activate']); ?>
        <br/>
    <?php endif; ?>
    <?= $this->Html->link(__d('user',  'Forgot password?'), ['_name' => 'user:passwordforgotten']); ?>
    <br/>
    <hr/>
    <?= $this->Html->link(__d('user',  'Back to website'), '/'); ?>

</div>