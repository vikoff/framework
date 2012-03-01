<?php

/**
 * Класс для работы с сообщениями для пользователей.
 * @using nothing
 */
class Messenger{
	
	// имя $_GET параметра для передачи идентификатора
	private $_qsParamName = 'fmsgid';
	
	// используемый namespace
	private $_ns = null;
	
	// контейнеры пользовательских сообщений
	private $_successMsgs = array();
	private $_infoMsgs = array();
	private $_errorMsgs = array();
	
	// ключ для будущих сообщений
	private $_futureKey = null;
	
	// полученный идентификатор будущих сообщений
	private $_receivedFutureKey = null;
	
	private static $_instance = null;
	
	// ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР Messenger)
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Messenger();
		
		// каждый вызов get() сбрасывает namespace на дефолтное
		self::$_instance->defaultNs();
		
		return self::$_instance;
	}
	
	// КОНСТРУКТОР
	private function __construct(){
		
		$this->_receivedFutureKey = !empty($_REQUEST[$this->_qsParamName]) ? (int)$_REQUEST[$this->_qsParamName] : 0;
		$this->_future2present();
	}
	
	// ЗАДАТЬ NAMESPACE
	public function ns($ns){
		
		if(!empty($ns))
			$this->_ns = $ns;
		else
			trigger_error('Значение namespace не должно быть пустым', E_USER_ERROR);
		return $this;
	}
	
	// СБРОСИТЬ NAMESPACE НА ДЕФОЛТНОЕ
	public function defaultNs(){
	
		$this->_ns = null;
		return $this;
	}
	
	public function addSuccess($msg, $detail = ''){
		
		$fullMsg = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), 
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
	
		$this->_successMsgs[] = is_null($this->_ns)
			? $fullMsg
			: array($this->_ns => $fullMsg);
	}
	
	public function addInfo($msg, $detail = ''){
		
		$fullMsg = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), 
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
			
		$this->_infoMsgs[] = is_null($this->_ns)
			? $fullMsg
			: array($this->_ns => $fullMsg);
	}
	
	public function addError($msg, $detail = ''){
		
		$fullMsg = str_replace(array("\n", "\r\n"), array('<br />', '<br />'), 
			'<div>'.$msg.'</div>'
			.($detail ? '<div class="detail">'.$detail.'</div>' : ''));
		
		$this->_errorMsgs[] = is_null($this->_ns)
			? $fullMsg
			: array($this->_ns => $fullMsg);
	}

	// ПОЛУЧИТЬ ВСЕ ПОЛЬЗОВАТЕЛЬСКИЕ СООБЩЕНИЯ
	public function getAll(){
		
		$html = '';
		if(count($this->_successMsgs)){
			$msgs = '';
			foreach($this->_successMsgs as &$m){
				$msg = '';
				if(is_null($this->_ns)){
					if(is_string($m))
						$msg = $m;
				}else{
					if(is_array($m) && isset($m[$this->_ns]))
						$msg = $m[$this->_ns];
				}
				if($msg){
					unset($m);
					$msgs .= '<div class="item">'.$msg.'</div>';
				}
			}
			if($msgs)
				$html .= '<div class="vik-user-message success">'.$msgs.'</div>';
		}

		if(count($this->_infoMsgs)){
			$msgs = '';
			foreach($this->_infoMsgs as &$m){
				$msg = '';
				if(is_null($this->_ns)){
					if(is_string($m))
						$msg = $m;
				}else{
					if(is_array($m) && isset($m[$this->_ns]))
						$msg = $m[$this->_ns];
				}
				if($msg){
					unset($m);
					$msgs .= '<div class="item">'.$msg.'</div>';
				}
			}
			if($msgs)
				$html .= '<div class="vik-user-message info">'.$msgs.'</div>';
		}

		if(count($this->_errorMsgs)){
			$msgs = '';
			foreach($this->_errorMsgs as &$m){
				$msg = '';
				if(is_null($this->_ns)){
					if(is_string($m))
						$msg = $m;
				}else{
					if(is_array($m) && isset($m[$this->_ns]))
						$msg = $m[$this->_ns];
				}
				if($msg){
					unset($m);
					$msgs .= '<div class="item">'.$msg.'</div>';
				}
			}
			if($msgs)
				$html .= '<div class="vik-user-message error">'.$msgs.'</div>';
		}

		return $html;
	}

	// ДОБАВИТЬ КЛЮЧ БУДУЩИХ СООБЩЕНИЙ В QS
	public function qsAppendFutureKey($qs){

		$this->_present2future();

		// если ключ будущих сообщений уже присутствует в QS
		if(strpos($qs, $this->_qsParamName.'=')){
			$key = $this->_hasFuture() ? $this->_getFutureKey() : 0;
			$qs = preg_replace('/'.$this->_qsParamName.'=\d*/', $this->_qsParamName.'='.$key, $qs);
		}
		// если ключ не присутствует, а добавить надо
		elseif($this->_hasFuture()){
			$concatenator = '';
			if(strpos($qs, '?') === FALSE){
				$concatenator = '?';
			}else{
				$lastChar = substr($qs, -1, 1);
				$concatenator = ($lastChar == '?' || $lastChar == '&') ? '' : '&';
			}
			$qs .= $concatenator.$this->_qsParamName.'='.$this->_getFutureKey();
		}
		
		return $qs;
	}
	
	// ИЗВЛЕЧЬ НУЖНЫЕ СООБЩЕНИЯ ИЗ МАССИВА (С УЧЕТОМ NAMESPACE)
	private function _extractMessages(&$msgArr){
		
		$html = '';
		
		if(count($msgArr)){
			$msgs = '';
			foreach($this->msgArr as &$m){
				$msg = '';
				if(is_null($this->_ns)){
					if(is_string($m))
						$msg = $m;
				}else{
					if(is_array($m) && isset($m[$this->_ns]))
						$msg = $m[$this->_ns];
				}
				if($msg){
					unset($m);
					$msgs .= '<div class="item">'.$msg.'</div>';
				}
			}
			if($msgs)
				$html .= '<div class="vik-user-message error">'.$msgs.'</div>';
		}
		
		return $html;
	}
	
	// ПРОВЕРКА ИНИЦИАЛИЗАЦИИ МЕХАНИЗМА БУДУЩИХ СООБЩЕНИЙ
	private function _checkFutureInitialization(){
	
		if(!isset($_SESSION['featureUserMessages']))
			$_SESSION['featureUserMessages'] = array('lastKey' => 0, 'messages' => array());
	}
	
	// ПОЛУЧИТЬ ТЕКУЩИЙ КЛЮЧ ДЛЯ БУДУЩИХ СООБЩЕНИЙ
	private function _getFutureKey(){
		
		$this->_checkFutureInitialization();
		
		if(is_null($this->_futureKey)){
			$this->_futureKey = ++$_SESSION['featureUserMessages']['lastKey'];
		}
		return $this->_futureKey;
	}
	
	// ПРОВЕРИТЬ, ИМЕЮТСЯ ЛИ БУДУЩИЕ СООБЩЕНИЯ
	private function _hasFuture(){
	
		$identifier = $this->_getFutureKey();
		return isset($_SESSION['featureUserMessages']['messages'][$identifier]);
	}
	
	private function _clearPresent(){
	
		$this->_successMsgs = array();
		$this->_infoMsgs = array();
		$this->_errorMsgs = array();
	}
	
	// ПРЕОБРАЗОВАТЬ НАСТОЯЩИЕ СООБЩЕНИЯ В БУДУЩИЕ
	private function _present2future(){
		
		$identifier = $this->_getFutureKey();
		
		foreach($this->_successMsgs as $msg)
			$_SESSION['featureUserMessages']['messages'][$identifier]['success'][] = $msg;
		
		foreach($this->_infoMsgs as $msg)
			$_SESSION['featureUserMessages']['messages'][$identifier]['info'][] = $msg;
		
		foreach($this->_errorMsgs as $msg)
			$_SESSION['featureUserMessages']['messages'][$identifier]['error'][] = $msg;
		
		$this->_clearPresent();
	}
	
	// ПРЕОБРАЗОВАТЬ БУДУЩИЕ СООБЩЕНИЯ В НАСТОЯЩИЕ
	private function _future2present(){
		
		$futureArr = $this->_getFuture();
		
		foreach($futureArr['success'] as $msg)
			$this->_successMsgs[] = $msg;
		
		foreach($futureArr['info'] as $msg)
			$this->_infoMsgs[] = $msg;
		
		foreach($futureArr['error'] as $msg)
			$this->_errorMsgs[] = $msg;
	}
	
	// ПОЛУЧИТЬ ВСЕ БУДУЩИЕ СООБЩЕНИЯ
	private function _getFuture(){
		
		$identifier = $this->_receivedFutureKey;
		$output = array('success' => array(), 'info' => array(), 'error' => array());
		
		if(!$identifier || empty($_SESSION['featureUserMessages']['messages'][$identifier]))
			return $output;
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['success']))
			$output['success'] = $_SESSION['featureUserMessages']['messages'][$identifier]['success'];
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['info']))
			$output['info'] = $_SESSION['featureUserMessages']['messages'][$identifier]['info'];
		
		if(!empty($_SESSION['featureUserMessages']['messages'][$identifier]['error']))
			$output['error'] = $_SESSION['featureUserMessages']['messages'][$identifier]['error'];
		
		unset($_SESSION['featureUserMessages']['messages'][$identifier]);
		
		return $output;
	}
	
}

?>