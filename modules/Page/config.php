<?

return array(
	'name' => 'page',
	'title' => 'Страницы',
	'controller' => 'Page_Controller',
	'adminController' => 'Page_AdminController',
	'resources' => array(
		'view' => 'Просмотр страниц',
		'edit' => 'Редактирование страниц',
		'root' => 'Root-привилегии (установка ограничений, запрет удаления)',
	),
);

?>