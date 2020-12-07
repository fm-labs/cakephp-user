<?php
declare(strict_types=1);

namespace User\Controller;

/**
 * Class SecurityController
 *
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \User\Model\Table\UsersTable $Users
 */
class SecurityController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.Users';

    /**
     * Passsword change method
     *
     * @return \Cake\Http\Response
     */
    public function passwordChange(): ?\Cake\Http\Response
    {
        /** @var \User\Model\Entity\User $user */
        $user = $this->Users->get($this->_getUser('id'));
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->getData())) {
                $this->Flash->success(__d('user', 'Your password has been changed.'), ['key' => 'auth']);

                return $this->redirect(['action' => 'passwordChanged']);
            }

            $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
        }
        $this->set('user', $user);
    }

    /**
     * Password changed success action
     *
     * @return void
     */
    public function passwordChanged()
    {
    }
}
