<?php
namespace User\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use User\View\Cell\UserSessionsCell;

/**
 * User\View\Cell\UserSessionsCell Test Case
 */
class UserSessionsCellTest extends TestCase
{

    /**
     * Request mock
     *
     * @var \Cake\Http\ServerRequest|\PHPUnit_Framework_MockObject_MockObject
     */
    public $request;

    /**
     * Response mock
     *
     * @var \Cake\Http\Response|\PHPUnit_Framework_MockObject_MockObject
     */
    public $response;

    /**
     * Test subject
     *
     * @var \User\View\Cell\UserSessionsCell
     */
    public $UserSessions;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Cake\Http\Request')->getMock();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->UserSessions = new UserSessionsCell($this->request, $this->response);
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
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
