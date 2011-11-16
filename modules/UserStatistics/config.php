<?

return array(
	'name' => 'userStatistics',
	'title' => 'Псевдонимы',
	'controller' => 'UserStatistics_Controller',
	'adminController' => 'UserStatistics_AdminController',
	'arrayParams' => TRUE,
	'resources' => array(
		'public' => 'Общедоступные действия',
		'view' => 'Просмотр данных пользователя',
		'edit' => 'Редактирование данных',
	)
);

?>