<?

if(!defined('FS_ROOT'))
	die('access denided (setup file)');
	
$GLOBALS['__vikOffTimerStart__'] = microtime(1);


########## ПОДКЛЮЧЕНИЕ ЯДРА ##########

require(FS_ROOT.'core/Func.php');
require(FS_ROOT.'core/App.php');
require(FS_ROOT.'includes/autoload.php');


########## ИНИЦИАЛИЗАЦИЯ КОНФИГА ##########

Config::init();


########## УСТАНОВКА ОБРАБОТЧИКА ОШИБОК ##########

set_error_handler(array('Error_Model', 'error_handler'));	
set_exception_handler(array('Error_Model', 'exception_handler'));


########## ИНИЦИАЛИЗАЦИЯ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ ##########

$user = CurUser::init();


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

mb_internal_encoding("UTF-8");

?>