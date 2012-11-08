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

	public function setUp() {

		$db = self::$_db;

		$dataColuns = array('id', 'field', 'num', 'select', 'date');
		$dataRows = array(
			array(1, 'hello', 123, TRUE, $db->raw('NOW()')),
			array(2, 'row2', NULL, NULL, NULL),
			array(3, '\'some\' "special" \symbols/', 0, FALSE, '2000-01-01 00:00:00'),
			array(4, "'", 1, TRUE, '2001-02-01 00:00:00'),
			array(5, '"', 1, TRUE, '2001-02-02 00:00:00'),
			array(6, "\\", 1, TRUE, '2001-02-03 00:00:00'),
		);

		$db->insertMulti(self::$_table, $dataColuns, $dataRows);
	}

	public function tearDown() {}

	// TEST METHODS //

	public function testGetOne() {

		$db = self::$_db;
		$field1 = $db->quoteFieldName('field');
		$field2 = $db->quoteFieldName('select');

		$data = array(
			0 => array('value' => 'hello', 'equals' => 1, 'field' => $field1),

			1 => array('value' => "'",     'equals' => 4, 'field' => $field1),
			2 => array('value' => '"',     'equals' => 5, 'field' => $field1),
			3 => array('value' => '\\',    'equals' => 6, 'field' => $field1),

			4 => array('value' => TRUE,    'equals' => 1, 'field' => $field2),
			5 => array('value' => FALSE,   'equals' => 3, 'field' => $field2),
			6 => array('value' => NULL,    'equals' => 2, 'field' => $field2),
		);

		$sql = $this->_getSqlTplForGetOne($field2, 'IS', '?');
		$result = $db->getOne($sql, null);
		var_dump($db->getError(true)); die;
		$this->assertEquals(2, $result, "ERROR IN SQL: $sql");

		foreach ($data as $index => $set) {

			$compare = $set['value'] === null ? 'IS' : '=';

			// test with usual escaping
			$sql = $this->_getSqlTplForGetOne($set['field'], $compare, $db->qe($set['value']));
			$result = $db->getOne($sql);
			$this->assertEquals($set['equals'], $result, "ERROR IN SQL($index): $sql");

			// test with placeholders
			$sql = $this->_getSqlTplForGetOne($set['field'], $compare, '?');
			$result = $db->getOne($sql, $set['value']);
			$this->assertEquals($set['equals'], $result, "ERROR IN SQL($index): $sql");
		}
	}

    protected function _getSqlTplForGetOne($field, $compare, $value) {

        $sqlTpl = "SELECT id FROM ".self::$_table." WHERE %s %s %s ORDER BY id";

        return sprintf($sqlTpl, $field, $compare, $value);
    }

}
