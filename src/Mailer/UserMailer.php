<?php
namespace User\Mailer;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\ORM\Entity;
use User\Controller\Component\AuthComponent;
use User\Model\Entity\User;
use User\Model\Table\UsersTable;

/**
 * Class UserMailer
 *
 * @package User\Mailer
 */
class UserMailer extends Mailer
{
    protected $_user;
    protected $_locale;

    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        $this->_locale = I18n::defaultLocale();

        if (Configure::check('User.Mailer.profile')) {
            $this->_email->profile(Configure::read('User.Mailer.profile'));
        }
    }

    protected function _setUser(User $user)
    {
        $this->to($user->email);
        $this->set('user', $user);

        $this->_user = $user;
        $this->_locale = ($user['locale']) ?: I18n::defaultLocale();
    }

    protected function _setProfile($profile)
    {
        // if profile is a string, check the static configuration in following order:
        // - if locale is set (TODO: and translation is enabled), email translation profile config: User.EmailTranslation.[LOCALE].[PROFILE]
        // - email translation profile config: User.Email.[PROFILE]
        if (is_string($profile)) {
            $check = [];
            // add User email profile translation config check
            if ($this->_locale /*&& $this->_locale != I18n::defaultLocale()*/) {
                $check[] = 'User.EmailTranslation.' . $this->_locale . '.' . $profile;
            }

            // add User email profile config check
            $check[] = 'User.Email.' . $profile;

            // use first match
            foreach ($check as $_check) {
                if (Configure::check($_check)) {
                    $this->_email->profile(Configure::read($_check));
                    break;
                }
            }

        }
        elseif (is_array($profile)) {
            $this->_email->profile($profile);
        }
        else {
            //throw new \Exception('User email profile not found: ' . $profile);
        }

        $subject = $this->_email->getOriginalSubject();
        $this->_email->set('_subject', $subject);
    }

    /**
     * User registration email
     *
     * @param User $user
     * @return void
     */
    public function userRegistration(User $user)
    {
        $this->_setUser($user);
        $this->_setProfile(__FUNCTION__);

        $verificationUrl = UsersTable::buildEmailVerificationUrl($user);
        if (!$verificationUrl) {
            throw new \InvalidArgumentException('UserMailer::userRegistration: Verification url missing');
        }
        $this->set(compact('verificationUrl'));
    }

    /**
     * User activation email
     *
     * @param User $user
     * @return void
     */
    public function userActivation(User $user)
    {
        $this->_setUser($user);
        $this->_setProfile(__FUNCTION__);
    }

    /**
     * User login email
     *
     * @param User $user
     * @return void
     */
    public function newLogin(User $user)
    {
        $this->_setUser($user);
        $this->_setProfile(__FUNCTION__);
    }

    /**
     * Password forgotten email with password reset link
     *
     * @param User $user
     * @return void
     */
    public function passwordForgotten(User $user)
    {
        $this->_setUser($user);
        $this->_setProfile(__FUNCTION__);

        $resetUrl = UsersTable::buildPasswordResetUrl($user);
        if (!$resetUrl) {
            throw new \InvalidArgumentException('UserMailer::passwordForgotten: Reset url missing');
        }
        $this->set(compact('resetUrl'));
    }

    /**
     * Password reset notification email
     *
     * @param User $user
     * @return void
     */
    public function passwordReset(User $user)
    {
        $this->_setUser($user);
        $this->_setProfile(__FUNCTION__);
    }

}
