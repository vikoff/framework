<?php

class UserStatistics_Controller extends Controller {
	
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/UserStatistics/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = FALSE;
	
	/** ассоциация методов контроллера с ресурсами */
	public $methodResources = array(
		'ajax_save_client_side' => 'public',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		return TRUE;
	}
	
	/** ПОЛУЧИТЬ ИМЯ КЛАССА */
	public function getClass(){
		return __CLASS__;
	}
	
	//////////////////
	////// AJAX //////
	//////////////////
	
	// AJAX SAVE CLIENT SIDE
	public function ajax_save_client_side($params = array()){
		
		if(
			preg_match('/^[\w ]{0,25}$/', getVar($_POST['browser_name'])) &&
			preg_match('/^[\d\.]{0,25}$/', getVar($_POST['browser_version']))
		){
			UserStatistics_Model::get()->saveClientSideStatistics(
				$_POST['browser_name'],
				$_POST['browser_version'],
				getVar($_POST['screen_width'], 0, 'int'),
				getVar($_POST['screen_height'], 0, 'int')
			);
			echo 'ok';
		}else{
			echo 'Недопустимые символы в имени или версии браузера';
		}
	}
	
}

?>