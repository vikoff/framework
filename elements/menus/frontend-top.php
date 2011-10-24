<?php

$r = Request::get();

return array (

	'name' => 'frontend-top',
	
	'items' => array(
	
		array(
			'title' => 'Главная',
			'href' => '',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => in_array($r->getParts(array(0, 1)), array('', 'page', 'page/main')),
		),
	
		array(
			'title' => 'Тест',
			'href' => 'page/test',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'page/test',
		),
	
		array(
			'title' => 'О Сайте',
			'href' => 'page/about',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'page/about',
		),
	
		array(
			'title' => 'Обратная связь',
			'href' => 'page/feedback',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'page/feedback',
		),
	
		array(
			'title' => 'Адм. панель',
			'href' => 'admin',
			'attrs' => 'class="external-link"',
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => false,
		),
	
	)
);

?>