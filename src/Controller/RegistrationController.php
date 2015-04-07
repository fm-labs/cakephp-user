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
            debug($user->errors());
            if ($user->id) {
                $this->Flash->success(__('USER_REGISTRATION_SUCCESS'));
                //return $this->redirect(Configure::read('User.Auth.loginAction'));
            } else {
                $this->Flash->error(__('USER_REGISTRATION_FAILURE'));
            }
        } else {
            $user = $this->Users->register(null);
        }

        /*
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->register($user)) {
                $this->Flash->success('The user has been saved.');
                return $this->redirect(Configure::read('User.Auth.loginAction'));
            } else {
                $this->Flash->error('The user could not be saved. Please, try again.');
                debug($user->errors());
            }
        }
        */

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

}
