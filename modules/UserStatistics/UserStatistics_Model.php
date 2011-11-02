<?
/**
 * 
 * @using constants
 * 		User::TABLE
 * 		
 * @using methods
 * 		YDate::timestamp2date()
 * 		App::href()
 * 		User::getPermName()
 */
class UserStatistics_Model {
	
	const TABLE = 'user_stat';
	const TABLE_PAGES = 'user_stat_pages';
	
	private static $_enabled = FALSE;
	
	private static $_instance = null;
	
	
	/** ВКЛЮЧИТЬ СБОР СТАТИСТИКИ */
	public static function enable(){
		self::$_enabled = TRUE;
	}
	
	/** ОТКЛЮЧИТЬ СБОР СТАТИСТИКИ */
	public static function disable(){
		self::$_enabled = FALSE;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР КЛАССА */
	public static function get(){
	
		if(is_null(self::$_instance))
			self::$_instance = new UserStatistics_Model();
			
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		if(!self::$_enabled)
			return;
			
		if(!$this->_isSessionInited())
			$this->_initSession();
	}
	
	// ПОЛУЧИТЬ СТРОКУ ДАННЫХ ИЗ ТАБЛИЦЫ
	public function getRowPrepared($id){
		
		$id = (int)$id;
		$db = db::get();
		$data = $db->getRow('SELECT * FROM '.self::TABLE.' WHERE id='.$id);
		
		if(!$data)
			throw new Exception('Данные не найдены');
		
		$data['pages'] = $db->getAll('SELECT * FROM '.UserStatistics_Model::TABLE_PAGES.' WHERE session_id='.$id);
		return self::beforeDisplay($data, TRUE);
	}
	
	// ПРОВЕРКА, ИНИЦИАЛИЗИРОВАНА ЛИ СЕССИЯ
	private function _isSessionInited(){
		
		return !empty($_SESSION['vik-off-user-statistics']);
	}
	
	// ИНИЦИАЛИЗАЦИЯ СЕССИИ
	private function _initSession(){
		$_SESSION['vik-off-user-statistics'] = array(
			'session-id' => 0,
			'last-url' => '',
			'is-client-stat-saved' => FALSE,
		);
	}
	
	public function reset(){
		$_SESSION['vik-off-user-statistics'] = null;
	}
	
	// СОХРАНЕНИЕ ПЕРВИЧНОЙ СТАТИСТИКИ
	public function savePrimaryStatistics(){
		
		// выход если сохранение статистики отключено
		if(!self::$_enabled)
			return;
		
		// определение запришиваемого URL
		$requestUrl = getVar($_SERVER['SERVER_NAME']).getVar($_SERVER['REQUEST_URI']);
		
		// если запрашиваемый URL совпадает с предыдущим, ничего не сохраняем
		if($requestUrl == $_SESSION['vik-off-user-statistics']['last-url'])
			return;
		
		$db = db::get();
		
		// создание сессии
		if(!$_SESSION['vik-off-user-statistics']['session-id']){
		
			$sid = $db->insert(self::TABLE, array(
				'user_ip' 		 => getVar($_SERVER['REMOTE_ADDR']),
				'user_agent_raw' => getVar($_SERVER['HTTP_USER_AGENT']),
				'referer' 		 => getVar($_SERVER['HTTP_REFERER']),
				'date'			 => time(),
			));
			$_SESSION['vik-off-user-statistics']['session-id'] = $sid;
			
		}
		
		// сохранение запрошенной страницы
		$db->insert(self::TABLE_PAGES, array(
			'session_id' => $_SESSION['vik-off-user-statistics']['session-id'],
			'url'        => $requestUrl,
			'is_ajax'    => AJAX_MODE ? TRUE : FALSE,
			'post_data'  => null,
			'date'       => time(),
		));
		
		// сохраняем запрашиваемый URL
		$_SESSION['vik-off-user-statistics']['last-url'] = $requestUrl;
	}
	
	// ПРОВЕРКА НЕОБХОДИМОСТИ СОХРАНЕНИЯ КЛИЕНТСКОЙ СТАТИСТИКИ
	public function checkClientSideStatistics(){
		
		// выход если сохранение статистики отключено
		if(!self::$_enabled)
			return FALSE;
	
		return empty($_SESSION['vik-off-user-statistics']['is-client-stat-saved']);
	}
	
	// ПОЛУЧИТЬ HTML ДЛЯ СОХРАНЕНИЯ КЛИЕНТСКОЙ СТАТИСТИКИ
	public function getClientSideStatisticsLoader(){
		
		// выход если сохранение статистики отключено
		if(!self::$_enabled)
			return '';
			
		return '
			<script type="text/javascript">
				$(function(){
					var data = {
						browser_name: $.browser.name,
						browser_version: $.browser.version,
						screen_width: screen.width,
						screen_height: screen.height
					};
					$.post(href("user-statistics/save-client-side"), data, function(r){
						if(r != "ok")
							alert("Ошибка сохранения статистики: \n" + r);
					});
				});
			</script>
		';
	}
	
	// СОХРАНЕНИЕ КЛИЕНТСКОЙ СТАТИСТИКИ
	public function saveClientSideStatistics($bName, $bVer, $sW, $sH){
		
		// выход если сохранение статистики отключено
		if(!self::$_enabled)
			return;
		
		$this->_dbSave(array(
			'has_js' => 1,
			'browser_name' => $bName,
			'browser_version' => $bVer,
			'screen_width' => $sW,
			'screen_height' => $sH,
		));
		$_SESSION['vik-off-user-statistics']['is-client-stat-saved'] = TRUE;
	}
	
	// СОХРАНЕНИЕ АВТОРИЗАЦИОННОЙ СТАТИСТИКИ
	public function saveAuthStatistics($uid){
		
		// выход если сохранение статистики отключено
		if(!self::$_enabled)
			return;
	
		$this->_dbSave(array(
			'uid' => $uid,
		));
	}
	
	// СОХРАНЕНИЕ ДАННЫХ В БД
	private function _dbSave($fieldvalues){
		
		if(!$_SESSION['vik-off-user-statistics']['session-id']){
			
			$fieldvalues['user_ip'] 		= getVar($_SERVER['REMOTE_ADDR']);
			$fieldvalues['user_agent_raw'] 	= getVar($_SERVER['HTTP_USER_AGENT']);
			$fieldvalues['referer'] 		= getVar($_SERVER['HTTP_REFERER']);
			$fieldvalues['date'] 			= time();
			$_SESSION['vik-off-user-statistics']['session-id'] = db::get()->insert(self::TABLE, $fieldvalues);
		}else{
			db::get()->update(self::TABLE, $fieldvalues, 'id='.$_SESSION['vik-off-user-statistics']['session-id']);
		}
	}
	
	// МЕТОД ПРИГОТОВЛЕНИЯ ДАННЫХ ПЕРЕД ОТОБРАЖЕНИЕМ
	public static function beforeDisplay($data, $detail = FALSE){
			
		$data['date'] = YDate::loadTimestamp($data['date'])->getStrDateTime();
		
		if (!empty($data['pages'])) {
			
			$num = count($data['pages']);
			$data['num_pages'] = $num;
			
			if ($detail)
				foreach($data['pages'] as &$p)
					$p['date'] = YDate::loadTimestamp($p['date'])->getStrDateTime();
					
			$data['pages_info'] = array(
				'first_page' => $data['pages'][0]['url'],
				'last_page' => $data['pages'][ $num - 1 ]['url'],
				'first_page_time' => $detail ? $data['pages'][0]['date'] : YDate::loadTimestamp($data['pages'][0]['date'])->getStrDateTime(),
				'last_page_time' => $detail ? $data['pages'][ $num - 1 ]['date']: YDate::loadTimestamp($data['pages'][ $num - 1 ]['date'])->getStrDateTime(),
			);
		} else {
			$data['pages'] = null;
			$data['pages_info'] = null;
		}
			
		return $data;
	}
	
	// УДАЛИТЬ СТАРУЮ СТАТИСТИКУ
	public function deleteOldStatistics($expireTime){
		
		$minDate = time() - $expireTime;
		db::get()->delete(self::TABLE, 'date < '.$minDate);
	}
	
}


class UserStatistics_Collection extends ARCollection{
	
	// поля, по которым возможно сортировка коллекции
	// каждый ключ должен быть корректным выражением для SQL ORDER BY
	protected $_sortableFieldsTitles = array(
		'id' => array('id _DIR_', 'id'),
		'uid' => array('uid _DIR_', 'uid'),
		'user_ip' => 'IP',
		'referer' => 'referer',
		'has_js' => 'JS',
		'browser' => array('browser_name _DIR_, browser_version _DIR_', 'Браузер'),
		'screen_resolution' => array('screen_width * screen_height _DIR_', 'Разрешение'),
	);
	
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function load(){
			
		$instance = new UserStatisticsCollection();
		return $instance;
	}

	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
	public function getPaginated(){
		
		$sorter = new Sorter('s.id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.UserStatistics_Model::TABLE.' s ORDER BY '.$sorter->getOrderBy()), '~50');
		
		$db = db::get();
		$data = $db->getAllIndexed($paginator->getSql(), 'id', array());
		
		// получение посещенных страниц
		if (!empty($data)){
			$pages = $db->getAll('SELECT * FROM '.UserStatistics_Model::TABLE_PAGES.' WHERE session_id IN('.implode(',', array_keys($data)).')');
			foreach($pages as $p)
				$data[ $p['session_id'] ]['pages'][] = $p;
		}
		
		// получение краткой информации о страницах
		foreach($data as &$row)
			$row = UserStatistics_Model::beforeDisplay($row);
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>