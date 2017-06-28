<?php

namespace User\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Controller\Controller;

/**
 * Class AppController
 *
 * @package User\Controller\Admin
 */
class AppController extends Controller
{
    use BackendActionsTrait;

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadComponent('Backend.Backend');
    }
}
