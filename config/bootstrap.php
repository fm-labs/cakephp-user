<?php
use Cake\Core\Configure;

/**
 * Configs
 */
Configure::load('User.user');
try { Configure::load('user'); } catch (\Exception $ex) {}
try { Configure::load('local/user'); } catch (\Exception $ex) {}

/**
 * Logs
 */
\Cake\Log\Log::config('user', [

    'className' => 'Cake\Log\Engine\FileLog',
    'path' => LOGS,
    'file' => 'user',
    //'levels' => ['info'],
]);


\Cake\Log\Log::config('auth', [

    'className' => 'Cake\Log\Engine\FileLog',
    'path' => LOGS,
    'file' => 'auth',
    //'levels' => ['info'],
]);

\Cake\Event\EventManager::instance()->on(new \User\UserPlugin());
\Cake\Event\EventManager::instance()->on(new \User\Event\UserEventListener());