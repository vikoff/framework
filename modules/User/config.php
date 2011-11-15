<?

return array(
	'name' => 'user',
	'title' => 'Пользователи',
	'controller' => 'User_Controller',
	'adminController' => 'User_AdminController',
	'resources' => array(
		'public' => 'Общедоступные действия',
		'view' => 'Просмотр данных пользователя',
		'edit' => 'Редактирование данных',
	)
);

?>