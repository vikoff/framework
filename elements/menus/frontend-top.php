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
$paths = array_merge( $paths, db::get()->fetchPairs($sql) );

return array (

	'name' => 'frontend-top',
	
	'items' => array(
	
		array(
			'id' => 'main',
			'title' => 'Главная',
			'href' => href($paths['page/main']),
			'active' => in_array($r->getParts(array(0, 1)), array('', 'page', 'page/main')),
		),
	
		array(
			'id' => 'test',
			'title' => 'Тест',
			'href' => href($paths['page/test']),
			'active' => $r->getParts(array(0, 1)) == 'page/test',
		),
	
		array(
			'id' => 'about',
			'title' => 'О Сайте',
			'href' => href($paths['page/about']),
			'active' => $r->getParts(array(0, 1)) == 'page/about',
		),
	
		array(
			'id' => 'feedback',
			'title' => 'Обратная связь',
			'href' => href($paths['page/feedback']),
			'active' => $r->getParts(array(0, 1)) == 'page/feedback',
		),
	
		array(
			'id' => 'registration',
			'title' => 'Регистрация',
			'href' => href($paths['user/profile/registration']),
			'display' => !$user->isLogged(),
			'active' => $r->getParts(array(0, 1, 2)) == 'user/profile/registration',
		),
	
		array(
			'id' => 'profile',
			'title' => 'Профиль',
			'href' => href($paths['user/profile/edit']),
			'display' => $user->isLogged(),
			'active' => $r->getParts(array(0, 1, 2)) == 'user/profile/edit',
		),
	
		array(
			'id' => 'admin',
			'title' => 'Адм. панель',
			'href' => href('admin'),
			'attrs' => 'class="external-link"',
			'active' => false,
		),
	
	)
);

?>