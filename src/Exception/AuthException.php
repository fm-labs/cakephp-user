<?php

namespace User\Exception;

class AuthException extends UserException
{
    /**
     * @var array Auth user data. Might be empty
     */
    protected $_user;

    /**
     * @param string $message Exception message
     * @param array $user Auth user data
     */
    public function __construct($message, $user = [])
    {
        $this->_user = $user;
        parent::__construct($message, 401);
    }

    /**
     * @return array Auth user data
     */
    public function user()
    {
        return $this->_user;
    }
}
