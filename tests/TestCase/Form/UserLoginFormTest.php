<?php
declare(strict_types=1);

namespace User\Test\TestCase\Form;

use Cake\TestSuite\TestCase;
use User\Form\UserLoginForm;

/**
 * User\Form\UserLoginForm Test Case
 */
class UserLoginFormTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \User\Form\UserLoginForm
     */
    protected $UserLogin;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->UserLogin = new UserLoginForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UserLogin);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \User\Form\UserLoginForm::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
