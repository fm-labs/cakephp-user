<?php
$this->extend('User./base');
// breadcrumbs
$this->loadHelper('Breadcrumbs');
//$this->loadHelper('Bootstrap.Form');
$this->Breadcrumbs->add(__d('user','Login'), ['_name' => 'user:login']);
$this->Breadcrumbs->add(__d('user','Account Registration'));

// no robots
$this->Html->meta('robots', 'noindex,nofollow', ['block' => true]);

$this->assign('title', __d('user','Registration'));
?>
<div id="user-registration-form">
    <?= $this->Form->create(null, ['novalidate']); ?>
    <?= $this->Form->hidden('group_id'); ?>
    <?= $this->Form->control('group_pass', [
            'type' => 'text',
            'required' => true,
            'label' => 'Zugangspasswort',
            'help' => 'Bitte geben Sie das Zugangspasswort für die Registrierung ein'
    ]); ?>
    <div class="text-right">
        <?= $this->Form->button(__d('user','Continue'), ['class' => 'btn btn-primary']); ?>
    </div>
    <hr />
    <?= $this->Html->link(__d('user', 'Cancel'), ['_name' => 'user:login'], ['class' => 'btn']); ?>
    <?= $this->Form->end(); ?>
</div>