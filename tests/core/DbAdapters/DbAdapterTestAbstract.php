<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

abstract class DbAdapterTestAbstract extends PHPUnit_Framework_TestCase {

	/** @var DbAdapter */
	protected static $_db = null;
    protected static $_dbName = null;
    protected static $_table = null;
	public static $adapter = null;

	public $dbClass = null;

	public static function setUpBeforeClass() {

		self::$_db->setErrorHandlingMode(DbAdapter::ERROR_TRIGGER);
	}

	public static function tearDownAfterClass() {}

	public function setUp() {

		$db = self::$_db;
		$this->dbClass = get_class($db);

		$dataColuns = array('id', 'field', 'num', 'select', 'date');
		$dataRows = array(
			array(1, 'hello', 123, TRUE, $db->raw('CURRENT_TIMESTAMP')),
			array(2, 'row2', NULL, NULL, NULL),
			array(3, '\'some\' "special" \symbols/', 0, FALSE, '2000-01-01 00:00:00'),
			array(4, "'", 1, TRUE, '2001-02-01 00:00:00'),
			array(5, '"', 2, TRUE, '2001-02-02 00:00:00'),
			array(6, "\\", 3, TRUE, '2001-02-03 00:00:00'),
		);

		$db->truncate(self::$_table);
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
			$this->assertEquals($set['equals'], $result, "ERROR IN $this->dbClass($index): $sql");

			// test with placeholders
			$sql = sprintf($sqlTpl, $set['field'], $compare, '?');
			$result = $db->fetchOne($sql, $set['value']);
			$this->assertEquals($set['equals'], $result, "ERROR IN $this->dbClass($index): $sql");
		}
	}

	public function testFetchRow() {
		$db = self::$_db;

		$row = $db->fetchRow("SELECT * FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('field')." = ?", "'");

		$expectedVal = array('id' => 4, 'field' => "'", 'num' => 1, 'select' => TRUE, 'date' => '2001-02-01 00:00:00');
		$this->assertEquals($expectedVal, $row, "$this->dbClass->fetchRow()");
	}

	public function testFetchCol() {
		$db = self::$_db;

		$col = $db->fetchCol("SELECT id FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('select')." = ? ORDER BY id", TRUE);

		$expectedVal = array(1, 4, 5, 6);
		$this->assertEquals($expectedVal, $col, "$this->dbClass->fetchCol()");
	}

	public function testFetchPairs() {
		$db = self::$_db;

		$pairs = $db->fetchPairs("SELECT id, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('num')." BETWEEN ? AND ? ORDER BY id", array(1, 3));

		$expectedVal = array(4 => 1, 5 => 2, 6 => 3);
		$this->assertEquals($expectedVal, $pairs, "$this->dbClass->fetchPairs()");
	}

	public function testFetchAll() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->fetchAll()";

		$all = $db->fetchAll("SELECT id, field, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('id')." < ? ORDER BY id", 3);

		$expectedFirstRow = array('id' => 1, 'field' => 'hello', 'num' => 123);
		$this->assertCount(2, $all, $errmsg);
		$this->assertArrayHasKey(0, $all, $errmsg);
		$this->assertEquals($expectedFirstRow, $all[0], $errmsg);
	}

	public function testFetchAssoc() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->fetchAssoc()";

		$assoc = $db->fetchAssoc("SELECT id, field, num FROM ".self::$_table
			." WHERE ".$db->quoteFieldName('id')." IN (?,?,?) ORDER BY id", 'id', array(1,2,3));

		$expectedFirstRow = array('id' => 2, 'field' => 'row2', 'num' => null);
		$this->assertCount(3, $assoc, $errmsg);
		$this->assertArrayHasKey(2, $assoc, $errmsg);
		$this->assertEquals($expectedFirstRow, $assoc[2], $errmsg);
	}

	public function testInsert() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->insert()";

		$dataSet = array('field' => 'new \\\row ?!#$%^&*() \'with\' "spec" \symbols/', 'num' => 100500, 'select' => true);
		$insertId = $db->insert(self::$_table, $dataSet);

		$this->assertEquals(7, $insertId, $errmsg);
		$this->assertEquals(7, $db->fetchOne('SELECT COUNT(1) FROM '.self::$_table), $errmsg);

		$fetchedRow = $db->fetchRow(
			'SELECT '.$db->quoteFieldName('field').', '.$db->quoteFieldName('num').', '.$db->quoteFieldName('select').'
			 FROM '.self::$_table.' WHERE id=?', 7);
		$this->assertEquals($dataSet, $fetchedRow, $errmsg);

		$fetchedDate = $db->fetchOne('SELECT '.$db->quoteFieldName('date').' FROM '.self::$_table.' WHERE id=?', 7);

		$this->assertRegExp('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $fetchedDate, $errmsg);
	}

	public function testInsertMulti() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->insertMulti()";

		$fields = array('field', 'num', 'select');
		$dataSet = array(
			array('spec !@#$%^&*()\'\\" symbols', -100500, true),
			array('', 0, 0),
			array(null, null, null),
		);
		$db->insertMulti(self::$_table, $fields, $dataSet);

		$this->assertEquals(9, $db->fetchOne('SELECT COUNT(1) FROM '.self::$_table), $errmsg);

		$fetchedData = $db->fetchAll(
			'SELECT '.$db->quoteFieldName('field').', '.$db->quoteFieldName('num').', '.$db->quoteFieldName('select').'
			 FROM '.self::$_table.' WHERE id>?', 6);

		foreach ($fetchedData as $index => $row)
			$this->assertEquals($dataSet[$index], array_values($row), $errmsg);
	}

	public function testUpdate() {
		$db = self::$_db;
		$dataSet = array('field' => 'new \\\row ?!#$%^&*() \'with\' "spec" \symbols/', 'num' => null, 'select' => true);
		$errmsg = "$this->dbClass->update()";

		$numAffected1 = $db->update(self::$_table, $dataSet, 'id=?', 1);
		$numAffected2 = $db->update(self::$_table, $dataSet, 'id=?', 10);

		$this->assertEquals(1, $numAffected1, $errmsg);
		$this->assertEquals(0, $numAffected2, $errmsg);

		$fetchedRow = $db->fetchRow(
			'SELECT '.$db->quoteFieldName('field').', '.$db->quoteFieldName('num').', '.$db->quoteFieldName('select').'
			 FROM '.self::$_table.' WHERE id=?', 1);
		$this->assertEquals($dataSet, $fetchedRow, $errmsg);
	}

	public function testDelete() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->delete()";

		$numAffected1 = $db->delete(self::$_table, 'id=?', 5);
		$numAffected2 = $db->delete(self::$_table, $db->quoteFieldName('field').'=?', "'");
		$numAffected3 = $db->delete(self::$_table, $db->quoteFieldName('field').'=?', 100500);

		$this->assertEquals(1, $numAffected1, $errmsg);
		$this->assertEquals(1, $numAffected2, $errmsg);
		$this->assertEquals(0, $numAffected3, $errmsg);

		$fetchedIds = $db->fetchCol('SELECT id FROM '.self::$_table.' ORDER BY id');
		$this->assertEquals(array(1,2,3,6), $fetchedIds, $errmsg);
	}

	public function testTruncate() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->truncate()";

		$db->truncate(self::$_table);

		$this->assertEquals(0, $db->fetchOne('SELECT COUNT(1) FROM '.self::$_table), $errmsg);

		$db->insert(self::$_table, array('field' => 'new row'));
		$this->assertEquals(1, $db->fetchOne('SELECT MAX(id) FROM '.self::$_table), $errmsg);
	}

	public function testGetLastId () {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getLastId()";

		$db->insert(self::$_table, array('field' => 'new row'));
		$this->assertEquals(7, $db->getLastId(), $errmsg);
	}

	public function testDescribe() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->describe()";

		$describe = $db->describe(self::$_table);

		$this->assertInternalType('array', $describe, $errmsg);
		$this->assertCount(5, $describe, $errmsg);
	}

	public function testShowTables() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->showTables()";

		$tables = $db->showTables();
		$this->assertEquals(array(self::$_table), $tables, $errmsg);
	}

	public function testIsConnected() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->isConnected()";

		$this->assertTrue($db->isConnected(), $errmsg);
	}

	public function testGetConnectTime() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getConnectTime()";

		$this->assertInternalType('float', $db->getConnectTime(), $errmsg);
	}

	public function testGetQueriesNum() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getQueriesNum()";

		$this->assertInternalType('int', $db->getQueriesNum(), $errmsg);
	}

	public function testGetQueries() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getQueries()";

		$this->assertInternalType('array', $db->getQueries(), $errmsg);
	}

	public function testGetQueriesTime() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getQueriesTime()";

		$this->assertInternalType('float', $db->getQueriesTime(), $errmsg);
	}

	public function testGetQueriesWithTime() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getQueriesWithTime()";

		$this->assertInternalType('array', $db->getQueriesWithTime(), $errmsg);
	}

	public function testGetLastQueryInfo() {
		$db = self::$_db;
		$errmsg = "$this->dbClass->getLastQueryInfo()";

		$info = $db->getLastQueryInfo();
		$this->assertArrayHasKey('sql', $info, $errmsg);
		$this->assertArrayHasKey('time', $info, $errmsg);
	}

}
