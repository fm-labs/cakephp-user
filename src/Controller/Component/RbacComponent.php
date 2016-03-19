<?php

namespace User\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use User\Model\Table\PermissionsTable;
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;

/**
 * Class RbacComponent
 * @package User\Controller\Component
 * @property AuthComponent $Auth
 */
class RbacComponent extends Component
{
    public $components = ['Auth'];

    protected $_defaultConfig = [
        'users_table' => 'User.Users',
        'roles_table' => 'User.Roles',
        'permissions_table' => 'User.Permissions'
    ];

    /**
     * @var UsersTable
     */
    public $Users;

    /**
     * @var RolesTable
     */
    public $Roles;

    /**
     * @var PermissionsTable
     */
    public $Permissions;

    public function initialize(array $config)
    {
        $this->Users = TableRegistry::get($this->config('users_table'));
        $this->Roles = TableRegistry::get($this->config('roles_table'));
        $this->Permissions = TableRegistry::get($this->config('permissions_table'));
    }

    /**
     * Get current authenticated user
     * via AuthComponent
     */
    public function getAuthUser()
    {
        return $this->Auth->user();
    }


    /**
     * Get user by model and id
     * @param $userId
     */
    public function getUser($userId)
    {

    }

    /**
     * Get roles of user
     * @param $userId
     */
    public function getUserRoles($userId)
    {

    }

    /**
     * Get permissions of user
     * @param $userId
     */
    public function getUserPermissions($userId)
    {

    }

    /**
     * Get role by id
     * @param $roleId
     */
    public function getRole($roleId)
    {

    }

    /**
     * Get list of users associated with given role
     * @param $roleId
     */
    public function getRoleUsers($roleId)
    {

    }

    /**
     * Get list of permissions associated with given role
     * @param $roleId
     */
    public function getRolePermissions($roleId)
    {

    }

    /**
     * Get permission by id
     * @param $permId
     */
    public function getPermission($permId)
    {

    }

    /**
     * Get list of roles associated with given permission
     * @param $permId
     */
    public function getPermissionRoles($permId)
    {

    }

    /**
     * Get list of permissions associated with given permission
     * @param $permId
     */
    public function getPermissionUsers($permId)
    {

    }

    public function userAddRole($user, $role)
    {

    }

    public function roleAddPermission($role, $permission)
    {

    }
}