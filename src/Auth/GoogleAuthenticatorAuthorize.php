<?php

namespace User\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest as Request;

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
     * @param ComponentRegistry $registry Component registry
     * @param array $config Adapter configuration
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * Authorize user for request
     *
     * @param array $user Current authenticated user
     * @param \Cake\Http\ServerRequest $request Request instance.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        if ($user['gauth_enabled'] == false) {
            return true;
        }
        if ($request->getSession()->read('Auth.GoogleAuth.verified') == true) {
            return true;
        }
        if ($request->getParam('plugin') == 'User' && $request->getParam('controller') == 'GoogleAuth') {
            return true;
        }
        if ($request->getParam('plugin') == 'User' && $request->getParam('controller') == 'User' && $request->getParam('action') == 'logout') {
            return true;
        }

        return false;
    }
}
