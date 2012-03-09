<?php

$r = Request::get();
$user = CurUser::get();
$paths = array(
	'page/main' => 'page/main',
	'page/test' => 'page/test',
	'page/about' => 'page/about',
	'page/feedback' => 'page/feedback',
	'user/profile/registration' => 'user/profile/registration',
	'user/profile/edit' => 'user/profile/edit',
);

$pathsQuoted = array();
foreach ($paths as $p)
	$pathsQuoted[] = '"'.$p.'"';

$sql = 'SELECT path, alias FROM aliases WHERE path IN('.implode(',', $pathsQuoted).')';
$paths = array_merge( $paths, db::get()->getColIndexed($sql) );

return array (

	'name' => 'frontend-top',
	
	'items' => array(
	
		array(
			'title' => 'Главная',
			'href' => href($paths['page/main']),
			'active' => in_array($r->getParts(array(0, 1)), array('', 'page', 'page/main')),
		),
	
		array(
			'title' => 'Тест',
			'href' => href($paths['page/test']),
			'active' => $r->getParts(array(0, 1)) == 'page/test',
		),
	
		array(
			'title' => 'О Сайте',
			'href' => href($paths['page/about']),
			'active' => $r->getParts(array(0, 1)) == 'page/about',
		),
	
		array(
			'title' => 'Обратная связь',
			'href' => href($paths['page/feedback']),
			'active' => $r->getParts(array(0, 1)) == 'page/feedback',
		),
	
		array(
			'title' => 'Регистрация',
			'href' => href($paths['user/profile/registration']),
			'display' => !$user->isLogged(),
			'active' => $r->getParts(array(0, 1, 2)) == 'user/profile/registration',
		),
	
		array(
			'title' => 'Профиль',
			'href' => href($paths['user/profile/edit']),
			'display' => $user->isLogged(),
			'active' => $r->getParts(array(0, 1, 2)) == 'user/profile/edit',
		),
	
		array(
			'title' => 'Адм. панель',
			'href' => href('admin'),
			'attrs' => 'class="external-link"',
			'active' => false,
		),
	
	)
);

?>