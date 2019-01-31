<?php

namespace User\Controller\Admin;

class UserController extends AppController
{
    public function index()
    {
        $this->redirect(['controller' => 'Users', 'action' => 'index']);
    }
}
