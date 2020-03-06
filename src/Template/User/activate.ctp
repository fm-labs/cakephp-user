<?php
$this->extend('base');
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
    <?= $this->Form->control('email', ['type' => 'text', 'required' => true]); ?>
    <?= $this->Form->control('email_verification_code'); ?>
    <div class="text-right">
        <?= $this->Form->button(__d('user','Continue'), ['class' => 'btn btn-primary']); ?>
    </div>
    <?= $this->Form->end(); ?>
    <hr />
</div>