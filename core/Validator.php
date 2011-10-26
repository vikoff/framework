<?

class Validator {
	
	// Разделитель валидационных сообщений об ошибках
	const ERRORS_SEPARATOR = "<br />";
	
	// контейнер преобразованных значений, прошедших валидацию
	private $validData = array();
	
	// контейнер всех правил
	private $elmsRules = array();
	
	// отключенные правила
	private $disabledRules = array();
	
	// массив названий полей (для сообщений об ошибках)
	private $_fieldTitles = array();
	
	// массив ошибок валидации
	private $validationErrors = array();
	
	// хранение сообщений об ошибках для кодов ошибок
	static private $_errorMsgs = array(
		'emptyDataSet'    => 'данные формы не были получены',
		'invalidDataType' => 'поле {fieldname} имеет недопустимый формат',
		'required'        => 'поле {fieldname} обязательно для заполнения',
		'email'           => 'в поле {fieldname} введен некорректный email-адрес',
		'match'           => 'поле {fieldname} содержит недопустимые символы или имеет недопустимый формат',
		'in'              => 'поле {fieldname} может принимать только значения {validValues}',
		'notIn'           => 'в поле {fieldname} введено недопустимое значение',
		'equal'           => 'в поле {fieldname} введено недопустимое значение',
		'notEqual'        => 'в поле {fieldname} введено недопустимое значение',
		'captcha'         => 'антибот тест не пройден',
		'compare'         => 'поля {fieldname} и {field2name} не совпадают',
		'length'          => 'поле {fieldname} должно быть длиной {minlength} {maxlength} символов',
		'dbDate'          => 'поле {fieldname} должно иметь формат ГГГГ-ММ-ДД',
		'dbTime'          => 'поле {fieldname} должно иметь формат ЧЧ-ММ-СС',
		'dbDateTime'      => 'поле {fieldname} должно иметь формат ГГГГ-ММ-ДД ЧЧ-ММ-СС',
		'int_type'        => 'поле {fieldname} должно содержать только цифры',
		'float_type'      => 'поле {fieldname} должно содержать число',
		'array'      => 'поле {fieldname} имеет неверный формат',
	);
	
	/** порядок вызова правил (заодно и полный их список) */
	static public $rulesOrder = array(
		'trim',
		'settype',
		'strip_tags',
		'htmlspecialchars',
		'escape',
		'required',
		'email',
		'match',
		'length',
		'in',
		'notIn',
		'equal',
		'notEqual',
		'hash',
		'captcha',
		'compare',
		'checkbox',
		'dbDate',
		'dbTime',
		'dbDateTime',
		'unsetAfter',
	);

	
	// ######### ЗАГРУЗКА ИСХОДНЫХ ДАННЫХ В КЛАСС ############# //

	// КОНСТРУКТОР
	public function __construct($individualRules = array(), $commonRules = array()){
		
		if(!empty($commonRules) || !empty($individualRules))
			$this->rules($individualRules, $commonRules);
	}
	
	// ЗАДАТЬ ЗАГОЛОВКИ ДЛЯ ПОЛЕЙ (ДЛЯ ОТОБРАЖЕНИЯ В СООБЩЕНИЯХ ОБ ОШИБКАХ)
	public function setFieldTitles($fieldsTitles){
		
		$this->_fieldTitles = $fieldsTitles;
		return $this;
	}
	
	// ЗАДАНИЕ ПРАВИЛ ВАЛИДАЦИИ
	public function rules($individualRules, $commonRules = array()){
		
		// допустимые индивидуальные правила
		$allowedIndividualRules = array_flip(self::$rulesOrder);
		
		// допустимые общие правила
		$allowedCommonRules = array_flip(array(
			'trim',
			'strip_tags',
			'htmlspecialchars',
			'escape',
			'required',
			'unsetAfter',
			'email',
		));
		
		// применение индивидуальных правил
		foreach($individualRules as $elm => $rules){
			$this->elmsRules[$elm] = array();
			foreach($rules as $rule => $params){
				if(isset($allowedIndividualRules[$rule]))
					$this->elmsRules[$elm][$rule] = $params;
				else
					$this->fatalError('Правило "'.$rule.'" не существует.');
			}
		}
		
		// применение общих правил
		foreach($commonRules as $name => $elms){
			
			if (!isset($allowedCommonRules[$name]))
				$this->fatalError('Правило "'.$name.'" не может быть задано в наборе общих правил');
				
			foreach( ($elms === '*' ? array_keys($this->elmsRules) : $elms) as $elm){
				if (!isset($this->elmsRules[$elm]))
					$this->fatalError('Элемент "'.$elm.'", описанный в общих правилах валидации, отсутствует в наборе индивидуальных правил.');
				if (!isset($this->elmsRules[$elm][$name]))
					$this->elmsRules[$elm][$name] = TRUE;
			}
		}

	}
	
	
	// ####################### ВАЛИДАЦИЯ ####################### //
	
	/**
	 * ПРИМЕНЕНИЕ ПРАВИЛ ВАЛИДАЦИИ
	 * наличие ошибок в исходных данных можно проверить методом $this->hasError()
	 * получить сообщения об ошибках (если они есть) можно методом $this->getError()
	 * @param array $inputData - данные для валидации в виде одномерного ассоциативного массива
	 * @param null|array $additValidationRules - дополнительные правила валидации
	 *        вида array('field' => array('rule1' => 'params', 'rule2' => 'params') )
	 * @return array - валидные данные
	 */
	public function validate($inputData){
		
		// сброс состояния валидатора
		$this->reset();
		
		// проверка наличия исходных данных
		if (!is_array($inputData)) {
			$this->setError($this->getErrorText('', 'emptyDataSet'));
			return;
		}
		
		// инициализация массива валидных данных (содержит разрешенные и назначенные поля)
		foreach($this->elmsRules as $field => $rules)
			if(isset($inputData[$field]))
				$this->validData[$field] = $inputData[$field];
		
		// применение правил валидации
		foreach($this->elmsRules as $field => $definedRules){
			
			// проверка, имеет ли поле допустимый формат (скаляр)
			if(isset($this->validData[$field]) && !is_scalar($this->validData[$field])){
				$this->setError($this->getErrorText($field, 'invalidDataType'));
				continue;
			}
			
			// вызов правил валидации
			foreach(self::$rulesOrder as $regularRule){
				if(isset($definedRules[$regularRule]) && empty($this->disabledRules[$regularRule])){
					call_user_func(
						array($this, 'rule_'.$regularRule), // имя метода			
						$field,								// имя поля
						$definedRules[$regularRule]			// параметры для правила
					);
				}
			}
		}
		
		return $this->validData;
	}
	
	
	// ############ УПРАВЛЕНИЕ ПРАВИЛАМИ ВАЛИДАЦИИ ############# //
	
	/** ЗАДАТЬ ПРАВИЛО ВАЛИДАЦИИ ЭЛЕМЕНТА */
	public function setRule($elm, $rule, $params){
		
		$this->elmsRules[$elm][$rule] = $params;
	}
	
	/** УДАЛИТЬ ПРАВИЛО ВАЛИДАЦИИ ЭЛЕМЕНТА */
	public function removeRule($elm, $rule){
		
		unset($this->elmsRules[$elm][$rule]);
	}
	
	/** ДОБАВИТЬ ЭЛЕМЕНТ */
	public function addElement($elm, $rules){
		
		$this->elmsRules[$elm] = $rules;
	}
	
	/** УДАЛИТЬ ЭЛЕМЕНТ */
	public function removeElement($elm){
		
		unset($this->elmsRules[$elm]);
	}
	
	/** ОТКЛЮЧИТЬ (не выполнять) НЕКОТОРЫЕ ПРАВИЛА */
	public function disableRules($rules){
		
		foreach($rules as $rule)
			if(in_array($rule, self::$rulesOrder))
				$this->disabledRules[$rule] = TRUE;
			else
				$this->fatalError('Правило "'.$rule.'" не найдено');
	}
	
	// ПОЛУЧИТЬ ВСЕ ОБЪЕДИНЕННЫЕ ПРАВИЛА ВАЛИДНЫМ JAVASCRIPT КОДОМ (ДЛЯ JQUERY ПЛАГИНА VALIDATE)
	public function getJsRules(){
		
		$lf = "\r\n";
		$t = "\t";
		
		$allRulesArr = array();
		$allMessagesArr = array();
		
		foreach($this->elmsRules as $field => $rules){
		
			$elmRulesArr = array();
			$elmMessagesArr = array();
			foreach($rules as $rule => $params){
				
				// RULE REQUIRED
				if($rule == 'required'){
				
					$elmRulesArr[] = 'required: true';
					$elmMessagesArr[] = 'required: "'.$this->getErrorText($field, 'required').'"';
				}
				// RULE EMAIL
				elseif($rule == 'email'){
				
					$elmRulesArr[] = 'email: true';
					$elmMessagesArr[] = 'email: "'.$this->getErrorText($field, 'email').'"';
				}
				// RULE LENGTH
				elseif($rule == 'length'){
					
					$isset = FALSE;
					if(isset($params['min'])){
						$elmRulesArr[] = 'minlength: '.(int)$params['min'];
						$elmMessagesArr[] = 'minlength: "'.$this->getErrorText($field, 'length', $params).'"';
					}
					if(isset($params['max'])){
						$elmRulesArr[] = 'maxlength: '.(int)$params['max'];
						$elmMessagesArr[] = 'maxlength: "'.$this->getErrorText($field, 'length', $params).'"';
					}
				}
				// RULE COMPARE
				elseif($rule == 'compare'){
				
					$elmRulesArr[] = 'equalTo: "input[name=\''.$params.'\']"';
					$elmMessagesArr[] = 'equalTo: "'.$this->getErrorText($field, 'compare', $params).'"';
				}
				// RULE SETTYPE
				elseif($rule == 'settype'){
					
					if($params == 'int'){
						$elmRulesArr[] = 'digits: true';
						$elmMessagesArr[] = 'digits: "'.$this->getErrorText($field, 'int_type').'"';
					}
					if($params == 'float'){
						$elmRulesArr[] = 'number: true';
						$elmMessagesArr[] = 'number: "'.$this->getErrorText($field, 'float_type').'"';
					}
				}
			}

			if(count($elmRulesArr)){
				$allRulesArr[] = $t.$field.": {".implode(", ", $elmRulesArr)."}";
				$allMessagesArr[] = $t.$field.": {".implode(", ", $elmMessagesArr)."}";
			}
		}
		
		$output = 'rules: {'.$lf.implode(",".$lf, $allRulesArr).$lf.'},'.$lf;
		$output .= 'messages: {'.$lf.implode(",".$lf, $allMessagesArr).$lf.'}'.$lf;
		
		return $output;
	}

	
	// ################## ПРАВИЛА ВАЛИДАЦИИ #################### //
	
	// ПРАВИЛО TRIM
	public function rule_trim($field, $execute){
		
		if(isset($this->validData[$field]) && $execute)
			$this->validData[$field] = trim($this->validData[$field]);
	}
	
	// ПРАВИЛО SETTYPE
	public function rule_settype($field, $type){
		
		if(isset($this->validData[$field]))
			settype($this->validData[$field], $type);
	}
	
	// ПРАВИЛО STRIP_TAGS
	public function rule_strip_tags($field, $execute){
	
		if(isset($this->validData[$field]) && $execute)
			$this->validData[$field] = strip_tags($this->validData[$field]);
	}
	
	// ПРАВИЛО HTMLSPECIALCHARS
	public function rule_htmlspecialchars($field, $execute){
	
		if(isset($this->validData[$field]) && $execute)
			$this->validData[$field] = htmlspecialchars($this->validData[$field], ENT_QUOTES);
	}
	
	// ПРАВИЛО ESCAPE
	public function rule_escape($field, $execute){
	
		if(isset($this->validData[$field]) && $execute)
			$this->validData[$field] = db::get()->escape($this->validData[$field]);
	}
	
	// ПРАВИЛО CHECKBOX
	public function rule_checkbox($field, $values){
		
		if(!is_array($values) && !count($values))
			$this->fatalError('Правило "checkbox" требует непустой массив в качестве параметра');
		
		if(empty($this->validData[$field]))
			$this->validData[$field] = isset($values['off']) ? $values['off'] : '';
		else
			$this->validData[$field] = isset($values['on']) ? $values['on'] : $this->validData[$field];
	}
	
	// ПРАВИЛО REQUIRE
	public function rule_required($field, $execute){
		
		if(empty($this->validData[$field]) && $execute)
			$this->setError($this->getErrorText($field, 'required'));
	}
	
	// ПРАВИЛО EMAIL
	public function rule_email($field, $execute){
			
		if(!$execute)
			return;
		
		if(empty($this->validData[$field])){
			$this->validData[$field] = '';
			return;
		}
		$result = preg_match('/^[\w._%+-]+@[\w.-]+\.\w{2,10}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'email'));
	}
	
	// ПРАВИЛО MATCH
	public function rule_match($field, $pattern){
		
		$result = isset($this->validData[$field]) ? preg_match($pattern, $this->validData[$field]) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'match'));
	}
	
	/**
	 * IN
	 * проверка совпадает ли переданный элемент с одним из допустимых значений
	 * @syntax 'field' => array( 'in' => array('a', 'b', 'c') )
	 */
	public function rule_in($field, $validValues){
		
		if(!is_array($validValues))
			$this -> fatalError('Правило IN должно получать массив список допустимых значений');
		$result = (isset($this->validData[$field]) && in_array($this->validData[$field], $validValues, TRUE)) ? TRUE : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'in', $validValues));
	}
	
	// ПРАВИЛО NOT-IN
	public function rule_notIn($field, $validValues){
		
		if(!is_array($validValues))
			$this -> fatalError('Правило NOT IN должно получать массив список допустимых значений');
		$result = (isset($this->validData[$field]) && !in_array($this->validData[$field], $validValues, TRUE)) ? TRUE : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'notIn', $validValues));
	}
	
	// ПРАВИЛО COMPARE
	public function rule_compare($field, $field2){
		
		$result = isset($this->validData[$field]) && isset($this->validData[$field2]) ? ($this->validData[$field] === $this->validData[$field2] ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'compare', $field2));
	}
	
	// ПРАВИЛО EQUAL
	public function rule_equal($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] == $val ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'equal', $val));
	}
 	
	// ПРАВИЛО NOT-EQUAL
	public function rule_notEqual($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] != $val ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'notEqual', $val));
	}

	// ПРАВИЛО CAPCHA (удаляет поле после проверки)
	public function rule_captcha($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] == $val ? TRUE : FALSE) : FALSE;
		unset($this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'captcha', $val));
	}
	
	// ПРАВИЛО LENGTH
	public function rule_length($field, $len){
		
		if(!is_array($len) && !count($len))
			$this->fatalError('Правило "length" требует непустой массив со возможными ключами "min", "max"');
		
		if(!isset($this->validData[$field])){
			if(isset($len['min']) && $len['min'] > 0)
				$this->setError($this->getErrorText($field, 'length', $len));
			return;
		}

		$actualLen = mb_strlen($this->validData[$field], 'UTF-8');
		$result = TRUE;
		if(isset($len['min']) && $actualLen < (int)$len['min'])
			$result = FALSE;
		if(isset($len['max']) && $actualLen > (int)$len['max'])
			$result = FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'length', $len));
	}
	
	// ПРАВИЛО DB DATE
	public function rule_dbDate($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = substr($this->validData[$field], 0, 10);
		$result = preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbDate'));
	}
	
	// ПРАВИЛО DB TIME
	public function rule_dbTime($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$result = preg_match('/^\d{2}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbTime'));
	}
	
	// ПРАВИЛО DB DATETIME
	public function rule_dbDateTime($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$result = preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbDateTime'));
	}
	
	// ПРАВИЛО UNSET AFTER
	public function rule_unsetAfter($field, $execute){
	
		if(!isset($this->validData[$field]) || !$execute)
			return;
		unset($this->validData[$field]);
	}
	
	// ПРАВИЛО HASH
	public function rule_hash($field, $type){
	
		if(!isset($this->validData[$field]))
			return;
			
		switch($type) {
			case 'base64': $this->validData[$field] = base64_encode($this->validData[$field]); break;
			case 'md5':    $this->validData[$field] = md5($this->validData[$field]); break;
			case 'sha1':   $this->validData[$field] = sha1($this->validData[$field]); break;
			default: $this->fatalError('Правило hash может принимать значения: base64, md5, sha1. Получено значение "'.$type.'"');
		}
	}
	
	
	// ####################### ОБРАБОТКА ОШИБОК ####################### //
	
	// ПОЛУЧИТЬ ТЕКСТ ВАЛИДАЦИОННОГО СООБЩЕНИЯ
	public function getErrorText($field, $rule, $additParams = ''){
		
		$this->_issetFatal(self::$_errorMsgs[$rule], 'Текст ошибки для правила "'.$rule.'" не найден');
		
		$msgText = self::$_errorMsgs[$rule];
		$msgText = str_replace('{fieldname}', '<b>'.$this->getFieldTitle($field).'</b>', $msgText);
		
		
		// in
		if($rule == 'in' && is_array($additParams)){
			foreach($additParams as &$val)
				$val = '"'.$val.'"';
			$msgText = str_replace('{validValues}', implode(', ', $additParams), $msgText);
		}
		
		// compare
		if($rule == 'compare'){
			$msgText = str_replace('{field2name}', '<b>'.$this->getFieldTitle($additParams).'</b>', $msgText);
		}
	
		// length
		if($rule == 'length'){
			$minlength = '';
			$maxlength = '';
			if(isset($additParams['min']))
				$minlength = ' от '.(int)$additParams['min'];
			if(isset($additParams['max']))
				$maxlength = ' до '.(int)$additParams['max'];
			$msgText = str_replace(array('{minlength}', '{maxlength}'), array($minlength, $maxlength), $msgText);
		}
		
		return $msgText;
		// $this->validationErrors[] = $msgText;
	}
	
	// ДОБАВИТЬ ВАЛИДАЦИОННОЕ СООБЩЕНИЕ ОБ ОШИБКЕ
	public function validationError($txt){
		$this->validationErrors[] = $txt;
	}
	
	// ALIAS: ДОБАВИТЬ ВАЛИДАЦИОННОЕ СООБЩЕНИЕ ОБ ОШИБКЕ
	public function setError($txt){
	
		$this->validationErrors[] = $txt;
	}
	
	// БЫЛИ ЛИ ОШИБКИ ВАЛИДАЦИИ
	public function hasError(){
		
		return count($this->validationErrors) ? TRUE : FALSE;
	}
	
	// ПОЛУЧИТЬ ОШИБКИ ВАЛИДАЦИИ
	public function getError(){
	
		return implode(self::ERRORS_SEPARATOR, $this->validationErrors);
	}
	
	// ЗАДАТЬ СООБЩЕНИЕ ОБ ОШИБКЕ (ПЕРЕПИСАВ СТАНДАРТНОЕ)
	public function setErrorMsg($error, $msg){
		
		if(!isset(self::$_errorMsgs[$error]))
			$this -> fatalError('Неверный код ошибки');
		
		self::$_errorMsgs[$error] = $msg;
	}
	
	// ПРОВЕРКА СУЩЕСТВОВАНИЯ ЭЛЕМЕНТА (ИНАЧЕ FATAL ERROR)
	private function _issetFatal(&$elm, $errorMsg = ''){

		if(!isset($elm))
			$this -> fatalError($errorMsg);
	}
	
	// ИНИЦИАЛИЗИРОВАТЬ ФАТАЛЬНУЮ ОШИБКУ
	public function fatalError($msg){
		
		trigger_error($msg, E_USER_ERROR);
		die('Извините, произошла ошибка');
	}
	
	
	// ####################### СЛУЖЕБНЫЕ ####################### //
	
	// СБРОС СОСТОЯНИЯ ВАЛИДАТОРА
	public function reset(){
		
		$this->validData = $this->validationErrors = array();
	}
	
	// ПОЛУЧИТЬ ЗАГОЛОВОК ПОЛЯ
	private function getFieldTitle($field){
		return isset($this->_fieldTitles[$field]) ? $this->_fieldTitles[$field] : $field;
	}
	
}

?>