<?php
declare(strict_types=1);

namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserGroup Entity.
 */
class UserGroup extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'password' => true,
        'users' => true,
    ];
}
