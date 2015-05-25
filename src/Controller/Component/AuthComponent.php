<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/7/15
 * Time: 8:04 PM
 */

namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as CakeAuthComponent;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Controller\ComponentRegistry;

class AuthComponent extends CakeAuthComponent
{
    /*
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
    */

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

        $userModel = (Configure::read('User.model')) ?: 'User.Users';

        // default login action
        if (!$this->config('loginAction')) {
            $this->config('loginAction', [
                'controller' => 'Auth',
                'action' => 'login',
                'plugin' => 'User'
            ]);
        }

        // default authenticate
        if (!$this->config('authenticate')) {
            $this->config('authenticate', [
                self::ALL => ['userModel' => $userModel],
                'Form' => ['userModel' => $userModel]
            ]);
        }

        // default authorize
        if (!$this->config('authorize')) {
            $this->config('authorize', [
                'Controller'
            ]);
        }

        debug($this->_config);
    }

    public function startup(Event $event)
    {
        parent::startup($event);
    }
}
