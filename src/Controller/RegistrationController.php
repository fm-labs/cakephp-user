<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/6/15
 * Time: 11:28 PM
 */

namespace User\Controller;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 * @package User\Controller
 *
 * @property UsersTable $Users
 */
class RegistrationController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['index']);

        // handle already authenticated users
        if ($this->Auth->user()) {
            $this->redirect($this->Auth->redirectUrl());
        }
    }

    /**
     *  method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function index()
    {
        $this->loadModel(Configure::read('User.model'));

        if ($this->request->is('post')) {
            $user = $this->Users->register($this->request->data);
            if ($user && $user->id) {
                $this->Flash->success(__('USER_REGISTRATION_SUCCESS'));
                $this->redirect($this->Auth->redirectUrl());
                return;
            } else {
                $this->Flash->error(__('USER_REGISTRATION_FAILURE'));
            }
        } else {
            $user = $this->Users->register(null);
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

}
