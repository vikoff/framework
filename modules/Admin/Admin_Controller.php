<?

class Admin_Controller extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'Admin/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = FALSE;
	protected $_defaultBackendDisplay = 'content';
	
	// права на выполнение методов контроллера
	public $permissions = array(
	
		'display_content'	=> PERMS_ADMIN,
		'display_users' 	=> PERMS_ADMIN,
		'display_root' 		=> PERMS_ADMIN,
		
		'action_sql_dump' 	=> PERMS_ROOT,
		'actionSave' 		=> PERMS_ADMIN,
		'actionDelete' 		=> PERMS_ADMIN,
		
		'ajax_get_tables_by_db' => PERMS_ROOT,
	);
	
	public function init(){
	
		$this->_adminMode = TRUE;
		BackendLayout::get()->setTitle('Административная панель');
	}
	
	// МОДИФИКАЦИЯ ИМЕНИ МЕТОДА
	public function modifyMethodName(&$method){
		// для этого контроллера модификация имен не требуется
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY CONTENT
	public function display_content($params = array()){
		
		$viewer = BackendLayout::get();
		$viewer
			->setTopMenuActiveItem('content')
			->setLeftMenuType('content');

		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'content_index.tpl')
				->setBreadcrumbs('auto')
				->render();
			exit();
		}
		
		$controllerIdentifier = array_shift($params);
		$controllerClass = App::getControllerClassName($controllerIdentifier);
		$displayMethodIdentifier = array_shift($params);
		
		if(!$controllerClass){
			BackendLayout::get()->error404('Контроллер не найден');
			exit();
		}
		
		$controllerInstance = new $controllerClass($adminMode = TRUE);
		$controllerInstance->performDisplay($displayMethodIdentifier, $params);
		$viewer
			->setLeftMenuActiveItem($controllerIdentifier)
			->setBreadcrumbs('auto')
			->render();
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
			
			case 'sql-console':
				
				$this->snippet_sql_console();
				break;
			
			case 'sql-dump':
				
				$this->snippet_sql_dump();
				break;
			
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
	
	// SNIPPET SQL CONSOLE
	public function snippet_sql_console(){
		
		$variables = array();
		$query = stripslashes(getVar($_POST['query']));
		$variables['query'] = $query;
		
		if($query){
		
			$variables['data'] = $this->_execSql($query);
			$variables['sql_error'] = db::get()->hasError() ? db::get()->getError() : '';
		}
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'root_sql_console.php', $variables);
	}
	
	// SNIPPET SQL DUMP
	public function snippet_sql_dump(){
		
		$db = db::get();
		
		$variables = array(
			'databases' => $db->showDatabases(),
			'curDatabase' => $db->getDatabase(),
			'encoding' => $db->getEncoding(),
		);

		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'root_sql_dump.php', $variables);
	}
	
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
	
	public function action_sql_dump(){
		
		$tblInputType = $_POST['tables-input-type'];
		$database = getVar($_POST['database'], null);
		$encoding = getVar($_POST['encoding'], null);
		$tables = null;
		$db = db::get();
		
		if($tblInputType == 'text'){
			$tables = explode(',', getVar($_POST['tables-text']));
			foreach($tables as &$tbl)
				$tbl = trim($tbl);
		}
		elseif($tblInputType == 'select'){
			$tables = getVar($_POST['tables-select']);
		}
		
		// установка корировки соединения (если задана)
		if(!empty($encoding))
			$db->setEncoding($encoding);
		
		$db->makeDump($database, $tables);
		exit;
	}
	
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
	////// OTHER  //////
	////////////////////
	
	////////////////////
	////// AJAX   //////
	////////////////////
	
	// AJAX GET TABLES BY DB
	public function ajax_get_tables_by_db($params = array()){
		
		$dbName = getVar($_POST['db']);
		if(empty($dbName))
			return '';
		
		$db = db::get();
		$db->selectDb($dbName);
		echo json_encode($db->showTables());
	}
	
	// EXEC SQL (FORM SQL CONSOLE)
	private function _execSql($sqls){
		
		$db = db::get();
		$sqls = preg_replace('/;\r\n/', ";\n", $sqls);
		$sqlsArr = explode(";\n", $sqls);
		$results = array();
		
		$db->enableErrorHandlingMode();
		
		foreach($sqlsArr as $sql){
			$sql = trim($sql);
			if(!empty($sql))
				$results[] = $db->getAll($sql, array());
		}
		
		$db->disableErrorHandlingMode();
		
		return $results;
	}
	
	// ОБРАБОТЧИК 403
	public function error403handler($method, $line = 0){
		
		BackendLayout::get()->showLoginPage();
	}

}

?>