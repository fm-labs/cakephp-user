<?php
use Cake\Core\Configure;

/**
 * Automatically load app's user configuration
 *
 * Copy user.default.php to your app's config folder,
 * rename to user.php and adjust contents
 */
try {
    Configure::load('user');
} catch (\Exception $ex) {
    // do nothing
}
