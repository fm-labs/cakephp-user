<?php
declare(strict_types=1);

namespace User\Form;

use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use User\Model\Entity\User;
use User\Model\Table\UsersTable;

abstract class UserForm extends Form
{
    /**
     * @var \User\Model\Table\UsersTable
     */
    public UsersTable|Table $Users;

    /**
     * @var \User\Model\Entity\User|null
     */
    public User|EntityInterface|null $user;

    /**
     * @var \Cake\Controller\Controller|null
     */
    protected ?Controller $controller;

    /**
     * @param \User\Model\Entity\User|null $user The user entity
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

    public function setController(?Controller $controller): void
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
