<div id="user-registration-form">
    <h2><?= __('Registration'); ?></h2>
    <?= $this->Form->create($user, ['novalidate']); ?>
    <?= $this->Form->input('username'); ?>
    <?= $this->Form->input('password1', ['type' => 'password', 'required' => true]); ?>
    <?= $this->Form->input('password2', ['type' => 'password', 'required' => true]); ?>
    <?= $this->Form->submit(__('Signup')); ?>
    or
    <?= $this->Html->link('Login', ['controller' => 'Auth', 'action' => 'login']); ?> here
    <?= $this->Form->end(); ?>

    <?= $this->element('User.customize', ['template' => 'src/Template/Plugin/User/Registration/index.ctp']); ?>
</div>