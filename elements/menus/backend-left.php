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
				'active' => $r->getParts(2) == 'page',
			),
		
			array(
				'title' => 'Тестовые сущности',
				'href' => $topMenuHref.'/test-item',
				'active' => $r->getParts(2) == 'test-item',
			),
		);
		break;
	
	// CONFIG //
	case 'admin/config':
		$items = array(
			array(
				'title' => 'Модули',
				'href' => $topMenuHref.'/modules',
				'active' => $r->getParts(2) == 'modules',
			),
			array(
				'title' => 'Псевдонимы',
				'href' => $topMenuHref.'/alias',
				'active' => $r->getParts(2) == 'alias',
			),
			array(
				'title' => 'Мета-теги',
				'href' => $topMenuHref.'/meta',
				'active' => $r->getParts(2) == 'meta',
			),
		);
		break;
		
	// USERS //
	case 'admin/users':
		$items = array(
			array(
				'title' => 'Список пользователей',
				'href' => $topMenuHref.'/list',
				'active' => in_array($r->getParts(2), array('', 'list')),
			),
		
			array(
				'title' => 'Создание пользователя',
				'href' => $topMenuHref.'/create',
				'active' => $r->getParts(2) == 'create',
			),
		
			array(
				'title' => 'Управление ролями',
				'href' => $topMenuHref.'/roles',
				'active' => $r->getParts(2) == 'roles',
			),
		
			array(
				'title' => 'Блокировки',
				'href' => $topMenuHref.'/ban',
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
				'active' => $r->getParts(2) == 'read-config',
			),
		);
		break;
		
	// MANAGE //
	case 'admin/manage':
		$items = array(
			array(
				'title' => 'Статистика посещений',
				'href' => $topMenuHref.'/user-statistics',
				'active' => $r->getParts(2) == 'user-statistics',
			),
		
			array(
				'title' => 'Лог ошибок',
				'href' => $topMenuHref.'/error-log',
				'active' => $r->getParts(2) == 'error-log',
			),
		
			array(
				'title' => 'Снимок файловой системы',
				'href' => $topMenuHref.'/fs-snapshot',
				'active' => $r->getParts(2) == 'fs-snapshot',
			),
		);
		break;
		
	// SQL //
	case 'admin/sql':
		$items = array(
			array(
				'title' => 'Консоль',
				'href' => $topMenuHref.'/console',
				'active' => $r->getParts(2) == 'console',
			),
		
			array(
				'title' => 'Создание дампа БД',
				'href' => $topMenuHref.'/make-dump',
				'active' => $r->getParts(2) == 'make-dump',
			),
		
			array(
				'title' => 'Загрузка дампа БД',
				'href' => $topMenuHref.'/load-dump',
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