<?php

class Request {
	
	private static $_instance = null;
	
	private $_requestString = '';
	private $_requestArr = array();
	private $_controller = null;
	private $_params = null;
	
	// ТОЧКА ВХОДА В КЛАСС
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Request(isset($_GET['r']) ? $_GET['r'] : '');
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	private function __construct($requestString){
		
		// hack to prevent display, if favicon.ico requested
		if($requestString == 'favicon.ico'){
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); // 'HTTP/1.1 404 Not Found'
			exit;
		}
		
		$this->_requestString = $requestString;
		$this->parseRequest();
		
		if (!empty($_GET))
			$_GET = Tools::unescape($_GET);
		
		if (!empty($_POST))
			$_POST = Tools::unescape($_POST);
	}
	
	/** РАЗБОР URL ЗАПРОСА */
	public function parseRequest(){
		
		$tmpRequestArr = explode('/', $this->_requestString);
		if ($tmpRequestArr[0] != 'admin')
			$this->checkAlias();
		
		$_rArr = array();
		foreach(explode('/', $this->_requestString) as $item){
			$item = trim($item);
			if(strlen($item))
				$_rArr[] = $item;
		}
		
		$this->_requestArr = $_rArr;
		
		$this->_controller = array_shift($_rArr);	// string
		$this->_params = $_rArr;					// array
		
	}
	
	/** ПРОВЕРИТЬ НАЛИЧИЕ ПСЕВДОНИМА */
	public function checkAlias(){
		
		if (empty($this->_requestString))
			return;
		
		$realpath = Alias_Manager::getPath($this->_requestString);
		if ($realpath)
			$this->_requestString = $realpath;
	}
	
	/** ПОЛУЧИТЬ ПАРАМЕТРЫ ЗАПРОСА В ВИДЕ МАССИВА */
	public function getArray(){
	
		return array($this->_controller, $this->_params);
	}
	
	/** ПОЛУЧИТЬ ПАРАМЕТРЫ ЗАПРОСА В ВИДЕ МАССИВА-СПИСКА */
	public function getRawArray(){
		
		return $this->_requestArr;
	}
	
	/** ПОЛУЧИТЬ ПАРАМЕТРЫ ЗАПРОСА В ВИДЕ СТРОКИ */
	public function getString(){
		
		return $this->_requestString;
	}
	
	/** ПОЛУЧИТЬ ВСЕ GET ПАРАМЕТРЫ (кроме 'r') */
	public function getParams(){
		$params = $_GET;
		unset($params['r']);
		return $params;
	}
	
	/**
	 * GET APPENDED
	 * Получить массив Request дополненный одним или несколькими элементами.
	 * @param string|array $forAppend string|array - элемент(ы) для добавления
	 * @param bool $toArray - если TRUE - выводить как массив, иначе как строку
	 * @return string|array $_requestArr with appended string
	 */
	public function getAppended($forAppend, $toArray = FALSE){
		
		$output = array();
		foreach(array_merge($this->_requestArr, (array)$forAppend) as $item)
			if(strlen($item))
				$output[] = $item;
		
		return $toArray
			? $output
			: implode('/', $output);
	}
	
	public function getParts($indexes){
		
		$parts = array();
		foreach((array)$indexes as $i)
			if(isset($this->_requestArr[$i]))
				$parts[] = $this->_requestArr[$i];
		
		return implode('/', $parts);
	}
	
}

?>