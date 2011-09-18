<?

class Admin_ModulesController extends Controller {
	
	const TPL_PATH = 'modules/Admin/templates/modules/';
	const MODULE = 'admin';
	
	protected $_displayIndex = 'index';
	
	public $permissions = array(
	
		'display_index'       => PERMS_ADMIN,
		'display_read_config' => PERMS_ADMIN,
		
		'action_read_config' => PERMS_ADMIN,
	);
	
	public function display_index($params = array()){
		
		BackendLayout::get()
			->setTopMenuActiveItem('modules')
			->setLeftMenuType('modules')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'index.php')
			->render();
	}
	
	public function display_read_config($params = array()){
		
		BackendLayout::get()
			->setTopMenuActiveItem('modules')
			->setLeftMenuType('modules')
			->setLeftMenuActiveItem('read-config')
			->setBreadcrumbs('auto')
			->setContentPhpFile(self::TPL_PATH.'read_config.php')
			->render();
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	public function action_read_config($params = array()){
		
		$modulesDir = FS_ROOT.'modules/';
		
		foreach(scandir($modulesDir) as $elm){
			
			if($elm == '.' || $elm == '..' || !is_dir($modulesDir.$elm))
				continue;
			
			if(file_exists($modulesDir.$elm.'/config.php'))
				;
			
			
		Messenger::get()->addSuccess('Конфигурация модулей обновлена');
	}
	
	
	////////////////////
	////// OTHER  //////
	////////////////////
	
	public function getClass(){
		return __CLASS__;
	}
	
}

?>