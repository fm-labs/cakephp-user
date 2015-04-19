<div id="user-login-form">
    <h2><?= __('Login'); ?></h2>
    <?= $this->Form->create(); ?>
    <?= $this->Form->input('username'); ?>
    <?= $this->Form->input('password', ['type' => 'password']); ?>
    <?= $this->Form->button(__('Login')); ?>
    <?= $this->Form->end(); ?>
</div>