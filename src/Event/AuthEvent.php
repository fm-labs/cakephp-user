<?php
declare(strict_types=1);

namespace User\Event;

use ArrayAccess;
use Authentication\Identity;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use User\Model\Entity\User;

/**
 * An event emitted by the AuthComponent (= subject)
 *
 * @method getSubject(): \User\Controller\Component\AuthComponent
 */
class AuthEvent extends Event
{
    /**
     * @return \Authentication\Identity|\User\Model\Entity\User|\ArrayAccess|null
     */
    public function getUser(): Identity|User|ArrayAccess|null
    {
        return $this->getData('user');
    }

    /**
     * @return \Cake\Controller\Controller
     */
    public function getController(): Controller
    {
        return $this->getSubject()->getController();
    }

    /**
     * @return \Cake\Http\ServerRequest
     */
    public function getRequest(): ServerRequest
    {
        return $this->getSubject()->getController()->getRequest();
    }
}
