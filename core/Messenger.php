<?php

/**
 * Класс для работы с сообщениями для пользователей.
 * @using nothing
 */
class Messenger {
	
	/* используемый namespace */
	private $_ns = 'default';
	
	/* контейнер сообщений */
	private $_messages = array('error' => array(), 'info' => array(), 'success' => array());
	
	/** экземпляр Messenger */
	private static $_instance = null;
	
	/**
	 * точка входа в класс
	 * @return Messenger
	 */
	public static function get($ns = 'default'){
		
		if(is_null(self::$_instance))
			self::$_instance = new Messenger();
		
		self::$_instance->ns($ns);
		
		return self::$_instance;
	}
	
	/* конструктор */
	private function __construct(){
		
		if (!empty($_SESSION['_messengerData'])) {
			$this->_messages = $_SESSION['_messengerData'];
			$_SESSION['_messengerData'] = null;
		}
	}
	
	/** задать namespace */
	public function ns($ns){
		
		$this->_ns = $ns;
		return $this;
	}
	
	public function addSuccess($msg, $detail = ''){
		
		$fullMsg = nl2br(
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
		
		if (!isset($this->_messages['success'][$this->_ns]))
			$this->_messages['success'][$this->_ns] = array();

		$this->_messages['success'][$this->_ns][] = $fullMsg;
	}
	
	public function addInfo($msg, $detail = ''){
		
		$fullMsg = nl2br(
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
		
		if (!isset($this->_messages['info'][$this->_ns]))
			$this->_messages['info'][$this->_ns] = array();

		$this->_messages['info'][$this->_ns][] = $fullMsg;
	}
	
	public function addError($msg, $detail = ''){
		
		$fullMsg = nl2br(
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
		
		if (!isset($this->_messages['error'][$this->_ns]))
			$this->_messages['error'][$this->_ns] = array();

		$this->_messages['error'][$this->_ns][] = $fullMsg;
	}

	/** получить все пользовательские сообщения */
	public function getAll(){
		
		$htmls = array();

		foreach ($this->_messages as $type => $messages) {
			$html = '';
			if (!empty($messages[$this->_ns])) {
				foreach ($messages[$this->_ns] as $m)
					$html .= '<div class="item">'.$m.'</div>';
				$htmls[$type] = '<div class="vik-user-message '.$type.'">'.$html.'</div>';
				unset($this->_messages[$type][$this->_ns]);
			}
		}

		return implode('', $htmls);
	}

	public function __destruct() {

		if (!empty($this->_messages)) {
			$_SESSION['_messengerData'] = $this->_messages;
		}
	}
	
}

?>