<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Controller\Controller;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\ORM\TableRegistry;
use User\Model\Entity\User;

abstract class UserForm extends Form
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public \User\Model\Table\UsersTable|\Cake\ORM\Table $Users;

    /**
     * @var \User\Model\Entity\User|null
     */
    public User|\Cake\Datasource\EntityInterface|null $user;

    /**
     * @var Controller|null
     */
    protected ?Controller $controller;

    /**
     * @param null|\User\Model\Entity\User $user The user entity
     */
    public function __construct(?User $user = null, ?EventManager $eventManager = null)
    {
        parent::__construct($eventManager);

        $this->Users = TableRegistry::getTableLocator()->get('User.Users');
        if ($user === null) {
            $user = $this->Users->newEmptyEntity();
        }
        $this->user = $user;
    }

    public function setController(?Controller $controller)
    {
        $this->controller = $controller;
        //$this->setEventManager($controller->getEventManager());
    }

    public function getController(): ?Controller
    {
        return $this->controller;
    }

    /**
     * Get user entity object
     *
     * @return \User\Model\Entity\User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
