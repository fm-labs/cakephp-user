<?php

namespace User\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * User Session Component class
 *
 * @property \User\Controller\Component\AuthComponent|\Cake\Controller\Component\AuthComponent $Auth
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class UserSessionComponent extends Component
{
    public $components = ['Auth', 'Flash'];

    protected $_defaultConfig = [
        // max user session lifetime in seconds. should be lower then global session timeout
        'maxLifetime' => 0,
        'ignoreActions' => []
    ];

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        $sessionTimeout = Configure::read('Session.timeout');
        if ($sessionTimeout && $sessionTimeout > 0 && $this->_config['maxLifetime'] >= $sessionTimeout*MINUTE) {
            //\Cake\Log\Log::warning("Configured user session maxLifetime is higher than global session timeout. Auto-adjusting maxLifetime to " . ($sessionTimeout * MINUTE - 1), ['user']);
            $this->_config['maxLifetime'] = $sessionTimeout * MINUTE - 1;
        }
    }

    /**
     * @param Event $event
     * @return \Cake\Network\Response|null
     */
    public function beforeFilter(Event $event)
    {
        return $this->checkSession($event);
    }

    /**
     * @param Event $event
     * @return \Cake\Network\Response|null
     */
    public function startup(Event $event)
    {
        return $this->checkSession($event);
    }

    /**
     * @param Event $event
     * @return \Cake\Network\Response|null
     */
    public function beforeRender(Event $event)
    {
        //debug($this->request->session()->read('Auth.UserSession'));
    }

    public function ignoreActions(array $actions, $merge = true)
    {
        $this->config('ignoreActions', $actions, $merge);
    }

    /**
     * Check user session
     *
     * @param Event $event
     * @return \Cake\Network\Response|null
     */
    public function checkSession(Event $event)
    {
        if ($this->Auth->config('checkAuthIn') != $event->name()) {
            return null;
        }

        if (!$this->Auth->user()) {
            $this->destroyUserSession();
            return null;
        }

        if ($this->request->session()->check('Auth.UserSession')) {
            $session = $this->request->session()->read('Auth.UserSession');
            if (!$this->validateUserSession($session)) {
                //debug("Session validation failed");
                $event->stopPropagation();
                return $this->_expired($event->subject());
            }

            if (!$this->request->is('ajax') && !in_array($this->request->param('action'), $this->_config['ignoreActions'])) {
                $this->extendUserSession($session);
            }
            return null;
        }

        $this->createUserSession();
    }

    /**
     * Create user session
     *
     * @return void
     */
    public function createUserSession()
    {
        $user = $this->Auth->user();
        if (!$user || empty($user) || !isset($user['id'])) {
            return null;
        }

        $userSession = [
            'user_id' => $user['id'],
            'session_id' => $this->request->session()->id(),
            'started' => time(),
            'expires' => ($this->_config['maxLifetime'] > 0)
                ? time() + $this->_config['maxLifetime']
                : null
        ];
        $this->request->session()->write('Auth.UserSession', $userSession);
    }

    /**
     * Validate user session
     *
     * @return bool
     */
    public function validateUserSession(array $userSession)
    {
        if (empty($userSession) || (isset($userSession['expires']) && $userSession['expires'] < time())) {
            return false;
        }

        if ($userSession['session_id'] != $this->request->session()->id()) {
            debug("SessionID mismatch! Possible Hijacking attempt.");
        }

        return true;
    }

    /**
     * Extend user session
     *
     * @return void
     */
    public function extendUserSession(array $userSession)
    {
        if (empty($userSession) || !isset($userSession['expires'])) {
            return null;
        }

        $userSession['expires'] = time() + $this->_config['maxLifetime'];
        $this->request->session()->write('Auth.UserSession', $userSession);
    }

    /**
     * Destroy user session
     *
     * @return void
     */
    public function destroyUserSession()
    {
        $this->request->session()->delete('Auth.UserSession');
        $this->request->session()->delete('Auth.User');
    }

    /**
     * Send 'expired' response
     *
     * @param \Cake\Controller\Controller $controller
     * @return \Cake\Network\Response|null
     */
    protected function _expired(Controller $controller)
    {

        $this->destroyUserSession();
        //$this->Auth->logout();

        if (!$controller->request->is('ajax')) {
            $this->Auth->flash(__('Session timed out'));
            $this->Auth->storage()->redirectUrl(false);

            return $controller->redirect($this->Auth->config('loginAction'));
        }

        $this->Auth->storage()->redirectUrl(false);
        $this->response->statusCode(401);
        return $this->response;
    }

    /**
     * Events supported by this component.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Controller.initialize' => 'beforeFilter',
            'Controller.startup' => 'startup',
            'Controller.beforeRender' => 'beforeRender'
        ];
    }
}
