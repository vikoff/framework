<?php

$r = Request::get();
$topMenuHref = $this->_data['topMenu']->activeItem['href'];
$items = array();

switch($topMenuHref){
	
	// CONTENT //
	case 'admin/content':
		$items = array(
			array(
				'title' => 'Страницы',
				'href' => $topMenuHref.'/page',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'page',
			),
		
			array(
				'title' => 'Тестовые сущности',
				'href' => $topMenuHref.'/test-item',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'test-item',
			),
		);
		break;
	
	// CONFIG //
	case 'admin/config':
		$items = array(
			array(
				'title' => 'Псевдонимы',
				'href' => $topMenuHref.'/alias',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'alias',
			),
		);
		break;
		
	// USERS //
	case 'admin/users':
		$items = array(
			array(
				'title' => 'Список пользователей',
				'href' => $topMenuHref.'/list',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => in_array($r->getParts(2), array('', 'list')),
			),
		
			array(
				'title' => 'Создание пользователя',
				'href' => $topMenuHref.'/create',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'create',
			),
		
			array(
				'title' => 'Блокировки',
				'href' => $topMenuHref.'/ban',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'ban',
			),
		);
		break;
		
	// MODULES //
	case 'admin/modules':
		$items = array(
			array(
				'title' => 'Получение данных о&nbsp;модулях',
				'href' => $topMenuHref.'/read-config',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'read-config',
			),
		);
		break;
		
	// ROOT //
	case 'admin/root':
		$items = array(
			array(
				'title' => 'Статистика посещений',
				'href' => $topMenuHref.'/user-statistics',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'user-statistics',
			),
		
			array(
				'title' => 'Лог ошибок',
				'href' => $topMenuHref.'/error-log',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'error-log',
			),
		);
		break;
		
	// SQL //
	case 'admin/sql':
		$items = array(
			array(
				'title' => 'Консоль',
				'href' => $topMenuHref.'/console',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'console',
			),
		
			array(
				'title' => 'Создание дампа БД',
				'href' => $topMenuHref.'/make-dump',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'make-dump',
			),
		
			array(
				'title' => 'Загрузка дампа БД',
				'href' => $topMenuHref.'/load-dump',
				'allowedRoles' => null,
				'deniedRoles' => null,
				'active' => $r->getParts(2) == 'load-dump',
			),
		);
		break;
}

return array (
	'name' => 'backend-left',
	'items' => $items
);

?>