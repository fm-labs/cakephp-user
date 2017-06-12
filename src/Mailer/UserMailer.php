<?php
namespace User\Mailer;

use Banana\Mailer\BananaMailer;
use Cake\Log\Log;
use User\Model\Entity\User;

/**
 * Class UserMailer
 *
 * @package User\Mailer
 */
class UserMailer extends BananaMailer
{
    /**
     * Password forgotten email with password reset link
     *
     * @param User $user
     */
    public function passwordForgotten(User $user)
    {
        Log::debug('[email] passwordf: create email');

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
