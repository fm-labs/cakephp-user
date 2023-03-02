<?php $this->extend('Backend./Base/form'); ?>
<?php $this->assign('heading', __d('user', 'Add {0}', __d('user', 'User'))); ?>
<div class="form">
    <?= $this->Form->create($entity); ?>
        <?php
        echo $this->Form->control('superuser');
        echo $this->Form->control('group_id');
        echo $this->Form->control('email');
        echo $this->Form->control('username');
        ?>
    <?= $this->Form->button(__d('user', 'Add')) ?>
    <?= $this->Form->end() ?>
</div>