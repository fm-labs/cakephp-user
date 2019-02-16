<?php
use Cake\Core\Configure;
use Cake\Log\Log;

include dirname(dirname(__FILE__)) . DS . 'src/functions.php';

/**
 * Logs
 */
Log::config('user', [
    'className' => 'Cake\Log\Engine\FileLog',
    'path' => LOGS,
    'file' => 'user',
    //'levels' => ['info'],
    'scopes' => ['user', 'auth']
]);

/**
 * Mailer support
 */
if (Configure::read('User.Mailer.enabled') == true && !Configure::check('User.Email')) {
    Configure::load('User.emails');
}
