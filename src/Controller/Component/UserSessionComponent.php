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
 * @property \User\Controller\Component\AuthComponent $Auth
 * @property \Cake\Controller\Component\FlashComponent $Flash
 */
class UserSessionComponent extends Component
{
    /**
     * @var array
     */
    public array $components = ['User.Auth', 'Flash'];

    /**
     * @var array
     */
    protected array $_defaultConfig = [
        'sessionKey' => 'UserSession', // Session storage key
        'sessionCheckEvent' => 'Controller.startup',
        'maxLifetimeSec' => 3600, // max user session lifetime in seconds. should be lower then global session timeout
        'ignoreActions' => [], // skip user session validation for these controller actions
    ];

    protected $_userSession = null;

    /**
     * @inheritDoc
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
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        if ($this->getConfig('sessionCheckEvent') === 'Controller.initialize') {
            $this->checkSession($event);
        }
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return \Cake\Http\Response|null|void
     */
    public function startup(\Cake\Event\EventInterface $event)
    {
        if ($this->getConfig('sessionCheckEvent') === 'Controller.startup') {
            $this->checkSession($event);
        }
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
//        if ($this->getConfig('sessionCheckEvent') != $event->getName()) {
//            return null;
//        }

//        if (!$this->Auth->user()) {
//            $this->Flash->warning("UserSession: No user found in session");
//            //$this->destroy();
//
//            return null;
//        }

        $this->Flash->info("Check Session");
//        if (in_array($this->getController()->getRequest()->getParam('action'), $this->_config['ignoreActions'])) {
//            return null;
//        }

        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();
        $userSession = $this->userSession();

//        if ($userSession !== null) {
//            if (!$this->validateUserSession()) {
//                $event->stopPropagation();
//
//                return $this->_expired($controller);
//            }
//
//            if (!$this->getController()->getRequest()->is(['ajax', 'requested']) && !$this->extend()) {
//                $event->stopPropagation();
//
//                return $this->_expired($controller);
//            }
//
//            return null;
//        }

        try {

            if ($userSession) {
                $this->Flash->success("USERSESSION: " . json_encode($userSession));
                $this->validateUserSession();
            } else {
                $this->Flash->warning("CREATING USERSESSION");
                $this->createUserSession();
            }

        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            Log::critical($ex->getMessage(), ['auth', 'user']);
        }

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
        if (!$this->_userSession) {
            $this->_userSession = $this->getController()->getRequest()
                ->getSession()
                ->read($this->_config['sessionKey']);
        }

        return $this->_userSession;
    }

    /**
     * Set the user session data
     *
     * @param array $userSession User session data
     * @return void
     */
    public function setUserSession(array $userSession)
    {
        $this->getController()->getRequest()
            ->getSession()
            ->write($this->_config['sessionKey'], $userSession);
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
        //$this->setUserSession($userSession);

        /** @var \Cake\Event\Event $event */
        $event = $this->getController()->dispatchEvent('User.Session.create', $userSession, $this);
        $this->setUserSession($event->getData());
    }

    /**
     * Validate user session
     *
     * @return void
     * @throws \Exception
     * @TODO Trigger security events
     */
    public function validateUserSession()
    {
        $userSession = $this->userSession();
        if (!$userSession) {
            throw new \LogicException(
                'No active user session found',
            );
        }

        if ($this->expiresIn() < 1) {
            throw new \Exception(
                'User Session expired',
            );
        }

        if ($userSession['sessionid'] != $this->getController()->getRequest()->getSession()->id()) {
            throw new \Exception(
                'SessionID mismatch! Possible Hijacking attempt. Expected: ' . $this->getController()->getRequest()->getSession()->id(),
            );
        }

        if ($userSession['client_ip'] != $this->getController()->getRequest()->clientIp()) {
            throw new \Exception(
                'ClientIP mismatch! Possible Hijacking attempt. IP: ' . $this->getController()->getRequest()->clientIp(),
            );
        }

        if ($userSession['user_agent'] != $this->getController()->getRequest()->getHeaderLine('User-Agent')) {
            throw new \Exception(
                'User agent mismatch! Possible Hijacking attempt. IP: ' . $this->getController()->getRequest()->clientIp(),
            );
        }
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
        $this->Auth->flash("Session expired");
        return null;

//        $this->destroy();
//        $this->Authentication->logout();
//        //$this->Auth->storage()->redirectUrl(false);
//
//        if (!$controller->getRequest()->is('ajax')) {
//            $this->Auth->flash(__d('user', 'Session timed out'));
//
//            return $controller->redirect($this->Auth->getConfig('loginAction'));
//        }
//
//        return $controller->getResponse()
//            ->withStatus(403);
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
