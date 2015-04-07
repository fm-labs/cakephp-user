<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/7/15
 * Time: 8:04 PM
 */

namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as BaseAuthComponent;
use Cake\Event\Event;

class AuthComponent extends BaseAuthComponent
{
    protected $_defaultConfig = [
        'authenticate' => [
            'Form' => ['userModel' => 'User.Users']
        ],
        'authorize' => false,
        'ajaxLogin' => null,
        'flash' => null,
        'loginAction' => [
            'controller' => 'Auth',
            'action' => 'login',
            'plugin' => 'User'
        ],
        'loginRedirect' => null,
        'logoutRedirect' => null,
        'authError' => null,
        'unauthorizedRedirect' => true
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    public function startup(Event $event)
    {
        parent::startup($event);
    }
}
