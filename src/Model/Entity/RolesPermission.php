<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * RolesPermission Entity.
 *
 * @property int $id
 * @property int $role_id
 * @property \User\Model\Entity\Role $role
 * @property int $permission_id
 * @property \User\Model\Entity\Permission $permission
 */
class RolesPermission extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
