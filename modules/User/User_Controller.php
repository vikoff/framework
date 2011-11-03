<?php

class User_Controller extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/User/templates/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = FALSE;
	
	protected $_proxy = array(
		'profile' => 'User_ProfileController',
	);
	
	// права на выполнение методов контроллера
	public $methodResources = array(
		'display_view'	=> 'edit',
		
		'action_paginator_set_items_per_page' => 'public',
		'ajax_check_login_unique' => 'public',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		echo 'hello';
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION PAGINATOR_SET_ITEMS_PER_PAGE
	public function action_paginator_set_items_per_page($params = array()){
		
		$num = $_POST['num'];
		if(isset(Paginator::$itemsPerPageVariants[$num]))
			$_SESSION['paginator-items-per-page'] = $num;
		else
			trigger_error('invalid num: '.$num);
	}
	
	//////////////////
	////// AJAX //////
	//////////////////
	
	// ПРОВЕРКА ЛОГИНА НА УНИКАЛЬНОСТЬ
	public function ajax_check_login_unique($params = array()){
	
		echo User_Model::isLoginInUse(getVar($_GET['login'])) ? 'false' : 'true';
	}
	
}

?>