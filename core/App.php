<?app
/**
 * Фронт-контроллер приложения. 
 * 
 * @using constants
 *		DEFAULT_CONTROLLER,
 *		CHECK_FORM_DUPLICATION,
 *		FS_ROOT,
 *		CFG_USE_SMARTY_CACHING,
 *		CFG_SMARTY_TRIMWHITESPACES,
 *		CFG_SITE_NAME,
 *		WWW_URI,
 */
class App {
	
	const ERROR_403 = 403;
	const ERROR_404 = 404;
	
	private $_preventDisplay = FALSE;
	
	public static $adminMode = FALSE;
	
	private static $_instance = null;
	private static $_smartyInstance = null;
	
	private $_requestModuleName = null;
	private $_requestModuleParams = array();
	
	/** флаг, включен ли режим администратора */
	private $_adminMode = FALSE;
	
	/** массив конфигурации модулей */
	private $_modulesConfig = array();

	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР КЛАССА */
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new App();
		
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		// извлечение параметров запроса
		list($this->_requestModuleName, $this->_requestModuleParams) = Request::get()->getArray();
		$this->_requestModuleName = $this->prepareModuleName($this->_requestModuleName);
		
		// получение конфигурации модулей
		$this->_modulesConfig = Config::get()->getModulesConfig();
		
		// определение режима администратора
		$this->_adminMode = $this->_requestModuleName == 'admin';
	}
	
	/** ПРОВЕРИТЬ, ВКЛЮЧЕН ЛИ РЕЖИМ АДМИНИСТРАТОРА */
	public function isAdminMode(){
		
		return $this->_adminMode;
	}
	
	/** ЗАПУСК ПРИЛОЖЕНИЯ */
	public function run(){
		
		$this->_checkAction();
		
		if($this->_checkDisplay())
			exit;
		
		$this->error404('Страница '.Request::get()->getString().' не найдена');
	}
	
	/** ЗАПУСК ПРИЛОЖЕНИЯ В AJAX-РЕЖИМЕ */
	public function ajax(){
		
		if($this->_checkAction())
			exit;
		
		if($this->_checkAjax())
			exit;
		
		if($this->_checkDisplay())
			exit;
		
		$this->error404('Страница '.Request::get()->getString().' не найдена');
	}
	
	/**
	 * ПОЛУЧЕНИЕ РЕАЛЬНОГО ИМЕНИ МОДУЛЯ 
	 * преобразование строки "user-statistics" в "userStatistics"
	 */
	public function prepareModuleName($module){
		
		$module = str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($module))));
		return strtolower(substr($module, 0, 1)).substr($module, 1);
	}
	
	/** ПРОВЕРКА, СУЩЕСТВУЕТ ЛИ УКАЗАННЫЙ МОДУЛЬ */
	public function issetModule($module){
		
		return isset($this->_modulesConfig[$module]);
	}
	
	/** ПРОВЕРКА, СУЩЕСТВУЕТ ЛИ УКАЗАННЫЙ МОДУЛЬ и есть ли у него контроллер */
	public function isModule($module, $adminMode = FALSE){
		
		return isset($this->_modulesConfig[$module][$adminMode ? 'adminController' : 'controller']);
	}
	
	/** ПОЛУЧЕНИЕ ЭКЗЕМПЛЯРА КОНТРОЛЛЕРА МОДУЛЯ */
	public function getModule($module, $adminMode = FALSE){
		
		$key = $adminMode ? 'adminController' : 'controller';
		if(!isset($this->_modulesConfig[$module][$key])){
			$this->error404('Модуль "'.$module.'" не найден');
			exit;
		}
		return new $this->_modulesConfig[$module][$key]( $this->_modulesConfig[$module] );
	}
	
	/** ПОЛУЧЕНИЕ МАССИВА КОНФИГУРАЦИИ ВСЕХ МОДУЛЕЙ */
	public function getModulesConfig(){
		
		return $this->_modulesConfig;
	}

	/** ПРОВЕРИТЬ НЕОБХОДИМОСТЬ ВЫПОЛЕННИЯ ДЕЙСТВИЯ */
	public function _checkAction(){
		
		if(!isset($_POST['action']) || !App::checkFormDuplication())
			return FALSE;
		
		$isArr = is_array($_POST['action']);
		$action = strtolower($isArr ? YArray::getFirstKey($_POST['action']) : $_POST['action']);
		$redirect = $isArr && is_array($_POST['action'][$action])
			? YArray::getFirstKey($_POST['action'][$action])
			: (isset($_POST['redirect']) ? $_POST['redirect'] : '');
		
		$params = YArray::trim(explode('/', $action));
		
		// параметр action должен иметь вид 'module/method[/param][/param]'
		if(count($params) == 1){
			trigger_error('Неверный формат параметра action: '.$action.' (требуется разделитель)', E_USER_ERROR);
		}
		
		$module = array_shift($params);
		$this->getModule($module)->action($params, $redirect);
		return TRUE;
	}
	
	/** ПРОВЕРКА НЕОБХОДИМОСТИ ВЫПОЛНЕНИЯ ОТОБРАЖЕНИЯ */
	protected function _checkDisplay(){
		
		$module = !empty($this->_requestModuleName) ? $this->_requestModuleName : DEFAULT_CONTROLLER;
		
		if(!$this->isModule($module))
			return FALSE;
		
		return $this->getModule($module)->display($this->_requestModuleParams);
	}
	
	/** ПРОВЕРКА НЕОБХОДИМОСТИ ВЫПОЛНЕНИЯ AJAX */
	protected function _checkAjax(){
		
		if(empty($this->_requestModuleName) || !$this->isModule($this->_requestModuleName))
			return FALSE;

		return $this->getModule($this->_requestModuleName)->ajax($this->_requestModuleParams);
	}
	
	/** ЗАПРЕТ ОТОБРАЖЕНИЯ */
	public function preventDisplay($prevent = TRUE){
	
		$this->_preventDisplay = (bool)$prevent;
	}
	
	#### ВЫПОЛНЕНИЕ РЕДИРЕКТОВ ####
	
	
	// REDIRECT
	public static function redirect($uri){
		
		$uri = href($uri);
		// echo '<a href="'.$uri.'">'.$uri.'</a>'; die;
		header('location: '.$uri);
		exit();
	}
	
	// REDIRECT HREF
	public static function redirectHref($href){
		
		// echo '<a href="'.App::href($href).'">'.App::href($href).'</a>'; die;
		header('location: '.App::href($href));
		exit();
	}
	
	// RELOAD
	public static function reload(){
	
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		header('location: '.$url);
		exit();
	}
	
	
	#### FORMCODE ####
	
	
	// ПОЛУЧИТЬ HTML INPUT СОДЕРЖАЩИЙ FORMCODE
	static public function getFormCodeInput(){
		return '<input type="hidden" name="formCode" value="'.self::_generateFormCode().'" />';
	}
	
	// ПРОВЕРКА ВАЛИДНОСТИ ФОРМЫ
	static public function checkFormDuplication(){
		
		if(isset($_POST['allowDuplication']))
			return TRUE;
			
		if(!isset($_POST['formCode'])){
			trigger_error('formCode не передан', E_USER_ERROR);
			return FALSE;
		}
		$formcode = (int)$_POST['formCode'];
		
		if(!CHECK_FORM_DUPLICATION)
			return TRUE;
			
		if(self::_isAllowedFormCode($formcode)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	// ПОМЕТИТЬ FORMCODE ИСПОЛЬЗОВАННЫМ
	static public function lockFormCode(&$code){
	
		if(CHECK_FORM_DUPLICATION && !empty($code))
			$_SESSION['userFormChecker']['used'][] = $code;
	}
	
	// СГЕНЕРИРОВАТЬ УНИКАЛЬНЫЙ FORMCODE
	static private function _generateFormCode(){
	
		// init session variable
		if(!isset($_SESSION['userFormChecker']))
			$_SESSION['userFormChecker'] = array('current' => 0, 'used' => array());
		// generate unique code
		$_SESSION['userFormChecker']['current']++;
		return $_SESSION['userFormChecker']['current'];
	}
	
	// ПРОВЕРИТЬ ПОЛУЧЕННЫЙ FORMCODE
	static private function _isAllowedFormCode($code){
	
		if(!$code)
			return FALSE;
		if(!isset($_SESSION['userFormChecker']['used']))
			return FALSE;
		return (bool)!in_array($code, $_SESSION['userFormChecker']['used']);
	}
	
	
	#### HREF ####
	
	/**
	 * HREF
	 * Генерация валидного абсолютного URL адреса
	 * @param string $href - строка вида 'contoller/method/addit?param1=val1&param2=val2
	 * return string абсолютный URL
	 */
	public static function href($href){
	
		return href($href);
	}
	
	/**
	 * GET HREF REPLACED
	 * Получить валидный url с замененным/добавленным параметром (одним или несколькими)
	 * @param string|array $nameOrPairs - имя параметра, или массив ($имя => $параметр)
	 * @param string|null $valueOrNull - значение параметра (если первый аргумент - строка) или null
	 * @return string валидный абсолютный URL с нужными параметрами
	 */
	public static function getHrefReplaced($nameOrPairs, $valueOrNull = null){
		
		// получить пары для замены
		$pairs = is_array($nameOrPairs)
			? $nameOrPairs
			: array($nameOrPairs => $valueOrNull);
		
		// получить копию $_GET с нужными заменами
		$copyOfGet = $_GET;
		foreach($pairs as $name => $value){
			if(is_null($value))	// если value == null, удалим параметр из QS
				unset($copyOfGet[$name]);
			else				// иначе добавим / заменим параметр в QS
				$copyOfGet[$name] = $value;
		}
		
		// сформировать валидный URL
		$r = isset($copyOfGet['r']) ? $copyOfGet['r'] : '';
		unset($copyOfGet['r']);
		$qs = http_build_query($copyOfGet);
		
		return App::href($r.($qs ? '?'.$qs : ''));
	}

	#### ПРОЧЕЕ ####
	
	public function setControllerErrCode($code){
		
		$this->_controllerErrCode = $code;
	}
	
	public function getControllerErrCode(){
		
		return $this->_controllerErrCode;
	}
	
	// ERROR 403
	public static function error403($msg = ''){
		
		if(AJAX_MODE){
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); // 'HTTP/1.1 403 Forbidden'
			echo $msg;
		}else{
			$layoutClass = $this->_adminMode ? BackendLayout::get() : FrontendLayout::get();
			$layoutClass->error403($msg);
		}
		exit();
	}
	
	/** ПОКАЗАТЬ СТРАНИЦУ 404 */
	public function error404($msg = ''){
		
		if(AJAX_MODE){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); // 'HTTP/1.1 404 Not Found'
			echo $msg;
		}else{
			$layoutClass = $this->_adminMode ? BackendLayout::get() : FrontendLayout::get();
			$layoutClass->error404($msg);
		}
		exit();
	}
	
}
?>