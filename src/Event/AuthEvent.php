<?php

namespace User\Event;

/**
 * An event emitted by the AuthComponent (= subject)
 *
 * @method getSubject(): \User\Controller\Component\AuthComponent
 */
class AuthEvent extends \Cake\Event\Event
{
    /**
     * @return \Authentication\Identity|\User\Model\Entity\User|\ArrayAccess|null
     */
    public function getUser()
    {
        return $this->getData('user');
    }

    /**
     * @return \Cake\Controller\Controller
     */
    public function getController(): \Cake\Controller\Controller
    {
        return $this->getSubject()->getController();
    }

    /**
     * @return \Cake\Http\ServerRequest
     */
    public function getRequest(): \Cake\Http\ServerRequest
    {
        return $this->getSubject()->getController()->getRequest();
    }
}