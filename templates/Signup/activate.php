<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Account Verification'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Verify your email address'));
?>
<div id="user-registration-form" class="form">

    <div class="alert alert-info">
        <?= __d('user', 'An activation code has been sent to your email upon registration. Please check your inbox.'); ?>
        <br />
        <small>
            <?= $this->Html->link(__d('user', 'Resend activation email'), ['action' => 'activateResend']); ?>
        </small>
    </div>

    <?= $this->Form->create($user, ['context' => ['validator' => 'activate']]); ?>


    <div class="form-floating">
        <?= $this->Form->text('email', [
            'class' => 'form-control mb-3',
            'required' => true,
            'type' => "text",
            //'placeholder' => 'yourname@example.org',
        ]); ?>
        <?= $this->Form->label('email', __d('user', 'Email')); ?>
    </div>
    <div class="form-floating">
        <?= $this->Form->text('email_verification_code', [
            'class' => 'form-control mb-3',
            'required' => true,
            'type' => "text"
        ]); ?>
        <?= $this->Form->label('email_verification_code', __d('user', 'Verification Code')); ?>
    </div>

    <?= $this->Form->button(
        __d('user', 'Verify'), [
        'class' => 'w-100 btn btn-lg btn-primary',
    ]); ?>

    <hr />

    <?= $this->Html->link(__d('user', 'Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>

</div>