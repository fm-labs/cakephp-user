<?php

namespace User\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Controller\Controller;

class AppController extends Controller
{
    use BackendActionsTrait;

    public function initialize()
    {
        $this->loadComponent('Backend.Backend');
    }
}