<?php
declare(strict_types=1);

namespace User\Service;

use Cake\Core\InstanceConfigTrait;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use User\Mailer\UserMailerAwareTrait;

/**
 * Class UserMailerService
 *
 * @package User\Event
 */
class UserMailerService implements EventListenerInterface
{
    use InstanceConfigTrait;
    use UserMailerAwareTrait;

    protected $_defaultConfig = [
        //'enabled' => true,
        //'profile' => 'default',
        'mailerClass' => 'User.User',
    ];

    /**
     * @param array $config Instance config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
        $this->setUserMailer($this->_config['mailerClass']);
    }

    /**
     * @param string $action Mailer action to invoke
     * @param array $args Mailer action args
     * @return void
     */
    public function sendEmail($action, $args = [])
    {
        try {
            $mailer = $this->getUserMailer();
            $mailer->send($action, $args);
        } catch (\Exception $ex) {
            Log::error('UserMailerService::sendEmail: ' . $ex->getMessage(), ['user']);
        }
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onRegister(Event $event)
    {
        $this->sendEmail('userRegistration', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onActivate(Event $event)
    {
        $this->sendEmail('userActivation', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onLogin(Event $event)
    {
        $this->sendEmail('newLogin', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onPasswordForgotten(Event $event)
    {
        $this->sendEmail('passwordForgotten', [$event->getData('user')]);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function onPasswordReset(Event $event)
    {
        $this->sendEmail('passwordReset', [$event->getData('user')]);
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'User.Model.User.register' => 'onRegister',
            'User.Model.User.activationResend' => 'onRegister',
            'User.Model.User.activate' => 'onActivate',
            'User.Model.User.passwordForgotten' => 'onPasswordForgotten',
            'User.Model.User.passwordReset' => 'onPasswordReset',
            'User.Model.User.newLogin' => 'onLogin',
        ];
    }
}
