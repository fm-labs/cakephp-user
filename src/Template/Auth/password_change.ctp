<div id="user-change-password-form">
    <h2><?= __('Create a new password'); ?></h2>
    <?= $this->Form->create($user); ?>
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
    <?= $this->Form->submit(__('Change my password now')); ?>
    <?= $this->Form->end(); ?>

    <br />
    <?= $this->Html->link(__('Back'), '/', [
       'onclick' => "javascript:history.go(-1)"
    ]); ?>

    <?= $this->element('User.customize', ['template' => 'src/Template/Plugin/User/Auth/password_change.ctp']); ?>
</div>