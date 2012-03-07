<?

if(!defined('WWW_ROOT'))
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


########## ИНИЦИАЛИЗАЦИЯ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ ##########

$user = CurUser::init();


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

?>