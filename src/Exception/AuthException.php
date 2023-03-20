<?php
declare(strict_types=1);

namespace User\Exception;

class AuthException extends UserException
{
    /**
     * @var array|null|\Authentication\Identity Auth user data. Might be empty
     */
    protected $_user;

    /**
     * @var mixed|null
     */
    protected $_redirectUrl;

    /**
     * @param string $message Exception message
     * @param array $user Auth user data
     */
    public function __construct(string $message = "", $user = null, $redirectUrl = null)
    {
        $this->_user = $user;
        $this->_redirectUrl = $redirectUrl;
        parent::__construct($message, 403);
    }

    /**
     * @return array Auth user data
     * @deprecated Use getUser() instead.
     */
    public function user()
    {
        return $this->_user;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }
}
