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

$roleId = $user->isLogged()
	? $user->role_id
	: User_RoleCollection::load()->getGuestRole('id');

define('USER_AUTH_ID', $user->getAuthData('id'));
define('USER_AUTH_LEVEL', User_RoleCollection::load()->getRole($roleId, 'level'));
define('USER_AUTH_ROLE_ID', $roleId);


########## ПРОЧЕЕ ##########

define('FORMCODE', App::getFormCodeInput());

?>