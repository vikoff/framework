<?

if(!defined('WWW_ROOT'))
	die('access denided (setup file)');
	
$GLOBALS['__vikOffTimerStart__'] = microtime(1);


########## ПОДКЛЮЧЕНИЕ ЯДРА ##########

require(FS_ROOT.'core/Func.php');
require(FS_ROOT.'core/App.php');
require(FS_ROOT.'includes/autoload.php');


########## УСТАНОВКА ОБРАБОТЧИКА ОШИБОК ##########

set_error_handler(array('Core_Error_Model', 'error_handler'));	


########## ПРАВА ПОЛЬЗОВАТЕЛЕЙ ##########

define('PERMS_UNREG', 		0);
define('PERMS_REG', 		10);
define('PERMS_MODERATOR',	20);
define('PERMS_ADMIN', 		30);
define('PERMS_SUPERADMIN',	40);
define('PERMS_ROOT', 		50);


########## ИНИЦИАЛИЗАЦИЯ КОНФИГА ##########

Config::init();


########## ИНИЦИАЛИЗАЦИЯ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ ##########

CurUser::init();

define('USER_AUTH_ID', CurUser::get()->getAuthData('id'));
define('USER_AUTH_PERMS', CurUser::get()->getAuthData('perms'));


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

?>