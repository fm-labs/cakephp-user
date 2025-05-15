<?php

namespace User\View\Helper;

use Cake\View\Helper;

/**
 * AuthHelper.
 */
class AuthHelper extends Helper
{
    protected array $_defaultConfig = [
        'sessionKey' => 'Auth'
    ];

    /**
     * Check if an auth key exists in session.
     *
     * @param $key
     * @return bool
     */
    public function check($key = null) {
        $lookupKey = $this->getConfig('sessionKey');
        if ($key !== null) {
            $lookupKey .= '.' . $key;
        }
        return $this->getView()->getRequest()->getSession()->check($lookupKey);
    }

    /**
     * Convenience wrapper for getUser().
     *
     * @see getUser()
     */
    public function user($key = null) {
        return $this->getUser($key);
    }

    /**
     * Get the current user object or property by key of the user object
     * @param $key
     * @return array|mixed|null
     */
    public function getUser($key = null) {
        $lookupKey = $this->getConfig('sessionKey');
        if ($key !== null) {
            $lookupKey .= '.' . $key;
        }
        return $this->getView()->getRequest()->getSession()->read($lookupKey);
    }

    /**
     * Check if a user is logged in.
     *
     * @return bool
     */
    public function isAuthenticated(): bool {
        return $this->check('id');
    }
}
