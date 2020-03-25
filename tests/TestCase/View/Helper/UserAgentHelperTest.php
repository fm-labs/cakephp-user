<?php
declare(strict_types=1);

namespace User\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use User\View\Helper\UserAgentHelper;

/**
 * User\View\Helper\UserAgentHelper Test Case
 */
class UserAgentHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \User\View\Helper\UserAgentHelper
     */
    public $UserAgent;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->UserAgent = new UserAgentHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserAgent);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
