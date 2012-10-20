<?php

//require_once 'AuthUser.php';
require_once 'User.php';

/**
 * Test class for User.
 * Generated by PHPUnit on 2010-10-17 at 02:38:45.
 */
class UserTest extends PHPUnit_Framework_TestCase {

    /**
     * @var User
     */
    protected $object;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new User();
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object = null;
    }

    /**
     * 
     */
    public function testObjectCreation() {
        $this->object->id = 1;
        $this->object->name = 'test user';

        $actual = new User(array(1, 'test user'));
        $this->assertNotEquals($this->object, $actual);

        $actual = new User(array('id' => 1, 'name' => 'test user'));
        $this->assertEquals($this->object, $actual);
    }


    /**
     * @todo Implement testGetPermissions().
     */
    public function testGetPermissions() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testRoles().
     */
    public function testRoles() {
        // Make sure the method exists
        $this->assertTrue(method_exists($this->object, 'roles'));
        
        $actual = $this->object->roles();
        $this->assertInternalType('array', $actual);
        $this->assertNotNull($actual);
    }


    /**
     * @todo Implement testFindBy().
     */
    public function testFindBy() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testBeforeInsert().
     */
    public function testBeforeInsert() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        
        // Make sure the method exists
        $this->assertTrue(method_exists($this->object, 'beforeInsert'));
        
        $actual = $this->object->beforeInsert();
        $this->assertTrue($actual);
    }


    /**
     * @todo Implement testBeforeUpdated().
     */
    public function testBeforeUpdated() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
                // Something doesn't work right with test below. Check later.
        );
        //$actual = $this->object->beforeUpdated();
        //$this->assertTrue($actual);
    }


    /**
     * @todo Implement testFind().
     */
    public function testFind() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testFindAll().
     */
    public function testFindAll() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testFindById().
     */
    public function testFindById() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

?>
