<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

abstract class DbAdapterTestAbstract extends PHPUnit_Framework_TestCase {

	/** @var DbAdapter */
	protected static $_db = null;
    protected static $_dbName = null;
    protected static $_table = null;

	public static function setUpBeforeClass() {

		self::$_db->setErrorHandlingMode(DbAdapter::ERROR_STORE);
	}

	public static function tearDownAfterClass() {}

	public function setUp() {

		$db = self::$_db;

		$dataColuns = array('id', 'field', 'num', 'select', 'date');
		$dataRows = array(
			array(1, 'hello', 123, TRUE, $db->raw('NOW()')),
			array(2, 'row2', NULL, NULL, NULL),
			array(3, '\'some\' "special" \symbols/', 0, FALSE, '2000-01-01 00:00:00'),
			array(4, "'", 1, TRUE, '2001-02-01 00:00:00'),
			array(5, '"', 2, TRUE, '2001-02-02 00:00:00'),
			array(6, "\\", 3, TRUE, '2001-02-03 00:00:00'),
		);

		$db->insertMulti(self::$_table, $dataColuns, $dataRows);
	}

	public function tearDown() {

		$db = self::$_db;
		$db->truncate(self::$_table);
	}

	// TEST METHODS //

	public function testFetchOne() {

		$db = self::$_db;
		$field1 = $db->quoteFieldName('field');
		$field2 = $db->quoteFieldName('select');
		$sqlTpl = "SELECT id FROM ".self::$_table." WHERE %s %s %s ORDER BY id";

		$data = array(
			0 => array('value' => 'hello', 'equals' => 1, 'field' => $field1),

			1 => array('value' => "'",     'equals' => 4, 'field' => $field1),
			2 => array('value' => '"',     'equals' => 5, 'field' => $field1),
			3 => array('value' => '\\',    'equals' => 6, 'field' => $field1),

			4 => array('value' => TRUE,    'equals' => 1, 'field' => $field2),
			5 => array('value' => FALSE,   'equals' => 3, 'field' => $field2),
			6 => array('value' => NULL,    'equals' => 2, 'field' => $field2),
		);

		foreach ($data as $index => $set) {

			$compare = $set['value'] === null ? 'IS' : '=';

			// test with usual escaping
			$sql = sprintf($sqlTpl, $set['field'], $compare, $db->qe($set['value']));
			$result = $db->fetchOne($sql);
			$this->assertEquals($set['equals'], $result, "ERROR IN SQL($index): $sql");

			// test with placeholders
			$sql = sprintf($sqlTpl, $set['field'], $compare, '?');
			$result = $db->fetchOne($sql, $set['value']);
			$this->assertEquals($set['equals'], $result, "ERROR IN SQL($index): $sql");
		}
	}

	public function testFetchRow() {
		$db = self::$_db;

		$row = $db->fetchRow("SELECT * FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('field')." = ?", "'");

		$expectedVal = array('id' => 4, 'field' => "'", 'num' => 1, 'select' => TRUE, 'date' => '2001-02-01 00:00:00');
		$this->assertEquals($expectedVal, $row);
	}

	public function testFetchCol() {
		$db = self::$_db;

		$col = $db->fetchCol("SELECT id FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('select')." = ? ORDER BY id", TRUE);

		$expectedVal = array(1, 4, 5, 6);
		$this->assertEquals($expectedVal, $col);
	}

	public function testFetchPairs() {
		$db = self::$_db;

		$pairs = $db->fetchPairs("SELECT id, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('num')." BETWEEN ? AND ? ORDER BY id", array(1, 3));

		$expectedVal = array(4 => 1, 5 => 2, 6 => 3);
		$this->assertEquals($expectedVal, $pairs);
	}

	public function testFetchAll() {
		$db = self::$_db;

		$all = $db->fetchAll("SELECT id, field, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('id')." < ? ORDER BY id", 3);

		$expectedFirstRow = array('id' => 1, 'field' => 'hello', 'num' => 123);
		$this->assertCount(2, $all);
		$this->assertArrayHasKey(0, $all);
		$this->assertEquals($expectedFirstRow, $all[0]);
	}

	public function testFetchAssoc() {
		$db = self::$_db;

		$assoc = $db->fetchAssoc("SELECT id, field, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('id')." IN (?,?,?) ORDER BY id", 'id', array(1,2,3));

		$expectedFirstRow = array('id' => 2, 'field' => 'row2', 'num' => null);
		$this->assertCount(3, $assoc);
		$this->assertArrayHasKey(2, $assoc);
		$this->assertEquals($expectedFirstRow, $assoc[2]);
	}

	public function testDescribe() {

		$db = self::$_db;
	}

	public function testShowTables() {

		$db = self::$_db;
	}

	public function testIsConnected() {

		$db = self::$_db;
	}

	public function testInsert() {

		$db = self::$_db;
	}

	public function testInsertMulti() {

		$db = self::$_db;
	}

	public function testGetLastId () {

		$db = self::$_db;
	}

	public function testUpdate() {

		$db = self::$_db;
	}

	public function testDelete() {

		$db = self::$_db;
	}

	public function testTruncate() {

		$db = self::$_db;
	}

	public function testGetConnectTime() {

		$db = self::$_db;
	}

	public function testGetQueriesNum() {

		$db = self::$_db;
	}

	public function testGetQueries() {

		$db = self::$_db;
	}

	public function testGetQueriesTime() {

		$db = self::$_db;
	}

	public function testGetQueriesWithTime() {

		$db = self::$_db;
	}

	public function testGetLastQueryInfo() {

		$db = self::$_db;
	}

}
