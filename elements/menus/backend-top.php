<?php

$r = Request::get();

return array (

	'name' => 'backend-top',
	
	'items' => array(
	
		array(
			'title' => 'Контент',
			'href' => 'admin/content',
			'active' => in_array($r->getParts(array(0, 1)), array('admin', 'admin/content')),
		),
	
		array(
			'title' => 'Конфигурация',
			'href' => 'admin/config',
			'active' => $r->getParts(array(0, 1)) == 'admin/config',
		),
	
		array(
			'title' => 'Пользователи',
			'href' => 'admin/users',
			'active' => $r->getParts(array(0, 1)) == 'admin/users',
		),
	
		array(
			'title' => 'Администрирование',
			'href' => 'admin/manage',
			'active' => $r->getParts(array(0, 1)) == 'admin/manage',
		),
	
		array(
			'title' => 'SQL',
			'href' => 'admin/sql',
			'active' => $r->getParts(array(0, 1)) == 'admin/sql',
		),
	),
);

?>