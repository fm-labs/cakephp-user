<?php
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
     * View method
     *
     * @return void
     */
    public function view()
    {
        $this->set('entityOptions', ['contain' => ['Users']]);
        $this->set('related', ['Users' => [
            'fields' => ['id', 'superuser', 'first_name', 'last_name', 'username', 'email', 'login_enabled'],
            'rowActions' => [
                [__('Details'), ['controller' => 'Users', 'action' => 'view', ':id']]
            ]
        ]]);
        $this->Action->execute();
    }
}
