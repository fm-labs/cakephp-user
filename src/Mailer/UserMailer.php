<?php
namespace User\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\ORM\Entity;
use User\Model\Entity\User;

/**
 * Class UserMailer
 *
 * @package User\Mailer
 */
class UserMailer extends Mailer
{
    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        if (Configure::check('User.Email.profile')) {
            $this->_email->profile(Configure::read('User.Email.profile'));
        }
    }

    /**
     * User registration email
     *
     * @param User $user
     * @return void
     */
    public function userRegistration(Entity $user)
    {
        $this
            ->to($user->email)
            ->subject(__d('user', 'Your registration'))
            ->template('User.user_registration')
            ->set(compact('user'));
    }

    /**
     * Password forgotten email with password reset link
     *
     * @param User $user
     * @return void
     */
    public function passwordForgotten(User $user)
    {
        $this
            ->to($user->email)
            ->subject(__d('user', 'Password forgotten'))
            ->template('User.password_forgotten')
            ->set(compact('user'));
    }

    /**
     * Password reset notification email
     *
     * @param User $user
     * @return void
     */
    public function passwordReset(User $user)
    {
        $this
            ->to($user->email)
            ->subject(__d('user', 'Password change notification'))
            ->template('User.password_reset')
            ->set(compact('user'));
    }
}
