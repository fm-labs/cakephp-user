<?php $this->extend('Backend./Base/form'); ?>
<?php $this->assign('heading', __('Add {0}', __('User'))); ?>
<div class="form">
    <?= $this->Form->create($entity); ?>
        <?php
        echo $this->Form->input('superuser');
        echo $this->Form->input('group_id');
        echo $this->Form->input('email');
        echo $this->Form->input('username');
        ?>
    <?= $this->Form->button(__d('user', 'Add')) ?>
    <?= $this->Form->end() ?>
</div>