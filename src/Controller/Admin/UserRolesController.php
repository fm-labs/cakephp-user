<?php
namespace User\Controller\Admin;

/**
 * Roles Controller
 *
 * @property \User\Model\Table\RolesTable $Roles
 */
class UserRolesController extends AppController
{
    public $modelClass = "User.Roles";

    /**
     * @var array
     */
    public $actions = [
        'index' => 'User.Index',
        'view' => 'User.View',
        'edit' => 'User.Edit',
        'delete' => 'User.Delete'
    ];

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @param string|null $id Role id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => []
        ]);
        $this->set('role', $role);
        $this->set('_serialize', ['role']);
    }

    /**
     * Add method
     *
     * @return void|\Cake\Http\Response Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->data);
            if ($this->Roles->save($role)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'role')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'role')));
            }
        }
        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return void|\Cake\Http\Response Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->data);
            if ($this->Roles->save($role)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'role')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'role')));
            }
        }
        $this->set(compact('role'));
        $this->set('_serialize', ['role']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return void|\Cake\Http\Response Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->success(__d('user', 'The {0} has been deleted.', __d('user', 'role')));
        } else {
            $this->Flash->error(__d('user', 'The {0} could not be deleted. Please, try again.', __d('user', 'role')));
        }

        return $this->redirect(['action' => 'index']);
    }
}
