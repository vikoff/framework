<?

class Controller{
	
	/**
	 * массив пар 'идентификатор' => 'Имя контроллера'
	 * для проксирования запросов на указанное действие/отображение в другой контроллер
	 */
	protected $_proxy = array();
	
	/*
	 * Идентификатор метода, вызываемого по умолчанию для фронтенда.
	 * Идентификатор задается без префикса 'display_'.
	 * Значение 'list' - верно; 'display_list' - неверно.
	 * Должно быть указано явно в классах наследниках.
	 * @var string
	 */
	protected $_defaultFrontendDisplay = null;
	
	/*
	 * Идентификатор метода, вызываемого по умолчанию для бэкенда.
	 * Идентификатор задается без префикса 'admin_display_'.
	 * Значение 'list' - верно; 'admin_display_list' - неверно.
	 * Должно быть указано явно в классах наследниках
	 * @var string
	 */
	protected $_defaultBackendDisplay = null;
	
	/**
	 * URL для редиректа после выполнения действия (action).
	 * Назначается из переменной $_POST['redirect'],
	 * может быть изменен через аксессор $this->setRedirectUrl()
	 * в методе действия (action).
	 * редирект выполняется при ненулевом значении.
	 * @var mixed string|null
	 */
	protected $_redirectUrl = null;
	
	/**
	 * Принудительный редирект. Выполняется даже если метод действия
	 * вернул FALSE. Назначается через аксессор $this->forceRedirect()
	 * в методе действия (action).
	 * @var bool
	 */
	protected $_forceRedirect = FALSE;
	
	/**
	 * Тип проверки разрешений на выполнение методов контроллера.
	 * Допустимые значения:
	 *     'inline' - права указываются в php-коде
	 *     'db'     - права хранятся в таблице базы данных
	 * @var string
	 */
	public $permissionsType = 'inline';
	
	/**
	 * Группы разрешений.
	 * Имеют смысл только при self::$_permissionsType установелнном в режиме 'db'
	 * Этот массив хранит возможные группы действий и их тайтлы для редактирования
	 * например: array('edit' => 'Редактирование', 'read' => 'Просмотр' и т.д.)
	 * права на которые хранятся в базе данных (таблица group_perms)
	 * @var array
	 */
	public $permissionsGroups = array();
	
	/**
	 * Ассоциативный массив методов класса (action, display, ajax)
	 * и пользовательских прав, необходимых для выполнения этих методов
	 * @var array
	 */
	public $permssions = array();
	
	/**
	 * Заголовок контроллера
	 * Используется для хлебных крошек и прочих подобных случаев
	 * Доступ через акксессор self::getTitle()
	 * @var null|string
	 */
	protected $_title = null;
	
	
	public function __construct($adminMode = FALSE){
	
		$this->init();
	}
	
	/**
	 * НАЧАЛЬНАЯ ИНИЦИАЛИЗАЦИЯ КОНТРОЛЛЕРА
	 * метод может быть объявлен в наследниках класса для вызова каких-либо действий
	 */
	public function init(){}
	
	
	/** ДОСТАТОЧНО ЛИ ПРАВ ДЛЯ ВЫПОЛНЕНИЯ */
	public function hasPermission($method, $userperm){
		return (isset($this->permissions[$method]) && $this->permissions[$method] <= $userperm) ? TRUE : FALSE;
	}
	
	/** ПРОВЕРКА КОРРЕКТНОСТИ МЕТОДА */
	public function checkMethod(&$method){
			
		// если действие не найдено
		if(!method_exists($this, $method)){
			
			$this->error404handler(get_class().'::'.$method, __LINE__);
			return FALSE;
		}
		
		// если недостаточно прав
		elseif(!$this->hasPermission($method, USER_AUTH_PERMS)){
		
			$this->error403handler($method, __LINE__);
			return FALSE;
		}
		
		// если прошел проверки, то все ок
		return TRUE;
		
	}
	
	/** ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ */
	public function display($params){
		
		$method = array_shift($params);
		
		// если метод не указан, то выполняется метод по умолчанию
		if(!$method){
			$this->_displayIndex($params);
			return TRUE;
		}
		
		// проксирование на вложенный контроллер
		if(isset($this->_proxy[$method])){
			$controller = new $this->_proxy[$method] ();
			return $controller->display($params);
		}
		
		$method = $this->getDisplayMethodName($method);
		
		if(!$this->checkMethod($method, $params))
			return FALSE;
		
		App::get()->setPerformedDisplay($this->getConst('MODULE'), $method, $params);
		
		try{
			$this->$method($params);
		}
		catch(Exception404 $e){$this->error404handler($e->getMessage());}
		catch(Exception403 $e){$this->error403handler($e->getMessage());}
		catch(Exception $e){$this->errorHandler($e->getMessage());}
		
		return TRUE;
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
	
		$method = array_shift($params);
		
		// проксирование на вложенный контроллер
		if(isset($this->_proxy[$method])){
			$controller = new $this->_proxy[$method] ();
			return $controller->action($params);
		}
		
		$method = $this->getActionMethodName($method);
		
		// если метод не прошел проверку, запускается error handler
		// и дальнейший вывод прекращается
		if(!$this->checkMethod($method)){
			exit();
		}
		
		// назначение URL для редиректа (если задан).
		// назначается раньше выполнения метода, чтобы
		// быть доступным из него.
		$this->_redirectUrl = $redirectUrl;
		
		try{
			// выполнение метода
			if($this->$method() !== FALSE){
				
				// блокирование formcode
				App::lockFormCode($_POST['formCode']);
				
				// выполнение редиректа (если надо)
				if(!empty($this->_redirectUrl))
					App::redirect(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
			}else{
				if($this->_forceRedirect && !empty($this->_redirectUrl))
					App::redirect(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
			}
		}
		catch(Exception404 $e){$this->error404handler($e->getMessage());}
		catch(Exception403 $e){$this->error403handler($e->getMessage());}
		catch(Exception $e){$this->errorHandler($e->getMessage());}
		
		return TRUE;
	}
	
	/** ВЫПОЛНЕНИЕ AJAX */
	public function ajax($params){
		
		$method = array_shift($params);
		
		// проксирование на вложенный контроллер
		if(isset($this->_proxy[$method])){
			$controller = new $this->_proxy[$method] ();
			return $controller->ajax($params);
		}
		
		$method = $this->getAjaxMethodName($method);
		
		if(!$this->checkMethod($method, $params))
			return FALSE;
			
		try{
			$this->$method($params);
		}
		catch(Exception404 $e){$this->error404handler($e->getMessage());}
		catch(Exception403 $e){$this->error403handler($e->getMessage());}
		catch(Exception $e){$this->errorHandler($e->getMessage());}
		
		return TRUE;
	}
	
	/** ПОЛУЧИТЬ ИМЯ МЕТОДА ОТОБРАЖЕНИЯ ПО ИДЕНТИФИКАТОРУ */
	public function getDisplayMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'display_'.(strlen($method) ? strtolower(str_replace('-', '_', $method)) : 'default');
		return $method;
	}
	
	/** ПОЛУЧИТЬ ИМЯ МЕТОДА ДЕЙСТВИЯ ПО ИДЕНТИФИКАТОРУ */
	public function getActionMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'action_'.strtolower(str_replace('-', '_', $method));
		return $method;
	}
	
	/** ПОЛУЧИТЬ ИМЯ AJAX МЕТОДА ПО ИДЕНТИФИКАТОРУ */
	public function getAjaxMethodName($method){
	
		// преобразует строку вида 'any-Method-name' в 'any_method_name'
		$method = 'ajax_'.strtolower(str_replace('-', '_', $method));
		return $method;
	}
	
	// ЗАДАТЬ URL ДЛЯ РЕДИРЕКТА после выполнения действия (action)
	public function setRedirectUrl($url){
	
		$this->_redirectUrl = $url;
	}
	
	// ВЫПОЛНИТЬ РЕДИРЕКТ ПРИНУДИТЕЛЬНО
	public function forceRedirect($forceRedirect = TRUE){
		
		$this->_forceRedirect = $forceRedirect;
	}
	
	// ОБРАБОТЧИК ОШИБКИ
	public function errorHandler($msg, $line = 0){
		
		$msg = $msg.(USER_AUTH_PERMS >= Error_Model::getConfig('minPermsForDisplay') && !empty($line) ? ' (#'.$line.')' : '');
		
		if(App::$adminMode)
			BackendViewer::get()->error($msg);
		else
			FrontendViewer::get()->error($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 403
	public function error403handler($msg, $line = 0){
		
		$msg = USER_AUTH_PERMS >= Error_Model::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
		$layoutClass = App::get()->isAdminMode() ? BackendLayout::get() : FrontendLayout::get();
		$layoutClass->error403($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 404
	public function error404handler($msg, $line = 0){
		
		$msg = USER_AUTH_PERMS >= Error_Model::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
		$layoutClass = App::get()->isAdminMode() ? BackendLayout::get() : FrontendLayout::get();
		$layoutClass->error404($msg);
	}
	
	// ВЫПОЛНЕНИЕ МЕТОДА ПО УМОЛЧАНИЮ
	protected function _displayIndex($params){
		
		$defaultMethodIdentifier = $this->_getDisplayIndex();
		
		// если метод по умолчанию определен
		if($defaultMethodIdentifier){
			if(CFG_REDIRECT_DEFAULT_DISPLAY){
				App::redirectHref(Request::get()->getAppended($defaultMethodIdentifier));
			}else{
				$displayParams = $params;
				array_unshift($displayParams, $defaultMethodIdentifier);
				$this->display($displayParams);
			}
		}
		// если метод по умолчанию не определен
		else{
			if($defaultMethodIdentifier === FALSE){
				$this->error404handler(get_class().'::default_method_for_'.(App::get()->isAdminMode() ? 'backend' : 'frontend'), __LINE__);
			}else{
				trigger_error('Неверное значение '.get_class($this).'::$_displayIndex для контроллера . Допускается идентификатор метода, или FALSE', E_USER_ERROR);
			}
		}
		
	}
	
	protected function _getDisplayIndex(){
		
		return $this->_displayIndex;
	}
	
	/** ПОЛУЧИТЬ КОНСТАНТУ ИЗ КЛАССА-ПОТОМКА */
	public function getConst($name){
		
		return constant($this->getClass().'::'.$name);
	}
	
}

?>