<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\AbstractPasswordHasher;

/**
 * User Entity.
 */
class User extends Entity
{

    public static $passwordHasherClass = 'Cake\\Auth\\DefaultPasswordHasher';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => false,
        'group_id' => false,
        'username' => false,
        'password' => false,
        'email' => false,
        'email_verification_required' => false,
        'email_verification_code' => false,
        'email_verification_expiry_timestamp' => false,
        'email_verified' => false,
        'password_change_min_days' => false,
        'password_change_max_days' => false,
        'password_change_warning_days' => false,
        'password_change_timestamp' => false,
        'password_expiry_timestamp' => false,
        'password_force_change' => false,
        'password_reset_code' => false,
        'password_reset_expiry_timestamp' => false,
        'login_enabled' => false,
        'login_last_login_ip' => false,
        'login_last_login_host' => false,
        'login_last_login_datetime' => false,
        'login_failure_count' => false,
        'login_failure_datetime' => false,
        'block_enabled' => false,
        'block_reason' => false,
        'block_datetime' => false,
        'groups' => false,
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
        return (new static::$passwordHasherClass());
    }
}
