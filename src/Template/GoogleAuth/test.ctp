<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','2-Factor Authentication'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user', 'Google Authentication'));
?>
<?= $this->Flash->render(); ?>
<div id="user-login-form" class="user-form">
    <?= $this->Form->create(null); ?>
    <?= $this->Form->control('secretKey', ['value' => $secretKey]); ?>
    <?= $this->Form->control('code', ['label' => __d('user', 'Authentication Code')]); ?>
    <?= $this->Form->button(__d('user', 'Authenticate'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Form->end(); ?>

    <?= $this->Html->image($imgUrl); ?>
</div>
