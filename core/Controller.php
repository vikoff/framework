<?

abstract class Controller {
	
	/**
	 * конфигурация модуля
	 * @var array
	 */
	protected $_config = array();
	
	/**
	 * массив пар 'идентификатор метода' => 'Класс контроллера или Имя модуля'
	 * для проксирования запросов в другой контроллер/модуль
	 * если указан класс контроллера, то он должен принадлежать тому же модулю, что и текущий контроллер.
	 */
	protected $_proxy = array();
	
	/*
	 * Идентификатор метода, вызываемого по умолчанию.
	 * Идентификатор задается без префикса 'display_'.
	 * Значение 'list' - верно; 'display_list' - неверно.
	 * Должен быть задан явно в классах наследниках.
	 * @var string
	 */
	protected $_displayIndex = null;
	
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
	 * Ассоциация методов контроллера с ресурсами
	 * @example array('display_list' => 'view')
	 * @var array
	 */
	public $methodResources = array();
	
	/** Контейнер для обмена данными внутри контроллера */
	protected $_data = array();
	
	
	/**
	 * КОНСТРУКТОР
	 * @param array $config - конфигурация модуля
	 */
	public function __construct($config){
		
		$this->_config = $config;
		$this->init();
	}
	
	/**
	 * НАЧАЛЬНАЯ ИНИЦИАЛИЗАЦИЯ КОНТРОЛЛЕРА
	 * метод может быть объявлен в наследниках класса для вызова каких-либо действий
	 */
	public function init(){}
	
	/**
	 * ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА
	 * @abstract
	 * @param string $resource - имя ресурса
	 * @return bool - есть ли у пользователя разрешение на выполнение ресурса
	 */
	abstract public function checkResourcePermission($resource);
	
	/** ПРОВЕРКА КОРРЕКТНОСТИ МЕТОДА */
	public function checkMethod(&$method){
		
		// если ресурс для метода не определен
		if(empty($this->methodResources[$method]))
			trigger_error('resource of '.$this->getClass().'::'.$method.' method not specified', E_USER_ERROR);
		
		
		// если недостаточно прав
		if(!$this->checkResourcePermission($this->methodResources[$method])){
			
			Debugger::get()->log($this->getClass().'::'.$method.' (resource: '.$this->methodResources[$method].')');
			$resourceTitle = $this->_config['resources'][ $this->methodResources[$method] ];
			$this->error403handler('Недостаточно прав, чтобы выполнить '.$resourceTitle, __LINE__);
			exit;
		}
		
	}
	
	/** ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ */
	public function display($params){
		
		$method = array_shift($params);
		
		// если метод не указан, то выполняется метод по умолчанию
		if(empty($method))
			return $this->_displayIndex($params);
		
		// проксирование на другой контроллер/модуль
		if(isset($this->_proxy[$method]))
			return $this->getProxyControllerInstance($this->_proxy[$method])->display($params);
		
		$method = $this->getDisplayMethodName($method);
		
		// если метод не найден
		if(!method_exists($this, $method))
			return FALSE;
		
		// проверка метода
		$this->checkMethod($method, $params);
		
		// вызов метода
		try{
			if (!empty($this->_config['arrayParams']))
				$this->$method($params);
			else
				call_user_func_array(array($this, $method), $params);
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
		
		// проксирование на другой контроллер/модуль
		if(isset($this->_proxy[$method]))
			return $this->getProxyControllerInstance($this->_proxy[$method])->action($params, $redirectUrl);
		
		$method = $this->getActionMethodName($method);
		
		// если метод не прошел проверку, запускается error handler
		// и дальнейший вывод прекращается
		$this->checkMethod($method);
		
		// назначение URL для редиректа (если задан).
		// назначается раньше выполнения метода, чтобы
		// быть доступным из него.
		$this->_redirectUrl = $redirectUrl;
		
		try{
			
			// выполнение метода
			if (!empty($this->_config['arrayParams']))
				$result = $this->$method($params);
			else
				$result = call_user_func_array(array($this, $method), $params);
				
			if ($result !== FALSE) {
				
				// блокирование formcode
				App::lockFormCode($_POST['formCode']);
				
				// выполнение редиректа (если надо)
				if(!empty($this->_redirectUrl))
					$this->redirect(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
			} else {
				if($this->_forceRedirect && !empty($this->_redirectUrl))
					$this->redirect(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
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
		
		// проксирование на другой контроллер/модуль
		if(isset($this->_proxy[$method]))
			return $this->getProxyControllerInstance($this->_proxy[$method])->ajax($params);
		
		$method = $this->getAjaxMethodName($method);
		
		// если метод не найден
		if(!method_exists($this, $method))
			return FALSE;
		
		// проверка метода
		$this->checkMethod($method, $params);
		
		// вызов метода
		try{
			if (!empty($this->_config['arrayParams']))
				$this->$method($params);
			else
				call_user_func_array(array($this, $method), $params);
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
	
	/** ПОЛУЧИТЬ ЭКЗЕМЛЯР КОНТРОЛЛЕРА ДЛЯ ПРОКСИРОВАНИЯ */
	public function getProxyControllerInstance($proxy){
		
		$app = App::get();
		$adminMode = $app->isAdminMode();
		return $app->isModule($proxy, $adminMode)
			? $app->getModule($proxy, $adminMode)
			: new $proxy($this->_config);
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
		
		$msg = $msg.(USER_AUTH_LEVEL >= Error_Model::getConfig('minPermsForDisplay') && !empty($line) ? ' (#'.$line.')' : '');
		
		if(App::$adminMode)
			BackendLayout::get()->error($msg);
		else
			FrontendLayout::get()->error($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 403
	public function error403handler($msg, $line = 0){
		
		$msg = USER_AUTH_LEVEL >= Error_Model::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
		$layoutClass = App::get()->isAdminMode() ? BackendLayout::get() : FrontendLayout::get();
		$layoutClass->error403($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 404
	public function error404handler($msg, $line = 0){
		
		$msg = USER_AUTH_LEVEL >= Error_Model::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
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
				exit();
			}else{
				$displayParams = $params;
				array_unshift($displayParams, $defaultMethodIdentifier);
				return $this->display($displayParams);
			}
		}
		// если метод по умолчанию не определен
		else{
			if($defaultMethodIdentifier === FALSE){
				$this->error404handler(get_class().'::default_method_for_'.(App::get()->isAdminMode() ? 'backend' : 'frontend'), __LINE__);
			}else{
				trigger_error('Неверное значение '.get_class($this).'::$_displayIndex для контроллера . Допускается идентификатор метода, или FALSE', E_USER_ERROR);
			}
			exit;
		}
		
	}
	
	protected function _getDisplayIndex(){
		
		return $this->_displayIndex;
	}
	
	/** ПОЛУЧИТЬ КОНСТАНТУ ИЗ КЛАССА-ПОТОМКА */
	public function getConst($name){
		return constant($this->getClass().'::'.$name);
	}
	
	public function redirect($uri){
		
		$uri = href($uri);
		header('location: '.$uri);
		exit();
	}
}

?>