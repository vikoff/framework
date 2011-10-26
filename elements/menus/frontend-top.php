<?php

$r = Request::get();

$paths = array(
	'page/main' => 'page/main',
	'page/test' => 'page/test',
	'page/about' => 'page/about',
	'page/feedback' => 'page/feedback',
);

$pathsQuoted = array();
foreach ($paths as $p)
	$pathsQuoted[] = '"'.$p.'"';
	
$paths = array_merge( $paths, db::get()->getColIndexed('SELECT path, alias FROM aliases WHERE path IN('.implode(',', $pathsQuoted).')') );

return array (

	'name' => 'frontend-top',
	
	'items' => array(
	
		array(
			'title' => 'Главная',
			'href' => $paths['page/main'],
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => in_array($r->getParts(array(0, 1)), array('', 'page', 'page/main')),
		),
	
		array(
			'title' => 'Тест',
			'href' => $paths['page/test'],
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'page/test',
		),
	
		array(
			'title' => 'О Сайте',
			'href' => $paths['page/about'],
			'allowedRoles' => null,
			'deniedRoles' => null,
			'active' => $r->getParts(array(0, 1)) == 'page/about',
		),
	
		array(
			'title' => 'Обратная связь',
			'href' => $paths['page/feedback'],
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