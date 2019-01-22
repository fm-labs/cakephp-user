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

        if ($request->param('plugin') == 'User' && $request->param('controller') == 'GoogleAuth') {
            return true;
        }
        if ($request->param('plugin') == 'User' && $request->param('controller') == 'User' && $request->param('action') == 'logout') {
            return true;
        }

        if($request->session()->read('Auth.GoogleAuth.verified') == true) {
            return true;
        }

        //debug($user);
        //@TODO Implemented RolesAuthorize::authorize() method
        /*
        $modelName = 'Users';
        $modelId = $user['id'];

        $controllerPermissions = [];
        $controller = $this->_registry->getController();
        if ($controller && isset($controller->permissions)) {
            $controllerPermissions = $controller->permissions;
        }

        $Rbac = $this->_registry->get('Rbac');
        $_user = $Rbac->getUser($modelName, $modelId);
        $_roles = $Rbac->getUserRoles($modelName, $modelId);
        $_permissions = $Rbac->getUserPermissions($modelName, $modelId);
        */
        return false;
    }
}
