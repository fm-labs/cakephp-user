<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\AbstractPasswordHasher;
use Cake\Routing\Router;

/**
 * User Entity.
 */
class User extends Entity
{
    /**
     * @var string
     */
    public static $passwordHasherClass = 'Cake\\Auth\\DefaultPasswordHasher';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => false,
        'superuser' => false,
        'name' => false,
        'group_id' => false,
        'username' => false,
        'password' => false,
        'password1' => false,
        'password2' => false,
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

    /**
     * @var array
     */
    protected $_virtual = [
        'display_name',
        'is_root',
        'is_superuser',
        'password_reset_url'
    ];

    /**
     * @return bool
     */
    protected function _getIsRoot()
    {
        return ($this->username === 'root');
    }

    /**
     * @return bool
     */
    protected function _getIsSuperuser()
    {
        return ($this->superuser || $this->username === 'root');
    }

    /**
     * @return null|string
     */
    protected function _getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        return $this->username;
    }

    /**
     * @return string
     * @todo Move url creation to controller (SOC)
     */
    protected function _getPasswordResetUrl()
    {
        $username = base64_encode($this->username);
        $code = base64_encode($this->password_reset_code);

        return Router::url(['prefix' => false, 'plugin' => 'User', 'controller' => 'User', 'action' => 'passwordreset', 'u' => $username, 'c' => $code], true);
    }

    /**
     * @param $password
     * @return string
     */
    protected function _setPassword($password)
    {
        if (self::$passwordHasherClass === false) {
            return $password;
        }

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
