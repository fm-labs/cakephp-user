<?php
declare(strict_types=1);

namespace User\Mailer;

use Cake\Mailer\Mailer;
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
    public function setUserMailerClass($className)
    {
        $this->_mailerClass = $className;

        return $this;
    }

    /**
     * Get mailer instance
     * @param array|null $config
     * @return \Cake\Mailer\Mailer|\User\Mailer\UserMailer
     */
    public function getUserMailer(?array $config = null): Mailer
    {
        return $this->getMailer($this->_mailerClass, $config);
    }
}
