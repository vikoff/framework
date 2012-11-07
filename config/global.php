<?

if(!defined('FS_ROOT'))
	die("access denided (global config file)");

// установить текущий часовой пояс
date_default_timezone_set('Europe/Kiev');

// название сайта
define('CFG_SITE_NAME', 'vik-off CMF');


########## EMAIL АДРЕСА ##########

// email разработчика
define('CFG_DEVELOPER_EMAIL', 'dev.yurijnovikov@gmail.com');

// email администратора сайта
define('CFG_ADMIN_EMAIL', 'yurijnovikov@gmail.com');


########## СОЗДАНИЕ СОЕДИНЕНИЯ С БД ##########

db::create(array(
	'adapter' => 'pdo_sqlite',
	'host' => '',
	'user' => '',
	'pass' => '',
	'database' => FS_ROOT.'db.pdo_sqlite',
	'keepFileLog' => 0,
));


########## КОНФИГУРАЦИЯ КЛАССА СТАТИСТИКИ ##########

UserStatistics_Model::enable();


########## РАЗНОЕ ##########

// использовать красивые url
define('CFG_USE_SEF', 0);

// отсеивать дублируемые формы
define('CHECK_FORM_DUPLICATION', 1);

// производить ли редирект на дефолтные display методы
define('CFG_REDIRECT_DEFAULT_DISPLAY', 0);


########## SMARTY ##########

// удаление лишних пробельных символов из html
define('CFG_SMARTY_TRIMWHITESPACES', 0);

// использовать ли кэширование шаблонов смарти
define('CFG_USE_SMARTY_CACHING', 0);

