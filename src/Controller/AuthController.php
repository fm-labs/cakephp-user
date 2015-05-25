<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/6/15
 * Time: 10:39 PM
 */

namespace User\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use User\Model\Table\UsersTable;

/**
 * Class AuthController
 *
 * @package User\Controller
 *
 * @property UsersTable $Users
 */
class AuthController extends AppController
{
    /**
     * @var string Name of auth layout
     */
    public $layout = 'User.auth';

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // allow login method to pass authentication
        $this->Auth->allow(['login']);
    }

    /**
     * Login method
     */
    public function login()
    {
        $this->Auth->userLogin();
    }

    /**
     * Logout method
     */
    public function logout()
    {
        $this->Auth->userLogout();
    }
}
