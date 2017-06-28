<?php
namespace User\Controller\Admin;

/**
 * Groups Controller
 *
 * @property \User\Model\Table\GroupsTable $Groups
 */
class UserGroupsController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.Groups';

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Backend.Index',
        'view' => 'Backend.View',
        'edit' => 'Backend.Edit',
        'delete' => 'Backend.Delete'
    ];

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userGroup = $this->Groups->newEntity();
        if ($this->request->is('post')) {
            $userGroup = $this->Groups->patchEntity($userGroup, $this->request->data);
            if ($this->Groups->save($userGroup)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'user group')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'user group')));
            }
        }
        $this->set(compact('userGroup'));
        $this->set('_serialize', ['userGroup']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User Group id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userGroup = $this->Groups->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userGroup = $this->Groups->patchEntity($userGroup, $this->request->data);
            if ($this->Groups->save($userGroup)) {
                $this->Flash->success(__d('user', 'The {0} has been saved.', __d('user', 'user group')));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'The {0} could not be saved. Please, try again.', __d('user', 'user group')));
            }
        }
        $this->set(compact('userGroup'));
        $this->set('_serialize', ['userGroup']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User Group id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userGroup = $this->Groups->get($id);
        if ($this->Groups->delete($userGroup)) {
            $this->Flash->success(__d('user', 'The {0} has been deleted.', __d('user', 'user group')));
        } else {
            $this->Flash->error(__d('user', 'The {0} could not be deleted. Please, try again.', __d('user', 'user group')));
        }

        return $this->redirect(['action' => 'index']);
    }
}
