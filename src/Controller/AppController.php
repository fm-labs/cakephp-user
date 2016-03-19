<?php
namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use User\Controller\Component\AuthComponent as UserAuthComponent;
use User\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * Class AppController
 * @package User\Controller
 *
 * @property UsersTable $Users
 * @property UserAuthComponent $Auth
 */
class AppController extends BaseAppController
{

    public function initialize()
    {
        parent::initialize();

        if (!$this->components()->has('Auth')) {
            throw new Exception('User: AuthComponent not loaded');

        } elseif (!$this->Auth instanceof UserAuthComponent) {
            throw new Exception('User: AuthComponent is not an instance of User.AuthComponent');
        }

        if (!$this->components()->has('Flash')) {
            $this->components()->load('Flash');
        }
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Users = $this->Auth->userModel();
    }

}
