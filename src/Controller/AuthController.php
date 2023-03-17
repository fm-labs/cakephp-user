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
    public $modelClass = 'User.Users';

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
        # Allow login method for unauthenticated users
        $this->Authentication->allowUnauthenticated(['login']);
    }

//    /**
//     * @inheritDoc
//     */
//    public function beforeFilter(\Cake\Event\EventInterface $event)
//    {
//        parent::beforeFilter($event);
//
//        //if ($this->components()->has('UserSession')) {
//        //    $this->UserSession->ignoreActions(['session']);
//        //}
//    }

    /**
     * Login method.
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function login(): ?\Cake\Http\Response
    {
        if ($this->Authentication->getIdentity()) {
            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
        }

        $form = null;
        try {
            $formClass = UserLoginForm::class;
            $form = new $formClass($this);

            if (Configure::read('User.Login.disabled')) {
                throw new AuthException(__d('user', 'Sorry, but login is currently disabled.'));
            }

            if (Configure::read('User.Login.layout')) {
                $this->viewBuilder()->setLayout(Configure::read('User.Login.layout'));
            }

            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->request->getData())) {
                    //debug($form->getErrors());
                    throw new AuthException(__d('user', 'Login failed'));
                }
                return $form->getResponse();
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
        $redirectUrl = $this->Authentication->logout();
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
    public function session()
    {
        $this->viewBuilder()->setClassName('Json');
        $data = $this->UserSession->extractSessionInfo();
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
