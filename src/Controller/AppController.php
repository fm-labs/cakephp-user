<?php
declare(strict_types=1);

namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;

/**
 * Class AppController
 *
 * @package User\Controller
 * @property \User\Controller\Component\AuthComponent $Auth
 * @property \User\Controller\Component\UserSessionComponent $UserSession
 * @property \User\Model\Table\UsersTable $Users
 */
class AppController extends BaseAppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        if (!$this->components()->has('Auth')) {
            $this->loadComponent('User.Auth', (array)Configure::read('User.Auth'));
        }

        if (!($this->components()->get('Auth') instanceof \User\Controller\Component\AuthComponent)) {
            throw new Exception('User: AuthComponent is not an instance of \User\Controller\Component\AuthComponent');
        }

        //@todo Enable UserSession component
        //if (!$this->components()->has('UserSession')) {
        //    $this->loadComponent('User.UserSession', (array)Configure::read('User.UserSession'));
        //}

        $this->loadComponent('Flash');
    }
}
