<?php

require_once(dirname(dirname(dirname(__FILE__))).'/setup.php');

abstract class DbAdapterTestAbstract extends PHPUnit_Framework_TestCase {

	/** @var DbAdapter */
	protected static $_db = null;
	protected static $_table = null;

	public function setUp() {}

	public function tearDown() {}

	// TEST METHODS //

	public function testGetOne() {

		$db = self::$_db;
//		var_dump("SELECT id FROM ".self::$_table
//			." WHERE ".$db->quoteFieldName('field2')."=".$db->qe('world')); die;
//		$result = $db->getOne("SELECT id FROM ".self::$_table
//			." WHERE ".$db->quoteFieldName('field2')."=".$db->qe('world'));
//
//		var_dump($result);
	}

}
