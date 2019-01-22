<?php
/**
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace User\Auth;

use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\Network\Response;
use GoogleRecaptcha\Lib\Recaptcha2;

/**
 * An authentication adapter for AuthComponent. Provides the ability to authenticate using POST
 * data. Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate config.
 *
 * ```
 *  $this->Auth->authenticate = [
 *      'Form' => [
 *          'finder' => ['auth' => ['some_finder_option' => 'some_value']]
 *      ]
 *  ]
 * ```
 *
 * When configuring FormAuthenticate you can pass in config to which fields, model and additional conditions
 * are used. See FormAuthenticate::$_config for more information.
 *
 * @see \Cake\Controller\Component\AuthComponent::$authenticate
 *
 * ! EXPERIMENTAL - UNUSED !
 */
class FormAuthenticate extends \Cake\Auth\FormAuthenticate
{
    protected function _checkRecaptcha(Request $request)
    {
        $gResponse = $request->data('g-recaptcha-response');
        if (!$gResponse) {
            return false;
        }

        try {
            return Recaptcha2::verify(Configure::read('GoogleRecaptcha.secretKey'), $gResponse);
        } catch (\Exception $ex) {
            debug($ex->getMessage());
        }

        return false;
    }

    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param \Cake\Network\Response $response Unused response object.
     * @return mixed False on login failure.  An array of User data on success.
     */
    public function authenticate(Request $request, Response $response)
    {
        //if (!$this->_checkRecaptcha($request)) {
        //    return false;
        //}

        return parent::authenticate($request, $response);
    }
}
