<?php
namespace User\Mailer;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Banana\Mailer\BananaMailer;
use User\Model\Entity\User;

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
            ->subject(__('Password forgotten'))
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
            ->subject(__('Password change notification'))
            ->template('User.password_reset')
            ->set(compact('user'));
    }
}