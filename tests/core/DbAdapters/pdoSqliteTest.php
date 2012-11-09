<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

class PdoSqliteTest extends DbAdapterTestAbstract {

	public static function setUpBeforeClass() {

		self::$_dbName = FS_ROOT.'vikoff_tests';
		self::$_table = 'test1';
		self::$adapter = 'PdoSqlite';

		db::create(array(
			'adapter' => self::$adapter,
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
//			'database' => self::$_dbName,
			'database' => '',
			'keepFileLog' => 0,
		), 'pdo_sqlite');

		self::$_db = db::get('pdo_sqlite');

		$db = self::$_db;
		$db->query('DROP TABLE IF EXISTS '.self::$_table);
		$db->query('CREATE TABLE '.self::$_table.' (
			"id"           INTEGER PRIMARY KEY,
			"field"        VARCHAR(100),
			"num"          INTEGER,
			"select"       BOOLEAN,
			"date"         DATETIME DEFAULT CURRENT_TIMESTAMP
		)');

		parent::setUpBeforeClass();
	}

	public static function tearDownAfterClass() {

		self::$_db->query("DROP TABLE ".self::$_table);
		parent::tearDownAfterClass();
	}

}
