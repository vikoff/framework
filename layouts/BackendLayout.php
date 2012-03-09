<?

class BackendLayout extends Layout {
	
	protected $_layoutName = 'backend';
	
	protected $_useAutoBreadcrumbs = TRUE;
	
	protected $_topMenu = null;
	protected $_leftMenu = null;
	
	private static $_instance = null;
	
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР КЛАССА */
	public static function get(){

		if(is_null(self::$_instance))
			self::$_instance = new BackendLayout();
		
		return self::$_instance;
	}
	
	/** ИНИЦИАЛИЗАЦИЯ */
	protected function init(){
		
		$this->_topMenu = new Html_Menu('backend-top');
		$this->_leftMenu = new Html_Menu('backend-left', array('topMenu' => $this->_topMenu));
	}
	
	protected function _constructAutoBreadcrumbs(){
		
		$breadcrumbs = array(array('admin', 'Административная панель'));
		
		if ($this->_topMenu->activeItem)
			$breadcrumbs[] = array($this->_topMenu->activeItem['href'], $this->_topMenu->activeItem['title']);
		
		if ($this->_leftMenu->activeItem)
			$breadcrumbs[] = array($this->_leftMenu->activeItem['href'], $this->_leftMenu->activeItem['title']);
			
		return $breadcrumbs;
	}
	
	public function showLoginPage(){
		
		$this->isLogged = CurUser::get()->isLogged();
		$this->errorMessage = Messenger::get()->ns('login')->getAll();
		include($this->_layoutDir.'login.php');
	}
	
	protected function _getTopMenuHTML(){
		
		$html = '';
		foreach($this->_topMenu->getItems() as $item)
			$html .= '<a href="'.$item['href'].'" '.($item['active'] ? 'class="active"' : '').'>'.$item['title'].'</a>';
		
		return $html;
	}
	
	protected function _getLeftMenuHTML(){
		
		$html = '';
		foreach($this->_leftMenu->getItems() as $item)
			$html .= '<a href="'.$item['href'].'" '.($item['active'] ? 'class="active"' : '').'>'.$item['title'].'</a>';
		
		return $html;
	}
}

?>