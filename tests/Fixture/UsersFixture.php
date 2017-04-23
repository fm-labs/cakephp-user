<?php
namespace User\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture
{

    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'user_users'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'Root User',
            'group_id' => 1,
            'username' => 'root',
            'password' => 'toortoor',
            'email' => 'root@example.org',
            'email_verification_required' => null,
            'email_verification_code' => null,
            'email_verification_expiry_timestamp' => null,
            'email_verified' => true,
            'password_change_min_days' => null,
            'password_change_max_days' => null,
            'password_change_warning_days' => null,
            'password_change_timestamp' => null,
            'password_expiry_timestamp' => null,
            'password_force_change' => null,
            'password_reset_code' => null,
            'password_reset_expiry_timestamp' => null,
            'login_enabled' => 1,
            'login_last_login_ip' => null,
            'login_last_login_host' => null,
            'login_last_login_datetime' => null,
            'login_failure_count' => null,
            'login_failure_datetime' => null,
            'block_enabled' => null,
            'block_reason' => null,
            'block_datetime' => null,
            'created' => '2015-04-19 15:52:37',
            'modified' => '2015-04-19 15:52:37'
        ],
        [
            'id' => 1,
            'name' => 'Normal User',
            'group_id' => 2,
            'username' => 'test',
            'password' => 'testtest',
            'email' => 'test@example.org',
            'email_verification_required' => null,
            'email_verification_code' => null,
            'email_verification_expiry_timestamp' => null,
            'email_verified' => true,
            'password_change_min_days' => null,
            'password_change_max_days' => null,
            'password_change_warning_days' => null,
            'password_change_timestamp' => null,
            'password_expiry_timestamp' => null,
            'password_force_change' => null,
            'password_reset_code' => null,
            'password_reset_expiry_timestamp' => null,
            'login_enabled' => 1,
            'login_last_login_ip' => null,
            'login_last_login_host' => null,
            'login_last_login_datetime' => null,
            'login_failure_count' => null,
            'login_failure_datetime' => null,
            'block_enabled' => null,
            'block_reason' => null,
            'block_datetime' => null,
            'created' => '2015-04-19 15:52:37',
            'modified' => '2015-04-19 15:52:37'
        ],
    ];
}
