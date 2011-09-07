<?

class BackendLayout extends Layout{
	
	protected $_layoutName = 'backend';
	
	private $_topMenu = array(
		'content' => array('perms' => PERMS_ADMIN, 'title' => 'Контент'           ),
		'users'   => array('perms' => PERMS_ADMIN, 'title' => 'Пользователи'      ),
		'root'    => array('perms' => PERMS_ADMIN, 'title' => 'Администрирование' ),
	);
	private $_topMenuActive = '';
	
	private $_leftMenuTypes = array(
		'content' => array(
			'page' => 'Страницы',
			'test-item' => 'Тестовые сущности',
			'project' => 'Проекты',
		),
		'users' => array(
			'list' => 'Список пользователей',
			'create' => 'Создание пользователя',
			'ban-list' => 'Блокировки',
		),
		'root' => array(
			'user-statistics' => 'Статистика посещений',
			'sql-console' => 'SQL-консоль',
			'sql-dump' => 'Создание дампа БД',
			'error-log' => 'Лог ошибок',
			'settings' => 'Настройки для сайта',
			'service' => 'Обслуживание',
		),
	);
	
	/**
	 * Список пунктов левого меню.
	 * Имеет вид списка ассоциативных массивов с ключами: 
	 * 		'hrefPrefix' - например 'admin/static/edit'
	 * 		'id'		 - инентифицирует пункт меню, добавляется к hrefPrefix в урлах
	 * 		'title'		 - отображаемая часть пункта меню
	 */
	private $_leftMenu = array();
	/** Активный пункт левого меню */
	private $_leftMenuActive = '';
	
	
	private static $_instance = null;
	
	
	// ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР CommonViewer)
	public static function get(){

		if(is_null(self::$_instance))
			self::$_instance = new BackendLayout();
		
		return self::$_instance;
	}
	
	public function setTopMenuActiveItem($active){
		
		$this->_topMenuActive = $active;
		return $this;
	}
	
	public function setLeftMenuType($type, $active = ''){
		
		if(empty($this->_leftMenuTypes[$type]))
			trigger_error('Неизвестный тип левого меню "'.$type.'"', E_USER_ERROR);
		
		foreach($this->_leftMenuTypes[$type] as $id => $title)
			$this->_leftMenu[] = array('hrefPrefix' => 'admin/'.$type.'/', 'id' => $id, 'title' => $title);
			
		$this->_leftMenuActive = $active;
		return $this;
	}
	
	public function setLeftMenuItems($items, $active = ''){
	
		$this->_leftMenu = $items;
		$this->_leftMenuActive = $active;
		return $this;
	}
	
	public function setLeftMenuActiveItem($active){
	
		$this->_leftMenuActive = $active;
		return $this;
	}
	
	protected function _getTopMenu(){
		
		$output = '';
		foreach($this->_topMenu as $name => $data)
			if(USER_AUTH_PERMS >= $data['perms'])
				$output .= '<a href="'.App::href('admin/'.$name).'"'.($name == $this->_topMenuActive ? ' class="active"' : '').'>'.$data['title'].'</a>';
		
		return $output;
	}
	
	protected function _getLeftMenu(){
		
		$output = '';
		if(count($this->_leftMenu)){
			foreach($this->_leftMenu as $item)
				$output .= '<a href="'.App::href($item['hrefPrefix'].$item['id']).'"'.($item['id'] == $this->_leftMenuActive ? ' class="active"' : '').'>'.$item['title'].'</a>';
		}else{
			$output .= '<p>Нет записей</p>';
		}
		return $output;
	}
	
	public function setBreadcrumbsAuto(){
		
		if($this->_isAutoBreadcrumbsAdded)
			return;
		
		$breadcrumbs = array(array('admin', 'Административная панель'));
		
		if($this->_topMenuActive){
			$breadcrumbs[] = array(
				'admin/'.$this->_topMenuActive,
				$this->_topMenu[$this->_topMenuActive]['title']);
			
			if($this->_leftMenuActive && !empty($this->_leftMenuTypes[$this->_topMenuActive][$this->_leftMenuActive]))
				$breadcrumbs[] = array(
					'admin/'.$this->_topMenuActive.'/'.$this->_leftMenuActive,
					$this->_leftMenuTypes[$this->_topMenuActive][$this->_leftMenuActive]);
		}
		
		if(count($this->_breadcrumbs))
			$this->_breadcrumbs = array_merge($breadcrumbs, $this->_breadcrumbs);
		else
			$this->_breadcrumbs = array_merge($this->_breadcrumbs, $breadcrumbs);
		
		$this->_isAutoBreadcrumbsAdded = TRUE;
	}
	
	public function showLoginPage(){
		
		$isLogged = CurUser::get()->isLogged();
		$errorMessage = Messenger::get()->ns('login')->getAll();
		include($this->_layoutDir.'login.php');
	}
	
}

?>