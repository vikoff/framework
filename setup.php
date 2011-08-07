<?

if(!defined('WWW_ROOT'))
	die('access denided (setup file)');
	
$GLOBALS['__vikOffTimerStart__'] = microtime(1);


########## ПОДКЛЮЧЕНИЕ ЯДРА ##########

require_once(FS_ROOT.'core/Func.php');
require_once(FS_ROOT.'core/App.php');


########## УСТАНОВКА ОБРАБОТЧИКА ОШИБОК ##########

set_error_handler(array('Error', 'error_handler'));	


########## ПРАВА ПОЛЬЗОВАТЕЛЕЙ ##########

define('PERMS_UNREG', 		0);
define('PERMS_REG', 		10);
define('PERMS_MODERATOR',	20);
define('PERMS_ADMIN', 		30);
define('PERMS_SUPERADMIN',	40);
define('PERMS_ROOT', 		50);


########## ПОДКЛЮЧЕНИЕ КОНФИГУРАЦИОННОГО ФАЙЛА ##########

require_once('config.php');


########## QUERY STRING PREFIX ##########

define('WWW_QS_PREFIX', CFG_USE_SEF ? '' : 'index.php?r=');
define('WWW_PREFIX', WWW_ROOT.WWW_QS_PREFIX);
define('WWW_ADMIN_PREFIX', WWW_ROOT.WWW_QS_PREFIX.'admin/');


########## ИНИЦИАЛИЗАЦИЯ ТЕКУЩЕГО ПОЛЬЗОВАТЕЛЯ ##########

CurUser::init();

define('USER_AUTH_ID', CurUser::get()->getAuthData('id'));
define('USER_AUTH_PERMS', CurUser::get()->getAuthData('perms'));


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

?>