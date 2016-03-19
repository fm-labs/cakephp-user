<?php $this->Html->addCrumb(__('Users'), ['action' => 'index']); ?>
<?php $this->Html->addCrumb(__('Change my password')); ?>
<?php $this->assign('title', __('Change password')); ?>
<div id="user-change-password-form">
    <?= $this->Form->create($user, ['class' => 'ui form']); ?>
    <h2 class="ui header">
        <?= __('Change password'); ?>
    </h2>
    <div class="ui top attached segment">
    <?= $this->Form->input('password0', [
        'label' => __('Current password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->input('password1', [
        'label' => __('New password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    <?= $this->Form->input('password2', [
        'label' => __('Repeat password'),
        'type' => 'password',
        'required' => true
    ]); ?>
    </div>
    <div class="ui bottom attached segment">
        <?= $this->Form->submit(__('Change password now')); ?>
    </div>
    <?= $this->Form->end(); ?>
</div>