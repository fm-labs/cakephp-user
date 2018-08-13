<?php
namespace User\Test\TestCase\Model\Table;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
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

    const TEST_PASS1 = "r0s3BuD$%";
    const TEST_PASS2 = "Ba23Jump_?";

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

        Configure::write([
            'User.Signup.disabled' => false,
            'User.Signup.verifyEmail' => false,
            'User.Signup.groupAuth' => false
        ]);

        UsersTable::$emailAsUsername = false;
        $config = TableRegistry::exists('Users') ? [] : [
            'className' => 'User\Model\Table\UsersTable'
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
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
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
        //$this->assertTrue($this->Users->validateNewPassword1('asdfasdf$', $fakeContext) !== true);

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

    public function testValidatePasswordComplexity()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];

        // test minLength
        $this->assertTrue($this->Users->validatePasswordComplexity('1234', ['minLength' => 8], $fakeContext) !== true);
        $this->assertTrue($this->Users->validatePasswordComplexity('12345678', ['minLength' => 8], $fakeContext));

        // test numbers
        $this->assertTrue($this->Users->validatePasswordComplexity('asdfasdf', ['numbers' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->validatePasswordComplexity('asdfasdf1', ['numbers' => 1], $fakeContext));

        // test uppercase
        $this->assertTrue($this->Users->validatePasswordComplexity('asdfasdf', ['uppercase' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->validatePasswordComplexity('Asdfasdf', ['uppercase' => 1], $fakeContext));

        // test lowercase
        $this->assertTrue($this->Users->validatePasswordComplexity('ASDFASDF', ['lowercase' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->validatePasswordComplexity('aSDFASDF', ['lowercase' => 1], $fakeContext));

        // test special
        $this->assertTrue($this->Users->validatePasswordComplexity('asdfasdf', ['special' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->validatePasswordComplexity('asdfasdf!', ['special' => 1], $fakeContext));

    }

    public function testRegister()
    {
        // test with no data
        $user = $this->Users->register([]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);

        // test with incomplete data - missing password
        $user = $this->Users->register(['username' => 'test1']);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);
        $this->assertEmpty($user->errors('username'));
        $this->assertNotEmpty($user->errors('password1'));
        $this->assertNotEmpty($user->errors('password2'));

        // test with valid username and password
        $user = $this->Users->register(['username' => 'test2', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);
        $this->assertEmpty($user->email);
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS1, $user->password));
        $this->assertTrue($user->login_enabled);

        // test with valid username and password + extra data
        $data = ['username' => 'test3', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1];
        $user = $this->Users->register($data);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS1, $user->password));
    }

    public function testRegisterWithEmailAsUsername()
    {
        TableRegistry::remove('Users');
        UsersTable::$emailAsUsername = true;
        $this->Users = TableRegistry::get('Users', [
            'className' => 'User\Model\Table\UsersTable'
        ]);

        // test with username instead of email
        $user = $this->Users->register(['username' => 'test1', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertNotEmpty($user->errors('username'));
        $this->assertNotEmpty($user->errors('email'));

        // test with valid username and password
        $user = $this->Users->register(['email' => 'test1@example.org', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);
        $this->assertEquals('test1@example.org', $user->username);
        $this->assertEquals('test1@example.org', $user->email);

        // test with email, password AND username (ignore additional username field)
        $user = $this->Users->register(['email' => 'test2@example.org', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1, 'username' => 'admin']);
        $this->assertEmpty($user->errors());
        $this->assertNotEmpty($user->id);
        $this->assertEquals('test2@example.org', $user->username);
        $this->assertEquals('test2@example.org', $user->email);

        TableRegistry::remove('Users');
    }

    public function testRegisterWithEmailVerification()
    {
        Configure::write('User.Signup.verifyEmail', true);

        $user = $this->Users->register(['username' => 'test1', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);

        $this->assertFalse($user->email_verified);
        $this->assertNotEmpty($user->email_verification_code);
        $this->assertTrue($user->login_enabled);
    }

    public function testRegisterWithGroupAuth()
    {
        Configure::write('User.Signup.groupAuth', true);

        $this->markTestIncomplete();
    }

    public function testChangePassword()
    {
        $user = $this->Users->register(['username' => 'test1', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertNotEmpty($user->id);

        // test with new password same as current password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => self::TEST_PASS1, 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]
        ));
        $this->assertNotEmpty($user->errors('password1'));
        $this->assertFalse($user->dirty('password1'));
        $this->assertFalse($user->dirty('password2'));


        // test with empty password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '', 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertNotEmpty($user->errors('password0'));
        $this->assertNotEmpty($user->errors('password0')['_empty']);

        // test with bad new password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '2short', 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertNotEmpty($user->errors('password0'));
        $this->assertNotEmpty($user->errors('password0')['password']);


        // test with good data
        $this->assertTrue($this->Users->changePassword(
            $user,
            ['password0' => self::TEST_PASS1, 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertFalse($user->dirty('password'));
        $this->assertFalse($user->dirty('password0'));
        $this->assertFalse($user->dirty('password1'));
        $this->assertFalse($user->dirty('password2'));
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS2, $user->password));
    }

    public function testForgottPassword()
    {
        $user = $this->Users->find()->first();
        if (!$user) {
            $this->fail('No test user found');
        }

        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testActivate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
