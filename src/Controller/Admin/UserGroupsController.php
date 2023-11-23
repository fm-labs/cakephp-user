<?php
declare(strict_types=1);

namespace User\Controller\Admin;

/**
 * UserGroups Controller
 *
 * @property \User\Model\Table\UserGroupsTable $UserGroups
 */
class UserGroupsController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.UserGroups';

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Admin.Index',
        'view' => 'Admin.View',
        'add' => 'Admin.Add',
        'edit' => 'Admin.Edit',
        'delete' => 'Admin.Delete',
    ];

    public function index() {
        $this->Action->execute();
    }

    public function add() {
        $this->Action->execute();
    }

    public function edit() {
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @return void
     */
    public function view()
    {
        $this->set('entityOptions', ['contain' => ['Users']]);
        $this->set('related', ['Users' => [
            'fields' => ['id', 'group_id', 'superuser', 'first_name', 'last_name', 'username', 'email', 'login_enabled'],
//            'rowActions' => [
//                [__d('user', 'Details'), ['controller' => 'Users', 'action' => 'view', ':id']],
//            ],
        ]]);
        $this->Action->execute();
    }
}
