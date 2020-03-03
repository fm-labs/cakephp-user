<?php

namespace User\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class UserActivityService
 *
 * @package User\Event
 * @property \Activity\Model\Table\ActivitiesTable $Activities
 */
class UserActivityService implements EventListenerInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Activities = TableRegistry::getTableLocator()->get('Activity.Activities');
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function userActivity(Event $event)
    {
        /* @var \Cake\ORM\Table $Table */
        $Table = $event->getSubject();
        $activity = $this->Activities->newEntity([
            'type' => 'user',
            'model' => $Table->registryAlias(),
            'foreign_key' => (isset($event->data['user'])) ? $event->data['user']['id'] : null,
            'name' => $event->name()
        ]);

        if (!$this->Activities->save($activity)) {
            Log::error('Failed to save user activity: ' . json_encode($activity->errors()), ['user']);
        }
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function authActivity(Event $event)
    {
        /* @var \User\Controller\Component\AuthComponent $auth */
        $Auth = $event->getSubject();
        $activity = $this->Activities->newEntity([
            'type' => 'auth',
            'model' => $Auth->Users->registryAlias(),
            'foreign_key' => (isset($event->data['user'])) ? $event->data['user']['id'] : null,
            'name' => $event->name()
        ]);

        if (!$this->Activities->save($activity)) {
            Log::error('Failed to save auth activity: ' . json_encode($activity->errors()), ['user']);
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Model.User.passwordForgotten' => 'userActivity',
            'User.Model.User.passwordReset' => 'userActivity',
            'User.Model.User.register' => 'userActivity',
            'User.Model.User.activate' => 'userActivity',
            'User.Model.User.activationResend' => 'userActivity',
            'User.Auth.login' => 'authActivity',
            'User.Auth.logout' => 'authActivity',
        ];
    }
}
