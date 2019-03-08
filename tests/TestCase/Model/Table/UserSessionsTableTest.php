<?php
namespace User\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use User\Model\Table\UserSessionsTable;

/**
 * User\Model\Table\UserSessionsTable Test Case
 */
class UserSessionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \User\Model\Table\UserSessionsTable
     */
    public $UserSessions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.user.user_sessions',
        'plugin.user.users',
        'plugin.user.user_groups'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('UserSessions') ? [] : ['className' => 'User\Model\Table\UserSessionsTable'];
        $this->UserSessions = TableRegistry::get('UserSessions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserSessions);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
