<?

class Validator{
	
	// Разделитель валидационных сообщений об ошибках
	const ERRORS_SEPARATOR = "<br />";
	
	// контейнер исходных данных
	private $inputData = null;
	
	// контейнер преобразованных значений, прошедших валидацию
	private $validData = array();
	
	// контейнер всех правил
	private $allRules = array();
	
	// отключенные правила
	private $disabledRules = array();
	
	// массив названий полей (для сообщений об ошибках)
	private $_fieldTitles = array();
	
	// нужно ли вести лог
	private $_logEnable = FALSE;
	
	// лог работы валидатора
	private $_log = array();
	
	// массив ошибок валидации
	private $validationErrors = array();
	
	// хранение сообщений об ошибках для кодов ошибок
	static private $_errorMsgs = array(
		'required' 	=> 'поле {fieldname} обязательно для заполнения',
		'email' 	=> 'в поле {fieldname} введен некорректный email-адрес',
		'match' 	=> 'поле {fieldname} содержит недопустимые символы или имеет недопустимый формат',
		'in' 		=> 'поле {fieldname} может принимать только значения {validValues}',
		'notIn'		=> 'в поле {fieldname} введено недопустимое значение',
		'equal'		=> 'в поле {fieldname} введено недопустимое значение',
		'notEqual'	=> 'в поле {fieldname} введено недопустимое значение',
		'captcha'	=> 'антибот тест не пройден',
		'compare'	=> 'поля {fieldname} и {field2name} не совпадают',
		'length'	=> 'поле {fieldname} должно быть длиной {minlength} {maxlength} символов',
		'dbDate'	=> 'поле {fieldname} должно иметь формат ГГГГ-ММ-ДД',
		'dbTime'	=> 'поле {fieldname} должно иметь формат ЧЧ-ММ-СС',
		'dbDateTime' => 'поле {fieldname} должно иметь формат ГГГГ-ММ-ДД ЧЧ-ММ-СС',
		'int_type'	=> 'поле {fieldname} должно содержать только цифры',
		'float_type' => 'поле {fieldname} должно содержать число',
	);
	
	// порядок вызова правил (заодно и полный их список)
	static public $rulesOrder = array(
		'allowed',
		'trim',
		'settype',
		'strip',
		'truncate',
		'htmlspecialchars',
		'safe',
		'required',
		'email',
		'match',
		'in',
		'notIn',
		'equal',
		'notEqual',
		'captcha',
		'compare',
		'length',
		'checkbox',
		'dbDate',
		'dbTime',
		'dbDateTime',
		'password',
		'unsetAfter',
	);

	
	
	// ######### ЗАГРУЗКА ИСХОДНЫХ ДАННЫХ В КЛАСС ############# //

	// КОНСТРУКТОР
	public function __construct($inputData = array()){
		if(count($inputData))
			$this -> setInputData($inputData);
	}
	
	// ЗАДАТЬ ИСХОДНЫЕ ДАННЫЕ
	public function setInputData($inputData = array()){
		$this->inputData = $inputData;
	}
	
	// ЗАДАТЬ ЗАГОЛОВКИ ДЛЯ ПОЛЕЙ (ДЛЯ ОТОБРАЖЕНИЯ В СООБЩЕНИЯХ ОБ ОШИБКАХ)
	public function setFieldTitles($fieldsTitles){
		$this->_fieldTitles = $fieldsTitles;
	}
	
	// ЗАДАНИЕ ПРАВИЛ ВАЛИДАЦИИ
	public function rules($_commonRules, $_individualRules = array(), $databaseValidation = FALSE){
		
		$commonRules = array();
		$individualRules = array();
		
		// НАБОР ОБЩИХ ПРАВИЛ ВАЛИДАЦИИ
		$allowedCommonRules = array(
			'allowed',
			'trim',
			'strip',
			'htmlspecialchars',
			'safe',
			'required',
			'unsetAfter',
			'email',
		);
		
		foreach($_commonRules as $name => $elms)
			if(in_array($name, $allowedCommonRules, TRUE))
				$commonRules[$name] = $elms;
			else
				$this->fatalError('Правило "'.$name.'" не может быть задано в наборе общих правил');
		
		
		// НАБОР ИНДИВИДУАЛЬНЫХ ПРАВИЛ ВАЛИДАЦИИ
		$allowedIndividualRules = array_unique(array_merge($allowedCommonRules, array(
			'settype',
			'truncate',
			'match',
			'in',
			'notIn',
			'equal',
			'notEqual',
			'compare',
			'captcha',
			'length',
			'checkbox',
			'dbDate',
			'dbTime',
			'dbDateTime',
			'password',
			'disableOtherRules',
		)));
		
		foreach($_individualRules as $elm => $rules){
			$individualRules[$elm] = array();
			foreach($rules as $rule => $params)
				if(in_array($rule, $allowedIndividualRules, TRUE))
					$individualRules[$elm][$rule] = $params;
				else
					$this->fatalError('Правило "'.$rule.'" не может быть задано в наборе индивидуальных правил');
		}
		
		// СОЕДИНЕНИЕ ВСЕХ ПРАВИЛ ВАЛИДАЦИИ
		
		// НАХОЖДЕНИЕ ВСЕХ ДОПУСТИМЫХ ПОЛЕЙ
		
		// нахождение допустимых элементов из общих правил - наименьший приоритет
		if($this->_isRuleEnable($commonRules['allowed'])){
			if($this->_isRuleApplyToAll($commonRules['allowed'])){
				if(is_array($this->inputData))
					foreach($this->inputData as $elm => $val)
						$this->allRules[$elm] = array();
			}else{
				foreach($commonRules['allowed'] as $elm)
					$this->allRules[$elm] = array();
			}
		}
		unset($commonRules['allowed']);

		// нахождение допустимых элементов из индивидуальных правил - наивысший авторитет
		foreach($individualRules as $elm => $rule){
			if(!isset($rule['allowed']) || $rule['allowed'] == TRUE)
				$this->allRules[$elm] = array();
			else
				unset($this->allRules[$elm]);
			unset($individualRules[$elm]['allowed']);
		}
		
		// ПРИМЕНЕНИЕ ПРАВИЛ К ДОПУСТИМЫМ ПОЛЯМ
		
		// общие правила - наименьший приоритет
		foreach($commonRules as $rule => $elms)
			if($this -> _isRuleEnable($elms))
				if($this -> _isRuleApplyToAll($elms))
					foreach($this->allRules as $elm => $tmp)
						$this->_addRule($elm, $rule, TRUE);
				else
					foreach($elms as $elm)
						if(isset($this->allRules[$elm]))
							$this->_addRule($elm, $rule, TRUE);
		
		// индивидуальные правила - наивысший авторитет
		foreach($individualRules as $elm => $rules){
			if(isset($this->allRules[$elm])){
			
				if($this->_isRuleEnable($rules['disableOtherRules']))
					$this->allRules[$elm] = array();
				unset($rules['disableOtherRules']);
				
				if(isset($rules['allowed']) && $rules['allowed'] == FALSE){
					unset($this->allRules[$elm]);
					continue;
				}

				foreach($rules as $rule => $params)
					$this->_addRule($elm, $rule, $params);
			}
		}
	
	}
	
	
	
	// ####################### ВАЛИДАЦИЯ ####################### //
	
	// ПРИМЕНЕНИЕ ПРАВИЛ ВАЛИДАЦИИ
	public function validate($inputData = array()){
		
		// загрузка исходных данных (если требуется)
		if(count($inputData))
			$this -> setInputData($inputData);
		
		// проверка исходных данных
		if(is_null($this->inputData))
			$this->fatalError('Исходные данные не заданы');
			
		// инициализация массива валидных данных (содержит разрешенные и назначенные поля)
		foreach($this->allRules as $field => $rules)
			if(isset($this -> inputData[$field]))
				$this->validData[$field] = $this -> inputData[$field];
		
		// применение правил валидации
		foreach($this->allRules as $field => $definedRules){
			foreach(self::$rulesOrder as $regularRule){
				if($regularRule == 'allowed')
					continue;
				if(isset($definedRules[$regularRule])){
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
	
	public function validatePart($inputData = array()){
	
		
		// загрузка исходных данных (если требуется)
		if(count($inputData))
			$this -> setInputData($inputData);
		
		// проверка исходных данных
		if(is_null($this->inputData))
			$this->fatalError('Исходные данные не заданы');
			
		// инициализация массива валидных данных (содержит разрешенные и назначенные поля)
		foreach($this->allRules as $field => $rules)
			if(isset($this -> inputData[$field]))
				$this->validData[$field] = $this -> inputData[$field];
		
		// применение правил валидации
		foreach($this->validData as $field => $value){
			$definedRules = $this->allRules[$field];
			foreach(self::$rulesOrder as $regularRule){
				if($regularRule == 'allowed')
					continue;
				if(isset($definedRules[$regularRule])){
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
	
	// ПОЗДНЕЕ ИЗМЕНЕНИЕ ПРАВИЛ ВАЛИДАЦИИ
	public function editRule($elm, $rule, $params){
		
		if(in_array($rule, self::$rulesOrder, TRUE)){
			
			if($rule == 'allowed' && $params == FALSE)
				unset($this->allRules[$elm]);
			else
				$this->allRules[$elm][$rule] = $params;
			
		}else{
			$this->fatalError('Правило "'.$rule.'" не найдено');
		}
	}
	
	// ПОЗДНЕЕ УДАЛЕНИЕ ПРАВИЛА
	public function delRule($elm, $rule = NULL){
		
		// выходим если элемент не найден
		if(!isset($this->allRules[$elm]))
			return;
			
		// если правило не задано, удаляем все правила
		if(is_null($rule)){
			$this->allRules[$elm] = array();
		}else{
			if(in_array($rule, self::$rulesOrder, TRUE)){
				unset($this->allRules[$elm][$rule]);
			}else{
				$this->fatalError('Правило "'.$rule.'" не найдено');
			}
		}
	}
	
	// ПОЗДНЕЕ УДАЛЕНИЕ ЭЛЕМЕНТА (ИЛИ НЕСКОЛЬКИХ)
	public function delElement($elements){
		
		foreach((array)$elements as $elm){
			unset($this->allRules[$elm]);
		}
	}
	
	// ОТКЛЮЧИТЬ НЕКОТОРЫЕ ПРАВИЛА
	public function disableRules(){ // список правил через запятую
		
		$rules = func_get_args();
		foreach($rules as $rule)
			if(in_array($rule, self::$rulesOrder))
				$this->disabledRules[$rule] = TRUE;
			else
				$this->fatalError('Правило "'.$rule.'" не найдено, поэтому не может быть отключено');
	}
	
	// ПОЛУЧИТЬ ВСЕ ОБЪЕДИНЕННЫЕ ПРАВИЛА ВАЛИДНЫМ PHP КОДОМ
	public function getPhpCodeRules(){
		
		//return var_export($this->allRules, TRUE);
		
		$lf = "\r\n";
		$t = "\t";
		
		$output = '$individualRules = array('.$lf;

		foreach($this->allRules as $elm => $rules){
		
			$output .= $t."'".$elm."' => array(";
			$rulesArr = array();
			
			foreach($rules as $rule => $params){

				$ruleStr = "'".$rule."' => ";

				if(is_array($params)){

					$ruleParamsArr = array();
					foreach($params as $k => $v)
						$ruleParamsArr[] = "'".$k."' => '".$v."'";
					$ruleStr .= "array(".implode(", ", $ruleParamsArr).")";
				
				}elseif(is_bool($params)){
					
					$ruleStr .= $params ? 'TRUE' : 'FALSE';
				
				}elseif(is_numeric($params)){
					
					$ruleStr .= $params;
				
				}else{
					
					$ruleStr .= "'".$params."'";
				}
				$rulesArr[] = $ruleStr;
			}
			$output .= implode(", ", $rulesArr)."),".$lf;
		}
		$output .= ');'.$lf;
		
		return $output;
	}
	
	// ПОЛУЧИТЬ ВСЕ ОБЪЕДИНЕННЫЕ ПРАВИЛА ВАЛИДНЫМ JAVASCRIPT КОДОМ (ДЛЯ JQUERY ПЛАГИНА VALIDATE)
	public function getJsRules(){
		
		$lf = "\r\n";
		$t = "\t";
		
		$allRulesArr = array();
		$allMessagesArr = array();
		
		foreach($this->allRules as $field => $rules){
		
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
	
	// ДОБАВИТЬ ПРАВИЛО (СЛУЖЕБНАЯ)
	private function _addRule($elm, $rule, $params){
		
		if(!isset($this->allRules[$elm]))
			$this->fatalError('Элемент "'.$elm.'" не найден в массиве "allRules". Разработчик, проверь почему так.');
		
		if(!isset($this->disabledRules[$rule]))
			$this->allRules[$elm][$rule] = $params;
	}
	
	// ЗАДАНО ЛИ ПРАВИЛО ВАЛИДАЦИИ
	private function _isRuleEnable(&$rule){
	
		return isset($rule) ? ($rule === FALSE ? FALSE : TRUE) : FALSE;
	}
	
	// ЗАДАНО ЛИ ПРАВИЛО ДЛЯ ВСЕХ ЭЛЕМЕНТОВ
	private function _isRuleApplyToAll(&$rule){
		
		$this->_issetFatal($rule, 'Правило валидации не определено');
		return (count($rule) == 1 && $rule[0] === '*') ? TRUE : FALSE;
	}


	
	// ################## ПРАВИЛА ВАЛИДАЦИИ #################### //
	
	// ПРАВИЛО TRIM
	public function rule_trim($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = trim($this->validData[$field]);
		if($this -> _logEnable)
			$this -> _log('Правило "trim" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО SETTYPE
	public function rule_settype($field, $type){
		
		if(!isset($this->validData[$field]))
			return;
		settype($this->validData[$field], $type);
		if($this -> _logEnable)
			$this -> _log('Правило "settype" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО STRIP
	public function rule_strip($field, $execute){
	
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = strip_tags($this->validData[$field]);
		if($this -> _logEnable)
			$this -> _log('Правило "strip" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО TRUNCATE
	public function rule_truncate($field, $len){
	
		if(!isset($this->validData[$field]))
			return;
		$this->validData[$field] = substr($this->validData[$field], 0, $len);
		if($this -> _logEnable)
			$this -> _log('Правило "truncate" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО HTMLSPECIALCHARS
	public function rule_htmlspecialchars($field, $execute){
	
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = htmlspecialchars($this->validData[$field], ENT_QUOTES);
		if($this -> _logEnable)
			$this -> _log('Правило "htmlspecialchars" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО SAFE
	public function rule_safe($field, $execute){
	
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = db::get()->escape($this->validData[$field]);
		if($this -> _logEnable)
			$this -> _log('Правило "safe" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО CHECKBOX
	public function rule_checkbox($field, $values){
		
		if(!is_array($values) && !count($values))
			$this->fatalError('Правило "checkbox" требует непустой массив в качестве параметра');
		
		if(empty($this->validData[$field]))
			$this->validData[$field] = isset($values['off']) ? $values['off'] : '';
		else
			$this->validData[$field] = isset($values['on']) ? $values['on'] : $this->validData[$field];
		if($this -> _logEnable)
			$this -> _log('Правило "checkbox" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
	}
	
	// ПРАВИЛО REQUIRE
	public function rule_required($field, $execute){
		
		if(!$execute)
			return;
		$result = isset($this->validData[$field]) && strlen($this->validData[$field]) ? TRUE : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'required'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" ('.$this->validData[$field].') '.($result ? 'прошел' : 'не прошел').' проверку "require"');
	}
	
	// ПРАВИЛО EMAIL
	public function rule_email($field, $execute){
		
		if(!$execute)
			return;
		$result = isset($this->validData[$field]) ? preg_match('/^[\w._%+-]+@[\w.-]+\.\w{2,4}$/', $this->validData[$field]) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'email'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "email"');
	}
	
	// ПРАВИЛО MATCH
	public function rule_match($field, $pattern){
		
		$result = isset($this->validData[$field]) ? preg_match($pattern, $this->validData[$field]) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'match'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "match"');
	}
	
	// ПРАВИЛО IN
	public function rule_in($field, $validValues){
		
		if(!is_array($validValues))
			$this -> fatalError('Правило IN должно получать массив список допустимых значений');
		$result = (isset($this->validData[$field]) && in_array($this->validData[$field], $validValues, TRUE)) ? TRUE : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'in', $validValues));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "in"');
	}
	
	// ПРАВИЛО NOT-IN
	public function rule_notIn($field, $validValues){
		
		if(!is_array($validValues))
			$this -> fatalError('Правило NOT IN должно получать массив список допустимых значений');
		$result = (isset($this->validData[$field]) && !in_array($this->validData[$field], $validValues, TRUE)) ? TRUE : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'notIn', $validValues));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "notIn"');
	}
	
	// ПРАВИЛО COMPARE
	public function rule_compare($field, $field2){
		
		$result = isset($this->validData[$field]) && isset($this->inputData[$field2]) ? ($this->validData[$field] == $this->inputData[$field2] ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'compare', $field2));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "compare"');
	}
	
	// ПРАВИЛО EQUAL
	public function rule_equal($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] == $val ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'equal', $val));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "equal"');
	}
 	
	// ПРАВИЛО NOT-EQUAL
	public function rule_notEqual($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] != $val ? TRUE : FALSE) : FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'notEqual', $val));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "notEqual"');
	}

	// ПРАВИЛО CAPCHA (удаляет поле после проверки)
	public function rule_captcha($field, $val){
		
		$result = isset($this->validData[$field]) ? ($this->validData[$field] == $val ? TRUE : FALSE) : FALSE;
		unset($this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'captcha', $val));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "captcha"');
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

		$actualLen = strlen($this->validData[$field]);
		$result = TRUE;
		if(isset($len['min']) && $actualLen < (int)$len['min'])
			$result = FALSE;
		if(isset($len['max']) && $actualLen > (int)$len['max'])
			$result = FALSE;
		if(!$result)
			$this->setError($this->getErrorText($field, 'length', $len));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "length"');
	}
	
	// ПРАВИЛО DB DATE
	public function rule_dbDate($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$this->validData[$field] = substr($this->validData[$field], 0, 10);
		$result = preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbDate'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" ['.$this->validData[$field].'] '.($result ? 'прошел' : 'не прошел').' проверку "DBDate"');
	}
	
	// ПРАВИЛО DB TIME
	public function rule_dbTime($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$result = preg_match('/^\d{2}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbTime'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "DBTime"');
	}
	
	// ПРАВИЛО DB DATETIME
	public function rule_dbDateTime($field, $execute){
		
		if(!isset($this->validData[$field]) || !$execute)
			return;
		$result = preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}\-\d{2}\-\d{2}$/', $this->validData[$field]);
		if(!$result)
			$this->setError($this->getErrorText($field, 'dbDateTime'));
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" '.($result ? 'прошел' : 'не прошел').' проверку "DBDateTime"');
	}
	
	// ПРАВИЛО UNSET AFTER
	public function rule_unsetAfter($field, $execute){
	
		if(!isset($this->validData[$field]) || !$execute)
			return;
		unset($this->validData[$field]);
		if($this -> _logEnable)
			$this -> _log('Элемент "'.$field.'" был удален правилом "unsetAfter"');
	}
	
	public function rule_password($field, $params){
	
		if(!isset($this->validData[$field]))
			return;
		$hash  = (isset($params['hash'])) ? $params['hash'] : 'no';
		if($hash == 'no'){
			$this->validData[$field] = $this->validData[$field];
		}elseif($hash == 'base64'){
			$this->validData[$field] = base64_encode($this->validData[$field]);
		}elseif($hash == 'md5'){
			$this->validData[$field] = md5($this->validData[$field]);
		}elseif($hash == 'sha1'){
			$this->validData[$field] = sha1($this->validData[$field]);
		}else{
			$this->fatalError('Параметр hash правила "password" может принимать значения: no, base64, md5, sha1, или быть не переданым совсем. Получено значение "'.$hash.'"');
		}
		
		if($this -> _logEnable)
			$this -> _log('Правило "htmlspecialchars" присвоило элементу "'.$field.'" значение "'.$this->validData[$field].'"');
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
	
	
	
	// ####################### ВЕДЕНИЕ ЛОГОВ ####################### //
	
	// ВКЛЮЧИТЬ / ВЫКЛЮЧИТЬ ВЕДЕНИЕ ЛОГА
	public function logEnable($enable = TRUE){
		$this -> _logEnable = (bool)$enable;
	}
	
	// ДОБАВИТЬ ЛОГ
	private function _log($msg){
	
		$this->_log[] = $msg;
	}
	
	// ПОЛУЧИТЬ ЛОГ
	public function showLog(){
	
		return implode("\n", $this->_log);
	}
	
	
	
	// ####################### СЛУЖЕБНЫЕ ####################### //
	
	// СБРОС СОСТОЯНИЯ ВАЛИДАТОРА
	public function reset(){
		
		$this->inputData = $this->validData = $this->validationErrors = array();
	}
	
	// ПОЛУЧИТЬ ЗАГОЛОВОК ПОЛЯ
	private function getFieldTitle($field){
		return isset($this->_fieldTitles[$field]) ? $this->_fieldTitles[$field] : $field;
	}
	
	// ПРЕОБРАЗОВАНИЕ СТРОКИ В МАССИВ-СПИСОК
	public function string2array($string, $separator = ','){
		
		$string = (string)$string;
		if(!strlen($string))
			return array();
		
		$output = array();
		foreach(array_unique(explode($separator, $string)) as $item){
			$item = trim($item);
			if(strlen($item))
				$output[] = $item;
		}
		
		return $output;
	}

	private function _dbRulesPrepare($struct){
	
		foreach($struct as $row){
			
			// значение PRIMARY KEY AUTO_INCREMENT является недопустимым для указания полем
			if(strtoupper($row['Key']) == 'PRI' && strtoupper($row['Extra']) == 'AUTO_INCREMENT')
				continue;
			
			// все остальные поля являются допустимыми
			$databaseRules['allowed'][] = $row['Field'];
			
			// если по умолчанию поле имеет значение NULL, но стоит флаг NOT NULL, то это поле обязательно.
			if(strtoupper($row['Null']) == 'NO' && strtoupper($row['Default']) == 'NULL'){
				$databaseRules['individual'][$row['Field']]['required'] = TRUE;
				$isRequired = TRUE;
			}else{
				$isRequired = FALSE;
			}
			
			// распознавание некоторых типов данных
			if(preg_match('/^(\w+)\s*(\((\d+)\))?\s*(\w*)$/', $row['Type'], $matches)){
				
				//print_r($matches);
				$fieldName = $row['Field'];
				$colType = strtoupper($matches[1]);
				$colLength = (int)$matches[3];
				$colFlags = strtoupper($matches[4]);
				
				switch($colType){
					
					case 'TINYINT': case 'SMALLINT': case 'MEDIUMINT': case 'INT': case 'INTEGER': case 'BIGINT':
						$databaseRules['individual'][$fieldName]['settype'] = 'int';
						break;
					
					case 'FLOAT': case 'DOUBLE': case 'REAL': case 'DECIMAL': case 'DEC': case 'NUMERIC': 
						$databaseRules['individual'][$fieldName]['settype'] = 'float';
						break;
					
					case 'DATE':
						if($isRequired)
							$databaseRules['individual'][$fieldName]['dbDate'] =  TRUE;
						break;
					
					case 'TIME':
						if($isRequired)
							$databaseRules['individual'][$fieldName]['dbTime'] = TRUE;
						break;
					
					case 'DATETIME':
						if($isRequired)
							$databaseRules['individual'][$fieldName]['dbDateTime'] = TRUE;
						break;
					
					case 'VARCHAR': case 'CHAR':
						$databaseRules['individual'][$fieldName]['safe'] = TRUE;
						break;
					
					case 'TINYTEXT': case 'TINYBLOB':
						$databaseRules['individual'][$fieldName]['safe'] = TRUE;
						$databaseRules['individual'][$fieldName]['length'] = array('max' => 255);
						break;
					
					case 'TEXT': case 'BLOB':
						$databaseRules['individual'][$fieldName]['safe'] = TRUE;
						$databaseRules['individual'][$fieldName]['length'] = array('max' => 65535);
						break;
					
					case 'MEDIUMTEXT': case 'MEDIUMBLOB':
						$databaseRules['individual'][$fieldName]['safe'] = TRUE;
						$databaseRules['individual'][$fieldName]['length'] = array('max' => 16777215);
						break;
					
					case 'LONGTEXT': case 'LONGBLOB':
						$databaseRules['individual'][$fieldName]['safe'] = TRUE;
						$databaseRules['individual'][$fieldName]['length'] = array('max' => 4294967295);
						break;
				}
				
				if($colLength)
					$databaseRules['individual'][$fieldName]['length'] = array('max' => $colLength);
					
			}
		}
		
		return $databaseRules;
	}
	
}

?>