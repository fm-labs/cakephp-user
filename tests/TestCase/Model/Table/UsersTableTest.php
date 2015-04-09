<?php
namespace User\Test\TestCase\Model\Table;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use User\Model\Table\UsersTable;

/**
 * User\Model\Table\UsersTable Test Case
 *
 * @property UsersTable $Users
 */
class UsersTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Users' => 'plugin.user.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Users') ? [] : [
            'className' => 'User\Model\Table\UsersTable',
            'userConfig' => ['emailAsUsername' => false]
        ];
        $this->Users = TableRegistry::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

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

    public function testValidationRegister()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testValidateNewPassword1()
    {
        $fakeContext = ['newEntity' => true, 'data' => ['username' => 'asdfasdf']];

        // test short password (min 8)
        $this->assertTrue($this->Users->validateNewPassword1('asdf', $fakeContext) !== true);

        // test illegal characters
        $this->assertTrue($this->Users->validateNewPassword1('asdfasdf$', $fakeContext) !== true);

        // test weak password - same as username
        $this->assertTrue($this->Users->validateNewPassword1('asdfasdf', $fakeContext) !== true);

        // test good password
        $this->assertTrue($this->Users->validateNewPassword1('testtest', $fakeContext) === true);

    }

    public function testValidateNewPassword2()
    {
        $fakeContext = ['newEntity' => true, 'data' => ['password1' => 'testtest']];

        // test no password
        $this->assertTrue($this->Users->validateNewPassword2('', $fakeContext) !== true);

        // test wrong password
        $this->assertTrue($this->Users->validateNewPassword2('wrongpass', $fakeContext) !== true);

        // test correct password
        $this->assertTrue($this->Users->validateNewPassword2('testtest', $fakeContext) === true);
    }

    public function testRegister()
    {
        // test with null data
        $user = $this->Users->register(null);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);

        // test with no data
        $user = $this->Users->register([]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);

        // test with incomplete data - missing password
        $user = $this->Users->register(['username' => 'test']);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);
        $this->assertEmpty($user->errors('username'));
        $this->assertNotEmpty($user->errors('password1'));
        $this->assertNotEmpty($user->errors('password2'));

        // test with valid username and password
        $user = $this->Users->register(['username' => 'test', 'password1' => 'rosebud1', 'password2' => 'rosebud1']);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);
        $this->assertTrue((new DefaultPasswordHasher())->check('rosebud1', $user->password));
    }

    public function testRegisterWithEmailAsUsername()
    {
        TableRegistry::remove('Users');
        $this->Users = TableRegistry::get('Users', [
            'className' => 'User\Model\Table\UsersTable',
            'userConfig' => ['emailAsUsername' => true]
        ]);

        // test with invalid username and password
        $user = $this->Users->register(['username' => 'test', 'password1' => 'rosebud1', 'password2' => 'rosebud1']);
        $this->assertNotEmpty($user->errors('username'));

        // test with valid username and password
        $user = $this->Users->register(['username' => 'test@example.org', 'password1' => 'rosebud1', 'password2' => 'rosebud1']);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);

        TableRegistry::remove('Users');
    }

    public function testChangePassword()
    {
        $user = $this->Users->register(['username' => 'test', 'password1' => 'rosebud1', 'password2' => 'rosebud1']);
        $this->assertNotEmpty($user->id);

        // test with new password same as current password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => 'rosebud1', 'password1' => 'rosebud1', 'password2' => 'rosebud1']
        ));
        $this->assertNotEmpty($user->errors('password0'));
        $this->assertFalse($user->dirty('password1'));
        $this->assertFalse($user->dirty('password2'));


        // test with empty password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '', 'password1' => 'basejump', 'password2' => 'basejump']
        ));
        $this->assertNotEmpty($user->errors('password0'));
        $this->assertNotEmpty($user->errors('password0')['_empty']);

        // test with bad new password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '2short', 'password1' => 'basejump', 'password2' => 'basejump']
        ));
        $this->assertNotEmpty($user->errors('password0'));
        $this->assertNotEmpty($user->errors('password0')['password']);


        // test with good data
        $this->assertTrue($this->Users->changePassword(
            $user,
            ['password0' => 'rosebud1', 'password1' => 'basejump', 'password2' => 'basejump']
        ));
        $this->assertFalse($user->dirty('password'));
        $this->assertFalse($user->dirty('password0'));
        $this->assertFalse($user->dirty('password1'));
        $this->assertFalse($user->dirty('password2'));
        #$this->assertEmpty($user->password); // hide (encrypted) password
        #$user = $this->Users->get($user->id);
        $this->assertTrue((new DefaultPasswordHasher())->check('basejump', $user->password));
    }

}
