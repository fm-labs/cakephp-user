<?php
namespace User\Mailer;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use User\Model\Entity\User;
use User\Model\Table\UsersTable;

/**
 * Class UserMailer
 *
 * @package User\Mailer
 */
class UserMailer extends Mailer
{
    /**
     * User entity
     * @var \User\Model\Entity\User
     */
    protected $_user;

    /**
     * @param Email|null $email Email object
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        if (Configure::check('User.Mailer.profile')) {
            $this->profile(Configure::read('User.Mailer.profile'));
        }
    }

    /**
     * Sets the active user for emailing
     *
     * @param User $user The user entity
     * @return void
     */
    protected function _setUser(User $user)
    {
        $this->_user = $user;

        $this->to($user->email);
        $this->set('user', $user);

        if (method_exists($this->_email, 'locale')) {
            //$this->locale($user->locale);
            $locale = ($user->locale) ?: I18n::locale();
            $this->_email->locale($locale);
        }
    }

    /**
     * @param null|string|array $profile Email profile
     * @return void
     * @deprecated Use profile() instead
     */
    protected function _setProfile($profile)
    {
        $this->profile($profile);
    }

    /**
     * Sets the email profile.
     * Reads configurations from config key `User.Email.[PROFILE]`
     *
     * @param null|string|array $profile Email profile
     * @return $this|Email
     */
    public function profile($profile = null)
    {
        if ($profile === null) {
            return $this->_email->profile();
        }

        if (is_string($profile) && Configure::check('User.Email.' . $profile)) {
            $profile = Configure::read('User.Email.' . $profile);
        }

        $this->_email->profile($profile);

        return $this;
    }

    /**
     * User registration email
     *
     * @param User $user The user entity
     * @return $this
     */
    public function userRegistration(User $user)
    {
        $this->profile(__FUNCTION__);
        $this->_setUser($user);

        $verificationUrl = UsersTable::buildEmailVerificationUrl($user);
        if (!$verificationUrl) {
            throw new \InvalidArgumentException('UserMailer::userRegistration: Verification url missing');
        }
        $this->set(compact('verificationUrl'));

        return $this;
    }

    /**
     * User activation email
     *
     * @param User $user The user entity
     * @return $this
     */
    public function userActivation(User $user)
    {
        $this->profile(__FUNCTION__);
        $this->_setUser($user);

        return $this;
    }

    /**
     * User login email
     *
     * @param User $user The user entity
     * @return $this
     */
    public function newLogin(User $user)
    {
        $this->profile(__FUNCTION__);
        $this->_setUser($user);

        return $this;
    }

    /**
     * Password forgotten email with password reset link
     *
     * @param User $user The user entity
     * @return $this
     */
    public function passwordForgotten(User $user)
    {
        $this->profile(__FUNCTION__);
        $this->_setUser($user);

        $resetUrl = UsersTable::buildPasswordResetUrl($user);
        if (!$resetUrl) {
            throw new \InvalidArgumentException('UserMailer::passwordForgotten: Reset url missing');
        }
        $this->set(compact('resetUrl'));

        return $this;
    }

    /**
     * Password reset notification email
     *
     * @param User $user The user entity
     * @return $this
     */
    public function passwordReset(User $user)
    {
        $this->profile(__FUNCTION__);
        $this->_setUser($user);

        return $this;
    }
}
