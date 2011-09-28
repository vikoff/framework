<?

class Admin_SqlController extends Controller {
	
	const TPL_PATH = 'modules/Admin/templates/sql/';
	const MODULE = 'admin';
	
	protected $_displayIndex = 'index';
	
	public $methodResources = array(
	
		'display_index' => 'sql',
		'display_console' => 'sql',
		'display_make_dump' => 'sql',
		'display_load_dump' => 'sql',
		
		'ajax_get_tables' => 'sql',
		
		'action_make_dump' => 'sql',
		'action_load_dump' => 'sql',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	public function display_index($params = array()){
		
		BackendLayout::get()
			->setTopMenuActiveItem('sql')
			->setLeftMenuType('sql')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'index.php')
			->render();
	}
	
	public function display_console($params = array()){
		
		$variables = array();
		$query = Tools::unescape(getVar($_POST['query']));
		$variables['query'] = $query;
		
		if($query){
			
			$model = new Admin_Model();
			$variables['data'] = $model->execSql($query);
			$variables['sql_error'] = db::get()->hasError() ? db::get()->getError() : '';
		}
		
		BackendLayout::get()
			->setTopMenuActiveItem('sql')
			->setLeftMenuType('sql')
			->setLeftMenuActiveItem('console')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'console.php', $variables)
			->render();
	}
	
	public function display_make_dump($params = array()){
		
		$db = db::get();
		
		$variables = array(
			'databases' => $db->showDatabases(),
			'curDatabase' => $db->getDatabase(),
			'encoding' => $db->getEncoding(),
		);
		
		BackendLayout::get()
			->setTopMenuActiveItem('sql')
			->setLeftMenuType('sql')
			->setLeftMenuActiveItem('make-dump')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'make_dump.php', $variables)
			->render();
	}
	
	public function display_load_dump($params = array()){
		
		BackendLayout::get()
			->setTopMenuActiveItem('sql')
			->setLeftMenuType('sql')
			->setLeftMenuActiveItem('load-dump')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'load_dump.php')
			->render();
	}
	
	////////////////////
	////// AJAX   //////
	////////////////////
	
	// AJAX GET TABLES BY DB
	public function ajax_get_tables($params = array()){
		
		$dbName = getVar($_POST['db']);
		if(empty($dbName))
			return '';
		
		$db = db::get();
		$db->selectDb($dbName);
		echo json_encode($db->showTables());
	}
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	public function action_make_dump($params = array()){
		
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
	
	public function action_load_dump($params = array()){
		
		if(!empty($_FILES['dump']) && file_exists($_FILES['dump']['tmp_name'])){
			
			$db = db::get();
			
			if($db->loadDump($_FILES['dump']['tmp_name'])){
				Messenger::get()->addSuccess('Дамп успешно загружен');
				return TRUE;
			}else{
				Messenger::get()->addError('Не удалось загрузить дамп', $db->getError());
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	
	////////////////////
	////// OTHER  //////
	////////////////////
	
	public function getClass(){
		return __CLASS__;
	}
	
}

?>