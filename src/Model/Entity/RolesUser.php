<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * RolesUser Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \User\Model\Entity\User $user
 * @property int $role_id
 * @property \User\Model\Entity\Role $role
 */
class RolesUser extends Entity
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
