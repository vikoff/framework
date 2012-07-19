<?

class Error_Model{
	
	const HANDLER_MODE = 1;
	const DISPLAY_MODE = 2;
	
	const MODULE = 'error';
	const FATAL_ERROR_MSG = 'Sorry, there was a mistake! Our experts are already working on a fix.';

	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Error/templates/';
	
	static private $_cssStylesDisplayed = FALSE;
	
	private $_dbId = 0;
	
	private $_errlevel;
	private $_errstr;
	private $_errfile;
	private $_errline;
	private $_errcontext;
	
	private $_backtrace;
	
	private $_textString;
	
	public $mode;
	public $firstTime;
	public $time;
	public $url;
	public $occurNum = 0;
	
	/**
	 * полный путь, по которому лежат шаблоны модуля
	 * назначается в конструкторе
	 */
	public $tplPath = null;
	
	private static $_config = array(
		'display' => TRUE,				// отображать ошибки или нет
		'minLevelForDisplay' => 0,		// минимальные права для отображения ошибок
		'keepFileLog' => FALSE,			// вести лог ошибок в файл
		'fileLogPath' => null,			// путь к файлу лога ошибок (обязателен при ведении файл-лога)
		'keepDbLog' => FALSE,			// вести лог ошибок в базу данных
		'dbTableName' => '',			// имя таблицы в БД
		'keepDbSessionDump' => FALSE,	// сохранять дамп сессии пользователя (только при включенном DB-логе)
		'keepEmailLog' => FALSE,		// отправлять сообщения об ошибках на email
		'emailForLog' => 'yurijnovikov@gmail.com', // email, на который отправлять лог ошибок
	);
	
	private static $_errorLevels = array(
		E_ERROR => 'E_ERROR',
		E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE',
		E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR',
		E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING => 'E_COMPILE_WARNING' ,
		E_USER_ERROR => 'E_USER_ERROR',
		E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE',
		E_STRICT => 'E_STRICT',
		'EXCEPTION' => 'UNCAUGHT EXCEPTION',
	);
	
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
				die('Не удалось установить конфигурацию обработчика обшибок. Неизвестный ключ ['.$key.']');
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
	
	
	// ОБРАБОТЧИК ОШИБОК (ТОЧКА ВХОДА В КЛАСС)(МЕТОД ВЫЗЫВАЕТСЯ ИНТЕРПРЕТАТОРОМ PHP)
	public static function error_handler($errlevel, $errstr, $errfile, $errline, $errcontext){
		
		$backtrace = debug_backtrace();
		array_shift($backtrace);
		foreach($backtrace as &$row)
			unset($row['object']);
		$instance = new Error_Model($errlevel, $errstr, $errfile, $errline, $errcontext, $backtrace, self::HANDLER_MODE);
	}

	public static function exception_handler(Exception $e){

		$backtrace = $e->getTrace();
		array_shift($backtrace);
		foreach($backtrace as &$row)
			unset($row['object']);
		$instance = new Error_Model('EXCEPTION', $e->getMessage(), $e->getFile(), $e->getLine(), null, $backtrace, self::HANDLER_MODE);
	}

	// ЗАГРУЗКА ОШИБКИ - МЕТОД САМОСТОЯТЛЬНО ИЗВЛЕКАЕТ ДАННЫЕ ИЗ БД (ТОЧКА ВХОДА В КЛАСС)
	public static function load($id){
		
		$data = db::get()->getRow('SELECT * FROM '.self::$_config['dbTableName'].' WHERE id='.(int)$id, FALSE);
		
		if(!$data)
			throw new Exception('Запись не найдена');
		
		return self::forceLoad($data['id'], $data);
	}
	
	// ЗАГРУЗКА ОШИБКИ - МЕТОД ПОЛУЧАЕТ УЖЕ ИЗВЛЕЧЕННЫЕ ДАННЫЕ(ТОЧКА ВХОДА В КЛАСС)
	public static function forceLoad($id, $data){
		
		$desc = unserialize(base64_decode($data['description']));
		$instance = new Error_Model(
			self::getVar($desc['errlevel']),
			self::getVar($desc['errstr']),
			self::getVar($desc['errfile']),
			self::getVar($desc['errline']),
			self::getVar($desc['errcontext']),
			self::getVar($desc['backtrace']),
			self::DISPLAY_MODE);
			
		$instance->_dbId = $id;
		$instance->firstTime = $data['firstdate'];
		$instance->time = $data['lastdate'];
		$instance->url  = $data['url'];
		$instance->occurNum  = $data['occur_num'];
		
		return $instance;
	}
	
	// КОНСТРУКТОР (СОЗДАЕТ ЭКЗЕМПЛЯР ОШИБКИ)
	public function __construct($errlevel, $errstr, $errfile, $errline, $errcontext, $backtrace, $mode = self::DISPLAY_MODE){
		
		$this->_errlevel = $errlevel;
		$this->_errstr = $errstr;
		$this->_errfile = $errfile;
		$this->_errline = $errline;
		$this->_errcontext = $errcontext;
		
		$this->_backtrace = $backtrace;
		$this->mode = $mode;
		
		// echo '<pre>'; print_r($this); die;
		if($mode == self::HANDLER_MODE)
			$this->handlerAction();
	}
	
	// СОХРАНИТЬ ПРОИЗОШЕДШУЮ ОШИБКУ
	public function handlerAction(){
		
		$this->time = time();
		$this->url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			
		if(self::$_config['keepFileLog'])
			$this->log2file();
			
		if(self::$_config['keepDbLog'])
			$this->log2db();
		
		if(self::$_config['keepEmailLog'])
			$this->log2email();
		
		if(self::$_config['display'] && (!self::$_config['minLevelForDisplay'] || CurUser::get()->level >= self::$_config['minLevelForDisplay']))
			$this->printHTML();
		
		if($this->_errlevel & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)){
			echo self::FATAL_ERROR_MSG;
			exit();
		}
		
	}
	
	public function getText(){
		
		if(is_null($this->_textString)){
			
			$this->_textString = self::$_errorLevels[$this->_errlevel].': '.$this->_errstr.' in '.$this->_errfile.' on line '.$this->_errline.".\n";

			foreach((array)$this->_backtrace as $index => $data){
				$this->_textString .= 
					'#'.$index.' '.self::getVar($data['file']).(isset($data['line']) ? ' ('.$data['line'].')' : '')
					.' '.self::getVar($data['class']).self::getVar($data['type']).self::getVar($data['function'])
					.'('.$this->_getArgsShort(self::getVar($data['args'], array())).');'."\n";
			}
			$this->_textString .= "\n";
		}
		return $this->_textString;
	}
	
	public function printHTML($return = FALSE){
			
		$_backtrace = '';
		foreach((array)$this->_backtrace as $index => $data){
			$_backtrace .= 
				'#'.$index.' '.self::getVar($data['file']).(isset($data['line']) ? ' ('.$data['line'].')' : '')
				.' <strong>'.self::getVar($data['class']).self::getVar($data['type']).self::getVar($data['function']).'(</strong>'
				.$this->_getArgsDetail(self::getVar($data['args'], array()))."<strong>)</strong>;<br />\n";
		}
		
		$MODE = $this->mode == self::HANDLER_MODE ? 'handler-mode' : 'display-mode';
		$DB_ID = $this->_dbId;
		$PLAIN_TEXT = $this->getText();
		$ERROR_LEVEL = self::getErrLevelString($this->_errlevel);
		$ERROR_STRING = $this->_errstr;
		$ERROR_FILE = $this->_errfile;
		$ERROR_LINE = $this->_errline;
		$BACKTRACE  = $_backtrace;
		$UID        = CurUser::id();
		$ERROR_FIRST_TIME = date('Y-m-d H:i:s', $this->firstTime);
		$ERROR_TIME = date('Y-m-d H:i:s', $this->time);
		$ERROR_URL  = $this->url;
		$OCCUR_NUM  = $this->occurNum;
		
		if($return){
			ob_start();
			echo $this->_getHtmlCssJs();
			include(FS_ROOT.self::TPL_PATH.'view.php');
			return ob_get_clean();
		}else{
			echo $this->_getHtmlCssJs();
			include(FS_ROOT.self::TPL_PATH.'view.php');
			return null;
		}
	
	}
	
	private function _getArgsShort($rawArgs){
		
		$args = array();
		foreach($rawArgs as $arg){
			$type = strtolower(gettype($arg));
			$string = $type;
			switch($type){
				case 'boolean': $string .= '['.($arg ? 'TRUE' : 'FALSE').']'; break;
				case 'integer': $string .= '['.$arg.']'; break;
				case 'double': $string .= '['.$arg.']'; break;
				case 'string': $string .= '[len: '.mb_strlen($arg, 'UTF-8').']'; break;
				case 'array': $string .= '[size: '.count($arg).']'; break;
			}
			$args[] = $string;
		}
		return implode(', ', $args);
	}
	
	private function _getArgsDetail($rawArgs){
		
		$args = array();
		foreach($rawArgs as $arg){
			$type = strtolower(gettype($arg));
			$string = $type;
			switch($type){
				case 'boolean': $string .= '['.($arg ? 'TRUE' : 'FALSE').']'; break;
				case 'integer': $string .= '['.$arg.']'; break;
				case 'double': $string .= '['.$arg.']'; break;
				case 'string': $string .= '[len: '.mb_strlen($arg, 'UTF-8').']'; break;
				case 'array': $string .= '[size: '.count($arg).']'; break;
				case 'object': $string .= '['.get_class($arg).']'; break;
				case 'resource': $string .= '[RESOURCE]'; break;
				case 'null': $string .= '[NULL]'; break;
			}
			$args[] = '<span onmouseover="Error.showDetail(this)" onmouseout="Error.hideDetail(this)" class="error-args-item"><span class="error-args-detail"><span class="error-args-short">'.$string.'</span><br />'.print_r($arg, 1).'</span><span class="error-args-short">'.$string.'</span></span>';
		}
		return implode(', ', $args);
	}
	
	private function _getHtmlCssJs(){
		
		if(self::$_cssStylesDisplayed){
			return '';
		}else{
			self::$_cssStylesDisplayed = TRUE;
			$f = FS_ROOT.self::TPL_PATH.'formatting.php';
			if(!file_exists($f))
				die('Файл стилей ['.$f.'] не найден '.__CLASS__.' #'.__LINE__);
			return preg_replace('/\s+/m', ' ', file_get_contents($f));
		}
	}
	
	private function _getUserString(){
	
		return date('j-m-Y H:i:s', $this->time).' Пользователь #'.$this->userid.' (права: '.$this->_usrePerms.')';
	}
	
	private function log2file(){
		
		if(is_null(self::$_config['fileLogPath'])){
			die('Путь к лог-файлу не указан '.__CLASS__.' #'.__LINE__);
		}
			
		if(!is_dir(self::$_config['fileLogPath']))
			mkdir(self::$_config['fileLogPath'], true);
		
		$txt = $this->_getUserString()."\n".$this->getText()."\n\n";
		
		$rs = fopen(self::$_config['fileLogPath'].'error.log', 'a') or die('Не удалось открыть лог-файл '.__CLASS__.' #'.__LINE__);
		fwrite($rs, $txt) or die('Не удалось произвести запись в лог-файл');
		fclose($rs) or die('Не удалось закрыть лог-файл');
	}
	
	private function log2db(){
		
		$fields = array();
		$fields['url'] = $this->url;
		$fields['description'] = base64_encode(serialize(array(
			'errlevel' => $this->_errlevel,
			'errstr' => $this->_errstr,
			'errfile' => $this->_errfile,
			'errline' => $this->_errline,
			// 'errcontext' => $this->_errcontext,
			'backtrace' => $this->_backtrace,
		)));
		$fields['hash'] = md5($fields['description']);
		if(self::$_config['keepDbSessionDump'])
			$fields['session_dump'] = base64_encode(serialize($_SESSION));
		
		$fields['uid'] = CurUser::id();
		$fields['lastdate'] = time();
		$db = db::get();
		
		if($lastid = $db->getOne('SELECT id FROM '.self::$_config['dbTableName'].' WHERE hash='.$db->qe($fields['hash']).' LIMIT 1', 0)){
			$db->update(self::$_config['dbTableName'],
				array('lastdate' => $fields['lastdate'], 'occur_num' => $db->raw('occur_num+1')),
				'id='.$lastid);
		}else{
			$fields['occur_num'] = 1;
			$fields['firstdate'] = $fields['lastdate'];
			$db->insert(self::$_config['dbTableName'], $fields);
		}
	}
	
	private function log2email(){

	}
	
	private static function getErrLevelString($errlevel){
		
		return isset(self::$_errorLevels[$errlevel])
			? self::$_errorLevels[$errlevel]
			: '';
	}

	public function destroy(){
		
		if(!$this->_dbId)
			throw new Exception('Невозможно удалить запись. Неверное значение ID: '.$this->_dbId);
		
		db::get()->delete(self::$_config['dbTableName'], 'id='.$this->_dbId);
	}
		
	public static function getVar(&$varname, $defaultVal = '', $type = ''){

		if(!isset($varname))
			return $defaultVal;
		
		if(strlen($type))
			settype($varname, $type);
		
		return $varname;
	}
	
	public function sendMail($body){

		$path = FS_ROOT.'models/PHPMailer/';
		require_once($path.'class.phpmailer.php');

		$mail             = new PHPMailer();
		$mail->PluginDir  = $path;

		$mail->IsSMTP();
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host       = "smtp.gmail.com";
		$mail->Port       = 465;
		$mail->Username   = "mailing.yurijnovikov@gmail.com";
		$mail->Password   = base64_decode('dVdRNDV2dTJxNTRxNw==');

		$mail->Subject    = "wimmarket.com error";
		$mail->MsgHTML($body);
		$mail->AltBody    = strip_tabs(preg_replace('~<br\s*/?>~', "\n", $body));

		$mail->AddAddress(self::$_config['emailForLog']);

		return $mail->Send()
			? 'ok'
			: "Mailer Error: " . $mail->ErrorInfo;
	}
}

class Error_Collection extends ARCollection {
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function load(){
			
		$instance = new ErrorCollection();
		return $instance;
	}

	// ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ
	public function getPaginated(){
		
		$paginator = new Paginator('sql', array('*', 'FROM '.Error_Model::getConfig('dbTableName').' ORDER BY id DESC'), '~50');
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = Error_Model::forceLoad($row['id'], $row)->printHTML($return = TRUE);
		
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>