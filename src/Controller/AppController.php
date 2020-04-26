<?php
declare(strict_types=1);

namespace User\Controller;

use App\Controller\AppController as BaseAppController;

/**
 * Class AppController
 *
 * @package User\Controller
 * @property \User\Controller\Component\AuthComponent $Auth
 * @property \User\Controller\Component\UserSessionComponent $UserSession
 * @property \User\Model\Table\UsersTable $Users
 */
class AppController extends BaseAppController
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();
    }
}
