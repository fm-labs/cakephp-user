<?php
$this->extend('base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__('My Account'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __('My Account'));
$this->assign('heading', '');
?>
<div id="user-profile">
    <div class="user-image" style="text-align: center">
        <i class="fa fa-5x fa-user"></i>
    </div>
    <h2 style="text-align: center;"><?= h($user->username); ?></h2>
    <hr />
    <div class="actions" style="text-align: center;">
        <?= $this->Html->link(__('Change password'), ['_name' => 'user:passwordchange']); ?><br />
        <?= $this->Html->link(__('Logout'), ['action' => 'logout']); ?>
    </div>
</div>
