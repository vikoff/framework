<?

class FrontendLayout extends Layout {
	
	protected $_layoutName = 'frontend';
	
	protected $_useAutoBreadcrumbs = FALSE;
	
	protected $_topMenu = null;
	
	private static $_instance = null;
	
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР КЛАССА */
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new FrontendLayout();
		
		return self::$_instance;
	}
	
	/** ИНИЦИАЛИЗАЦИЯ */
	protected function init(){
		
		$this->_topMenu = new Menu_Model('frontend-top');
	}
	
	public function _getLoginBlockHTML(){
		
		if(CurUser::get()->isLogged()){
			
			$user_io = CurUser::get()->getName('io');
			$user_perms_string = User::getPermName(USER_AUTH_PERMS);
			include($this->_tplPath.'Profile/logged_block.php');
		}else{
			
			$error = Messenger::get()->ns('login')->getAll();
			include($this->_tplPath.'Profile/login_block.php');
		}
	}
	
	
	protected function _getTopMenuHTML(){
		
		$html = '';
		foreach($this->_topMenu->getItems() as $item)
			$html .= '<a href="'.$item['href'].'" '.(!empty($item['attrs']) ? $item['attrs'] : '').' '.($item['active'] ? 'class="active"' : '').'>'.$item['title'].'</a>';
		
		return $html;
	}
	
	/**
	 * ВЫВЕСТИ/ВЕРНУТЬ ЭЛЕМЕНТЫ СТРАНИЦЫ В ФОРМАТЕ JSON
	 * @access protected
	 * @param bool $boolReturn - флаг, возвращать контент, или выводить
	 * @param void|string контент в формате json
	 */
	protected function _renderJSON($boolReturn){
		
		$json = json_encode(array(
			'content' => $this->_getContentHTML(),
			'topMenuActiveIndex' => $this->_topMenu->activeIndex,
		));
		
		if($boolReturn)
			return $json;
		else
			echo $json;
	}

}

?>