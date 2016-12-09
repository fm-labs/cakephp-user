<?php $this->Breadcrumbs->add(__('Users'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Edit {0}', __('User'))); ?>
<?php $this->Toolbar->addLink(
    __('Delete'),
    ['action' => 'delete', $user->id],
    ['data-icon' => 'trash', 'confirm' => __('Are you sure you want to delete # {0}?', $user->id)]
);?>
<?php $this->Toolbar->addLink(
    __('List {0}', __('Users')),
    ['action' => 'index'],
    ['data-icon' => 'list']
);?>
<?= $this->Toolbar->addLink(
    __('List {0}', __('User Groups')),
    ['controller' => 'Groups', 'action' => 'index'],
    ['data-icon' => 'list']
) ?>

<?= $this->Toolbar->addLink(
    __('New {0}', __('User Group')),
    ['controller' => 'Groups', 'action' => 'add'],
    ['data-icon' => 'plus']
); ?>
<div class="backend user">
    <h2 class="ui header">
        <?= __('Edit {0}', __('User')) ?>
    </h2>
    <?= $this->Form->create($user); ?>
    <div class="users ui attached segment">
        <div class="ui form">
        <?php
        echo $this->Form->input('superuser');
        echo $this->Form->input('group_id', ['options' => $primaryGroup, 'empty' => true]);
        echo $this->Form->input('username');
        //echo $this->Form->input('password');
        echo $this->Form->input('name');
        echo $this->Form->input('email');
        echo $this->Form->input('email_verification_required');
        echo $this->Form->input('email_verification_code');
        //echo $this->Form->input('email_verification_expiry_timestamp');
        echo $this->Form->input('email_verified');
        echo $this->Form->input('password_change_min_days');
        echo $this->Form->input('password_change_max_days');
        echo $this->Form->input('password_change_warning_days');
        //echo $this->Form->input('password_change_timestamp');
        //echo $this->Form->input('password_expiry_timestamp');
        echo $this->Form->input('password_force_change');
        echo $this->Form->input('password_reset_code');
        //echo $this->Form->input('password_reset_expiry_timestamp');
        echo $this->Form->input('login_enabled');
        echo $this->Form->input('login_last_login_ip');
        echo $this->Form->input('login_last_login_host');
        //echo $this->Form->input('login_last_login_datetime');
        echo $this->Form->input('login_failure_count');
        //echo $this->Form->input('login_failure_datetime');
        echo $this->Form->input('block_enabled');
        echo $this->Form->input('block_reason');
        //echo $this->Form->input('block_datetime');
        echo $this->Form->input('groups._ids', ['options' => $userGroups]);
        ?>
        </div>
    </div>
    <div class="ui bottom attached segment">
        <?= $this->Form->button(__('Submit')) ?>
    </div>
    <?= $this->Form->end() ?>

</div>