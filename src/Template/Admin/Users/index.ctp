<?php $this->Html->addCrumb(__('Users')); ?>
<?= $this->Toolbar->addLink(
    __('New {0}', __('User')),
    ['controller' => 'Users', 'action' => 'add'],
    ['icon' => 'add']
); ?>
<?= $this->Toolbar->addLink(
    __('List {0}', __('User Groups')),
    ['controller' => 'UserGroups', 'action' => 'index'],
    ['icon' => 'list']
); ?>
<?= $this->Toolbar->addLink(
    __('New {0}', __('User Group')),
    ['controller' => 'UserGroups', 'action' => 'add'],
    ['icon' => 'add']
); ?>
<div class="users index">

    <?= $this->cell('Backend.DataTable', [[
        'paginate' => true,
        'model' => 'User.Users',
        'data' => $users,
        'fields' => [
            'id',
            'login_enabled' => [
                'formatter' => function($val, $row) {
                    return $this->Ui->statusLabel($val);
                }
            ],
            'superuser' => [
                'formatter' => function($val, $row) {
                    return $this->Ui->statusLabel($val);
                }
            ],
            'username',
            'group_id' => [
                'formatter' => function($val, $row) {
                    $row->has('primary_group')
                        ? $this->Html->link($row->primary_group->name, ['controller' => 'UserGroups', 'action' => 'view', $row->primary_group->id])
                        : '';
                }
            ],
            'name',
            'email'
        ],
        'rowActions' => [
            [__d('shop','View'), ['action' => 'view', ':id'], ['class' => 'view']],
            [__d('shop','Edit'), ['action' => 'edit', ':id'], ['class' => 'edit']],
            [__d('shop','Delete'), ['action' => 'delete', ':id'], ['class' => 'delete', 'confirm' => __d('shop','Are you sure you want to delete # {0}?', ':id')]]
        ]
    ]]);
    ?>
    <?php debug($users); ?>
</div>
