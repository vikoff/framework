<?

class Admin_Controller extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'modules/Admin/templates/';
	
	const MODULE = 'admin';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'content';
	
	// права на выполнение методов контроллера
	public $permissions = array(
	
		'display_content'	=> PERMS_ADMIN,
		'display_users' 	=> PERMS_ADMIN,
		'display_modules' 	=> PERMS_ADMIN,
		'display_root' 		=> PERMS_ADMIN,
		
		'actionSave' 		=> PERMS_ADMIN,
		'actionDelete' 		=> PERMS_ADMIN,
	);
	
	public $_proxy = array(
		'sql' => 'Admin_SqlController',
		'modules' => 'Admin_ModulesController',
	);
	
	public function init(){
	
		BackendLayout::get()->setTitle('Административная панель');
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY CONTENT
	public function display_content($params = array()){
		
		$viewer = BackendLayout::get();
		$viewer
			->setTopMenuActiveItem('content')
			->setLeftMenuType('content')
			->setBreadcrumbs('auto');
		
		// display index
		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'content_index.tpl')
				->render();
			exit();
		}
		
		$module = array_shift($params);
		
		if(!App::get()->isModule($module, TRUE)){
			BackendLayout::get()->error404('Контроллер не найден');
			exit();
		}
		
		$viewer->setLeftMenuActiveItem($module);
		App::get()->getModule($module, TRUE)->display($params);
	}

	// DISPLAY USERS
	public function display_users($params = array()){
			
		$viewer = BackendLayout::get();
		$viewer
			->setTopMenuActiveItem('users')
			->setLeftMenuType('users');
			
		
		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'users_index.tpl')
				->setBreadcrumbs('auto')
				->render();
			exit();
		}
		
		$controllerInstance = new UserController($adminMode = TRUE);
		$displayMethodIdentifier = array_shift($params);
		
		$controllerInstance->performDisplay($displayMethodIdentifier, $params);
		
		$viewer
			->setLeftMenuActiveItem($displayMethodIdentifier)
			->setBreadcrumbs('auto')
			->render();
	}
	
	// DISPLAY ROOT
	public function display_root($params = array()){
		
		$section = getVar($params[0]);
		
		$viewer = BackendLayout::get();
		$viewer
			->setTopMenuActiveItem('root')
			->setLeftMenuType('root')
			->setLeftMenuActiveItem($section)
			->setBreadcrumbs('auto');

		if(!$section){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'root_index.tpl')
				->render();
			exit();
		}
		
		switch($section){
			
			case 'error-log':
				$this->snippet_error_log();
				break;
				
			default:
				$controllerClass = App::getControllerClassName(array_shift($params));
				$displayMethodIdentifier = array_shift($params);
				
				if(!$controllerClass){
					BackendLayout::get()->error404('Контроллер не найден');
					exit();
				}
				
				$controllerInstance = new $controllerClass($adminMode = TRUE);
				$controllerInstance->performDisplay($displayMethodIdentifier, $params);
		}
		
		$viewer->render();
	}
	
	//////////////////////
	////// SNIPPETS //////
	//////////////////////
	
	// SNIPPET ERROR LOG
	public function snippet_error_log(){
		
		$collection = new ErrorCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
		);
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'root_error_log.php', $variables);
	}
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	public function action_smarty_clear_compiled_tpl($params = array()){
	
		$instanceId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$instance = new RootController($instanceId);
		
		if($instance->Save($_POST)){
			App::addSuccessMessage('Запись сохранена');
			App::forceDisplay();
			return TRUE;
		}else{
			App::addErrorMessage('Не удалось сохранить запись:<div style="margin-left: 10px; font-size: 13px;">'.$instance->getError().'</div>');
			self::displayEdit($params);
			return FALSE;
		}
	}
	
	// DELETE OLD ERRORS
	public function action_delete_old_errors($params = array()){
		
		$expire = getVar($_POST['expire']);
		
		$expiredValues = array(
			'1day'   => 86400,
			'1week'  => 604800,
			'1month' => 2592000,
			'3month' => 7776000,
			'6month' => 15552000,
			'9month' => 23328000,
			'1year'  => 31536000);
			
		if(!isset($expiredValues[$expire])){
			Messenger::get()->addError('Неверный временной промежуток.');
			return FALSE;
		}
		
		UserStatistics::get()->deleteOldStatistics($expiredValues[$expire]);
		Messenger::get()->addSuccess('Старая статистика удалена.');
		return TRUE;
	}
	
	////////////////////
	////// AJAX   //////
	////////////////////
	
	// ОБРАБОТЧИК 403
	public function error403handler($method, $line = 0){
		
		BackendLayout::get()->showLoginPage();
	}
	
	
	////////////////////
	////// OTHER  //////
	////////////////////
	
	public function getClass(){
		return __CLASS__;
	}

}

?>