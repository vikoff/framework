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
	exit("USAGE: php ".basename(__FILE__)." path/to/structure.sql 'comma-separated tables to recreate (drop, create)'\n");
}

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
	$sql = preg_replace('/\)\s*ENGINE[^;]*;/i', ');', $sql);
	return $sql;
}


$sqlFile = $argv[1];
$tablesToRecreate = isset($argv[2]) ? explode(',', $argv[2]) : array();

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

echo "EXISTING TABLES:\n\t".implode("\n\t", $existTables)."\n";
echo "TABLES TO CREATE:\n";
foreach ($newTables as $t) echo "\t{$t['name']}\n";

echo "\nContinue? [Y/n]: ";
$ans = mb_strtolower(trim(fread(STDIN, 1)));
if ($ans !== 'y' && $ans !== '')
	exit("Aborted\n");

$counters = array('skipped' => 0, 'created' => 0, 'drop-created' => 0);

foreach ($newTables as $table) {
	if (in_array($table['name'], $existTables)) {
		if (in_array($table['name'], $tablesToRecreate)) {
			$db->query('DROP TABLE '.$table['name']);
			$db->query(convert($table['sql']));
			echo "table {$table['name']} drop, create\n";
			$counters['drop-created']++;
		} else {
			echo "table {$table['name']} already exists, skip\n";
			$counters['skipped']++;
		}
	} else {
		$db->query(convert($table['sql']));
		echo "table {$table['name']} created\n";
		$counters['created']++;
	}
}

echo "\nTOTAL\n"
	."\t{$counters['created']} tables created\n"
	."\t{$counters['skipped']} tables skipped\n"
	."\t{$counters['drop-created']} tables dropped and created\n";