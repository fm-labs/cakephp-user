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

    public function index()
    {
    }

    public function add()
    {
    }

    public function edit()
    {
    }

    /**
     * View method
     *
     * @return void
     */
    public function view()
    {
        $this->set('entityOptions', ['contain' => ['Users']]);
        $this->set('related', ['Users']);
        /*
        $this->set('related', ['Users' => [
            'fields' => ['id', 'superuser', 'first_name', 'last_name', 'username', 'email', 'login_enabled'],
            'rowActions' => [
                [__('Details'), ['controller' => 'Users', 'action' => 'view', ':id']],
            ],
        ]]);
        */
    }
}
