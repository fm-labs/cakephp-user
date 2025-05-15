<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use User\Exception\AuthException;
use User\Form\UserLoginForm;

/**
 * Class AuthController
 *
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \User\Model\Table\UsersTable $Users
 */
class AuthController extends AppController
{
    /**
     * @var string
     */
    public ?string $defaultTable = 'User.Users';

    /**
     * @var array
     */
    public array $config = [
        'loginDisabled' => false,
        'loginRedirectUrl' => '/',
        'logoutRedirectUrl' => ['_name' => 'user:login'],
        'registerRedirectUrl' => ['_name' => 'user:login'],
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('User.Auth');
        # Allow login method for unauthenticated users
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * Login method.
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function login(): ?\Cake\Http\Response
    {
//        if ($this->Authentication->getIdentity()) {
//            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
//        }
//        if ($this->Auth->user()) {
//            return $this->redirect($this->Auth->redirectUrl() ?? '/');
//        }

        $form = null;
        try {
            //$formClass = UserLoginForm::class;
            //$form = new $formClass($this);
            $form = new UserLoginForm();
            $form->setController($this);

            // @todo Move to AuthenticationListener::beforeLogin()
            if (Configure::read('User.Login.disabled')) {
                throw new AuthException(__d('user', 'Sorry, but login is currently disabled.'));
            }

            if (Configure::read('User.Login.layout')) {
                $this->viewBuilder()->setLayout(Configure::read('User.Login.layout'));
            }

            // perform login using the UserLoginForm
            // @todo Refactor that we use the form only for validation
            //       and invoke the AuthComponent::login() method here
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->request->getData())) {
                    //debug($form->getErrors());
                    throw new AuthException(__d('user', 'Login failed'));
                }
                $this->Flash->success(__d('user', 'Login successful'), ['key' => 'auth']);
                //return $form->getResponse();
            }

            // login redirect
            // if user is logged in, redirect to configured login redirect url
            if ($this->Auth->user()) {
                //print_r($result->getData());
                $defaultRedirect = $controller->config['loginRedirectUrl'] ?? '/';
                $redirectUrl = $this->Auth->redirectUrl() ?? $defaultRedirect;
                $this->redirect($redirectUrl);
            }

        } catch (AuthException $ex) {
            $this->Flash->error($ex->getMessage(), ['key' => 'auth']);
        } catch (\Exception $ex) {
            $this->Flash->error(__d('user', 'Login unavailable'), ['key' => 'auth']);
            if (Configure::read('debug')) {
                $this->Flash->error($ex->getMessage(), ['key' => 'auth']);
            }
        } finally {
            $this->set('form', $form);
        }

        return null;
    }

    /**
     * Logout method.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?\Cake\Http\Response
    {
        //$redirectUrl = $this->Authentication->logout();
        $redirectUrl = $this->Auth->logout();
        if (!$redirectUrl) {
            $redirectUrl = $this->config['logoutRedirectUrl'] ?? ['_name' => 'user:login'];
        }
        $this->Flash->success(__d('user', 'You are logged out now!'), ['key' => 'auth']);

        return $this->redirect($redirectUrl);
    }

    /**
     * @todo Return client session info in JSON format
     *
     * @return void
     */
    public function session(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $data = $this->UserSession->extractSessionInfo();
        $this->set('data', $data);
        $this->viewBuilder()->setOption('serialize', 'data');
    }
}
