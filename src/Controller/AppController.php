<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/6/15
 * Time: 11:31 PM
 */

namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use User\Controller\Component\AuthComponent;
use User\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Class AppController
 * @package User\Controller
 *
 * @property UsersTable $Users
 * @property AuthComponent $Auth
 */
class AppController extends BaseAppController
{
    /**
     * @var string Name of user model used for authentication
     */
    protected $_userModel;

    public function initialize()
    {
        parent::initialize();

        if (!$this->components()->has('Auth')) {
            throw new Exception('User: AuthComponent not loaded');
        } elseif (!$this->Auth instanceof AuthComponent) {
            throw new Exception('User: AuthComponent is not an instance of User.AuthComponent');
        }

        if (!$this->components()->has('Flash')) {
            throw new Exception('User: FlashComponent not loaded');
        }
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Users = $this->Auth->userModel();
    }

}
