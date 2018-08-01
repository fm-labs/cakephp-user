<?php

namespace User\Test\TestCase\Auth;

use Cake\TestSuite\TestCase;
use User\Auth\RolesAuthorize;

/**
 * Class RolesAuthorizeTest
 *
 * @package User\Test\TestCase\Auth
 */
class RolesAuthorizeTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();


        $this->componentRegistry = $this->getMockBuilder('Cake\Controller\ComponentRegistry')->getMock();
    }

    /**
     * Test constructor.
     */
    public function testConstruct()
    {
        $rolesAuth = new RolesAuthorize($this->componentRegistry, []);
        $this->markTestIncomplete();
    }

    /**
     * Test authorize method.
     */
    public function testAuthorize()
    {
        $this->markTestIncomplete();
    }
}
