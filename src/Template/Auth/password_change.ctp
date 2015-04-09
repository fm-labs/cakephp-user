<div id="user-change-password-form">
    <h2><?= __('USER_CHANGE_PASSWORD'); ?></h2>
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
    <?= $this->Form->submit(__('USER_CHANGE_PASSWORD_SUBMIT')); ?>
    <?= $this->Form->end(); ?>

    <?= $this->element('User.customize', ['template' => 'src/Template/Plugin/User/Auth/change_password.ctp']); ?>
</div>