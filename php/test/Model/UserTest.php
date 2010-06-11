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
        $this->assertTrue(Model_User::isLoggedIn());
        $this->_was = Model_User::me();
        $this->assertTrue($this->_was instanceof Model_User);
    }

    public function tearDown() 
    {
        Model_User::logIn($this->_was->email);
        $this->assertTrue(Model_User::isLoggedIn());

        parent::tearDown();
    }

    public function testLoginWorks() 
    {
        Model_User::logIn(self::EMAIL);
        $this->assertTrue(Model_User::isLoggedIn());
        $this->assertEquals(self::EMAIL, Model_User::me()->email);
        Model_User::logOut();
        $this->assertFalse(Model_User::isLoggedIn());
    }

}