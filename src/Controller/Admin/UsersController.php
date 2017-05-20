<?php
namespace User\Controller\Admin;

/**
 * Users Controller
 *
 * @property \User\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $modelClass = 'User.Users';

    public $actions = [
        'index' => 'Backend.Index',
        'view' => 'Backend.View'
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['PrimaryGroup']
        ];

        $this->set('fields', [
            'username' => ['formatter' => function($val, $row, $args, $view) {
                return $view->Html->link(
                    $val,
                    ['action' => 'edit', $row->id]);
            }],
           'primary_group.name' => ['formatter' => function($val, $row, $args, $view) {
               if ($row->primary_group) {
                   return $view->Html->link(
                       $row->primary_group->name,
                       ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'edit', $row->primary_group->id]);
               }
           }]
        ]);
        $this->set('fields.whitelist', ['id', 'display_name', 'primary_group.name', 'username', 'email', 'login_enabled']);

        $this->Backend->executeAction();
        /*
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
        */
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('fields', [
            'email' => ['formatter' => 'email'],
            'password_reset_url' => ['formatter' => 'link']
        ]);
        $this->set('fields.blacklist', ['password']);
        $this->Backend->executeAction();
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        $user->accessible([
            'username', 'group_id', 'name', 'email', 'password'
        ], true);
        if ($this->request->is('post')) {
            $user = $this->Users->add($this->request->data);
            if ($user->id) {
                $this->Flash->success(__d('user','The {0} has been saved.', __d('user','user')));
                return $this->redirect(['action' => 'edit', $user->id]);
            } else {
                $this->Flash->error(__d('user','The {0} could not be saved. Please, try again.', __d('user','user')));
            }
        }
        $primaryGroup = $this->Users->PrimaryGroup->find('list', ['limit' => 200]);
        $userGroups = $this->Users->Groups->find('list', ['limit' => 200]);
        $this->set(compact('user', 'primaryGroup', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Groups']
        ]);
        $user->accessible('*', true);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__d('user','The {0} has been saved.', __d('user','user')));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user','The {0} could not be saved. Please, try again.', __d('user','user')));
            }
        }
        $primaryGroup = $this->Users->PrimaryGroup->find('list', ['limit' => 200]);
        $userGroups = $this->Users->Groups->find('list', ['limit' => 200]);
        $this->set(compact('user', 'primaryGroup', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__d('user','The {0} has been deleted.', __d('user','user')));
        } else {
            $this->Flash->error(__d('user','The {0} could not be deleted. Please, try again.', __d('user','user')));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordChange($userId = null)
    {
        if ($userId === null) {
            $userId = $this->Auth->user('id');
        } elseif ($userId !== $this->Auth->user('id')) {
            $this->Flash->error(__d('user','You are not allowed to do this'));
            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__d('user','Your password has been changed.'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user','Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordReset($userId = null)
    {
        $authUserId = $this->Auth->user('id');
        if ($userId === null) {
            $userId = $authUserId;
        } elseif ($userId !== $authUserId && $authUserId !== 1) {
            $this->Flash->error(__d('user','Only root can do this'));
            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->resetPassword($user, $this->request->data)) {
                $this->Flash->success(__d('user','Your password has been changed.'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user','Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

}
