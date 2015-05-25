<?php
namespace User\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;
use User\Model\Table\UsersTable;

/**
 * Class RegistrationController
 * @package User\Controller
 *
 * @property UsersTable $Users
 */
class UserController extends AppController
{
    /**
     * @var string Name of user layout
     */
    public $layout = 'User.auth';

    /**
     * @param Event $event
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['register']);
    }

    /**
     * Register method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        //@TODO Make user registration configurable
        //@TODO Dispatch 'User.register' event

        if ($this->request->is('post')) {
            $user = $this->Users->register($this->request->data);
            if ($user && $user->id) {
                $this->Flash->success(__('Your registration was successful!'));
                $this->redirect($this->Auth->redirectUrl());
                return;
            } else {
                $this->Flash->error(__('Ups, something went wrong. Please check the form.'));
            }
        } else {
            $user = $this->Users->register(null);
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }


    /**
     * Passsword change method
     */
    public function password_change()
    {
        $user = $this->Users->get($this->Auth->user('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__('Your password has been changed.'));
                //@todo make configurable 'user password change' success redirect url
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }
}
