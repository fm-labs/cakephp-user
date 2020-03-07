<?php
namespace User\Test\TestCase\Model\Table;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\I18n\I18n;
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
        'Users' => 'plugin.User.Users'
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
            'User.Mailer.enabled' => false,
            'User.Logging.enabled' => false,
            'User.Signup.disabled' => false,
            'User.Signup.verifyEmail' => false,
            'User.Signup.groupAuth' => false,
            'User.Signup.formClass' => '\User\Form\UserRegisterForm',
            'User.Recaptcha.enabled' => false,

            //'User.Blacklist.enabled' => true,
            //'User.Blacklist.domainList' => ['example.net']
        ]);

        UsersTable::$emailAsUsername = false;
        UsersTable::$passwordMinLowercase = -1;
        UsersTable::$passwordMinUppercase = -1;
        UsersTable::$passwordMinSpecialChars = -1;
        UsersTable::$passwordMinNumbers = -1;

        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : [
            'className' => 'User\Model\Table\UsersTable'
        ];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
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

    /**
     * @return void
     */
    public function testValidationRegister()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * @return void
     */
    public function testCheckNewPassword1()
    {
        $fakeContext = ['newEntity' => true, 'data' => ['username' => 'asdfasdf']];

        // test short password (min 8)
        //$this->assertTrue($this->Users->checkNewPassword1('asdf', $fakeContext) !== true);

        // test illegal characters
        //$this->assertTrue($this->Users->checkNewPassword1('asdfasdf$', $fakeContext) !== true);

        // test weak password - same as username
        $this->assertTrue($this->Users->checkNewPassword1('asdfasdf', $fakeContext) !== true);

        // test good password
        $this->assertTrue($this->Users->checkNewPassword1('testtest', $fakeContext) === true);
    }

    /**
     * @return void
     */
    public function testCheckNewPassword2()
    {
        $fakeContext = ['newEntity' => true, 'data' => ['password1' => 'testtest']];

        // test no password
        $this->assertTrue($this->Users->checkNewPassword2('', $fakeContext) !== true);

        // test wrong password
        $this->assertTrue($this->Users->checkNewPassword2('wrongpass', $fakeContext) !== true);

        // test correct password
        $this->assertTrue($this->Users->checkNewPassword2('testtest', $fakeContext) === true);
    }

    /**
     * @return void
     */
    public function testCheckEmailBlacklist()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        $options = [
            'enabled' => true,
            'domainList' => ['example.net']
        ];

        // test minLength
        $this->assertTrue($this->Users->checkEmailBlacklist('asdf@example.org', $options, $fakeContext));
        $this->assertInternalType('string', $this->Users->checkEmailBlacklist('asdf@example.net', $options, $fakeContext));
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexity()
    {
        //$fakeContext = ['newEntity' => true, 'data' => []];
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexityMinLength()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        // test minLength
        $this->assertTrue($this->Users->checkPasswordComplexity('1234', ['minLength' => 8], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('12345678', ['minLength' => 8], $fakeContext));
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexityMinNumbers()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        // test numbers
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf', ['numbers' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf1', ['numbers' => 1], $fakeContext));
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf1', ['numbers' => 2], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('1asdfasdf1', ['numbers' => 2], $fakeContext));
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexityMinUppercase()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        // test uppercase
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf', ['uppercase' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('Asdfasdf', ['uppercase' => 1], $fakeContext));
        $this->assertTrue($this->Users->checkPasswordComplexity('Asdfasdf', ['uppercase' => 2], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('AsdfasdA', ['uppercase' => 2], $fakeContext));
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexityMinLowercase()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        // test lowercase
        $this->assertTrue($this->Users->checkPasswordComplexity('ASDFASDF', ['lowercase' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('aSDFASDF', ['lowercase' => 1], $fakeContext));
        $this->assertTrue($this->Users->checkPasswordComplexity('aSDFASDF', ['lowercase' => 2], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('aSDFASDb', ['lowercase' => 2], $fakeContext));
        $this->assertTrue($this->Users->checkPasswordComplexity('aSDFASbc', ['lowercase' => 2], $fakeContext));
    }

    /**
     * @return void
     */
    public function testCheckPasswordComplexityMinSpecial()
    {
        $fakeContext = ['newEntity' => true, 'data' => []];
        // test special
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf', ['special' => 1], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf!', ['special' => 1], $fakeContext));
        $this->assertTrue($this->Users->checkPasswordComplexity('asdfasdf!', ['special' => 2], $fakeContext) !== true);
        $this->assertTrue($this->Users->checkPasswordComplexity('!asdfasdf!', ['special' => 2], $fakeContext));
    }

    /**
     * @return void
     */
    public function testRegister()
    {
        // test with no data
        $user = $this->Users->register([]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);

        // test with incomplete data - missing password
        $user = $this->Users->register(['username' => 'test1', 'email' => 'test1@example.org']);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);
        $this->assertEmpty($user->getError('username'));
        $this->assertEmpty($user->getError('email'));
        $this->assertNotEmpty($user->getError('password1'));
        $this->assertNotEmpty($user->getError('password2'));
        $this->Users->delete($user);

        // test with no login
        $user = $this->Users->register(['username' => 'test1', 'email' => 'test1@example.org', '_nologin' => true]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->getError('username'));
        $this->assertEmpty($user->getError('email'));
        $this->assertEmpty($user->getError('password1'));
        $this->assertEmpty($user->getError('password2'));
        $this->assertNotEmpty($user->id);
        $this->Users->delete($user);

        // test with valid username and password
        $user = $this->Users->register(['username' => 'test2', 'email' => 'test2@example.org', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEmpty($user->id);

        $user = $this->Users->get($user->id);
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS1, $user->password));
        $this->assertTrue($user->login_enabled);
        $this->Users->delete($user);

        // test with valid username and password + extra data
        $data = [
            'username' => 'test3',
            'email' => 'test3@example.org',
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1,
            'locale' => 'de',
            'first_name' => 'First',
            'last_name' => 'Last'
        ];
        $user = $this->Users->register($data);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEmpty($user->id);

        $user = $this->Users->get($user->id);
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS1, $user->password));
        $this->assertArraySubset(['locale' => 'de', 'first_name' => 'First', 'last_name' => 'Last'], $user->toArray());
        $this->Users->delete($user);

        // test with valid username and password + default data (+i18n)
        $_tmpLocale = I18n::getLocale();
        $_tmpTz = date_default_timezone_get();
        $_tmpCur = 'EUR';
        I18n::setLocale('en');
        $data = [
            'username' => 'test3',
            'email' => 'test3@example.org',
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1
        ];
        $user = $this->Users->register($data);
        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEmpty($user->id);

        $user = $this->Users->get($user->id);
        $this->assertArraySubset(['locale' => 'en', 'timezone' => $_tmpTz, 'currency' => $_tmpCur], $user->toArray());
        $this->Users->delete($user);
        I18n::setLocale($_tmpLocale); // restore locale
    }

    /**
     * @return void
     */
    public function testRegisterWithEmailAsUsername()
    {
        TableRegistry::getTableLocator()->remove('Users');
        UsersTable::$emailAsUsername = true;

        $this->Users = TableRegistry::getTableLocator()->get('Users', [
            'className' => 'User\Model\Table\UsersTable'
        ]);

        // test with username instead of email
        $user = $this->Users->register(['username' => 'test1', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertNotEmpty($user->getError('username'));
        $this->assertNotEmpty($user->getError('email'));

        // test with valid username and password
        $user = $this->Users->register(['email' => 'test1@example.org', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEmpty($user->id);
        $this->assertEquals('test1@example.org', $user->username);
        $this->assertEquals('test1@example.org', $user->email);

        // test with email, password AND username (ignore additional username field)
        $user = $this->Users->register([
            'email' => 'test2@example.org',
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1,
            'username' => 'admin', // <-- this should get overridden
            'locale' => 'en'
        ]);
        $this->assertEmpty($user->getErrors());
        $this->assertNotEmpty($user->id);
        $this->assertEquals('test2@example.org', $user->username);
        $this->assertEquals('test2@example.org', $user->email);
        $this->assertEquals('en', $user->locale);

        TableRegistry::getTableLocator()->remove('Users');
        UsersTable::$emailAsUsername = false;
    }

    /**
     * @return void
     */
    public function testRegisterWithBlacklistedEmail()
    {
        Configure::write('User.Blacklist', ['enabled' => true, 'domainList' => ['example.net']]);

        $user = $this->Users->register([
            'username' => 'test1',
            'email' => 'test1@example.net', // @example.net is blacklisted in test config
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1
        ]);

        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);
        $this->assertArrayHasKey('email_blacklist', $user->getError('email'));
    }

    /**
     * @return void
     */
    public function testRegisterWithBlacklistedEmailAsUsername()
    {
        TableRegistry::getTableLocator()->remove('Users');
        UsersTable::$emailAsUsername = true;

        Configure::write('User.Blacklist', ['enabled' => true, 'domainList' => ['example.net']]);

        $user = $this->Users->register([
            'email' => 'test1@example.net', // @example.net is blacklisted in test config
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1
        ]);

        $this->assertInstanceOf('User\\Model\\Entity\\User', $user);
        $this->assertEmpty($user->id);
        $this->assertArrayHasKey('email_blacklist', $user->getError('email'));

        TableRegistry::getTableLocator()->remove('Users');
        UsersTable::$emailAsUsername = true;
    }

    /**
     * @return void
     */
    public function testRegisterWithEmailVerification()
    {
        Configure::write('User.Signup.verifyEmail', true);

        $user = $this->Users->register([
            'username' => 'test1',
            'email' => 'test1@example.org',
            'password1' => self::TEST_PASS1,
            'password2' => self::TEST_PASS1
        ]);

        $this->assertFalse($user->email_verified);
        $this->assertNotEmpty($user->email_verification_code);
        $this->assertTrue($user->login_enabled);
        $this->assertNotEmpty($user->id);
    }

    /**
     * @return void
     */
    public function testRegisterWithGroupAuth()
    {
        Configure::write('User.Signup.groupAuth', true);

        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testChangePassword()
    {
        $user = $this->Users->register(['username' => 'test1', 'email' => 'test1@example.org', 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]);
        $this->assertNotEmpty($user->id);

        // test with new password same as current password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => self::TEST_PASS1, 'password1' => self::TEST_PASS1, 'password2' => self::TEST_PASS1]
        ));
        $this->assertNotEmpty($user->getError('password1'));
        $this->assertFalse($user->isDirty('password1'));
        $this->assertFalse($user->isDirty('password2'));

        // test with empty password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '', 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertNotEmpty($user->getError('password0'));
        $this->assertNotEmpty($user->getError('password0')['_empty']);

        // test with bad new password
        $this->assertFalse($this->Users->changePassword(
            $user,
            ['password0' => '2short', 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertNotEmpty($user->getError('password0'));
        $this->assertNotEmpty($user->getError('password0')['password']);

        // test with good data
        $this->assertTrue($this->Users->changePassword(
            $user,
            ['password0' => self::TEST_PASS1, 'password1' => self::TEST_PASS2, 'password2' => self::TEST_PASS2]
        ));
        $this->assertFalse($user->isDirty('password'));
        $this->assertFalse($user->isDirty('password0'));
        $this->assertFalse($user->isDirty('password1'));
        $this->assertFalse($user->isDirty('password2'));
        $this->assertTrue((new DefaultPasswordHasher())->check(self::TEST_PASS2, $user->password));
    }

    /**
     * @return void
     */
    public function testForgotPassword()
    {
        $user = $this->Users->find()->first();
        if (!$user) {
            $this->fail('No test user found');
        }

        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * @return void
     */
    public function testActivate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
