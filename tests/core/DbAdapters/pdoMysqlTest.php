<?php


class PdoMysqlTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {

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