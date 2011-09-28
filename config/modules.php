<?

return array(

	'admin' => array(
		'title' => 'Панель управления',
		'controller' => 'Admin_Controller',
		'resources' => array(
			'content' => 'Редактирование страниц',
			'edit' => 'Редактирование страниц',
			'root' => 'Root-привилегии (установка ограничений, запрет удаления)',
			'modules' => 'Управление модулями',
			'sql' => 'SQL-утилиты',
		)
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
	
	'user' => array(
		'title' => 'Пользователи',
		'controller' => 'User_Controller',
		'adminController' => 'User_AdminController',
		'resources' => array(
			'public' => 'Общедоступные действия',
			'view' => 'Просмотр данных пользователя',
			'edit' => 'Редактирование данных',
		)
	),


);

?>