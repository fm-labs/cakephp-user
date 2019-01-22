<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Two-Factor Authentication'));
?>
<?= $this->Flash->render(); ?>
<div id="user-login-form" class="user-form">
    <?= $this->Form->create(null); ?>
    <?= $this->Form->input('secretKey', ['value' => $secretKey]); ?>
    <?= $this->Form->input('code', ['label' => __d('user', 'Authentication Code')]); ?>
    <?= $this->Form->button(__d('user', 'Authenticate'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Form->end(); ?>

    <?= $this->Html->image($imgUrl); ?>
</div>
