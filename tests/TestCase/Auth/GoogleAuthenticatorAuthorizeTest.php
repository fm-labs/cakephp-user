<?php
namespace User\Test\TestCase\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest as Request;
use Cake\TestSuite\TestCase;
use User\Auth\GoogleAuthenticatorAuthorize;

class GoogleAuthenticatorAuthorizeTest extends TestCase
{
    /**
     * @var Controller
     */
    public $controller;

    /**
     * @var ComponentRegistry
     */
    public $components;

    /**
     * @var GoogleAuthenticatorAuthorize
     */
    public $auth;

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->controller = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->components = $this->getMockBuilder(ComponentRegistry::class)
            ->getMock();
        $this->components->expects($this->any())
            ->method('getController')
            ->will($this->returnValue($this->controller));
        $this->auth = new GoogleAuthenticatorAuthorize($this->components);
    }

    /**
     * Test authorize method
     *
     * @return void
     */
    public function testAuthorizeFailure()
    {
        $user = ['gauth_enabled' => true];
        $request = new Request([
            'url' => '/user',
        ]);
        $this->assertFalse($this->auth->authorize($user, $request));
    }

    /**
     * Test authorize method
     *
     * @return void
     */
    public function testAuthorizeDisabled()
    {
        $user = ['gauth_enabled' => false];
        $request = new Request('/posts/index');

        $this->assertTrue($this->auth->authorize($user, $request));
    }

    /**
     * Test authorize method
     *
     * @return void
     */
    public function testAuthorizeController()
    {
        $user = ['gauth_enabled' => true];
        $request = new Request([
            'url' => '/user/google-auth',
            'params' => [
                'plugin' => 'User',
                'controller' => 'GoogleAuth',
                'action' => 'index',
                '_ext' => null,
                'pass' => []
            ]
        ]);
        $this->assertTrue($this->auth->authorize($user, $request));
    }

    /**
     * Test authorize method
     *
     * @return void
     */
    public function testAuthorizeLogout()
    {
        $user = ['gauth_enabled' => true];
        $request = new Request([
            'url' => '/user/logout',
            'params' => [
                'plugin' => 'User',
                'controller' => 'User',
                'action' => 'logout',
                '_ext' => null,
                'pass' => []
            ]
        ]);
        $this->assertTrue($this->auth->authorize($user, $request));
    }

    /**
     * Test authorize method
     *
     * @return void
     */
    public function testAuthorizeSuccess()
    {
        $user = ['gauth_enabled' => true];
        $request = new Request('/posts/index');
        $request->session()->write('Auth.GoogleAuth.verified', true);

        $this->assertTrue($this->auth->authorize($user, $request));
    }

}
