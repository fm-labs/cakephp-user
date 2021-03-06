<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user', 'Security Check'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Google Authentication'));
$this->assign('userActions', '&nbsp;');
?>
<div class="user-gauth-form user-form form">
    <?= $this->Form->create(); ?>
    <?= $this->Form->hidden('user_id', ['value' => $user->id]); ?>
    <?= $this->Form->control('code', ['placeholder' => __d('user','Enter code here'), 'autocomplete' => 'off']); ?>
    <?= $this->Form->submit(__d('user', 'Verify')); ?>
    <?= $this->Form->end(); ?>
</div>

