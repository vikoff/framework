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
		
		$data = db::get()->getRow('
			SELECT s.*, u.name, u.surname, u.level
			FROM '.self::TABLE.' AS s
			LEFT JOIN '.User::TABLE.' AS u ON u.id=s.uid
			WHERE s.id='.(int)$id,
			FALSE);
		
		if(!$data)
			throw new Exception('Данные не найдены');
			
		return self::beforeDisplay($data, $detail = TRUE);

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
			
		$urlsArr = !empty($data['request_urls'])
			? explode("\n", $data['request_urls'])
			: array();
		unset($data['request_urls']); // некоторое высвобождение памяти
		$data['num_pages'] = count($urlsArr);
		
		if(count($urlsArr)){
		
			$data['has_pages'] = TRUE;
			
			// данные для детального просмотра
			if($detail){
				$data['request_urls'] = array();
				foreach($urlsArr as $url){
					if($url){
						$rowArr = explode(' ', $url);
						$data['request_urls'][] = array(
							'url' => $rowArr[0],
							'date' => YDate::timestamp2date($rowArr[1]));
					}
				}
			}
			// данные для списка (кратко)
			else{
				$firstPage = explode(' ', $urlsArr[0]);
				$lastPage = explode(' ', $urlsArr[count($urlsArr) - 2]);
				
				$data['first_page'] = array(
					'url' => $firstPage[0],
					'date' => YDate::timestamp2date($firstPage[1])
				);
				$data['last_page'] = array(
					'url' => $lastPage[0],
					'date' => YDate::timestamp2date($lastPage[1])
				);
			}
			
		}
		
		$data['user'] = $data['uid']
			? '<a href="'.App::href('admin/users/view/'.$data['uid']).'">'.$data['surname'].' '.$data['name'].'</a><br /><i>'.User::getPermName($data['level']).'</i>'
			: User::getPermName(0);
		$data['screen_resolution'] = $data['has_js']
			? $data['screen_width'].'x'.$data['screen_height']
			: '-';
		$data['browser'] = $data['has_js']
			? $data['browser_name'].' '.$data['browser_version']
			: '-';
		$data['has_js_text'] = $data['has_js']
			? '<span class="green">✔</span>'
			: '<span class="red">✘</span>';
			
		return $data;
	}
	
	// УДАЛИТЬ СТАРУЮ СТАТИСТИКУ
	public function deleteOldStatistics($expireTime){
		
		$minDate = time() - $expireTime;
		db::get()->delete(self::TABLE, 'date < '.$minDate);
	}
	
}


class UserStatisticsCollection extends ARCollection{
	
	// поля, по которым возможно сортировка коллекции
	// каждый ключ должен быть корректным выражением для SQL ORDER BY
	protected $_sortableFieldsTitles = array(
		'id' => array('s.id _DIR_', 'id'),
		'uid' => array('u.surname _DIR_, u.name _DIR_', 'uid'),
		'user_ip' => 'IP',
		'referer' => 'referer',
		'has_js' => 'JS',
		'browser' => array('browser_name _DIR_, browser_version _DIR_', 'Браузер'),
		'screen_resolution' => array('screen_width * screen_height _DIR_', 'Разрешение'),
	);
	
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function Load(){
			
		$instance = new UserStatisticsCollection();
		return $instance;
	}

	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
	public function getPaginated(){
		
		$sorter = new Sorter('s.id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('s.*, u.name, u.surname, u.level',
			'FROM '.UserStatistics_Model::TABLE.' AS s
			LEFT JOIN '.User::TABLE.' AS u ON u.id=s.uid
			ORDER BY '.$sorter->getOrderBy()), '~50');
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = UserStatistics_Model::beforeDisplay($row);
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>