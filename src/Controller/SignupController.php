<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Form\Form;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use User\Form\UserRegisterForm;

class SignupController extends AppController
{
    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // @todo Replace hardcoded user model with configured model name
        $this->Users = $this->getTableLocator()->get('User.Users');

        $this->Authentication->allowUnauthenticated([
            'register', 'registerGroup', 'activate', 'activateResend',
        ]);

        $this->viewBuilder()->setLayout(Configure::read('User.layout'));
    }

    /**
     * @return \User\Form\UserRegisterForm
     */
    protected function _buildSignupForm(): UserRegisterForm
    {
        $formClass = Configure::read('User.Signup.formClass', UserRegisterForm::class);
        if (!class_exists($formClass)) {
            throw new InternalErrorException("Class not found: $formClass");
        }
        $form = new $formClass();
        if (!($form instanceof Form)) {
            throw new InternalErrorException('Object is not an instance of \\Cake\\Form\\Form');
        }

        return $form;
    }

    /**
     * Register method
     * No authentication required
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
        # send logged-in users away
        if ($this->_getUser('id')) {
            return $this->redirect('/');
        }

        # force group auth
        if (
            Configure::read('User.Signup.groupAuth')
            && !$this->request->getSession()->read('User.Signup.group_id')
        ) {
            return $this->redirect(['action' => 'registerGroup']);
        }

        # build signup form
        $form = $this->_buildSignupForm();
        $this->set('form', $form);

        # check if signup is disabled
        if (Configure::read('User.Signup.disabled')) {
            $this->Flash->error(__d('user', 'Sorry, but user registration is currently disabled.'), ['key' => 'auth']);

            return;
        }

        # process signup form
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (Configure::read('User.Signup.groupAuth')) {
                $data['group_id'] = $this->request->getSession()->read('User.Signup.group_id');
            }

            //$user = $this->Users->register($data);
            $form->execute($data);
            $user = $form->getUser();
            $this->set('user', $user);
            if (!$user || !$user->id) {
                $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);

                return;
            }

            $this->request->getSession()->delete('User.Signup');
            $this->Flash->success(
                __d('user', 'An activation email has been sent to your email address!'),
                ['key' => 'auth']
            );
            $redirect = Configure::read('User.Signup.redirectUrl') ?? ['_name' => 'user:login'];
            $this->redirect($redirect);
        }
    }

    /**
     * Group registration
     *
     * @return void
     */
    public function registerGroup(): void
    {
        if ($this->request->is(['put', 'post'])) {
            $grpPass = $this->request->getData('group_pass');
            $grpPass = trim($grpPass);
            if (!$grpPass) {
                $this->Flash->error(__d('user', 'No password entered'), ['key' => 'auth']);

                return;
            }

            // find user group with that password
            //$this->loadModel('User.Groups');
            $userGroup = $this->Users->UserGroups->find()->where(['password' => $grpPass])->first();

            if (!$userGroup) {
                $this->request->getSession()->delete('User.Signup.group_id');
                $this->Flash->error(__d('user', 'Invalid password'), ['key' => 'auth']);

                return;
            }

            // store group auth info in session
            $this->request->getSession()->write('User.Signup.group_id', $userGroup->id);
            $this->request->getSession()->write('User.Signup.group_pass', $grpPass);

            // continue registration
            $this->redirect(['action' => 'register']);
        }
    }

    /**
     * Activate
     *
     * @return \Cake\Http\Response|null
     */
    public function activate(): ?Response
    {
        // send logged in users away
        if ($this->_getUser()) {
            return $this->redirect('/');
        }

        /** @var \User\Model\Entity\User $user */
        $user = $this->Users->newEmptyEntity();
        $user->email = $this->request->getQuery('m')
            ? base64_decode($this->request->getQuery('m')) : null;
        $user->email_verification_code = $this->request->getQuery('c')
            ? base64_decode($this->request->getQuery('c')) : null;

        // auto-activation
        if ($user->email && $user->email_verification_code) {
            $_user = $this->Users->activate([
                'email' => $user->email,
                'email_verification_code' => $user->email_verification_code,
            ]);
            if ($_user) {
                $this->getEventManager()->dispatch(new Event('User.Signup.afterActivate', $this, ['user' =>  $_user]));

                $this->Flash->success(
                    __d('user', 'Your account has been activated. You can login now.'),
                    ['key' => 'auth']
                );
                return $this->redirect(['_name' => 'user:login', '?' =>  ['m' => base64_encode($_user->email), 'ref' => 'activate'] ]);
            }

            $this->Flash->error(__d('user', 'Account activation failed'), ['key' => 'auth']);
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $_user = $this->Users->activate($this->request->getData());
            if ($_user) {
                $this->getEventManager()->dispatch(new Event('User.Signup.afterActivate', $this, ['user' =>  $_user]));

                $this->Flash->success(
                    __d('user', 'Your account has been activated. You can login now.'),
                    ['key' => 'auth']
                );
                return $this->redirect(['_name' => 'user:login', '?' =>  ['m' => base64_encode($_user->email), 'ref' => 'activate'] ]);
            }

            $this->Flash->error(__d('user', 'Account activation failed'), ['key' => 'auth']);
        }

        $this->set('user', $user);

        return null;
    }

    /**
     * Resend email verification email
     *
     * @return \Cake\Http\Response|null
     */
    public function activateResend(): ?Response
    {
        // send logged in users away
        if ($this->_getUser()) {
            return $this->redirect('/');
        }

        /** @var \User\Model\Entity\User $user */
        $user = $this->Users->newEmptyEntity();
        $user->email = $this->request->getQuery('m') ? base64_decode($this->request->getQuery('m')) : null;
        $this->set('user', $user);

        if ($this->request->is('post') || $this->request->is('put')) {
            $email = trim($this->request->getData('email'));
            if (!$email) {
                $this->Flash->error(__d('user', 'Please enter an email address'), ['key' => 'auth']);

                return null;
            }

            $user = $this->Users->find()->where(['email' => $email])->contain([])->first();
            if (!$user) {
                $this->Flash->error(__d('user', 'No user with such email address'), ['key' => 'auth']);

                return null;
            }

            $user = $this->Users->updateEmailVerificationCode($user);
            if ($user && !$user->getErrors()) {
                $this->getEventManager()
                    ->dispatch(new Event('User.Signup.registrationResend', $this, compact('user')));

                $this->Flash->success(
                    __d('user', 'An activation email has been sent to {0}', $user->email),
                    ['key' => 'auth']
                );

                return $this->redirect(['_name' => 'user:login', '?' =>  ['m' => base64_encode($user->email), 'ref' => 'activateResend'] ]);
                //return $this->redirect(['action' => 'activate', 'm' => base64_encode($user->email)]);
            }

            $this->Flash->error(__d('user', 'Please fill all required fields'), ['key' => 'auth']);
            $this->set('user', $user);
        }

        return null;
    }
}
