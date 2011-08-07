<?

class FrontendLayout extends Layout{
	
	protected $_layoutName = 'frontend';
		
	private $_topMenuItems = array(
		'main' => array('href' => '', 'title' => 'Главная'),
		'contacts' => array('href' => 'page/contacts', 'title' => 'Контакты'),
		'admin' => array('href' => 'admin', 'title' => 'Админ-панель'),
	);

	// активный пункт главного меню
	private $_topMenuActiveItem = null;
	
	private static $_instance = null;
	
	
	// ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР FrontendLayout)
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new FrontendLayout();
		
		return self::$_instance;
	}
	
	public function getLoginBlock(){
		
		if(CurUser::get()->isLogged()){
			
			$user_io = CurUser::get()->getName('io');
			$user_perms_string = User::getPermName(USER_AUTH_PERMS);
			include($this->_tplPath.'Profile/logged_block.php');
		}else{
			
			$error = Messenger::get()->ns('login')->getAll();
			include($this->_tplPath.'Profile/login_block.php');
		}
	}
	
	public function setTopMenuActiveItem($active){
		
		$this->_topMenuActiveItem = $active;
		return $this;
	}
	
	public function getTopMenu(){
		
		include($this->_tplPath.'top_menu.php');
	}

}

?>