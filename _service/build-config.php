<?php

require (dirname(__FILE__).'/setup.php');

$model = new Admin_Model();

$log = $model->readModulesConfig();

echo "\n";

foreach ($log['info'] as $msg)
	echo "\t".strip_tags($msg)."\n";

foreach ($log['error'] as $msg)
	echo "\terror: ".strip_tags($msg)."\n";

echo "\n";

if (empty($log['error']))
	echo "\tДАННЫЕ МОДУЛЕЙ УСПЕШНО ОБНОВЛЕНЫ!\n\n";
else
	echo "\tДАННЫЕ МОДУЛЕЙ ОБНОВЛЕНЫ С ОШИБКАМИ!\n\n";
