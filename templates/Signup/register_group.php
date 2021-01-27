<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Account Registration'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Registration'));
?>
<div id="user-registration-form">
    <?= $this->Form->create(null, ['novalidate']); ?>
    <?= $this->Form->control('group_pass', ['type' => 'text', 'required' => true, 'label' => 'Bitte geben Sie das Zugangspasswort für die Registrierung ein']); ?>
    <?= $this->Form->hidden('group_id'); ?>
    <div class="text-right">
        <?= $this->Form->button(__d('user','Continue'), ['class' => 'btn btn-primary']); ?>
    </div>
    <?= $this->Form->end(); ?>
    <hr />
</div>