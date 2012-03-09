<?php

class Page_Controller extends Controller{
	
	/** имя модуля */
	const MODULE = 'page';
	
	const DEFAULT_VIEW_GUEST = 'main';
	const DEFAULT_VIEW_USER = 'main-howto';
	
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
		
		return User_Acl::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	/** ПОЛУЧИТЬ ИМЯ КЛАССА */
	public function getClass(){
		return __CLASS__;
	}
	
	/** ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ */
	public function display($params){
		
		// вместо метода передается идентификатор страницы
		// а метод всегда только view
		array_unshift($params, 'view');
		
		return parent::display($params);
	}
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY VIEW */
	public function display_view($pageAlias = null){
		
		if (empty($pageAlias))
			$pageAlias = CurUser::get()->isLogged() ? self::DEFAULT_VIEW_USER : self::DEFAULT_VIEW_GUEST;
		
		$variables = Page_Model::LoadByAlias($pageAlias)->GetAllFieldsPrepared();
		FrontendLayout::get()
			->setTitle($variables['title'])
			->setContentPhpFile(self::TPL_PATH.'view.php', $variables)
			->render();
	}
	
}

?>