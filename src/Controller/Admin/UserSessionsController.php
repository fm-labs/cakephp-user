<?php
declare(strict_types=1);

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
    public array $actions = [
        'index' => 'Admin.Index',
        'view' => 'Admin.View',
        'delete' => 'Admin.Delete',
    ];
}
