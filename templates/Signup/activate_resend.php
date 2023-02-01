<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Account Verification'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Resend verification code'));
?>
<div id="user-registration-form" class="form">

    <?= $this->Form->create($user); ?>
    <div class="form-floating">
        <?= $this->Form->text('email', [
            'class' => 'form-control mb-3',
            'type' => 'email',
            'required' => true,
            'placeholder' => 'name@example.org',
            'label' => 'Your email address']); ?>
        <?= $this->Form->label('email', __('Email')); ?>
    </div>
    <div>
        <?= $this->Form->button(__d('user','Continue'), ['class' => 'btn btn-primary']); ?>
    </div>
    <hr />
    <?= $this->Html->link(__d('user', 'Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>