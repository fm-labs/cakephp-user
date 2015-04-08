<div id="user-change-password-form">
    <h2><?= __('Change Password'); ?></h2>
    <?= $this->Form->create($user, ['novalidate']); ?>
    <?= $this->Form->input('password0'); ?>
    <?= $this->Form->input('password1', ['type' => 'password']); ?>
    <?= $this->Form->input('password2', ['type' => 'password']); ?>
    <?= $this->Form->submit(__('Change my password')); ?>
    <?= $this->Form->end(); ?>

    <?= $this->element('User.customize', ['template' => 'src/Template/Plugin/User/Auth/change_password.ctp']); ?>
</div>