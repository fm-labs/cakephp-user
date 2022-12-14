<?php

namespace User\View\Helper;

use Cake\View\Helper;

/**
 * AuthHelper.
 */
class AuthHelper extends Helper
{
    protected $_defaultConfig = [
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
     * Get the current user object or a property of the object.
     *
     * @param $key
     * @return array|mixed|null
     */
    public function user($key = null) {
        return $this->getUser($key);
    }
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
        $lookupKey = $this->getConfig('sessionKey') . ".id";
        return $this->getView()->getRequest()->getSession()->read($lookupKey);
    }
}
