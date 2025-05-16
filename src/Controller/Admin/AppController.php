<?php
declare(strict_types=1);

namespace User\Controller\Admin;

use App\Controller\Admin\AppController as AppAdminController;

/**
 * Class AppController
 *
 * @package User\Controller\Admin
 */
class AppController extends AppAdminController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Admin.Action');
    }
}
