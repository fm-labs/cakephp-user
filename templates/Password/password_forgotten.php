<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user', 'Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user', 'Password recovery'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Password forgotten?'));

?>
<div id="user-password-forgotten-form" class="user-form">
    <?= $this->Form->create($form); ?>

    <div class="form-floating">
        <?= $this->Form->text('username', [
            'class' => 'form-control mb-3',
            'type' => "text",
            'placeholder' => __d('user', 'yourname@example.org'),
        ]); ?>
        <?= $this->Form->label('username', __d('user', 'Email')); ?>

    </div>

    <?= $this->Form->button(
        __d('user', 'Reset password'), [
        'class' => 'w-100 btn btn-lg btn-primary',
    ]); ?>

    <?= $this->Html->link(__d('user', 'Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>