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
    /**
     * User entity
     * @var \User\Model\Entity\User
     */
    protected $_user;

    /**
     * Locale used for email & translations
     * @var string
     */
    protected $_locale;

    /**
     * The original locale used by the application.
     * @var string
     */
    protected $_originalLocale;

    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        // set the default email locale
        $this->_locale = I18n::defaultLocale();

        // backup the application locale
        $this->_originalLocale = I18n::locale();

        if (Configure::check('User.Mailer.profile')) {
            $this->_email->profile(Configure::read('User.Mailer.profile'));
        }
    }

    /**
     * Overloaded send method
     */
    public function send($action, $args = [], $headers = [])
    {
        parent::send($action, $args, $headers);
    }

    /**
     * Overloaded reset method.
     * Resets temp locale switch.
     */
    public function reset()
    {
        parent::reset();
        $this->_locale = I18n::defaultLocale();
        //$this->_user = null;

        // restore original locale
        I18n::locale($this->_originalLocale);
    }

    /**
     * Sets the active user for emailing
     */
    protected function _setUser(User $user)
    {
        $this->to($user->email);
        $this->set('user', $user);

        $this->_user = $user;
        $this->_originalLocale = I18n::locale();
        $this->_locale = ($user['locale']) ?: I18n::defaultLocale();

        // temporarily switch to email locale
        I18n::locale($this->_locale);
    }

    /**
     * Sets the email profile.
     * Checks configurations in following order:
     *
     * User.EmailTranslation.[LOCALE].[PROFILE]
     * User.Email.[PROFILE]
     *
     * @param string|array $profile
     * @return void
     */
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
