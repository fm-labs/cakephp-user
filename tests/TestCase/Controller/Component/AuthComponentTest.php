<?php
namespace User\Test\TestCase\Controller\Component;

use Cake\Auth\FormAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Http\ServerRequest as Request;
use Cake\Http\Response;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
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
    public $Auth;

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
        $this->Auth = new AuthComponent($registry);
        $event = new Event('Controller.startup', $this->controller);
        $this->Auth->startup($event);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Auth);

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

        $AuthLoginFormAuthenticate = $this->getMockBuilder(FormAuthenticate::class)
            ->setMethods(['authenticate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->Auth->authenticate = [
            'AuthLoginForm' => [
                'userModel' => 'AuthUsers'
            ]
        ];
        $this->Auth->setAuthenticateObject(0, $AuthLoginFormAuthenticate);
        $this->controller->request = $this->controller->request->withParsedBody([
            'AuthUsers' => [
                'username' => 'mark',
                'password' => Security::hash('cake', null, true)
            ]
        ]);
        $user = [
            'id' => 1,
            'username' => 'mark'
        ];
        $AuthLoginFormAuthenticate->expects($this->once())
            ->method('authenticate')
            ->with($this->Controller->request)
            ->will($this->returnValue($user));
        $result = $this->Auth->identify();
        $this->assertEquals($user, $result);
        $this->assertSame($AuthLoginFormAuthenticate, $this->Auth->authenticationProvider());
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
}
