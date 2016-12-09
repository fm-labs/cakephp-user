<div class="view">
    <div class="user-login-form" style="max-width: 400px; margin: 0 auto;">
        <?= $this->Flash->render('auth'); ?>
        <h2><?= __('Login'); ?></h2>
        <?= $this->Form->create(); ?>
        <?= $this->Form->input('username'); ?>
        <?= $this->Form->input('password', ['type' => 'password']); ?>
        <?= $this->Form->button(__('Login')); ?>
        <?= $this->Form->end(); ?>
    </div>
</div>