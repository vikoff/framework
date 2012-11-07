<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

class PdoMysqlTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {

		db::create(array(
			'adapter' => 'PdoMysql',
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'database' => 'vikoff_tests',
			'keepFileLog' => 0,
		));
	}

	public static function tearDownAfterClass() {

	}

	public function setUp() {

	}

	public function tearDown() {

	}

	public function testFirst() {

		$this->assertEmpty(array());
	}

	public function testSecond() {

		$arr = range(0, 100);
		$this->assertCount(100, $arr);
	}
}