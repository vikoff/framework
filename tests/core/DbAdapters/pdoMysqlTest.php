<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

class PdoMysqlTest extends DbAdapterTestAbstract {

	protected static $_dbName = null;
	protected static $_table = null;

	public static function setUpBeforeClass() {

		self::$_dbName = 'vikoff_tests';
		self::$_table = 'test1';

		db::create(array(
			'adapter' => 'PdoMysql',
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'database' => '',
			'keepFileLog' => 0,
		), 'pdo_mysql');

		self::$_db = db::get('pdo_mysql');

		self::$_db->query("DROP DATABASE IF EXISTS ".self::$_dbName);
		self::$_db->query("CREATE DATABASE ".self::$_dbName);
		self::$_db->query("USE ".self::$_dbName);

		parent::setUpBeforeClass();
	}

	public function setUp() {

		self::$_db->query("CREATE TABLE `".self::$_table."` (
			`id`            INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			`field1`        VARCHAR(100),
			`field2`        TEXT,
			`num`			INT,
			`date`          TIMESTAMP DEFAULT NOW()
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		");

		self::$_db->query("INSERT INTO ".self::$_table." (`id`, `field1`, `field2`, `num`, `date`) VALUES
			(1, 'hello', 'world', 123, NOW()),
			(2, 'row2', NULL, NULL, NULL),
			(3, '', 'the text of column 2', 0, '2000-01-01 00:00:00')
		");
	}

	public function tearDown() {

		self::$_db->query("DROP TABLE ".self::$_table);
	}

	// TEST METHODS //

	public function testSelectDb() {

		self::$_db->selectDb(self::$_dbName);
	}

}