<?

class Admin_SqlController extends Controller {
	
	const TPL_PATH = 'modules/Admin/templates/sql/';
	
	protected $_defaultBackendDisplay = 'index';
	
	public $permissions = array(
		'display_index' => PERMS_ADMIN,
		'display_console' => PERMS_ADMIN,
		'display_make_dump' => PERMS_ADMIN,
		
	);
	
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
}

?>