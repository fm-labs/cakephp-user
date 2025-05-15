<?php
declare(strict_types=1);

namespace User\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserSession Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $login_provider
 * @property string $client_ip
 * @property string $geo_location
 * @property string $sessionid
 * @property string $sessiontoken
 * @property \Cake\I18n\Time $timestamp
 * @property \Cake\I18n\Time $expires
 * @property \Cake\I18n\Time $created
 *
 * @property \User\Model\Entity\User $user
 */
class UserSession extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
