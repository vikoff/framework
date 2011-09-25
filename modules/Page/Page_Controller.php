<?

class Page_Controller extends Controller{
	
	const DEFAULT_VIEW = 'main';
	
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Page/templates/';
	const MODULE = 'page';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'view';
	
	// права на выполнение методов контроллера
	public $resources = array(
		'display_view' => 'view',
	);
	
	public function getResourcePermission($resource){
		
		return ACL_Model::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	/** ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ */
	public function display($params){
		
		// вместо метода передается идентификатор страницы
		// а метод всегда только view
		array_unshift($params, 'view');
		
		parent::display($params);
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
	
	
	////////////////////
	////// OTHER  //////
	////////////////////
	
	public function getClass(){
		return __CLASS__;
	}
	
}

?>