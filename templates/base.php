<?php
//$layout = 'user'
//$layout = \Cake\Core\Configure::read('User.layout');
//$this->setLayout('user');
$this->Html->meta('robots', 'noindex, nofollow', ['block' => true]);
?>
<div class="user-view view form-user w-100 m-auto text-center">
    <?= $this->Flash->render('auth'); ?>
    <h1 class="h3 mb-3 fw-normal"><?= $this->fetch('heading', $this->fetch('title')); ?></h1>
    <?= $this->fetch('content'); ?>
</div>