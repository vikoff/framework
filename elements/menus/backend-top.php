<?php

$r = Request::get();

return array (

	'name' => 'backend-top',
	
	'items' => array(
	
		array(
			'title' => 'Контент',
			'href' => 'admin/content',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => in_array($r->getParts(array(0, 1)), array('admin', 'admin/content')),
		),
	
		array(
			'title' => 'Конфигурация',
			'href' => 'admin/config',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'admin/config',
		),
	
		array(
			'title' => 'Пользователи',
			'href' => 'admin/users',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'admin/users',
		),
	
		array(
			'title' => 'Модули',
			'href' => 'admin/modules',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'admin/modules',
		),
	
		array(
			'title' => 'Администрирование',
			'href' => 'admin/root',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'admin/root',
		),
	
		array(
			'title' => 'SQL',
			'href' => 'admin/sql',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'admin/sql',
		),
	),
);

?>