<?php
declare(strict_types=1);

namespace User\Service;

use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class UserSessionService
 *
 * @package User\Service
 */
class UserSessionService implements EventListenerInterface
{
    /**
     * @var \GeoIp\Model\Table\GeoIpTable|null $GeoIp
     */
    protected $GeoIp = null;

    /**
     * @var \User\Model\Table\UserSessionsTable|null
     */
    protected ?\Cake\ORM\Table $UserSessions = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->UserSessions = TableRegistry::getTableLocator()->get('User.UserSessions');

        if (Plugin::isLoaded('GeoIp')) {
            $this->GeoIp = TableRegistry::getTableLocator()->get('GeoIp.GeoIp');
        }
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function sessionCreate(Event $event)
    {
        $data = $event->getData();

        // Determine geo location
        if ($this->GeoIp) {
            try {
                $location = $this->GeoIp->lookup($data['client_ip'], ['precision' => 'city']);
                if (!empty($location)) {
                    $data['geo_location'] = $location;
                }
                if (isset($location['country_iso2'])) {
                    $data['geo_country_code'] = $location['country_iso2'];
                }
            } catch (\Exception $ex) {
                Log::error('UserSessionService: ' . $ex->getMessage(), ['user']);
            }
        }

        $userSession = $this->UserSessions->newEntity($data);
        if (!$this->UserSessions->save($userSession)) {
            Log::error('Failed to save user session: ' . json_encode($userSession->getErrors()), ['user']);
        }

        $event->setData($data);
        Log::debug("User session created for user with ID " . $data['user_id'], ['user']);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function sessionExtend(Event $event)
    {
        /** @var \User\Model\Entity\UserSession $userSession */
        $userSession = $this->UserSessions->findBySessionid($event->getData('sessionid'))->first();
        if (!$userSession) {
            return;
        }

        $userSession->setAccess('*', false);
        $userSession->setAccess('expires', true);
        $userSession = $this->UserSessions->patchEntity($userSession, $event->getData());

        $this->UserSessions->save($userSession);
        if (!$this->UserSessions->save($userSession)) {
            Log::error('Failed to save user session: ' . json_encode($userSession->getErrors()), ['user']);
        }

        Log::debug("User session extended for user with ID " . $userSession['user_id'], ['user']);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @return void
     */
    public function sessionDestroy(Event $event)
    {
        /** @var \User\Model\Entity\UserSession $userSession */
        $userSession = $this->UserSessions->findBySessionid($event->getData('sessionid'))->first();
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
    public function implementedEvents(): array
    {
        return [
            'User.Session.create' => 'sessionCreate',
            'User.Session.extend' => 'sessionExtend',
            'User.Session.destroy' => 'sessionDestroy',
        ];
    }
}
