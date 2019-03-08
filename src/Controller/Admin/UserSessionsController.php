<?php
namespace User\Controller\Admin;

/**
 * UserSessions Controller
 *
 * @property \User\Model\Table\UserSessionsTable $UserSessions
 */
class UserSessionsController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.UserSessions';

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Backend.Index',
        'view' => 'Backend.View',
        'delete' => 'Backend.Delete',
    ];
}
