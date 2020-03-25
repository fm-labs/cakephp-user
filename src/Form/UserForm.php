<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Form\Form;
use Cake\ORM\TableRegistry;
use User\Model\Entity\User;

abstract class UserForm extends Form
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public $Users;

    /**
     * @var \User\Model\Entity\User
     */
    public $user;

    /**
     * @param null|\User\Model\Entity\User $user The user entity
     */
    public function __construct(?User $user = null)
    {
        $this->Users = TableRegistry::getTableLocator()->get('User.Users');
        if ($user === null) {
            $user = $this->Users->newEntity();
        }
        $this->user = $user;
    }

    /**
     * Get user entity object
     *
     * @return \User\Model\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
