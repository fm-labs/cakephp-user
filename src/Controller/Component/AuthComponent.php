<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/7/15
 * Time: 8:04 PM
 */

namespace User\Controller\Component;

use Cake\Controller\Component\AuthComponent as BaseAuthComponent;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Controller\ComponentRegistry;

class AuthComponent extends BaseAuthComponent
{
    public static $loadDefaultConfigFile = true;

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

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        //@TODO refactor
        if (self::$loadDefaultConfigFile === true && Configure::check('User.Auth') === true) {
            $this->_defaultConfig = array_merge($this->_defaultConfig, (array) Configure::read('User.Auth'));
        }

        parent::__construct($registry, $config);
    }

    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    public function startup(Event $event)
    {
        parent::startup($event);
    }
}
