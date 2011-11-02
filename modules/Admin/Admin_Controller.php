<?

class Admin_Controller extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'modules/Admin/templates/';
	
	const MODULE = 'admin';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'content';
	
	// права на выполнение методов контроллера
	public $methodResources = array(
	
		'display_content'   => 'content',
		'display_config'    => 'content',
		'display_users'     => 'content',
		'display_modules'   => 'content',
		'display_root'      => 'content',
		
		'action_make_fs_snapshot' => 'content',
	);
	
	/**
	 * массив пар 'идентификатор метода' => 'Класс контроллера или Имя модуля'
	 * для проксирования запросов в другой контроллер/модуль
	 * если указан класс контроллера, то он должен принадлежать тому же модулю, что и текущий контроллер.
	 */
	public $_proxy = array(
		'sql' => 'Admin_SqlController',
		'users' => 'user',
		'modules' => 'Admin_ModulesController',
	);
	
	public function init(){
	
		BackendLayout::get()->setTitle('Административная панель');
	}
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	/**
	 * ВЫПОЛНЕНИЕ ДЕЙСТВИЯ
	 * @exception Exception - ловит стандартные исключения
	 * @exception Exception403 - ловит исключения 403
	 * @exception Exception404 - ловит исключения 404
	 * @param string $method - идентификатор метода
	 * @param string $redirectUrl - url, куда надо сделать редирект после успешного выполнения
	 * @return void
	 */
	public function action($params, $redirectUrl = null){
		
		// для проксируемых методов, и тех, которые идут непосредственно к Admin_Controller
		if(isset($this->_proxy[ getVar($params[0]) ]) || count($params) == 1)
			return parent::action($params, $redirectUrl);
		
		// запросы на бэкенд-контроллеры других модулей
		$app = App::get();
		$module = $app->prepareModuleName(array_shift($params));
		return $app->getModule($module, TRUE)->action($params, $redirectUrl);
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМЛЯР КОНТРОЛЛЕРА ДЛЯ ПРОКСИРОВАНИЯ */
	public function getProxyControllerInstance($proxy){
		
		if (!CurUser::get()->isLogged()){
			BackendLayout::get()->showLoginPage();
			exit;
		}
		
		return App::get()->isModule($proxy, TRUE)
			? App::get()->getModule($proxy, TRUE)
			: new $proxy($this->_config);
	}
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY CONTENT */
	public function display_content($params = array()){
		
		$viewer = BackendLayout::get();
		
		// display index
		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'content_index.tpl')
				->render();
			exit();
		}
		
		$app = App::get();
		$module = $app->prepareModuleName(array_shift($params));
		
		if(!$app->isModule($module, TRUE)){
			$this->error404handler('модуль <b>'.$module.'</b> не найден');
			exit();
		}
		
		if(!$app->getModule($module, TRUE)->display($params))
			$this->error404handler('недопустимое действие <b>'.getVar($params[0]).'</b> модуля <b>'.$module.'</b>');
	}
	
	/** DISPLAY CONFIG */
	public function display_config($params = array()){
		
		$viewer = BackendLayout::get();
		
		// display index
		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'content_index.tpl')
				->render();
			exit();
		}
		
		$app = App::get();
		$module = $app->prepareModuleName(array_shift($params));
		
		if(!$app->isModule($module, TRUE)){
			$this->error404handler('модуль <b>'.$module.'</b> не найден');
			exit();
		}
		
		if(!$app->getModule($module, TRUE)->display($params))
			$this->error404handler('недопустимое действие <b>'.getVar($params[0]).'</b> модуля <b>'.$module.'</b>');
	}

	/** DISPLAY USERS */
	public function display_users($params = array()){
			
		$viewer = BackendLayout::get();
		
		if(empty($params[0])){
			$viewer
				->setContentHtmlFile(self::TPL_PATH.'users_index.tpl')
				->render();
			exit();
		}
		
		$controllerInstance = new UserController($adminMode = TRUE);
		$displayMethodIdentifier = array_shift($params);
		
		$controllerInstance->performDisplay($displayMethodIdentifier, $params);
		
		$viewer->render();
	}
	
	/** DISPLAY ROOT */
	public function display_root($params = array()){
		
		$section = getVar($params[0]);
		
		$viewer = BackendLayout::get();

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
			
			case 'fs-snapshot':
				$this->snippet_fs_snapshot();
				break;
				
			default:
		
				$app = App::get();
				$module = $app->prepareModuleName(array_shift($params));
				
				if(!$app->isModule($module, TRUE)){
					$this->error404handler('модуль <b>'.$module.'</b> не найден');
					exit();
				}
				
				if(!$app->getModule($module, TRUE)->display($params))
					$this->error404handler('недопустимое действие <b>'.getVar($params[0]).'</b> модуля <b>'.$module.'</b>');
				}
	}
	
	//////////////////////
	////// SNIPPETS //////
	//////////////////////
	
	/** SNIPPET ERROR LOG */
	public function snippet_error_log(){
		
		$collection = new ErrorCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
		);
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'root_error_log.php', $variables);
	}
	
	public function snippet_fs_snapshot(){
		
		BackendLayout::get()
			->setContentPhpFile(self::TPL_PATH.'root_fs_snapshot.php')
			->render();
		
	}
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION MAKE FS SNAPSHOT */
	public function action_make_fs_snapshot(){
		
		$model = new Admin_Model();
		
		Tools::sendDownloadHeaders('fs_snapshot_'.date("Y-m-d_H-i").'.txt');
		$model->makeFsSnapshot(FS_ROOT, array('.git' => true));
		exit;
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