<?php $this->Breadcrumbs->add(__d('user','Users'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__d('user','Edit {0}', __d('user','User'))); ?>
<?php $this->extend('Backend./Base/form'); ?>
<div class="backend user">
    <?= $this->Form->create($user, ['horizontal' => true]); ?>
        <?php
        echo $this->Form->control('superuser');
        echo $this->Form->control('group_id', ['options' => $userGroups, 'empty' => true]);
        echo $this->Form->control('username');
        //echo $this->Form->control('password');
        echo $this->Form->control('name');
        echo $this->Form->control('email');
        echo $this->Form->control('email_verification_required');
        echo $this->Form->control('email_verification_code');
        //echo $this->Form->control('email_verification_expiry_timestamp');
        echo $this->Form->control('email_verified');
        echo $this->Form->control('password_change_min_days');
        echo $this->Form->control('password_change_max_days');
        echo $this->Form->control('password_change_warning_days');
        //echo $this->Form->control('password_change_timestamp');
        //echo $this->Form->control('password_expiry_timestamp');
        echo $this->Form->control('password_force_change');
        echo $this->Form->control('password_reset_code');
        //echo $this->Form->control('password_reset_expiry_timestamp');
        echo $this->Form->control('login_enabled');
        echo $this->Form->control('login_last_login_ip');
        echo $this->Form->control('login_last_login_host');
        //echo $this->Form->control('login_last_login_datetime');
        echo $this->Form->control('login_failure_count');
        //echo $this->Form->control('login_failure_datetime');
        echo $this->Form->control('block_enabled');
        echo $this->Form->control('block_reason');
        //echo $this->Form->control('block_datetime');
        //echo $this->Form->control('groups._ids', ['options' => $userGroups]);
        ?>
    <?= $this->Form->button(__d('user','Submit')) ?>
    <?= $this->Form->end() ?>
</div>
