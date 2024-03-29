<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user', 'Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user', 'Account Registration'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Registration'));
$form = $this->get('form');
?>
<div id="user-registration-form">
    <?= $this->Form->create($form); ?>
    <?= $this->Form->control(
        'first_name',
        ['label' => __d('user', 'First name'), 'placeholder' => __d('user', 'Firstname')]
    ); ?>
    <?= $this->Form->control(
        'last_name',
        ['label' => __d('user', 'Last name'), 'placeholder' => __d('user', 'Lastname')]
    ); ?>
    <?= $this->Form->control(
        'email',
        ['label' => __d('user', 'Email'), 'placeholder' => 'email@example.com']
    ); ?>
    <?= $this->Form->control(
        'password1',
        ['type' => 'password', 'required' => true, 'label' => __d('user', 'Password')]
    ); ?>
    <?= $this->Form->control(
        'password2',
        ['type' => 'password', 'required' => true, 'label' => __d('user', 'Repeat password')]
    ); ?>

    <div class="my-3">
        <?= $this->Form->button(__d('user', 'Signup'),
            ['class' => 'btn btn-lg btn-primary w-100']); ?>
    </div>
    <div class="mb-2">
        <?= $this->Html->link(__d('user', 'I\'m already registered'),
            ['_name' => 'user:login'],
            ['class' => 'btn btn-secondary-outline w-100']); ?>
    </div>
    <?= $this->Form->end(); ?>
    <hr />
</div>