<?php

return array (
  'admin' => 
  array (
    'name' => 'admin',
    'title' => 'Панель управления',
    'controller' => 'Admin_Controller',
    'arrayParams' => true,
    'resources' => 
    array (
      'content' => 'Редактирование страниц',
      'users' => 'Управление пользователями',
      'config' => 'Конфигурирование',
      'manage' => 'Администрирование сайта',
      'sql' => 'SQL-утилиты',
    ),
  ),
  'alias' => 
  array (
    'name' => 'alias',
    'title' => 'Псевдонимы',
    'adminController' => 'Alias_AdminController',
    'arrayParams' => true,
    'resources' => 
    array (
      'public' => 'Общедоступные действия',
      'view' => 'Просмотр данных пользователя',
      'edit' => 'Редактирование данных',
    ),
  ),
  'error' => 
  array (
    'name' => 'error',
    'title' => 'Ошибки сайта',
    'adminController' => 'Admin_Controller',
    'resources' => 
    array (
      'view' => 'Просмотр',
      'edit' => 'Редактирование',
    ),
  ),
  'page' => 
  array (
    'name' => 'page',
    'title' => 'Страницы',
    'controller' => 'Page_Controller',
    'adminController' => 'Page_AdminController',
    'dependencies' => 
    array (
      0 => 'alias',
    ),
    'resources' => 
    array (
      'view' => 'Просмотр страниц',
      'edit' => 'Редактирование страниц',
      'root' => 'Root-привилегии (установка ограничений, запрет удаления)',
    ),
  ),
  'testItem' => 
  array (
    'name' => 'testItem',
    'title' => 'Тестовые сущности',
    'controller' => 'TestItem_Controller',
    'adminController' => 'TestItem_AdminController',
    'resources' => 
    array (
      'view' => 'Просмотр',
      'edit' => 'Редактирование',
    ),
  ),
  'user' => 
  array (
    'name' => 'user',
    'title' => 'Пользователи',
    'controller' => 'User_Controller',
    'adminController' => 'User_AdminController',
    'resources' => 
    array (
      'public' => 'Общедоступные действия',
      'own-view' => 'Просмотр собственных данных',
      'own-edit' => 'Редактирование собственных данных',
      'view' => 'Просмотр данных других пользователей',
      'edit' => 'Редактирование данных других пользователей',
    ),
  ),
  'userStatistics' => 
  array (
    'name' => 'userStatistics',
    'title' => 'Статистика посещений',
    'controller' => 'UserStatistics_Controller',
    'adminController' => 'UserStatistics_AdminController',
    'arrayParams' => true,
    'resources' => 
    array (
      'public' => 'Общедоступные действия',
      'view' => 'Просмотр данных',
      'edit' => 'Редактирование данных',
    ),
  ),
);

?>