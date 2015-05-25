<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserGroupsUser Entity.
 */
class UserGroupsUser extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'user_group_id' => true,
        'user' => true,
        'user_group' => true,
    ];
}
