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
     *
     * @param string $className Name of mailer class
     * @return $this
     */
    public function setUserMailer($className)
    {
        $this->_mailerClass = $className;

        return $this;
    }

    /**
     * Get mailer instance
     * @param Email $email The email object
     * @return \Cake\Mailer\Mailer|UserMailer
     */
    public function getUserMailer(Email $email = null)
    {
        $localizedEmailClass = '\\Banana\\Mailer\\LocalizedEmail';
        if ($email === null && class_exists($localizedEmailClass)) {
            $email = new $localizedEmailClass();
        }

        return $this->getMailer($this->_mailerClass, $email);
    }
}
