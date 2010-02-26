<?php
/**
 * @version $Id: WobotTest.php 718 2010-02-21 15:55:39Z yegor256@yahoo.com $
 */

require_once 'AbstractTest.php';

class Model_UserTest extends AbstractTest 
{

    const EMAIL = 'testUser444@example.com';

    public function setUp() 
    {
        parent::setUp();
        if (Model_User::isLoggedIn()) {
            $this->_was = Model_User::getCurrentUser();
            $this->assertTrue($this->_was instanceof Model_User);
        }
    }

    public function testLoginWorks() 
    {
        Model_User::logIn(self::EMAIL);
        $this->assertTrue(Model_User::isLoggedIn());
        $this->assertEquals(self::EMAIL, Model_User::getCurrentUser()->email);
        Model_User::logOut();
        $this->assertFalse(Model_User::isLoggedIn());
    }

    public function tearDown() 
    {
        parent::tearDown();
        if (isset($this->_was)) {
            Model_User::logIn($this->_was->email);
        }
    }

}