<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * GroupsUser Entity.
 */
class GroupsUser extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'group_id' => true,
        'user' => true,
        'group' => true,
    ];
}
