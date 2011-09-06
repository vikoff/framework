<?

class Config {
	
	private static $_instance = null;
	
	
	/** ИНИЦИАЛИЗАЦИЯ ЭКЗЕМПЛЯРА КЛАССА */
	public static function init(){
		
		if(!is_null(self::$_instance))
			trigger_error('Объект класса Config уже инициализирован', E_USER_ERROR);
		
			self::$_instance = new Config();
	}
	
	/** ПОЛУЧЕНИЕ ЭКЗЕМПЛЯРА КЛАССА */
	public static function get(){
		
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	public function __construct(){
		
		// подключение файлов
		require(FS_ROOT.'config/config.'.RUN_MODE.'.php');
		require(FS_ROOT.'config/global.php');
		require(FS_ROOT.'config/modules.php');
	}

}

?>