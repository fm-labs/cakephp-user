<div id="user-login-form">
    <h2><?= __('Login'); ?></h2>
    <?= $this->Form->create(); ?>
    <?= $this->Form->input('username'); ?>
    <?= $this->Form->input('password', ['type' => 'password']); ?>
    <?= $this->Form->submit(__('Login')); ?>
    or
    <?= $this->Html->link('Register', ['controller' => 'Registration', 'action' => 'index']); ?> here
    <?= $this->Form->end(); ?>
</div>