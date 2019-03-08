<?php

namespace User\Service;

use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class UserSessionService
 *
 * @package User\Event
 * @property \User\Model\Table\UserSessionsTable $UserSessions
 * @property \GeoIp\Model\Table\GeoIpTable $GeoIp
 */
class UserSessionService implements EventListenerInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->UserSessions = TableRegistry::get('User.UserSessions');

        if (Plugin::loaded('GeoIp')) {
            $this->GeoIp = TableRegistry::get('GeoIp.GeoIp');
        }
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function sessionCreate(Event $event)
    {
        $data = $event->data;

        // Determine geo location
        if ($this->GeoIp) {
            try {
                $location = $this->GeoIp->lookup($data['client_ip'], ['precision' => 'city']);
                $data['geo_location'] = $location;
                $data['geo_country_code'] = $location['country_iso2'];
            } catch (\Exception $ex) {
                Log::error('UserSessionService: ' . $ex->getMessage(), ['user']);
            }
        }

        $userSession = $this->UserSessions->newEntity($data);
        if (!$this->UserSessions->save($userSession)) {
            Log::error('Failed to save user session: ' . json_encode($userSession->errors()), ['user']);
        }

        $event->data = $data;
        Log::debug("User session created for user with ID " . $data['user_id'], ['user']);
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function sessionExtend(Event $event)
    {
        /* @var \User\Model\Entity\UserSession $userSession */
        $userSession = $this->UserSessions->findBySessionid($event->data['sessionid'])->first();
        if (!$userSession) {
            return;
        }

        $userSession->accessible('*', false);
        $userSession->accessible('expires', true);
        $userSession = $this->UserSessions->patchEntity($userSession, $event->data);

        $this->UserSessions->save($userSession);
        if (!$this->UserSessions->save($userSession)) {
            Log::error('Failed to save user session: ' . json_encode($userSession->errors()), ['user']);
        }

        Log::debug("User session extended for user with ID " . $userSession['user_id'], ['user']);
    }

    /**
     * @param Event $event The event object
     * @return void
     */
    public function sessionDestroy(Event $event)
    {
        /* @var \User\Model\Entity\UserSession $userSession */
        $userSession = $this->UserSessions->findBySessionid($event->data['sessionid'])->first();
        if (!$userSession) {
            return;
        }

        if (!$this->UserSessions->delete($userSession)) {
            Log::error('Failed to delete user session with ID ' . $userSession->id, ['user']);
        }

        Log::debug("User session destroyed for user with ID " . $userSession['user_id']);
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'User.Session.create' => 'sessionCreate',
            'User.Session.extend' => 'sessionExtend',
            'User.Session.destroy' => 'sessionDestroy',
        ];
    }
}
