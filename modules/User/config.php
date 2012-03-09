<?

return array(
	'name' => 'user',
	'title' => 'Пользователи',
	'controller' => 'User_Controller',
	'adminController' => 'User_AdminController',
	'resources' => array(
		'public' => 'Общедоступные действия',
		'own-view' => 'Просмотр собственных данных',
		'own-edit' => 'Редактирование собственных данных',
		'view' => 'Просмотр данных других пользователей',
		'edit' => 'Редактирование данных других пользователей',
	)
);

?>