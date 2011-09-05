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
	
	/*
	МОДУЛИ

	new Page_Model;
	new Page_Forms_Form1;

	Page/
		Forms/
			Form1.php
			Form2.php
			Form3.php
		PageModel.php
		PageController.php
	*/
	if(strpos($name, '_')){
		
		list($module, $classIdentifier) = explode('_', $name, 2);
		
		// класс [SomeClass]Collection всегда лежит в файле [SomeClass].php
		if(substr($classIdentifier, -10) == 'Collection')
			$classIdentifier = substr($classIdentifier, 0, -10);
			
		$path = FS_ROOT.'modules/'.$module.'/';
		if(strpos($classIdentifier, '_')){
			$path .= str_replace('_', DIRECTORY_SEPARATOR, $classIdentifier).'.php';
		}else{
			$path .= $module.$classIdentifier.'.php';
		}
		
		require($path);
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

