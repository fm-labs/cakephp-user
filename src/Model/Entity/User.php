<?php
namespace User\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\AbstractPasswordHasher;
use Cake\Routing\Router;

/**
 * User Entity.
 *
 * @property string first_name
 * @property string last_name
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

    protected $_virtual = [
        'display_name',
        'is_root',
        'is_superuser',
        'password_reset_url'
    ];

    protected function _getIsRoot()
    {
        return ($this->username === 'root');
    }

    protected function _getIsSuperuser()
    {
        return ($this->superuser || $this->username === 'root');
    }

    protected function _getDisplayName()
    {
        if ($this->first_name && $this->last_name) {
            return sprintf("%s, %s", $this->last_name, $this->first_name);
        }
        return $this->username;
    }

    protected function _getPasswordResetUrl()
    {
        $username = base64_encode($this->username);
        $code = base64_encode($this->password_reset_code);
        return Router::url(['plugin' => 'User', 'controller' => 'User', 'action' => 'passwordreset', 'u' => $username, 'c' => $code], true);
    }

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
