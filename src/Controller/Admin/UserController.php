<?php

namespace User\Controller\Admin;

class UserController extends AppController
{
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(['controller' => 'Users', 'action' => 'index']);
    }
}
