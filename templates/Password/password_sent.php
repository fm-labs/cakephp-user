<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Password recovery'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Password sent'));
?>
<div id="user-password-forgotten-sent" class="user-form">
    <div class="view">
        <div class="alert alert-success">
            <?= __d('user', 'Password sent'); ?>
        </div>

        <?= $this->Html->link(__d('user', 'Go to login'), ['_name' => 'user:login']); ?>
    </div>
</div>