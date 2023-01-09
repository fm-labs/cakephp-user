<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Log\Log;
use User\Exception\PasswordResetException;
use User\Form\PasswordForgottenForm;
use User\Model\Table\UsersTable;

/**
 * Class RecoveryController
 *
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \User\Model\Table\UsersTable $Users
 */
class PasswordController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.Users';

    /**
     * @inheritDoc
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated([
            'passwordForgotten', 'passwordSent', 'passwordReset',
        ]);

        $this->viewBuilder()->setLayout(Configure::read('User.layout'));
    }

    /**
     * Password forgotten method
     * Creates a new password reset code and sends email with password reset link
     * No authentication required
     *
     * @return \Cake\Http\Response
     */
    public function passwordForgotten()
    {
        if ($this->_getUser()) {
            return $this->redirect('/');
        }

        $form = new PasswordForgottenForm();

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($form->execute($this->request->getData())) {
                $this->Flash->success(
                    __d('user', 'Password recovery info has been sent to you via email. Please check your inbox.'),
                    ['key' => 'auth']
                );

                if (Configure::read('debug')) {
                    $user = $form->getUser();
                    $this->Flash->set(UsersTable::buildPasswordResetUrl($user), ['key' => 'auth']);
                }

                return $this->redirect(['action' => 'passwordSent']);
            }

            $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
        }

        $this->set('form', $form);
    }

    /**
     * Password forgotten default success action
     *
     * @return void
     */
    public function passwordSent()
    {
    }

    /**
     * Password reset method
     * User can assign new password with username and a password reset code
     * No authentication required
     *
     * @return void|\Cake\Http\Response
     */
    public function passwordReset(): ?\Cake\Http\Response
    {
        if ($this->_getUser()) {
            return $this->redirect('/');
        }

        $user = null;
        try {
            $query = [];
            $query['username'] = base64_decode($this->request->getQuery('u'));
            $query['password_reset_code'] = base64_decode($this->request->getQuery('c'));

            if (!isset($query['password_reset_code'])) {
                throw new PasswordResetException(__d('user', 'Password reset code missing'));
            }

            /** @var \User\Model\Entity\User $user */
            $user = $this->Users->find()->where($query)->first();
            if (!$user) {
                throw new PasswordResetException('Invalid request');
            }

            if ($this->request->is(['post', 'put'])) {
                $user = $this->Users->resetPassword($user, $this->request->getData());
                if ($user && !$user->getErrors()) {
                    $this->Flash->success(__d('user', 'You can now login with your new password'), ['key' => 'auth']);

                    return $this->redirect(['_name' => 'user:login', 'u' => base64_encode($user->username)]);
                }
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            }
        } catch (PasswordResetException $ex) {
            $this->Flash->error($ex->getMessage(), ['key' => 'auth']);

            return $this->redirect(['_name' => 'user:login']);
        } catch (\Exception $ex) {
            Log::error('UsersController::resetPassword: ' . $ex->getMessage(), ['user']);
            $this->Flash->error(__d('user', 'Something went wrong. Please try again.'), ['key' => 'auth']);

            return $this->redirect(['_name' => 'user:login']);
        }

        $this->set('user', $user);
    }


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
        $this->render('password_change');
        return null;
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
