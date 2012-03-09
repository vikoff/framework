<?php

/** ФУНКЦИЯ AUTOLOAD */
function __autoload($className){
	
	// индекс всех элементов из CORE (кроме App и Func),
	// а так же всех компонентов.
	static $filesIndex = array(
		'Tools' 			=> 'core/Tools.php',
		'db' 				=> 'core/Db.php',
		'Layout'			=> 'core/Layout.php',
		'Debugger'			=> 'core/Debugger.php',
		'DbAdapter_mysql'	=> 'core/DbAdapters/mysql.php',
		'DbAdapter_sqlite'	=> 'core/DbAdapters/sqlite.php',
		'DbAdapter_pdo_sqlite'	=> 'core/DbAdapters/pdo_sqlite.php',
		'Controller' 		=> 'core/Controller.php',
		'ActiveRecord' 		=> 'core/ActiveRecord.php',
		'ARCollection' 		=> 'core/ActiveRecord.php',
		'ImageMaster'		=> 'core/ImageMaster.php',
		'Messenger' 		=> 'core/Messenger.php',
		'Paginator' 		=> 'core/Paginator.php',
		'Validator' 		=> 'core/Validator.php',
		'YDate' 			=> 'core/YDate.php',
		'CsvParser' 		=> 'core/CsvParser.php',
		'YArray' 			=> 'core/YArray.php',
		'Sorter'			=> 'core/Sorter.php',
		'Exception403'		=> 'core/Exception.php',
		'Exception404'		=> 'core/Exception.php',
		'HtmlForm'			=> 'core/HtmlForm.php',
		
		'Def'				=> 'components/Def.component.php',
		
		/* NEW */
		
		'Config'			=> 'includes/Config.php',
		'CurUser' 			=> 'includes/CurUser.php',
		'Request' 			=> 'includes/Request.php',
		'Debugger'			=> 'includes/Debugger.php',
		
		'FrontendLayout' 	=> 'layouts/FrontendLayout.php',
		'BackendLayout' 	=> 'layouts/BackendLayout.php',
		
	);
	
	// поиск по индексу
	if(isset($filesIndex[$className])){
		require(FS_ROOT.$filesIndex[$className]);
		return;
	}
	
	/*
	MODULES, LAYOUTS
	
	Page_Controller, Page_Model, Page_Collection, Page_Forms_Form1

	Page/
		Forms/
			Form1.php
		PageModel.php
		PageController.php
	*/
	if(strpos($className, '_')){
		
		list($module, $classIdentifier) = explode('_', $className, 2);
		
		// класс ModuleName_Collection всегда лежит в файле ModuleName_Model.php
		if(substr($classIdentifier, -10) == 'Collection')
			$classIdentifier = substr($classIdentifier, 0, -10).'Model';
		
		$path = 'modules/'.$module.'/';
		
		if(strpos($classIdentifier, '_')){
			$path .= str_replace('_', DIRECTORY_SEPARATOR, $classIdentifier).'.php';
		}else{
			$path .= $module.'_'.$classIdentifier.'.php';
		}
		
		// echo $path.'<hr>';
		require(FS_ROOT.$path);
	}
	
}

?>