<?php

$r = Request::get();
$acl = User_Acl::get();

return array (

	'name' => 'backend-top',
	
	'items' => array(
	
		array(
			'title' => 'Контент',
			'href' => 'admin/content',
			'active' => in_array($r->getParts(array(0, 1)), array('admin', 'admin/content')),
			'display' => $acl->isResourceAllowed('admin', 'content'),
		),
	
		array(
			'title' => 'Конфигурация',
			'href' => 'admin/config',
			'active' => $r->getParts(array(0, 1)) == 'admin/config',
			'display' => $acl->isResourceAllowed('admin', 'config'),
		),
	
		array(
			'title' => 'Пользователи',
			'href' => 'admin/users',
			'active' => $r->getParts(array(0, 1)) == 'admin/users',
			'display' => $acl->isResourceAllowed('admin', 'users'),
		),
	
		array(
			'title' => 'Администрирование',
			'href' => 'admin/manage',
			'active' => $r->getParts(array(0, 1)) == 'admin/manage',
			'display' => $acl->isResourceAllowed('admin', 'manage'),
		),
	
		array(
			'title' => 'SQL',
			'href' => 'admin/sql',
			'active' => $r->getParts(array(0, 1)) == 'admin/sql',
			'display' => $acl->isResourceAllowed('admin', 'sql'),
		),
	),
);

?>