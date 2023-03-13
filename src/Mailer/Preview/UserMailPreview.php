<?php
namespace User\Mailer\Preview;

use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use DebugKit\Mailer\MailPreview;
use User\Mailer\UserMailer;

/**
 * @property \User\Model\Table\UsersTable $Users
 */
class UserMailPreview extends MailPreview
{
    protected function getPreviewUser()
    {
        $this->Users = TableRegistry::getTableLocator()->get('User.Users');
        /** @var \User\Model\Entity\User $user */
        //$user = $this->Users->find()->first();
        $user = $this->Users->newEmptyEntity();
        $user->locale = "de";
        $user->username = "testuser";
        $user->email = "test@example.org";
        $user->email_verification_required = true;
        $user->email_verification_code = "dummy-verification-code";
        $user->password_reset_code = "dummy-reset-code";

        return $user;
    }

    public function userRegistration()
    {
        /** @var UserMailer $mailer */
        $mailer = $this->getMailer("User.User");
        $user = $this->getPreviewUser();
        return $mailer
            ->userRegistration($user);
    }

    public function userActivation()
    {
        /** @var UserMailer $mailer */
        $mailer = $this->getMailer("User.User");
        $user = $this->getPreviewUser();
        return $mailer
            ->userActivation($user);
    }

    public function newLogin()
    {
        /** @var UserMailer $mailer */
        $mailer = $this->getMailer("User.User");
        $user = $this->getPreviewUser();
        return $mailer
            ->newLogin($user);
    }

    public function passwordForgotten()
    {
        /** @var UserMailer $mailer */
        $mailer = $this->getMailer("User.User");
        $user = $this->getPreviewUser();
        return $mailer
            ->passwordForgotten($user);
    }

    public function passwordReset()
    {
        /** @var UserMailer $mailer */
        $mailer = $this->getMailer("User.User");
        $user = $this->getPreviewUser();
        return $mailer
            ->passwordReset($user);
    }

}
