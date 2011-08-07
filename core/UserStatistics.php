<?
/**
 * Фронт-контроллер приложения. 
 * 
 * @using constants
 * 		User::TABLE
 * 		
 * @using methods
 * 		YDate::timestamp2date()
 * 		App::href()
 * 		User::getPermName()
 * 		
 * 		
 */
class UserStatistics{
	
	private $_statisticsEnabled = null;
	
	// конфигурация класса
	private static $_config = array(
		'enable' => FALSE,			// вести статистику
		'dbTableName' => '',		// имя таблицы в БД
	);
	
	private static $_instance = null;
	
	
	/**
	 * Задать конфигурацию класса
	 * @param array $config - массив директива=>значение
	 * @return void;
	 */
	public static function setConfig($config){
	
		foreach($config as $key => $val){
			if(array_key_exists($key, self::$_config)){
				self::$_config[$key] = $val;
			}else{
				trigger_error('Не удалось установить конфигурацию класса UserStatistics. Неизвестный ключ ['.$key.']', E_USER_ERROR);
			}
		}
	}
	
	/**
	 * Получить значение конфигурационной директивы, или весь массив конфигурации
	 * @param null|string $key
	 * @return array|string
	 */
	public static function getConfig($key = null){
		
		return is_null($key)
			? self::$_config
			: self::$_config[$key];
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР КЛАССА */
	public static function get(){
	
		if(is_null(self::$_instance))
			self::$_instance = new UserStatistics();
			
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		$this->_statisticsEnabled = self::$_config['enable'];
		
		if(!$this->_statisticsEnabled)
			return;
			
		if(!$this->_isSessionInited())
			$this->_initSession();
	}
	
	// ПОЛУЧИТЬ СТРОКУ ДАННЫХ ИЗ ТАБЛИЦЫ
	public function getRowPrepared($id){
		
		$data = db::get()->getRow('
			SELECT s.*, u.name, u.surname, u.level
			FROM '.self::$_config['dbTableName'].' AS s
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
		if(!$this->_statisticsEnabled)
			return;
		
		// определение запришиваемого URL
		$requestUrl = getVar($_SERVER['SERVER_NAME']).getVar($_SERVER['REQUEST_URI']);
		
		// если запрашиваемый URL совпадает с предыдущим, ничего не сохраняем
		if($requestUrl == $_SESSION['vik-off-user-statistics']['last-url'])
			return;
		
		if(!$_SESSION['vik-off-user-statistics']['session-id']){
		
			$_SESSION['vik-off-user-statistics']['session-id'] = db::get()->insert(self::$_config['dbTableName'], array(
				'request_urls' 	 => $requestUrl.' '.time().'\\n',
				'user_ip' 		 => getVar($_SERVER['REMOTE_ADDR']),
				'user_agent_raw' => getVar($_SERVER['HTTP_USER_AGENT']),
				'referer' 		 => getVar($_SERVER['HTTP_REFERER']),
				'date'			 => time(),
			));
			
		}else{
		
			$requestUrlAddit = $requestUrl.' '.time().'\\n';
			db::get()->query("
				UPDATE ".self::$_config['dbTableName']." SET
				request_urls=COALESCE(request_urls,'')||'".$requestUrlAddit."',
				-- request_urls=IF(request_urls IS NULL, '".$requestUrlAddit."', CONCAT(request_urls,'".$requestUrlAddit."')),
				date='".time()."'
				WHERE id=".$_SESSION['vik-off-user-statistics']['session-id']);
		}
		
		// сохраняем запрашиваемый URL
		$_SESSION['vik-off-user-statistics']['last-url'] = $requestUrl;
	}
	
	// ПРОВЕРКА НЕОБХОДИМОСТИ СОХРАНЕНИЯ КЛИЕНТСКОЙ СТАТИСТИКИ
	public function checkClientSideStatistics(){
		
		// выход если сохранение статистики отключено
		if(!$this->_statisticsEnabled)
			return FALSE;
	
		return empty($_SESSION['vik-off-user-statistics']['is-client-stat-saved']);
	}
	
	// ПОЛУЧИТЬ HTML ДЛЯ СОХРАНЕНИЯ КЛИЕНТСКОЙ СТАТИСТИКИ
	public function getClientSideStatisticsLoader(){
		
		// выход если сохранение статистики отключено
		if(!$this->_statisticsEnabled)
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
					$.post("ajax.php?r=profile/save_user_stat", data, function(r){
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
		if(!$this->_statisticsEnabled)
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
		if(!$this->_statisticsEnabled)
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
			$_SESSION['vik-off-user-statistics']['session-id'] = db::get()->insert(self::$_config['dbTableName'], $fieldvalues);
		}else{
			db::get()->update(self::$_config['dbTableName'], $fieldvalues, 'id='.$_SESSION['vik-off-user-statistics']['session-id']);
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
		db::get()->delete(self::$_config['dbTableName'], 'date < '.$minDate);
	}
	
}


class UserStatisticsCollection extends GenericObjectCollection{
	
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
			'FROM '.UserStatistics::getConfig('dbTableName').' AS s
			LEFT JOIN '.User::TABLE.' AS u ON u.id=s.uid
			ORDER BY '.$sorter->getOrderBy()), '~50');
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = UserStatistics::beforeDisplay($row);
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>