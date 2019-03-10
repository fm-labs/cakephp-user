<?php

namespace User\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\TableRegistry;
use User\Model\Table\PermissionsTable;
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;

/**
 * Class RbacComponent
 *
 * @package User\Controller\Component
 * @property AuthComponent $Auth
 */
class RbacComponent extends Component
{
    /**
     * @var array
     */
    public $components = ['Auth'];

    /**
     * @var array
     */
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

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->Users = TableRegistry::get($this->config('users_table'));
        $this->Roles = TableRegistry::get($this->config('roles_table'));
        $this->Permissions = TableRegistry::get($this->config('permissions_table'));
    }

    /**
     * Get current authenticated user
     * via AuthComponent
     *
     * @return array
     */
    public function getAuthUser()
    {
        return $this->Auth->user();
    }

    /**
     * Get user by model and id
     *
     * @param int $userId User ID
     * @throws NotImplementedException
     * @return void
     */
    public function getUser($userId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get roles of user
     *
     * @param int $userId User ID
     * @throws NotImplementedException
     * @return void
     */
    public function getUserRoles($userId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get permissions of user
     *
     * @param int $userId User ID
     * @throws NotImplementedException
     * @return void
     */
    public function getUserPermissions($userId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get role by id
     *
     * @param int $roleId Role ID
     * @throws NotImplementedException
     * @return void
     */
    public function getRole($roleId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get list of users associated with given role
     *
     * @param int $roleId Role ID
     * @throws NotImplementedException
     * @return void
     */
    public function getRoleUsers($roleId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get list of permissions associated with given role
     *
     * @param int $roleId Role ID
     * @throws NotImplementedException
     * @return void
     */
    public function getRolePermissions($roleId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get permission by id
     *
     * @param int $permId Permission ID
     * @throws NotImplementedException
     * @return void
     */
    public function getPermission($permId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get list of roles associated with given permission
     *
     * @param int $permId Permission ID
     * @throws NotImplementedException
     * @return void
     */
    public function getPermissionRoles($permId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * Get list of permissions associated with given permission
     *
     * @param int $permId Permission ID
     * @throws NotImplementedException
     * @return void
     */
    public function getPermissionUsers($permId)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * @param string $user User name
     * @param string $role Role name
     * @throws NotImplementedException
     * @return void
     */
    public function userAddRole($user, $role)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }

    /**
     * @param string $role Role name
     * @param string $permission Permission name
     * @throws NotImplementedException
     * @return void
     */
    public function roleAddPermission($role, $permission)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . "() not implemented yet");
    }
}
