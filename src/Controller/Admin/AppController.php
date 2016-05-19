<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 2/16/16
 * Time: 6:32 PM
 */

namespace User\Controller\Admin;

use App\Controller\Admin\AppController as BaseAdminAppController;

class AppController extends BaseAdminAppController
{

    /**
     * @return array
     * @deprecated Use backend config file instead
     */
    public static function backendMenu()
    {
        return [
            'plugin.user' => [
                'title' => 'Users',
                'url' => ['plugin' => 'User', 'controller' => 'Users', 'action' => 'index'],
                'icon' => 'lock',
                'requireRoot' => true, // temporary access control workaround

                '_children' => [
                ]
            ],
        ];
    }
}