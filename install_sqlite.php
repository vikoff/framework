<?php
session_start();

if (PHP_SAPI != 'cli')
	exit("command line run only!<br />");

define('FS_ROOT', realpath('.').'/');
define('YNPROJECT', 1);
define('AJAX_MODE', 0);

/** режим работы сайта */
define('RUN_MODE', 'dev');

header('Content-Type: text/html; charset=utf-8');

$cfgNoUserInit = true;
require_once('setup.php');


if ($argc == 1) {
	exit("USAGE: php ".basename(__FILE__)." path/to/structure.sql [ --recreate table1,table2 ] [ --drop table1, table2 ]'\n");
}

$args = Cmd::parseArgs(array('recreate' => '-r --recreate', 'drop' => '-d --drop'));

function convert($sql){
	
	$sql = preg_replace(
		array('/\s+/', '/`/', '/UNSIGNED/i', '/AUTO_INCREMENT/i', '/IF\s+(NOT)?\s+EXISTS/'),
		array(' ',     '',    '',            '',                  ''),
		$sql
	);

	if (!preg_match('/CREATE TABLE(?: IF NOT EXISTS)? [\'"]?(\w+)[\'"]?\s*\((.+)\)/mi', $sql, $matches))
		exit("could not parse create table string: $sql\n");

	$name = $matches[1];
	$columns = preg_split('/\s*,\s*/', trim($matches[2]));
	foreach ($columns as $index => $column) {
		if (preg_match('/^INDEX\W/i', $column))
			unset($columns[$index]);
	}

	$sql = "CREATE TABLE '$name' (\n\t".implode(",\n\t", $columns)."\n)\n";
	
	$sql = preg_replace('/\)[^)]*$/', ')', $sql);
	$sql = preg_replace('/int\(\d+\)/i', 'INTEGER', $sql);
	$sql = preg_replace('/NOT NULL\s+PRIMARY KEY/i', 'PRIMARY KEY', $sql);
	$sql = preg_replace('/NOW\(\)/i', 'CURRENT_TIMESTAMP', $sql);
	$sql = preg_replace('/\)\s*ENGINE[^;]*;/i', ');', $sql);
	return $sql;
}

$sqlFile = $argv[1];
$argRecreate = $args['recreate'] ? explode(',', $args['recreate']) : array();
$argDrop = $args['drop'] ? explode(',', $args['drop']) : array();

if (!file_exists($sqlFile))
	exit("ERROR: file $sqlFile not found\n");

$sqlData = file_get_contents($sqlFile);
$sqlData = preg_replace('~/\*.+?\*/~ms', '', $sqlData);
$sqlData = preg_replace('~--.*~', '', $sqlData);

$sqlsArrRaw = preg_split('/;\r?\n/', $sqlData);
$newTables = array();
foreach ($sqlsArrRaw as $sql) {
	$sql = trim($sql);
	if (preg_match('/CREATE TABLE\s+[`\'"]?(\w+)[`\'"]?/i', $sql, $matches)) {
		if (preg_match('/^_+$/', $matches[1])) continue;
		$newTables[] = array(
			'name' => $matches[1],
			'sql'   => $sql,
		);
	}
}


$db = db::get();
$existTables = $db->showTables();
$tablesToCreate = array();
$tablesToRecreate = array();
$tablesToDrop = array();
$counters = array('skipped' => 0, 'created' => 0, 'drop-created' => 0, 'dropped' => 0);

foreach ($newTables as $table) {
	if (in_array($table['name'], $existTables)) {
		if (in_array($table['name'], $argRecreate)) {
			$tablesToRecreate[] = $table;
		} else {
			$counters['skipped']++;
		}
	} else {
		$tablesToCreate[] = $table;
	}
}

foreach ($argDrop as $i => $table)
	if (in_array($table, $existTables))
		$tablesToDrop[] = $table;

echo "EXISTING TABLES:\n\t".implode("\n\t", $existTables)."\n\n";
if ($tablesToCreate) {
	echo "TABLES TO CREATE:\n";
	foreach ($tablesToCreate as $t) echo "\t{$t['name']}\n";
	echo "\n";
}
if ($tablesToRecreate) {
	echo "TABLES TO RECREATE:\n";
	foreach ($tablesToRecreate as $t) echo "\t{$t['name']}\n";
	echo "\n";
}
if ($tablesToDrop) {
	echo "TABLES TO DROP:\n";
	foreach ($tablesToDrop as $t) echo "\t$t\n";
	echo "\n";
}

echo "\nContinue? [Y/n]: ";
$ans = mb_strtolower(trim(fread(STDIN, 1)));
if ($ans !== 'y' && $ans !== '')
	exit("Aborted\n");

foreach ($tablesToCreate as $table) {
	$db->query(convert($table['sql']));
	echo "table {$table['name']} created\n";
	$counters['created']++;
}

foreach ($tablesToRecreate as $table) {
	$db->query('DROP TABLE '.$table['name']);
	$db->query(convert($table['sql']));
	echo "table {$table['name']} recreated\n";
	$counters['drop-created']++;
}

foreach ($tablesToDrop as $table) {
	$db->query('DROP TABLE '.$table);
	echo "table $table dropped\n";
	$counters['dropped']++;
}

echo "\nTOTAL\n"
	."\t{$counters['created']} tables created\n"
	."\t{$counters['skipped']} tables skipped\n"
	."\t{$counters['drop-created']} tables recreated (dropped and created)\n"
	."\t{$counters['dropped']} tables dropped\n";