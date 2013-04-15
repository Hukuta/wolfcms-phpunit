<?php
class Object extends Record {
    const TABLE_NAME = 'object_table';
    
    public $id;
    public $name;
    
    public function someValueAccessor($value = null) {
        if ($value !== null) {
            $this->name = $value;
        }
        else return $this->name;
    }
}

class ObjectNoTableName extends Record { /* .. */ }

/**
 * Test class for Record.
 * Generated by PHPUnit on 2010-10-17 at 00:02:22.
 */
class RecordTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Record
     */
    protected $object;
    protected $conn;


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Record();

        // Attempt to create DB connection
        try {
            $conn = new PDO(DB_DSN, DB_USER, DB_PASS);
        } catch (PDOException $error) {
            die('DB Connection failed: '.$error->getMessage());
        }
        
        $this->driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($this->driver === 'mysql') {
            $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }

        Record::connection($conn);
        Record::getConnection()->exec("set names 'utf8'");

        $this->conn = $conn;
        $this->object->connection($conn);
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->conn->exec("DROP TABLE object_table");

        $this->object = null;
        $this->conn = null;
        Record::$__QUERIES__ = array();
        Record::$__CONN__ = false;
    }


    /**
     * 
     */
    public function testObjectCreation() {
        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'an object';

        $actual = new Object(array(1, 'an object'));
        $this->assertNotEquals($expected, $actual);

        $actual = new Object(array('id' => 1, 'name' => 'an object'));
        $this->assertEquals($expected, $actual);

        $actual = new Object(array());
        $this->assertNotEquals($expected, $actual);

        $actual = new Object(false);
        $this->assertNotEquals($expected, $actual);
    }


    /**
     *
     */
    public function testConnection() {
        $this->object = null;

        $this->object = new Record();

        // Attempt to create DB connection
        try {
            $conn = new PDO(DB_DSN, DB_USER, DB_PASS);
        }
        catch (PDOException $error) {
            die('DB Connection failed: '.$error->getMessage());
        }

        $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'mysql') {
            $conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }

        Record::connection($conn);

        $expected = $conn;
        $actual = Record::getConnection();

        $this->assertEquals($expected, $actual);
        $this->assertInstanceOf('PDO', $actual);
    }


    /**
     * @todo Implement testGetConnection().
     */
    public function testGetConnection() {
        $expected = $this->conn;
        $actual = Record::getConnection();

        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testLogQuery().
     */
    public function testLogQuery() {
        $q1 = 'SELECT * FROM object_table';
        $q2 = 'SELECT name FROM object_table';
        $q3 = 'SELECT id FROM object_table';

        $this->assertTrue(count(Record::$__QUERIES__) == 0, 'Record::$__QUERIES__ is not empty!');
        
        $this->object->logQuery($q1);
        $this->assertTrue(count(Record::$__QUERIES__) == 1);
        $this->assertEquals($q1, Record::$__QUERIES__[0]);

        $this->object->logQuery($q2);
        $this->assertTrue(count(Record::$__QUERIES__) == 2);
        $this->assertEquals($q2, Record::$__QUERIES__[1]);

        $this->object->logQuery($q3);
        $this->assertTrue(count(Record::$__QUERIES__) == 3);
        $this->assertEquals($q3, Record::$__QUERIES__[2]);
    }


    /**
     * @todo Implement testGetQueryLog().
     */
    public function testGetQueryLog() {
        $q1 = 'SELECT * FROM object_table';
        $q2 = 'SELECT name FROM object_table';
        $q3 = 'SELECT id FROM object_table';

        $expected = array($q1, $q2, $q3);

        $this->assertTrue($this->object->getQueryCount() == 0);

        $this->object->logQuery($q1);
        $this->object->logQuery($q2);
        $this->object->logQuery($q3);

        $actual = $this->object->getQueryLog();

        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testGetQueryCount().
     */
    public function testGetQueryCount() {
        $q1 = 'SELECT * FROM object_table';
        $q2 = 'SELECT name FROM object_table';
        $q3 = 'SELECT id FROM object_table';

        $this->assertTrue($this->object->getQueryCount() == 0);

        $this->object->logQuery($q1);
        $this->assertTrue($this->object->getQueryCount() == 1);
        
        $this->object->logQuery($q2);
        $this->assertTrue($this->object->getQueryCount() == 2);

        $this->object->logQuery($q3);
        $this->assertTrue($this->object->getQueryCount() == 3);
    }


    /**
     * @todo Split in multiple tests?
     */
    public function testQuery() {
        // Test without table
        $expected = false;
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertEquals($expected, $actual);

        // Create table
        if ($this->driver === 'mysql') {
            $this->conn->exec("CREATE TABLE object_table (
                id int(11) unsigned NOT NULL auto_increment,
                name text,
                PRIMARY KEY  (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        }
        
        if ($this->driver === 'sqlite') {
            $this->conn->exec("CREATE TABLE object_table (
                id INTEGER NOT NULL PRIMARY KEY,
                name varchar(100) default NULL
            )");
        }
        
        if ($this->driver === 'pgsql') {
            $this->markTestIncomplete('This test is not yet complete!');
        }
        
        // Test without records
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertInstanceOf('PDOStatement', $actual);
        $this->assertNotNull($actual);

        // Test with one record
        $this->conn->exec("INSERT INTO object_table (id, name) VALUES (1, 'A Test Record')");
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertInstanceOf('PDOStatement', $actual);
        $this->assertNotNull($actual);

        // Test with one record
        $actual = $this->object->query('SELECT * FROM object_table WHERE id=?');
        if ($this->driver !== 'sqlite') {
            $this->assertFalse($actual);
        }
        else {
            $this->assertInstanceOf('PDOStatement', $actual);
        }
        $actual = $this->object->query('SELECT * FROM object_table WHERE id=?', array(1));
        $this->assertInternalType('array', $actual);
        $this->assertNotNull($actual);
        $this->assertTrue(count($actual) == 1);

        // Test if record was fetched
        $expected = new stdClass();
        $expected->id = 1;
        $expected->name = 'A Test Record';
        $actual = $this->object->query('SELECT * FROM object_table');
        $actual = $actual->fetchObject();
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Make sure function does not return null?
     */
    public function testTableNameFromClassName() {
        $this->markTestIncomplete('This test is not yet complete!');

        $actual = Record::tableNameFromClassName('Record');
        $this->assertNull($actual);
        //$this->assertNotNull($actual);

        $expected = Object::TABLE_NAME;
        $actual = Record::tableNameFromClassName('Object');
        $this->assertEquals($expected, $actual);

        $expected = 'object_no_table_name';
        $actual = Record::tableNameFromClassName('ObjectNoTableName');
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testEscape().
     */
    public function testEscape() {
        $input = "Ain't this lovely?";
        if ($this->driver === 'sqlite') {
            $expected = "'Ain''t this lovely?'";
        }
        if ($this->driver === 'pgsql') {
            $expected = "'Ain''t this lovely?'";
        }
        if ($this->driver === 'mysql') {
            $expected = "'Ain\'t this lovely?'";
        }
        
        $actual = $this->object->escape($input);
        $this->assertEquals($expected, $actual);

        $input = 'Aint "this" lovely?';
        
        if ($this->driver === 'sqlite') {
            $expected = '\'Aint "this" lovely?\'';
        }
        if ($this->driver === 'pgsql') {
            $expected = '\'Aint "this" lovely?\'';
        }
        if ($this->driver === 'mysql') {
            $expected = '\'Aint \"this\" lovely?\'';
        }
        
        $actual = $this->object->escape($input);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testLastInsertId().
     */
    public function testLastInsertId() {
        $expected = 0;
        $actual = $this->object->lastInsertId();
        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);

        // Create table
        if ($this->driver === 'mysql') {
        $this->conn->exec("CREATE TABLE object_table (
                id int(11) unsigned NOT NULL auto_increment,
                name text,
                PRIMARY KEY  (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        }
        
        if ($this->driver === 'sqlite') {
            $this->conn->exec("CREATE TABLE object_table (
                id INTEGER NOT NULL PRIMARY KEY,
                name varchar(100) default NULL
            )");
        }

        if ($this->driver === 'pgsql') {
            $this->markTestIncomplete('This test is not yet complete!');
        }

        
        $expected = 0;
        $actual = $this->object->lastInsertId();
        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);

        // Test with one record
        $this->conn->exec("INSERT INTO object_table (id, name) VALUES (1, 'A Test Record')");
        $expected = 1;
        $actual = $this->object->lastInsertId();
        $this->assertNotNull($actual);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testSetFromData().
     */
    public function testSetFromData() {
        $expected = new Object();
        $actual = new Object();
        $actual->setFromData();
        $this->assertEquals($expected, $actual);

        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Test name';
        $actual = new Object();
        $actual->setFromData(array('id' => 1, 'name' => 'Test name'));
        $this->assertEquals($expected, $actual);

        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Test name';
        $actual = new Object();
        $actual->setFromData(array('id' => 1, 'name' => 'Test name', 'data' => 'data'));
        $this->assertNotEquals($expected, $actual);
    }
    
    /**
     * 
     */
    public function test__Get() {
        $expected = new Object();
        $actual = new Object();
        $this->assertEquals($expected, $actual);

        $expected->id = 1;
        $expected->name = 'Test name';
        $actual->id = 1;
        $actual->name = 'Test name';
        $this->assertEquals($expected, $actual);
        
        $this->assertEquals('Test name', $actual->name);
    }


    /**
     * @todo Implement testSave().
     */
    public function testSave() {
        // Test without table
        $expected = false;
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertEquals($expected, $actual);

        // Create table
        if ($this->driver === 'mysql') {
        $this->conn->exec("CREATE TABLE object_table (
                id int(11) unsigned NOT NULL auto_increment,
                name text,
                PRIMARY KEY  (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        }
        
        if ($this->driver === 'sqlite') {
            $this->conn->exec("CREATE TABLE object_table (
                id INTEGER NOT NULL PRIMARY KEY,
                name varchar(100) default NULL
            )");
        }
        
        if ($this->driver === 'pgsql') {
            $this->markTestIncomplete('This test is not yet complete!');
        }

        // Test without records
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertInstanceOf('PDOStatement', $actual);
        $this->assertNotNull($actual);

        // Test with one record
        $input = new Object();
        $input->name = 'Just a SAVE test object';
        $this->assertTrue($input->save());

        // Test if generic object can be fetched
        $expected = new stdClass();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = $this->object->query('SELECT * FROM object_table');
        $actual = $actual->fetchObject();
        $this->assertEquals($expected, $actual);

        // Test if object can be fetched
        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);
    }
    
    
    /**
     * 
     */
    public function testSaveAfterUpdate() {
        // Test without table
        $expected = false;
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertEquals($expected, $actual);

        // Create table
        if ($this->driver === 'mysql') {
        $this->conn->exec("CREATE TABLE object_table (
                id int(11) unsigned NOT NULL auto_increment,
                name text,
                PRIMARY KEY  (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        }
        
        if ($this->driver === 'sqlite') {
            $this->conn->exec("CREATE TABLE object_table (
                id INTEGER NOT NULL PRIMARY KEY,
                name varchar(100) default NULL
            )");
        }
        
        if ($this->driver === 'pgsql') {
            $this->markTestIncomplete('This test is not yet complete!');
        }

        // Test without records
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertInstanceOf('PDOStatement', $actual);
        $this->assertNotNull($actual);

        // Test with one record
        $input = new Object();
        $input->name = 'Just a SAVE test object';
        $this->assertTrue($input->save());

        // Test if generic object can be fetched
        $expected = new stdClass();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = $this->object->query('SELECT * FROM object_table');
        $actual = $actual->fetchObject();
        $this->assertEquals($expected, $actual);

        // Test if object can be fetched
        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);
        
        // Test if object can be fetched after being modified
        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Just a SAVED AND CHANGED test object';
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertNotEquals($expected, $actual);
        $actual->name = 'Just a SAVED AND CHANGED test object';
        $this->assertTrue($actual->save());
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);
        
        // Test if object can be fetched after being modified to have empty string
        $expected = new Object();
        $expected->id = 1;
        $expected->name = '';
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertNotEquals($expected, $actual);
        $actual->name = '';
        $this->assertTrue($actual->save());
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testDelete().
     */
    public function testDelete() {
        // Test without table
        $expected = false;
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertEquals($expected, $actual);

        // Create table
        if ($this->driver === 'mysql') {
            $this->conn->exec("CREATE TABLE object_table (
                id int(11) unsigned NOT NULL auto_increment,
                name text,
                PRIMARY KEY  (id)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        }
        
        if ($this->driver === 'sqlite') {
            $this->conn->exec("CREATE TABLE object_table (
                id INTEGER NOT NULL PRIMARY KEY,
                name varchar(100) default NULL
            )");
        }

        if ($this->driver === 'pgsql') {
            $this->markTestIncomplete('This test is not yet complete!');
        }
        
        // Test without records
        $actual = $this->object->query('SELECT * FROM object_table');
        $this->assertInstanceOf('PDOStatement', $actual);
        $this->assertNotNull($actual);

        // Test with one record
        $input = new Object();
        $input->name = 'Just a SAVE test object';
        $this->assertTrue($input->save());

        // Test if generic object can be fetched
        $expected = new stdClass();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = $this->object->query('SELECT * FROM object_table');
        $actual = $actual->fetchObject();
        $this->assertEquals($expected, $actual);

        // Test if object can be fetched
        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'Just a SAVE test object';
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);

        // Delete object
        $this->assertTrue($actual->delete());

        // Test if object can be fetched
        $expected = false;
        $actual = Object::findByIdFrom('Object', 1);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testBeforeSave().
     */
    public function testBeforeSave() {
        $this->assertTrue($this->object->beforeSave());
    }


    /**
     * @todo Implement testBeforeInsert().
     */
    public function testBeforeInsert() {
        $this->assertTrue($this->object->beforeInsert());
    }


    /**
     * @todo Implement testBeforeUpdate().
     */
    public function testBeforeUpdate() {
        $this->assertTrue($this->object->beforeUpdate());
    }


    /**
     * @todo Implement testBeforeDelete().
     */
    public function testBeforeDelete() {
        $this->assertTrue($this->object->beforeDelete());
    }


    /**
     * @todo Implement testAfterSave().
     */
    public function testAfterSave() {
        $this->assertTrue($this->object->afterSave());
    }


    /**
     * @todo Implement testAfterInsert().
     */
    public function testAfterInsert() {
        $this->assertTrue($this->object->afterInsert());
    }


    /**
     * @todo Implement testAfterUpdate().
     */
    public function testAfterUpdate() {
        $this->assertTrue($this->object->afterUpdate());
    }


    /**
     * @todo Implement testAfterDelete().
     */
    public function testAfterDelete() {
        $this->assertTrue($this->object->afterDelete());
    }


    /**
     * @todo Implement testGetColumns().
     */
    public function testGetColumns() {
        $obj = new Object();
        $expected = array('id','name');
        $actual = $obj->getColumns();
        $this->assertEquals($expected, $actual);

        $obj = new Object();
        $obj->description = 'Description of some sort.';
        $expected = array('id','name','description');
        $actual = $obj->getColumns();
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testInsert().
     */
    public function testInsert() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testUpdate().
     */
    public function testUpdate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testDeleteWhere().
     */
    public function testDeleteWhere() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testExistsIn().
     */
    public function testExistsIn() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testFindByIdFrom().
     */
    public function testFindByIdFrom() {
        $obj = new Object();
        $obj->name = 'an object';
        $obj->save();

        $expected = new Object();
        $expected->id = 1;
        $expected->name = 'an object';
        $actual = Object::findById(1);
        $this->assertEquals($expected, $actual);
    }


    /**
     * @todo Implement testFindOneFrom().
     */
    public function testFindOneFrom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testFindAllFrom().
     */
    public function testFindAllFrom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }


    /**
     * @todo Implement testCountFrom().
     */
    public function testCountFrom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }
    
    
    /**
     * 
     */
    public function testIsDirty() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        
        $obj = new Object();
        
        // Make sure the method exists
        $this->assertTrue(method_exists($obj, 'isDirty'));

        // Make sure default state is clean
        $this->assertFalse($obj->isDirty());
        
        // Dirty the record by changing a variable and check its state
        $obj->someValueAccessor(1);
        $this->assertEquals(1, $obj->someValueAccessor());
        $this->assertTrue($obj->isDirty());
        
        // Save the dirty record, thereby making it clean again
        $obj->save();
        $this->assertFalse($obj->isDirty());
        
        // Set through direct access
        /*
        $obj->name = 'Another value';
        $this->assertEquals('Another value', $obj->someValueAccessor());
        $this->assertTrue($obj->isDirty());
         * 
         */
        
        // Save the dirty record, thereby making it clean again
        /*
        $obj->save();
        $this->assertFalse($obj->isDirty());
         * 
         */
        
        // Set through setFromData()
        /*
        $obj->setFromData(array('name' => 'Some name value'));
        $this->assertEquals('Some name value', $obj->someValueAccessor());
        $this->assertTrue($obj->isDirty());
         * 
         */
        
        // Save the dirty record, thereby making it clean again
        /*
        $obj->save();
        $this->assertFalse($obj->isDirty());
         * 
         */
    }
    
    /*
     * 
     */
    public function testDirtyFields() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        
        $expected = array('name' => 'Test name');
        $actual = new Object();
        
        // Make sure the method exists
        $this->assertTrue(method_exists($actual, 'dirtyFields'));
        
        $actual->someValueAccessor('Test name');
        $actual->save();
                
        $this->assertFalse($actual->isDirty());
        /*
        $actual->setFromData(array('id' => 1, 'name' => 'Test name'));
        $this->assertEquals(1, $actual->id);
        $this->assertFalse($actual->isDirty());
         * 
         */
        
        $actual->someValueAccessor('New test name');
        $this->assertEquals('New test name', $actual->someValueAccessor());
        $this->assertTrue($actual->isDirty());
        
        $actualFields = $actual->dirtyFields();
        $this->assertInternalType('array', $actual);
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * 
     */
    public function testDirtyValueOf() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        
        $expected = 'New test name';
        $actual = new Object();

        // Make sure the method exists
        $this->assertTrue(method_exists($actual, 'dirtyValueOf'));
        
        $this->assertFalse($actual->isDirty());
        $actual->setFromData(array('id' => 1, 'name' => 'Test name'));
        $this->assertFalse($actual->isDirty());
        
        $actual->setName('New test name');
        $this->assertTrue($actual->isDirty());
        
        $this->assertEquals($expected, $actual->dirtyValueOf('name'));
    }
    
    /**
     *
     */ 
    public function testMandatoryFields() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
        
        $className = 'Record';
        
        // Testing for presence of...
        $attributeName = '__CONN__';
        $this->assertClassHasStaticAttribute($attributeName, $className);
        
        $attributeName = '__QUERIES__';
        $this->assertClassHasStaticAttribute($attributeName, $className);

        $attributeName = '__DIRTY__';
        $this->assertClassHasAttribute($attributeName, $className);
    }

}

?>
