<?php
session_start();

// обозначение корня ресурса
$_url = dirname($_SERVER['SCRIPT_NAME']);
define('WWW_ROOT', null);
define('WWW_URI', null);
define('FS_ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/** определение ajax-запроса */
define('AJAX_MODE', null);

/** режим работы сайта */
define('RUN_MODE', 'dev');
// define('RUN_MODE', 'production');

// отправка Content-type заголовка
header('Content-Type: text/html; charset=utf-8');

// подключение файлов CMF
require_once(FS_ROOT.'setup.php');
