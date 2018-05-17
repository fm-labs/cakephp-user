<?php

namespace User;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Settings\SettingsManager;
use User\Service\UserEventLoggerService;
use User\Service\UserLoginLoggerService;
use User\Service\UserMailerService;

/**
 * Class UserPlugin
 *
 * @package User
 */
class UserPlugin implements EventListenerInterface
{
    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Settings.build' => 'buildSettings'
        ];
    }

    /**
     * @param Event $event
     */
    public function buildSettings(Event $event)
    {
        if ($event->subject() instanceof SettingsManager) {
            $event->subject()->add('User', [
                'layout' => [
                    'type' => 'string',
                ],
                'Login.layout' => [
                    'type' => 'string',
                ],
                'Login.disabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Signup.disabled' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Signup.groupAuth' => [
                    'type' => 'boolean',
                    'default' => false
                ],
                'Signup.verifyEmail' => [
                    'type' => 'boolean',
                    'default' => false
                ],
            ]);
        }
    }

    /**
     * Run user plugin
     *
     * @return void
     */
    public function __invoke()
    {
        EventManager::instance()->on(new UserEventLoggerService());
        EventManager::instance()->on(new UserLoginLoggerService());
        //EventManager::instance()->on(new UserMailerService());
    }
}
