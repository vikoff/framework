<?

class Controller{
	
	/**
	 * Режим администратора. Задается в конструкторе.
	 * Автоматически устанавливается в TRUE при вызове из адм. панели
	 * @var bool
	 */
	protected $_adminMode = FALSE;
	
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
	
		$this->_adminMode = $adminMode;
		$this->init();
	}
	
	// ИНИЦИАЛИЗАЦИЯ КОНТРОЛЛЕРА
	public function init(){}
	
	/**
	 * Получить заголовок контроллера
	 * Если заголовок не задан, возвращается имя класса
	 * (например "user" для UserController)
	 * @return string - заголовок контроллера
	 */
	public function getTitle(){
	
		return !is_null($this->_title) ? $this->_title : strtolower(str_replace('Controller', '', __CLASS__));
	}
	
	/** ДОСТАТОЧНО ЛИ ПРАВ ДЛЯ ВЫПОЛНЕНИЯ */
	public function hasPermission($method, $userperm){
		return (isset($this->permissions[$method]) && $this->permissions[$method] <= $userperm) ? TRUE : FALSE;
	}
	
	/** ПРОВЕРКА КОРРЕКТНОСТИ МЕТОДА */
	public function checkMethod(&$method){
			
		// если действие не найдено
		if(!method_exists($this, $method)){
			
			$this->error404handler(__CLASS__.'::'.$method, __LINE__);
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
	
	/**
	 * ВЫПОЛНЕНИЕ ДЕЙСТВИЯ
	 * @exception Exception - ловит стандартные исключения
	 * @exception Exception403 - ловит исключения 403
	 * @exception Exception404 - ловит исключения 404
	 * @param string $method - идентификатор метода
	 * @param string $redirectUrl - url, куда надо сделать редирект после успешного выполнения
	 * @return void
	 */
	public function performAction($method, $redirectUrl){
	
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
					App::redirectHref(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
			}else{
				if($this->_forceRedirect && !empty($this->_redirectUrl))
					App::redirectHref(Messenger::get()->qsAppendFutureKey($this->_redirectUrl));
			}
		}
		catch(Exception404 $e){$this->error404handler($e->getMessage());}
		catch(Exception403 $e){$this->error403handler($e->getMessage());}
		catch(Exception $e){$this->errorHandler($e->getMessage());}
	}
	
	// ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ
	public function performDisplay($method, $params){
		
		// если метод не указан, то выполняется метод по умолчанию
		if(!$method){
			$this->_displayDefault($params);
			return;
		}
		
		$method = App::getDisplayMethodName($method);
		
		// модификация имени метода
		$this->modifyMethodName($method);
		
		if($this->checkMethod($method, $params)){
			
			try{
				$this->$method($params);
			}
			catch(Exception404 $e){$this->error404handler($e->getMessage());}
			catch(Exception403 $e){$this->error403handler($e->getMessage());}
			catch(Exception $e){$this->errorHandler($e->getMessage());}
		}
		
	}
	
	// МОДИФИКАЦИЯ ИМЕНИ МЕТОДА
	// применяется к display методам
	public function modifyMethodName(&$method){
		
		// добавляет всем методам, запущенным из адм. панели суффикс 'admin_'
		if($this->_adminMode){
			$method = 'admin_'.$method;
		}
	}
	
	// ВЫПОЛНЕНИЕ AJAX
	public function performAjax($method, $params){
		
		$method = App::getAjaxMethodName($method);
		
		if($this->checkMethod($method, $params)){
			
			try{
				$this->$method($params);
			}
			catch(Exception404 $e){$this->error404handler($e->getMessage());}
			catch(Exception403 $e){$this->error403handler($e->getMessage());}
			catch(Exception $e){$this->errorHandler($e->getMessage());}
		}
		
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
		
		$msg = $msg.(USER_AUTH_PERMS >= Error::getConfig('minPermsForDisplay') && !empty($line) ? ' (#'.$line.')' : '');
		
		if(App::$adminMode)
			BackendViewer::get()->error($msg);
		else
			FrontendViewer::get()->error($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 403
	public function error403handler($msg, $line = 0){
		
		$msg = USER_AUTH_PERMS >= Error::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
		if(App::$adminMode)
			BackendViewer::get()->error403($msg);
		else
			FrontendViewer::get()->error403($msg);
	}
	
	// ОБРАБОТЧИК ОШИБКИ 404
	public function error404handler($msg, $line = 0){
		
		$msg = USER_AUTH_PERMS >= Error::getConfig('minPermsForDisplay') ? $msg.(!empty($line) ? ' (#'.$line.')' : '') : '';
		
		if(App::$adminMode)
			BackendViewer::get()->error404($msg);
		else
			FrontendViewer::get()->error404($msg);
	}
	
	// ВЫПОЛНЕНИЕ МЕТОДА ПО УМОЛЧАНИЮ
	protected function _displayDefault($params){
		
		$defaultMethodIdentifier = $this->_getDisplayDefaultIdentifier();
		
		if($defaultMethodIdentifier){
			if(CFG_REDIRECT_DEFAULT_DISPLAY){
				App::redirectHref(Request::get()->getAppended($defaultMethodIdentifier));
			}else{
				$this->performDisplay($defaultMethodIdentifier, $params);
			}
		}else{
			if($defaultMethodIdentifier === FALSE){
				$this->error404handler(__CLASS__.'::default_method_for_'.($this->_adminMode ? 'backend' : 'frontend'), __LINE__);
			}else{
				trigger_error('Неверное значение _default'.($this->_adminMode ? 'Backend' : 'Frontend').'Display для контроллера '.get_class($this).'. Допускается идентификатор метода, или FALSE', E_USER_ERROR);
			}
		}
		
	}
	
	protected function _getDisplayDefaultIdentifier(){
		
		return $this->_adminMode
			? $this->_defaultBackendDisplay
			: $this->_defaultFrontendDisplay;
	}
	
}

?>