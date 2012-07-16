<?

class Admin_SqlController extends Controller {
	
	const TPL_PATH = 'modules/Admin/templates/sql/';
	const MODULE = 'admin';
	
	protected $_displayIndex = 'tables';
	
	public $methodResources = array(
	
		'display_console' => 'sql',
		'display_tables' => 'sql',
		'display_make_dump' => 'sql',
		'display_load_dump' => 'sql',
		
		'ajax_get_tables' => 'sql',
		
		'action_drop_table' => 'sql',
		'action_make_dump' => 'sql',
		'action_load_dump' => 'sql',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return User_Acl::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	public function display_console($params = array()){
		
		$variables = array();
		$query = getVar($_POST['query']);
		$variables['query'] = $query;
		
		if($query){
			
			$model = new Admin_Model();
			$variables['data'] = $model->execSql($query);
			// echo '<pre>'; print_r($variables); die;
			$variables['sql_error'] = db::get()->hasError() ? db::get()->getError() : '';
		}
		
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'console.php', $variables)
			->render();
	}
	
	public function display_tables($params = array()){
		
		$db = db::get();
		$table = isset($params[0]) ? $params[0] : null;
		$action = isset($params[1]) ? $params[1] : 'view';

		if ($table) {

			switch ($action) {
				case 'delete':
					$variables = array('table' => $table);
					BackendLayout::get()
						->addBreadcrumb('Удаление таблицы '.$table)
						->addContentLink('admin/sql/tables', 'Вернуться к списку таблиц')
						->addContentLink('admin/sql/tables/'.$table, 'Вернуться к таблице '.$table)
						->setContentPhpFile(self::TPL_PATH.'table_delete.php', $variables)
						->render();
					break;
				case 'show-create':
					$variables = array('table' => $table);
					BackendLayout::get()
						->prependTitle('show create table '.$table)
						->addBreadcrumb('show create table '.$table)
						->addContentLink('admin/sql/tables', 'Вернуться к списку таблиц')
						->addContentLink('admin/sql/tables/'.$table, 'Вернуться к таблице '.$table)
						->setContent('<p><pre style="-o-tab-size: 4;">'.$db->showCreateTable($table).'</pre></p>')
						->render();
					break;
				case 'view':
					$model = new Admin_Model();

					$variables = array(
						'table' => $table,
						'tableData' => $model->getTableData($table),
						'tags' => isset($_GET['tags']) ? $_GET['tags'] : 'strip',
						'len' => isset($_GET['len']) ? $_GET['len'] : '500',
					);
					
					// echo '<pre>'; print_r($variables); die;
					BackendLayout::get()
						->addBreadcrumb('Просмотр таблицы '.$table)
						->addContentLink('admin/sql/tables', 'Вернуться к списку таблиц')
						->setContentPhpFile(self::TPL_PATH.'table_view.php', $variables)
						->render();
			}
		} else {

			$variables = array(
				'tables' => $db->showTables(),
			);
			
			BackendLayout::get()
				->setContentPhpFile(self::TPL_PATH.'tables.php', $variables)
				->render();
		}
	}
	
	public function display_make_dump($params = array()){
		
		$db = db::get();
		
		$variables = array(
			'databases' => $db->showDatabases(),
			'curDatabase' => $db->getDatabase(),
			'encoding' => $db->getEncoding(),
		);
		
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'make_dump.php', $variables)
			->render();
	}
	
	public function display_load_dump($params = array()){
		
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'load_dump.php')
			->render();
	}
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	public function action_drop_table($params = array()){

		$table = getVar($_POST['table']);
		if (!$table) {
			Messenger::get()->addError('Таблица не найдена');
			return false;
		} else {
			db::get()->query('DROP TABLE '.$table);
			$this->setRedirectUrl('admin/sql/tables');
			Messenger::get()->addSuccess('Таблица '.$table.' удалена');
			return TRUE;
		}
	}

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
	////// AJAX   //////
	////////////////////
	
	// AJAX GET TABLES BY DB
	public function ajax_get_tables($params = array()){
		
		$dbName = getVar($_POST['db']);
		if(empty($dbName))
			return '';
		
		$db = db::get();
		$db->selectDb($dbName);
		$tables = $db->showTables();
		if ($tables)
			sort($tables);
		echo json_encode($tables);
	}
	
}

?>