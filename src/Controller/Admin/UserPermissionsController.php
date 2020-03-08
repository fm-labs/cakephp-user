<?php
namespace User\Controller\Admin;

/**
 * Permissions Controller
 *
 * @property \User\Model\Table\PermissionsTable $Permissions
 */
class UserPermissionsController extends AppController
{
    public $modelClass = "User.Permissions";

    /**
     * @var array
     */
    public $actions = [
        'index' => 'User.Index',
        'view' => 'User.View',
        'edit' => 'User.Edit',
        'delete' => 'User.Delete',
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
     * @param string|null $id Permission id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => [],
        ]);
        $this->set('permission', $permission);
        $this->set('_serialize', ['permission']);
    }

    /**
     * Add method
     *
     * @return void|\Cake\Http\Response Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $permission = $this->Permissions->newEntity();
        if ($this->request->is('post')) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->data);
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'permission')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'permission')));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Permission id.
     * @return void|\Cake\Http\Response Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $permission = $this->Permissions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $this->Permissions->patchEntity($permission, $this->request->data);
            if ($this->Permissions->save($permission)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'permission')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'permission')));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permission = $this->Permissions->get($id);
        if ($this->Permissions->delete($permission)) {
            $this->Flash->success(__d('user', 'The {0} has been deleted.', __d('user', 'permission')));
        } else {
            $this->Flash->error(__d('user', 'The {0} could not be deleted. Please, try again.', __d('user', 'permission')));
        }

        return $this->redirect(['action' => 'index']);
    }
}
