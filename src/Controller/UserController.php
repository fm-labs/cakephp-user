<?php
declare(strict_types=1);

namespace User\Controller;

use Cake\Core\Configure;

/**
 * Class RegistrationController
 *
 * @package User\Controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \User\Model\Table\UsersTable $Users
 */
class UserController extends AppController
{
    /**
     * @var string
     */
    public $modelClass = 'User.Users';

    /**
     * Index method
     * Show user profile
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        if (Configure::check('User.profileUrl')) {
            return $this->redirect(Configure::read('User.profileUrl'));
        }

        $user = $this->Users->get($this->_getUser('id'));
        $this->set('user', $user);
    }
}
