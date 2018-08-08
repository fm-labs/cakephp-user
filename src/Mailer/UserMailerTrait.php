<?php

namespace User\Mailer;

trait UserMailerTrait
{

    /**
     * UserMailer class name
     * @var string
     */
    protected $_mailerClass = '\\User\\Mailer\\UserMailer';

    /**
     * UserMailer instance
     * @var \User\Mailer\UserMailer
     */
    protected $_mailer;

    /**
     * Get mailer instance
     * @return \Cake\Mailer\Mailer|UserMailer
     * @throws \Exception
     */
    public function getMailer()
    {
        if (!$this->_mailer) {
            $className = $this->_mailerClass;
            if (!class_exists($className)) {
                throw new \Exception('Mailer class not found');
            }
            $this->_mailer = new $className();
        }

        return $this->_mailer;
    }
}