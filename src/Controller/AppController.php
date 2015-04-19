<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 4/6/15
 * Time: 11:31 PM
 */

namespace User\Controller;

use App\Controller\AppController as BaseAppController;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;

class AppController extends BaseAppController
{
    public function initialize()
    {
        parent::initialize();

        if (!$this->components()->has('Auth')) {
            throw new Exception('User: AuthComponent not loaded');
        }

        if (!$this->components()->has('Flash')) {
            throw new Exception('User: FlashComponent not loaded');
        }

        $this->layout = (Configure::read('User.userLayout')) ?: 'User.user';
    }
}
