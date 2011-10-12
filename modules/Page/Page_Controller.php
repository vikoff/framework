<?php

class Page_Controller extends Controller{
	
	/** имя модуля */
	const MODULE = 'page';
	
	const DEFAULT_VIEW = 'main';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Page/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'view';
	
	/** ассоциация методов контроллера с ресурсами */
	public $methodResources = array(
		'display_view' => 'view',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	/** ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ */
	public function display($params){
		
		// вместо метода передается идентификатор страницы
		// а метод всегда только view
		array_unshift($params, 'view');
		
		return parent::display($params);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY VIEW */
	public function display_view($params = array()){
		
		$pageAlias = getVar($params[0], self::DEFAULT_VIEW);
		$topMenuActiveItem = '';
		
		switch($pageAlias){
			case 'main': $topMenuActiveItem = 'main'; break;
			case 'contacts': $topMenuActiveItem = 'contacts'; break;
		}
		
		$variables = Page_Model::LoadByAlias($pageAlias)->GetAllFieldsPrepared();
		FrontendLayout::get()
			->setTitle($variables['title'])
			->setTopMenuActiveItem($topMenuActiveItem)
			->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
			->render();
	}
	
}

?>