<?

return array(
	'name' => 'user-statistics',
	'title' => 'Статистика посещений',
	'controller' => 'UserStatistics_Controller',
	'adminController' => 'UserStatistics_AdminController',
	'arrayParams' => TRUE,
	'resources' => array(
		'public' => 'Общедоступные действия',
		'view' => 'Просмотр данных',
		'edit' => 'Редактирование данных',
	)
);

?>