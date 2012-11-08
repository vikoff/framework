<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

class PdoMysqlTest extends DbAdapterTestAbstract {

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

		$db = self::$_db;
		$db->query("CREATE TABLE `".self::$_table."` (
			`id`            INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			`field`        VARCHAR(100),
			`num`			INT,
			`select`		BOOLEAN,
			`date`          TIMESTAMP DEFAULT NOW()
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		");

		parent::setUp();
	}

	public function tearDown() {

		self::$_db->query("DROP TABLE ".self::$_table);
	}

	// TEST METHODS //

	public function testSelectDb() {

		self::$_db->selectDb(self::$_dbName);
	}

}