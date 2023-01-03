<?php
//$layout = 'user'
//$layout = \Cake\Core\Configure::read('User.layout');
//$this->setLayout('user');
?>
<div class="user-view view">
    <?= $this->Flash->render('auth'); ?>
    <h1 class="heading"><?= $this->fetch('heading', $this->fetch('title')); ?></h1>
    <?= $this->fetch('content'); ?>
</div>