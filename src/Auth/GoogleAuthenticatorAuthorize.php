<?php

namespace User\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;

/**
 * Class RolesAuthorize
 *
 * @package User\Auth
 */
class GoogleAuthenticatorAuthorize extends BaseAuthorize
{
    /**
     * Constructor
     *
     * @param ComponentRegistry $registry
     * @param array $config
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * Authorize user for request
     *
     * @param array $user Current authenticated user
     * @param \Cake\Network\Request $request Request instance.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        if ($user['gauth_enabled'] == false) {
            return true;
        }
        if($request->session()->read('Auth.GoogleAuth.verified') == true) {
            return true;
        }
        if ($request->param('plugin') == 'User' && $request->param('controller') == 'GoogleAuth') {
            return true;
        }
        if ($request->param('plugin') == 'User' && $request->param('controller') == 'User' && $request->param('action') == 'logout') {
            return true;
        }

        return false;
    }
}
