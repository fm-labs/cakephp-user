<?php
namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use User\Controller\Component\AuthComponent as UserAuthComponent;
use User\Model\Table\UsersTable;

/**
 * Class AppController
 *
 * @package User\Controller
 * @property UsersTable $Users
 * @property UserAuthComponent $Auth
 */
class AppController extends BaseAppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (!$this->components()->has('Auth')) {
            $this->loadComponent('User.Auth');
        }

        if (!($this->components()->get('Auth') instanceof \User\Controller\Component\AuthComponent)) {
            throw new Exception('User: AuthComponent is not an instance of User.AuthComponent');
        }

        $this->loadComponent('Flash');
    }
}
