<?php
declare(strict_types=1);

namespace User\Exception;

use Authentication\Identity;

class AuthException extends UserException
{
    /**
     * @var \Authentication\Identity|array|null  Auth user data. Might be empty
     */
    protected array|Identity|null $_user = null;

    /**
     * @var mixed|null
     */
    protected mixed $_redirectUrl = null;

    /**
     * @param string $message Exception message
     * @param array $user Auth user data
     */
    public function __construct(string $message = '', ?array $user = null, $redirectUrl = null)
    {
        $this->_user = $user;
        $this->_redirectUrl = $redirectUrl;
        parent::__construct($message, 403);
    }

    /**
     * @return array Auth user data
     * @deprecated Use getUser() instead.
     */
    public function user(): array
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
