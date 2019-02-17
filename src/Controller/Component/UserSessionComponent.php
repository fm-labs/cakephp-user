<?php

namespace User\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;

/**
 * User Session Component class
 *
 * @property \User\Controller\Component\AuthComponent|\Cake\Controller\Component\AuthComponent $Auth
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class UserSessionComponent extends Component
{
    /**
     * @var array
     */
    public $components = ['Auth', 'Flash'];

    /**
     * @var array
     */
    protected $_defaultConfig = [
        // max user session lifetime in seconds. should be lower then global session timeout
        'maxLifetime' => 0,
        'ignoreActions' => [],
        'sessionKey' => 'Auth.UserSession',
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $sessionTimeout = Configure::read('Session.timeout');
        if ($sessionTimeout && $sessionTimeout > 0 && $this->_config['maxLifetime'] >= $sessionTimeout * MINUTE) {
            //\Cake\Log\Log::warning("Configured user session maxLifetime is higher than global session timeout. Auto-adjusting maxLifetime to " . ($sessionTimeout * MINUTE - 1), ['user']);
            $this->_config['maxLifetime'] = $sessionTimeout * MINUTE - 1;
        }
    }

    /**
     * @param Event $event The event object
     * @return \Cake\Network\Response|null
     */
    public function beforeFilter(Event $event)
    {
        return $this->checkSession($event);
    }

    /**
     * @param Event $event The event object
     * @return \Cake\Network\Response|null
     */
    public function startup(Event $event)
    {
        return $this->checkSession($event);
    }

    /**
     * Set the ignoreActions.
     * If the current request action is an ignored action,
     * the user session will not be extended.
     *
     * @param array $actions List of actions
     * @param bool $merge Merge flag
     * @return void
     */
    public function ignoreActions(array $actions, $merge = true)
    {
        $this->config('ignoreActions', $actions, $merge);
    }

    /**
     * Check user session
     *
     * @param Event $event The event object
     * @return \Cake\Network\Response|void|null
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

        if ($this->request->session()->check($this->_config['sessionKey'])) {
            $session = $this->request->session()->read($this->_config['sessionKey']);
            if (!$this->validateUserSession($session)) {
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
            return;
        }

        $userSession = [
            'user_id' => $user['id'],
            'session_id' => $this->request->session()->id(),
            'started' => time(),
            'expires' => ($this->_config['maxLifetime'] > 0)
                ? time() + $this->_config['maxLifetime']
                : null
        ];
        $this->request->session()->write($this->_config['sessionKey'], $userSession);
    }

    /**
     * Validate user session
     *
     * @param array $userSession User session data
     * @return bool
     */
    public function validateUserSession(array $userSession)
    {
        if (empty($userSession) || (isset($userSession['expires']) && $userSession['expires'] < time())) {
            return false;
        }

        if ($userSession['session_id'] != $this->request->session()->id()) {
            Log::alert("SessionID mismatch! Possible Hijacking attempt.");
            //@TODO Handle SessionID mismatch
            return false;
        }

        return true;
    }

    /**
     * Extend user session
     *
     * @param array $userSession User session data
     * @return void
     */
    public function extendUserSession(array $userSession)
    {
        if (empty($userSession) || !isset($userSession['expires'])) {
            return;
        }

        $userSession['expires'] = time() + $this->_config['maxLifetime'];
        $this->request->session()->write($this->_config['sessionKey'], $userSession);
    }

    /**
     * Destroy user session
     *
     * @return void
     */
    public function destroyUserSession()
    {
        $this->request->session()->delete($this->_config['sessionKey']);
    }

    /**
     * Send 'expired' response
     *
     * @param \Cake\Controller\Controller $controller Current controller instance
     * @return \Cake\Network\Response|null
     */
    protected function _expired(Controller $controller)
    {
        $this->destroyUserSession();
        $this->Auth->logout();
        $this->Auth->storage()->redirectUrl(false);

        if (!$controller->request->is('ajax')) {
            $this->Auth->flash(__d('user', 'Session timed out'));

            return $controller->redirect($this->Auth->config('loginAction'));
        }

        $this->response->statusCode(403);

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
            'User.Auth.logout' => 'destroyUserSession'
        ];
    }
}
