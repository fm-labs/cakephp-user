<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use User\Exception\AuthException;

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

    public $config = [
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

        $this->Authentication->allowUnauthenticated(['login']);

        //@todo Enable UserSession component
        //if (!$this->components()->has('UserSession')) {
        //    $this->loadComponent('User.UserSession', (array)Configure::read('User.UserSession'));
        //}
    }

    /**
     * @inheritDoc
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        //if ($this->components()->has('UserSession')) {
        //    $this->UserSession->ignoreActions(['session']);
        //}
    }

    /**
     * Login method.
     *
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function login(): ?\Cake\Http\Response
    {
        try {
            if (Configure::read('User.Login.disabled') == true) {
                throw new AuthException(__d('user', 'Sorry, but login is currently disabled.'));
            }

            $result = $this->Authentication->getResult();
            // If the user is logged in send them away.
            if ($result->isValid()) {
                print_r($result->getData());
                $target = $this->Authentication->getLoginRedirect() ?? $this->config['loginRedirectUrl'];
                $this->Flash->success('Login successful', ['key' => 'auth']);
                return $this->redirect($target);
            }
            if ($this->request->is('post') && !$result->isValid()) {
                $this->Flash->error(__('Invalid credentials'), ['key' => 'auth']);
            }
        } catch (AuthException $ex) {
            $this->Flash->error($ex->getMessage(), ['key' => 'auth']);
        } catch (\Exception $ex) {
            $this->Flash->error(__('Login unavailable'), ['key' => 'auth']);
            if (Configure::read('debug')) {
                throw $ex;
            }
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
     * Return client session info in JSON format
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
