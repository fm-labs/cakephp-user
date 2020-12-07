<?php
declare(strict_types=1);

namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Utility\Hash;

/**
 * Class AppController
 *
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \User\Controller\Component\AuthComponent $Auth
 * @property \User\Controller\Component\UserSessionComponent $UserSession
 * @property \User\Model\Table\UsersTable $Users
 */
class AppController extends BaseAppController
{

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Flash');

        //@todo Enable UserSession component
        //if (!$this->components()->has('UserSession')) {
        //    $this->loadComponent('User.UserSession', (array)Configure::read('User.UserSession'));
        //}
    }


    /**
     * @param null|string $key Identity data key
     * @return \Authentication\IdentityInterface|mixed|null
     */
    protected function _getUser($key = null)
    {
        $identity = $this->getRequest()->getAttribute('identity');
        //$identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return null;
        }

        if ($key !== null) {
            return Hash::get($identity, $key);
            //return $this->Authentication->getIdentityData($key);
        }

        return $identity;
    }
}
