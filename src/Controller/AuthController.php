<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use User\Exception\AuthException;

/**
 * Class AuthController
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AuthController extends AppController
{
    public $config = [
        'loginDisabled' => false,
        'loginRedirectUrl' => '/',
        'logoutRedirectUrl' => ['_name' => 'user:login'],
    ];

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Flash');
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        //$layout = Configure::read('User.layout') ?: 'User.user';
        //$this->viewBuilder()->setLayout($layout);
    }

    /**
     * Login method.
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
                $target = $this->Authentication->getLoginRedirect() ?? $this->config['loginRedirectUrl'];
                $this->Flash->success("Login successful");

                return $this->redirect($target);
            }
            if ($this->request->is('post') && !$result->isValid()) {
                $this->Flash->error('Invalid username or password');
            }
        } catch (AuthException $ex) {
            $this->Auth->flash($ex->getMessage());
        } catch (\Exception $ex) {
            $this->Auth->flash(__('Login unavailable'));
            if (Configure::read('debug')) {
                throw $ex;
            }
        }

        return null;
    }

    /**
     * Logout method.
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?\Cake\Http\Response
    {
        $this->Authentication->logout();
        $this->Flash->success(__d('user', 'You are logged out now!'), ['key' => 'auth']);
        $redirectUrl = $this->config['logoutRedirectUrl'] ?? ['_name' => 'user:login'];

        return $this->redirect($redirectUrl);
    }
}
