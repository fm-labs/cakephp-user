<?php

namespace User\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;

class RolesAuthorize extends BaseAuthorize
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
        debug($user);
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
    }
}