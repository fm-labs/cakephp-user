<?php $this->Html->addCrumb(__('Users')); ?>
<div class="actions">
    <div class="ui secondary menu">
        <div class="item"></div>
        <div class="right menu">
            <div class="item">
                <i class="add icon"></i>
                <?= $this->Html->link(__('New {0}', __('User')), ['action' => 'add']) ?>
            </div>
            <div class="ui dropdown item">
                <i class="dropdown icon"></i>
                <i class="tasks icon"></i>Actions
                <div class="menu">
                    <div class="item">No Actions</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ui divider"></div>

<div class="users index">
    <table class="ui table striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('username') ?></th>
            <th><?= $this->Paginator->sort('password') ?></th>
            <th><?= $this->Paginator->sort('is_login_allowed') ?></th>
            <th><?= $this->Paginator->sort('created') ?></th>
            <th><?= $this->Paginator->sort('modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $this->Number->format($user->id) ?></td>
            <td><?= h($user->username) ?></td>
            <td><?= h($user->password) ?></td>
            <td><?= h($user->is_login_allowed) ?></td>
            <td><?= h($user->created) ?></td>
            <td><?= h($user->modified) ?></td>
            <td class="actions">
                <div class="ui basic mini buttons">
                    <div class="ui button">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $user->id]) ?>
                    </div>
                    <div class="ui floating dropdown icon button">
                        <i class="dropdown icon"></i>
                        <div class="menu">
                            <div class="item"><i class="edit icon"></i>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user->id]) ?>
                            </div>
                            <div class="item"><i class="delete icon"></i>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['action' => 'delete', $user->id],
                                    ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <div class="ui pagination menu">
            <?= $this->Paginator->prev(__('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next')) ?>

            <div class="item">
                <?= $this->Paginator->counter() ?>
            </div>
        </div>
    </div>
</div>
