<?php
session_start();

define('WWW_ROOT', 'http://'.$_SERVER['SERVER_NAME'].(strlen(dirname($_SERVER['SCRIPT_NAME'])) > 1 ? dirname($_SERVER['SCRIPT_NAME']) : '').'/');
define('FS_ROOT', realpath('.').DIRECTORY_SEPARATOR);
define('YNPROJECT', 1);
define('AJAX_MODE', 0);

/** режим работы сайта */
define('RUN_MODE', 'dev');

header('Content-Type: text/html; charset=utf-8');
require_once('setup.php');

// db::get()->query('DROP TABLE page');
// echo'<pre>'; print_r(db::get()->getAll('SELECT * FROM users')); die;
$tables = db::get()->showTables();

echo"<pre>";
print_r($tables);
echo"</pre>";

if(!in_array("users", $tables)){

	db::get()->query("
			CREATE TABLE 'users' (
				'id' 			INTEGER PRIMARY KEY,
				'login'			VARCHAR(100) NOT NULL,
				'password'		VARCHAR(100) NOT NULL,
				'surname'		VARCHAR(255),
				'name'			VARCHAR(255),
				'patronymic'	VARCHAR(255),
				'sex'			VARCHAR(10),
				'birthdate' 	VARCHAR(15),
				'country' 		VARCHAR(255),
				'city'		 	VARCHAR(255),
				'level'			SMALLINT,
				'active' 		CHAR(1),
				'regdate'		INTEGER
			)
	");
	
	echo"<div>таблица <b>users</b> создана</div>";
}

if(!in_array("page", $tables)){

	db::get()->query("
			CREATE TABLE 'page' (
			  'id' 				INTEGER PRIMARY KEY,
			  'title' 			TEXT NOT NULL,
			  'alias' 			VARCHAR(255) NOT NULL,
			  'body' 			TEXT,
			  'author' 			INTEGER NOT NULL,
			  'published' 		CHAR(1) DEFAULT '0',
			  'locked'			CHAR(1) DEFAULT '0',
			  'meta_description' TEXT,
			  'meta_keywords'	TEXT,
			  'modif_date'		INTEGER DEFAULT '0',
			  'create_date'		INTEGER DEFAULT '0'
			)
	");
	
	echo"<div>таблица <b>page</b> создана</div>";
}

if(!in_array("error_log", $tables)){

	db::get()->query("
			CREATE TABLE 'error_log' (
			  'id' 			INTEGER PRIMARY KEY,
			  'url'			TEXT,
			  'description' TEXT,
			  'session_dump' TEXT,
			  'hash'		CHAR(32),
			  'lastdate' 	INTEGER DEFAULT NULL
			)
	");
	
	echo"<div>таблица <b>error_log</b> создана</div>";
}

if(!in_array("user_statistics", $tables)){

	db::get()->query("
			CREATE TABLE 'user_statistics' (
			  'id' 				INTEGER PRIMARY KEY,
			  'uid' 			INTEGER DEFAULT 0,
			  'request_urls'	TEXT,
			  'user_ip'			VARCHAR(255),
			  'referer'			VARCHAR(255),
			  'user_agent_raw'	VARCHAR(255),
			  'has_js'			BOOLEAN,
			  'browser_name'	VARCHAR(50),
			  'browser_version'	VARCHAR(50),
			  'screen_width'	SMALLINT,
			  'screen_height'	SMALLINT,
			  'date'			INTEGER
			)
	");
	
	echo"<div>таблица <b>user_statistics</b> создана</div>";
}

if(!in_array("test_sections", $tables)){

	db::get()->query("
			CREATE TABLE 'test_sections' (
			  'id'			INTEGER PRIMARY KEY,
			  'name'		VARCHAR(255),
			  'alias'		VARCHAR(255),
			  'published'	CHAR(1),
			  'date' 		INTEGER DEFAULT NULL
			)
	");
	
	echo"<div>таблица <b>test_sections</b> создана</div>";
}

if(!in_array("test_categories", $tables)){

	db::get()->query("
			CREATE TABLE 'test_categories' (
			  'id'			INTEGER PRIMARY KEY,
			  'section_id'	INT,
			  'name'		VARCHAR(255),
			  'alias'		VARCHAR(255),
			  'published'	CHAR(1),
			  'date' 		INTEGER DEFAULT NULL
			)
	");
	
	echo"<div>таблица <b>test_categories</b> создана</div>";
}

if(!in_array("test_items", $tables)){

	db::get()->query("
			CREATE TABLE 'test_items' (
			  'id'			INTEGER PRIMARY KEY,
			  'category_id'	INT,
			  'item_name'	VARCHAR(255),
			  'item_text'	TEXT,
			  'published'	CHAR(1),
			  'date' 		INTEGER DEFAULT NULL
			)
	");
	
	echo"<div>таблица <b>test_items</b> создана</div>";
}

?>