<?php

class Config {
	
	private static $_instance = null;

	private $_envHost = null;
	private $_envMode = null;
	
	
	/** инициализация экземпляра класса */
	public static function init(){
		
		if(!is_null(self::$_instance))
			trigger_error('Объект класса Config уже инициализирован', E_USER_ERROR);
		
		self::$_instance = new Config();
		self::$_instance->loadFiles();
	}
	
	/**
	 * получение экземпляра класса
	 * @return Config
	 */
	public static function get(){
		
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	private function __construct(){
		
		// определение окружения
		if (!file_exists(FS_ROOT.'config/env.php'))
			trigger_error('Enviroment file not found', E_USER_ERROR);

		$env = include(FS_ROOT.'config/env.php');
		
		$this->_envMode = $env['mode'];
		$this->_envHost = $env['host'];

	}

	public function loadFiles(){

		require(FS_ROOT.'config/config.'.$this->_envMode.'.php');
		require(FS_ROOT.'config/global.php');
	}
	
	public function getModulesConfig(){
		
		return require(FS_ROOT.'config/modules.php');
	}

	public function getEnvMode(){

		return $this->_envMode;
	}

	public function getEnvHost(){

		return $this->_envHost;
	}

}

?>