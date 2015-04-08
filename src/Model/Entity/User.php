<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'username' => false,
        'password' => false,
        'is_login_allowed' => true,
    ];

    protected function _setPassword($password)
    {
        return $this->getPasswordHasher()->hash($password);
    }

    /**
     * @return AbstractPasswordHasher
     */
    public function getPasswordHasher()
    {
        return (new DefaultPasswordHasher());
    }
}
