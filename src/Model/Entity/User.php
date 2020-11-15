<?php
declare(strict_types=1);

namespace User\Model\Entity;

use Authentication\IdentityInterface;
use Authorization\AuthorizationServiceInterface;
use Authorization\Policy\ResultInterface;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property bool $superuser
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property int $group_id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property bool $email_verification_required
 * @property string $email_verification_code
 * @property \Cake\I18n\Time $email_verification_expiry_timestamp
 * @property bool $email_verified
 * @property int $password_change_min_days
 * @property int $password_change_max_days
 * @property int $password_change_warning_days
 * @property \Cake\I18n\Time $password_change_timestamp
 * @property \Cake\I18n\Time $password_expiry_timestamp
 * @property bool $password_force_change
 * @property string $password_reset_code
 * @property \Cake\I18n\Time $password_reset_expiry_timestamp
 * @property bool $login_enabled
 * @property string $login_last_login_ip
 * @property string $login_last_login_host
 * @property \Cake\I18n\Time $login_last_login_datetime
 * @property int $login_failure_count
 * @property \Cake\I18n\Time $login_failure_datetime
 * @property bool $block_enabled
 * @property string $block_reason
 * @property \Cake\I18n\Time $block_datetime
 * @property string $locale
 * @property string $timezone
 * @property string $currency
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \User\Model\Entity\UserGroup $group
 * @property \Authorization\AuthorizationServiceInterface $authorization
 */
class User extends Entity implements IdentityInterface//, \Authorization\IdentityInterface
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
        'locale' => false,
        'timezone' => false,
        'is_deleted' => false,
    ];

    /**
     * @var array
     */
    protected $_virtual = [
        'display_name',
        'is_root',
        'is_superuser',
    ];

    /**
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalData()
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function can($action, $resource): bool
    {
        return $this->authorization->can($this, $action, $resource);
    }

    /**
     * @inheritDoc
     */
    public function canResult($action, $resource): ResultInterface
    {
        return $this->authorization->canResult($this, $action, $resource);
    }

    /**
     * @inheritDoc
     */
    public function applyScope($action, $resource)
    {
        return $this->authorization->applyScope($this, $action, $resource);
    }

    /**
     * @inheritDoc
     */
    public function setAuthorization(AuthorizationServiceInterface $service)
    {
        $this->authorization = $service;

        return $this;
    }

    /**
     * @return bool
     */
    protected function _getIsRoot()
    {
        return $this->username === 'root';
    }

    /**
     * @return bool
     */
    protected function _getIsSuperuser()
    {
        return $this->superuser || $this->username === 'root';
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
     * @param string $password Password value
     * @return string
     */
    protected function _setPassword($password)
    {
        if (self::$passwordHasherClass === false) {
            return $password;
        }

        return $this->getPasswordHasher()->hash((string)$password);
    }

    /**
     * @return \Cake\Auth\AbstractPasswordHasher
     */
    public function getPasswordHasher()
    {
        return new static::$passwordHasherClass();
    }
}
