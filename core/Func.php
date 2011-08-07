<?php

// ФУНКЦИЯ AUTOLOAD
function __autoload($className){
	
	// индекс всех элементов из CORE (кроме App и Func),
	// а так же всех компонентов.
	static $filesIndex = array(
		'Common' 			=> 'core/Common.php',
		'db' 				=> 'core/Db.php',
		'DbAdapter_mysql'	=> 'core/DbAdapters/mysql.php',
		'DbAdapter_sqlite'	=> 'core/DbAdapters/sqlite.php',
		'CommonViewer' 		=> 'core/CommonViewer.php',
		'Controller' 		=> 'core/Controller.php',
		'Error' 			=> 'core/Error.php',
		'GenericObject' 	=> 'core/GenericObject.php',
		'GenericObjectCollection' => 'core/GenericObject.php',
		'ImageMaster'		=> 'core/ImageMaster.php',
		'Messenger' 		=> 'core/Messenger.php',
		'Paginator' 		=> 'core/Paginator.php',
		'UserStatistics'	=> 'core/UserStatistics.php',
		'UserStatisticsCollection' => 'core/UserStatistics.php',
		'Validator' 		=> 'core/Validator.php',
		'YDate' 			=> 'core/YDate.php',
		'CsvParser' 		=> 'core/CsvParser.php',
		'YArray' 			=> 'core/YArray.php',
		'Sorter'			=> 'core/Sorter.php',
		'Exception403'		=> 'core/Exception.php',
		'Exception404'		=> 'core/Exception.php',
		'FormBuilder'		=> 'core/FormBuilder.php',
		'Debugger'			=> 'core/Debugger.php',
		
		'BackendViewer'		=> 'components/BackendViewer.component.php',
		'Def'				=> 'components/Def.component.php',
		'FrontendViewer'	=> 'components/FrontendViewer.component.php',
		'Request' 			=> 'components/Request.component.php',
		'User' 				=> 'components/User.component.php',
		'UserCollection'	=> 'components/User.component.php',
		'CurUser' 			=> 'components/CurUser.component.php',
		'Page' 				=> 'components/Page.component.php',
		'PageCollection'	=> 'components/Page.component.php',
	);
	
	// поиск по индексу
	if(isset($filesIndex[$className])){
		require(FS_ROOT.$filesIndex[$className]);
		return;
	}
	
	// библиотеки
	if(strpos($className, 'Com_') === 0){
		// преобразование Com_Auth_Model в /components/Auth/Model.php
		$fname = FS_ROOT.'components/'.str_replace(array('Com_', '_'), array('', '/'), $className).'.php';
		if(file_exists($fname))
			require($fname);
		return;
	}
	
	
	// контроллер
	if(strpos($className, 'Controller')){
	
		$fname = FS_ROOT.'controllers/'.str_replace('Controller', '.controller.php', $className);
		if(file_exists($fname))
			require($fname);
		return;
	}
	
	else{
		
		// модель
		$fileName = FS_ROOT.'models/'.$className.'.model.php';
		if(file_exists($fileName)){
			require($fileName);
			return;
		}
		
		// коллекция
		$fileName = FS_ROOT.'models/'.str_replace('Collection', '', $className).'.model.php';
		if(file_exists($fileName)){
			require($fileName);
			return;
		}
	}
	
}

// ФУНКЦИЯ GETVAR
function getVar(&$varname, $defaultVal = '', $type = ''){

	if(!isset($varname))
		return $defaultVal;
	
	if(strlen($type))
		settype($varname, $type);
	
	return $varname;
}

