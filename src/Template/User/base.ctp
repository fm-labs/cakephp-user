<?php
// Privacy directives
$this->set('_private', true);
$this->set('_no_tracking', true);
?>
<style>
    .user-view {
        max-width: 500px;
        margin: 0 auto;
    }
</style>
<div class="user-view view">
    <?= $this->Flash->render('auth'); ?>
    <h1 class="heading"><?= $this->fetch('heading', $this->fetch('title')); ?></h1>
    <?= $this->fetch('content'); ?>
</div>