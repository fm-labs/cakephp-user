<?php

namespace User\Mailer;

use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;

trait UserMailerAwareTrait
{
    use MailerAwareTrait;

    /**
     * UserMailer class name
     * @var string
     */
    protected $_mailerClass = 'User.User';

    /**
     * Set user mailer class
     */
    public function setUserMailer($className)
    {
        $this->_mailerClass = $className;
        return $this;
    }

    /**
     * Get mailer instance
     * @return \Cake\Mailer\Mailer|UserMailer
     * @throws \Exception
     */
    public function getUserMailer(Email $email = null)
    {
        return $this->getMailer($this->_mailerClass, $email);
    }
}