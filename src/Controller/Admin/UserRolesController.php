<?php
declare(strict_types=1);

namespace User\Controller\Admin;

/**
 * Roles Controller
 *
 * @property \User\Model\Table\RolesTable $Roles
 */
class UserRolesController extends AppController
{
    public ?string $defaultTable = 'User.Roles';

    /**
     * @var array
     */
    public array $actions = [
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
     * @param string|null $id Role id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => [],
        ]);
        $this->set('role', $role);
        $this->viewBuilder()->setOption('serialize', ['role']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEmptyEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'role')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'role')));
            }
        }
        $this->set(compact('role'));
        $this->viewBuilder()->setOption('serialize', ['role']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'role')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'role')));
            }
        }
        $this->set(compact('role'));
        $this->viewBuilder()->setOption('serialize', ['role']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|void Redirects to index.
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function delete(?string $id = null)
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
