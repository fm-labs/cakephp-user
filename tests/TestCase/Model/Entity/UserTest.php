<?php
declare(strict_types=1);

namespace User\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use User\Model\Entity\User;

/**
 * User\Model\Entity\User Test Case
 */
class UserTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \User\Model\Entity\User
     */
    public $User;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->User = new User();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->User);

        parent::tearDown();
    }

    /**
     * Test getPasswordHasher method
     *
     * @return void
     */
    public function testGetPasswordHasher()
    {
        $pwHasher = $this->User->getPasswordHasher();
        $this->assertInstanceOf('Cake\Auth\DefaultPasswordHasher', $pwHasher);
    }

    /**
     * Test virtual property 'is_root'.
     *
     * @return void
     */
    public function testGetVirtualPropertyIsRoot()
    {
        $user = new User(['username' => 'root']);
        $this->assertTrue($user->is_root);

        $user = new User(['username' => 'admin', 'superuser' => true]);
        $this->assertFalse($user->is_root);
    }

    /**
     * Test virtual property 'is_superuser'.
     *
     * @return void
     */
    public function testGetVirtualPropertyIsSuperuser()
    {
        $user = new User(['username' => 'admin', 'superuser' => true]);
        $this->assertTrue($user->is_superuser);

        $user = new User(['username' => 'admin', 'superuser' => false]);
        $this->assertFalse($user->is_superuser);

        // root is always superuser, even if superuser flag is not set
        $user = new User(['username' => 'root', 'superuser' => false]);
        $this->assertTrue($user->is_superuser);
    }

    /**
     * Test virtual property 'display_name'.
     *
     * @return void
     */
    public function testGetVirtualPropertyDisplayName()
    {
        $user = new User(['username' => 'test']);
        $this->assertEquals('test', $user->display_name);

        $user = new User(['username' => 'test', 'name' => 'Test User']);
        $this->assertEquals('Test User', $user->display_name);
    }
}
