<?

return array(

	'admin' => array(
		'title' => 'Панель управления',
		'controller' => 'Admin_Controller',
		'arrayParams' => TRUE,
		'resources' => array(
			'content' => 'Редактирование страниц',
			'edit' => 'Редактирование страниц',
			'root' => 'Root-привилегии (установка ограничений, запрет удаления)',
			'modules' => 'Управление модулями',
			'sql' => 'SQL-утилиты',
		),
	),
	
	'page' => array(
		'title' => 'Страницы',
		'controller' => 'Page_Controller',
		'adminController' => 'Page_AdminController',
		'resources' => array(
			'view' => 'Просмотр страниц',
			'edit' => 'Редактирование страниц',
			'root' => 'Root-привилегии (установка ограничений, запрет удаления)',
		)
	),
	
	'menu' => array(
		'title' => 'Меню',
		'controller' => 'Menu_Controller',
		'adminController' => 'Menu_AdminController',
		'arrayParams' => TRUE,
		'resources' => array(
			'view' => 'Просмотр страниц',
			'edit' => 'Редактирование страниц',
		)
	),
	
	'testItem' => array(
		'title' => 'Тестовые сущности',
		'controller' => 'TestItem_Controller',
		'adminController' => 'TestItem_AdminController',
		'arrayParams' => TRUE,
		'resources' => array(
			'view' => 'Просмотр страниц',
			'edit' => 'Редактирование страниц',
		)
	),
	
	'user' => array(
		'title' => 'Пользователи',
		'controller' => 'User_Controller',
		'adminController' => 'User_AdminController',
		'arrayParams' => TRUE,
		'resources' => array(
			'public' => 'Общедоступные действия',
			'view' => 'Просмотр данных пользователя',
			'edit' => 'Редактирование данных',
		)
	),

	'alias' => array(
		'title' => 'Псевдонимы',
		'adminController' => 'Alias_AdminController',
		'arrayParams' => TRUE,
		'resources' => array(
			'public' => 'Общедоступные действия',
			'view' => 'Просмотр данных пользователя',
			'edit' => 'Редактирование данных',
		)
	),

	'userStatistics' => array(
		'title' => 'Псевдонимы',
		'controller' => 'UserStatistics_Controller',
		'adminController' => 'UserStatistics_AdminController',
		'arrayParams' => TRUE,
		'resources' => array(
			'public' => 'Общедоступные действия',
			'view' => 'Просмотр данных пользователя',
			'edit' => 'Редактирование данных',
		)
	),


);

?>