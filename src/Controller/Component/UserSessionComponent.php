<?php
declare(strict_types=1);

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
        'sessionKey' => 'Auth.UserSession', // Session storage key
        'maxLifetimeSec' => 3600, // max user session lifetime in seconds. should be lower then global session timeout
        'ignoreActions' => [], // skip user session validation for these controller actions
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        $sessionTimeout = Configure::read('Session.timeout');
        if ($sessionTimeout && $sessionTimeout > 0 && $this->_config['maxLifetimeSec'] > $sessionTimeout * MINUTE) {
            $this->_config['maxLifetimeSec'] = $sessionTimeout * MINUTE;
            //\Cake\Log\Log::warning("Configured user session maxLifetimeSec is higher than global session timeout. Auto-adjusting maxLifetimeSec to " . ($sessionTimeout * MINUTE - 1), ['user']);
        }
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        return $this->checkSession($event);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return \Cake\Http\Response|null
     */
    public function startup(\Cake\Event\EventInterface $event)
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
        $this->setConfig('ignoreActions', $actions, $merge);
    }

    /**
     * Check user session
     *
     * @param \Cake\Event\Event $event The event object
     * @return \Cake\Http\Response|null|void
     */
    public function checkSession(Event $event)
    {
        if ($this->Auth->getConfig('checkAuthIn') != $event->getName()) {
            return null;
        }

        if (!$this->Auth->user()) {
            $this->destroy();

            return null;
        }

        if (in_array($this->getController()->getRequest()->getParam('action'), $this->_config['ignoreActions'])) {
            return null;
        }

        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();
        $userSession = $this->userSession();

        if ($userSession !== null) {
            if (!$this->validateUserSession()) {
                $event->stopPropagation();

                return $this->_expired($controller);
            }

            if (!$this->getController()->getRequest()->is(['ajax', 'requested']) && !$this->extend()) {
                $event->stopPropagation();

                return $this->_expired($controller);
            }

            return null;
        }

        $this->createUserSession();
    }

    /**
     * Automatically extend user session
     *
     * @return bool|null Returns NULL if operation has been ignored. A boolean value represents the success status
     *  of the user session extension
     */
    public function autoExtend()
    {
        if ($this->userSession() === null || $this->getController()->getRequest()->is(['ajax', 'requested'])) {
            return null;
        }

        $expiresIn = $this->expiresIn();
        // auto-extend when 80% of the lifetime has been exeeded
        if ($expiresIn < 0 || $expiresIn > $this->getConfig('maxLifetimeSec') * 0.2) {
            return null;
        }

        return true;
    }

    /**
     * Get active user session data.
     * Returns NULL if no user session is active.
     *
     * @return array|null
     */
    public function userSession()
    {
        if ($this->getController()->getRequest()->getSession()->check($this->_config['sessionKey'])) {
            return $this->getController()->getRequest()->getSession()->read($this->_config['sessionKey']);
        }

        return null;
    }

    /**
     * Set the user session data
     *
     * @param array $userSession User session data
     * @return void
     */
    public function setUserSession(array $userSession)
    {
        $this->getController()->getRequest()->getSession()->write($this->_config['sessionKey'], $userSession);
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

        $sessionId = $this->getController()->getRequest()->getSession()->id();
        $userSession = [
            'user_id' => $user['id'],
            'sessionid' => $sessionId,
            'sessiontoken' => $this->_createToken($sessionId),
            'timestamp' => time(),
            'expires' => $this->_config['maxLifetimeSec'] > 0 ? time() + $this->_config['maxLifetimeSec'] : null,
            'client_ip' => $this->getController()->getRequest()->clientIp(),
            'user_agent' => $this->getController()->getRequest()->getHeaderLine('User-Agent'),
        ];

        /** @var \Cake\Event\Event $event */
        $event = $this->getController()->dispatchEvent('User.Session.create', $userSession, $this);
        $this->setUserSession($event->getData());
    }

    /**
     * Validate user session
     *
     * @return bool
     * @TODO Trigger security events
     */
    public function validateUserSession()
    {
        $userSession = $this->userSession();
        if (!$userSession) {
            return false;
        }

        if ($this->expiresIn() < 1) {
            return false;
        }

        if ($userSession['sessionid'] != $this->getController()->getRequest()->getSession()->id()) {
            Log::alert(
                "SessionID mismatch! Possible Hijacking attempt. IP: " . $this->getController()->getRequest()->clientIp(),
                ['auth', 'user']
            );

            return false;
        }

        if ($userSession['client_ip'] != $this->getController()->getRequest()->clientIp()) {
            Log::alert(
                "ClientIP mismatch! Possible Hijacking attempt. IP: " . $this->getController()->getRequest()->clientIp(),
                ['auth', 'user']
            );

            return false;
        }

        if ($userSession['user_agent'] != $this->getController()->getRequest()->getHeaderLine('User-Agent')) {
            Log::alert(
                "User agent mismatch! Possible Hijacking attempt. IP: " . $this->getController()->getRequest()->clientIp(),
                ['auth', 'user']
            );

            return false;
        }

        return true;
    }

    /**
     * Extend user session
     *
     * @return bool
     */
    public function extend()
    {
        $userSession = $this->userSession();
        if (!$userSession || empty($userSession)) {
            return false;
        }

        //if (!isset($userSession['expires'])) {
        //    return true;
        //}

        $userSession['expires'] = time() + $this->_config['maxLifetimeSec'];

        /** @var \Cake\Event\Event $event */
        $event = $this->getController()->dispatchEvent('User.Session.extend', $userSession, $this);
        $this->setUserSession($event->getData());

        return true;
    }

    /**
     * Returns time from now to session expiration timestamp in seconds.
     *
     * @return int
     */
    public function expiresIn()
    {
        $userSession = $this->userSession();
        if (!$userSession || empty($userSession)) {
            return -1;
        }

        if (!isset($userSession['expires'])) {
            return 0;
        }

        return $userSession['expires'] - time();
    }

    /**
     * Destroy user session
     *
     * @return void
     */
    public function destroy()
    {
        $userSession = $this->userSession();
        $this->getController()->dispatchEvent('User.Session.destroy', $userSession, $this);
        $this->getController()->getRequest()->getSession()->delete($this->_config['sessionKey']);
    }

    /**
     * Returns session info that can be exposed to the client
     *
     * @return array
     */
    public function extractSessionInfo()
    {
        $userSession = $this->userSession();
        if (empty($userSession)) {
            return [];
        }

        $data = [
            't' => time(),
            'l' => $this->Auth->user('id') ? 1 : 0,
            'lt' => $this->getConfig('maxLifetimeSec'),
            'e' => $userSession['expires'],
            'efmt' => $userSession['expires'] ? date(DATE_ATOM, $userSession['expires']) : 0,
        ];

        return $data;
    }

    /**
     * Create user session token
     *
     * @param string $sessionId Session ID
     * @return string
     */
    protected function _createToken($sessionId)
    {
        return md5($sessionId . Configure::read('Security.salt'));
    }

    /**
     * Send 'expired' response
     *
     * @param \Cake\Controller\Controller $controller Current controller instance
     * @return \Cake\Http\Response|null
     */
    protected function _expired(Controller $controller)
    {
        $this->destroy();
        $this->Auth->logout();
        $this->Auth->storage()->redirectUrl(false);

        if (!$controller->getRequest()->is('ajax')) {
            $this->Auth->flash(__d('user', 'Session timed out'));

            return $controller->redirect($this->Auth->getConfig('loginAction'));
        }

        return $controller->getResponse()
            ->withStatus(403);
    }

    /**
     * Events supported by this component.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Controller.initialize' => 'beforeFilter',
            'Controller.startup' => 'startup',
            'User.Auth.logout' => 'destroy',
        ];
    }
}
