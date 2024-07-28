<?php
declare(strict_types=1);

namespace User\Mailer;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Exception;
use InvalidArgumentException;
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
     *
     * @var \User\Model\Entity\User
     */
    protected User $_user;

    protected string $_locale;

    protected array $_profiles;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct(Configure::read('User.Mailer.profile', null));

        // load localized email configurations
        Configure::load('User.emails');
        $this->_profiles = Configure::consume('User.Email', []);
        $this->_locale = I18n::getLocale();
    }

    /**
     * Sets the active user for emailing
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return \User\Mailer\UserMailer
     */
    protected function setUser(User $user): UserMailer
    {
        $this->_user = $user;

        $this->setTo($user->email);
        $this->setViewVars('user', $user);

        if ($user->locale) {
            $this->setLocale($user->locale);
        }

        return $this;
    }

    /**
     * Set locale used in email.
     *
     * @param string|null $locale
     * @return $this
     */
    protected function setLocale(?string $locale): UserMailer
    {
        if (!$locale) {
            $locale = I18n::getLocale();
        }
        $this->_locale = $locale;

        return $this;
    }

    protected function setLocalized(array $localConfigs)
    {
        if (isset($localConfigs[$this->_locale])) {
            $localized = $localConfigs[$this->_locale];
            $this->setProfile($localized);
        }

        return $this;
    }

    /**
     * @param string $templateName
     * @return $this
     */
    protected function setLocalizedTemplate(string $templateName): UserMailer
    {
//        $defaultLocale = I18n::getDefaultLocale();
//        $locale = I18n::getLocale();
//
//        if ($defaultLocale !== $locale) {
//            $templateName = sprintf("%s_%s", $templateName, $locale);
//        }
        $this->viewBuilder()->setTemplate($templateName);

        return $this;
    }

    /**
     * @param array|string $config
     * @return \User\Mailer\UserMailer
     */
    protected function setLocalizedProfile(array|string $config): UserMailer
    {
//        if (is_string($config) && Configure::check('User.Email.' . $config)) {
//            $config = Configure::read('User.Email.' . $config);
//        }
        if (is_string($config) && isset($this->_profiles[$config])) {
            $config = $this->_profiles[$config];
        }

         $localConfigs = [];
        if (isset($config['localized'])) {
            $localConfigs = $config['localized'];
            unset($config['localized']);
        }

         $this->setProfile($config);
         $this->setLocalized($localConfigs);

         return $this;
    }

    /**
     * Sets the email profile.
     * Reads configurations from config key `User.Email.[PROFILE]`
     *
     * @param array|string|null $config Email profile
     * @return $this
     */
    public function setProfile($config): UserMailer
    {
        parent::setProfile($config);

        return $this;
    }

    public function send(?string $action = null, array $args = [], array $headers = []): array
    {
        //@todo Evaluate this exception wrapper, if it is necessary
        try {
            $sent = parent::send($action, $args, $headers);
            return $sent;
        } catch (Exception $ex) {
            return ['message' => 'Failed to send email: ' . $ex->getMessage()];
        }
    }

    /**
     * User registration email
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return $this
     */
    public function userRegistration(User $user)
    {
        $verificationUrl = UsersTable::buildEmailVerificationUrl($user);
        if (!$verificationUrl) {
            throw new InvalidArgumentException('UserMailer::userRegistration: Verification url missing');
        }
        $loginUrl = Router::url(['_name' => 'user:login'], true);

        $this
            ->setUser($user)
            ->setLocalizedProfile(__FUNCTION__)
            ->setViewVars(compact('verificationUrl', 'loginUrl'));

        return $this;
    }

    /**
     * User activation email
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return $this
     */
    public function userActivation(User $user)
    {
        $this
            ->setUser($user)
            ->setLocalizedProfile(__FUNCTION__);

        return $this;
    }

    /**
     * User login email
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return $this
     */
    public function newLogin(User $user)
    {
        $this
            ->setUser($user)
            ->setLocalizedProfile(__FUNCTION__);

        return $this;
    }

    /**
     * Password forgotten email with password reset link
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return $this
     */
    public function passwordForgotten(User $user)
    {
        $resetUrl = UsersTable::buildPasswordResetUrl($user);
        if (!$resetUrl) {
            throw new InvalidArgumentException('UserMailer::passwordForgotten: Reset url missing');
        }

        $this
            ->setUser($user)
            ->setLocalizedProfile(__FUNCTION__)
            ->setViewVars(compact('resetUrl'));

        return $this;
    }

    /**
     * Password reset notification email
     *
     * @param \User\Model\Entity\User $user The user entity
     * @return $this
     */
    public function passwordReset(User $user)
    {
        $this
            ->setUser($user)
            ->setLocalizedProfile(__FUNCTION__);

        return $this;
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onRegister(Event $event): void
    {
        $this->send('userRegistration', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onActivate(Event $event): void
    {
        $this->send('userActivation', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onLogin(Event $event): void
    {
        $this->send('newLogin', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onPasswordForgotten(Event $event): void
    {
        $this->send('passwordForgotten', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onPasswordReset(Event $event): void
    {
        $this->send('passwordReset', [$event->getData('user')]);
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'User.Model.User.register' => 'onRegister',
            'User.Signup.registrationResend' => 'onRegister',
            'User.Signup.afterActivate' => 'onActivate',
            'User.Password.forgotten' => 'onPasswordForgotten',
            'User.Password.reset' => 'onPasswordReset',
            'User.Model.User.newLogin' => 'onLogin',
        ];
    }
}
