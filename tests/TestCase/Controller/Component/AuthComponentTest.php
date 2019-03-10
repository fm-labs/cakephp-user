<?php
namespace User\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use User\Controller\Component\AuthComponent;

/**
 * User\Controller\Component\AuthComponent Test Case
 */
class AuthComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \User\Controller\Component\AuthComponent
     */
    public $AuthComponent;

    public $controller = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // Setup our component and fake test controller
        $request = new Request();
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->AuthComponent = new AuthComponent($registry);
        $event = new Event('Controller.startup', $this->controller);
        $this->AuthComponent->startup($event);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AuthComponent);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->markTestIncomplete('Not implemented yet.');
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
     * Test login method
     *
     * @return void
     */
    public function testLogin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test logout method
     *
     * @return void
     */
    public function testLogout()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test table method
     *
     * @return void
     */
    public function testTable()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test userModel method
     *
     * @return void
     */
    public function testUserModel()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
