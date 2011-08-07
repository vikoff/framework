<?

class YArray{

	/* ПРЕОБРАЗУЕТ ВСЕ ЗНАЧЕНИЯ МАССИВА В ЧИСЛА (поддерживает многомерные массивы) */
	public static function intvals(&$arr){
		
		if(!is_array($arr))
			return;
		foreach($arr as &$v)
			if(is_array($v))
				self::intvals($v);			
			else
				$v = (int)$v;
	}
	
	// INTVAL ДЛЯ ВСЕХ ЗНАЧЕНИЙ МАССИВА (ПЕРЕДАЧА ПО ЗНАЧЕНИЮ)
	public static function intvalsReturn($arr){
	
		if(!is_array($arr))
			return array();
		foreach($arr as &$v)
			if(is_array($v))
				$v = self::intvalsReturn($v);			
			else
				$v = (int)$v;
		return $arr;
	}
	
	// TRIM ДЛЯ МАССИВА
	public static function trim($arr){
		
		foreach($arr as &$v){
			if(is_array($v))
				$v = self::trim($v);
			if(is_string($v))
				$v = trim($v);
		}
		
		return $arr;
	}
	
	// ПОЛУЧИТЬ ПЕРВЫЙ КЛЮЧ В МАССИВЕ
	static public function getFirstKey($arr){
		if(!is_array($arr))
			return NULL;
		reset($arr);
		return key($arr);
	}
	
	// ПОЛУЧИТЬ ПОСЛЕДНИЙ КЛЮЧ В МАССИВЕ
	static public function getLastKey($arr){
		return (is_array($arr)) ? array_pop(array_keys($arr)) : 0;
	}
	
	// ПОЛУЧИТЬ СЛЕДУЮЩИЙ КЛЮЧ В МАССИВЕ
	static function getNextIndex($cur_index, $arr){
	
		$cur_index = (int)$cur_index;
		$desired_index = FALSE;
		$catch_now = FALSE;
		
		foreach($arr as $index => $val){
			if($index == $cur_index){
				$catch_now = true;
				continue;
			}
			if($catch_now){
				$desired_index = $index;
				break;
			}
		}
		if($desired_index === FALSE)
			$desired_index = self::getFirstKey($arr);
		return $desired_index;
	}
	
	// ПОЛУЧИТЬ ПРЕДЫДУЩИЙ КЛЮЧ В МАССИВЕ
	static function get_prev_index($cur_index, $arr){
		
		if(!count($arr))
			return 0;
		
		$cur_index = (int)$cur_index;
		$prev_index = FALSE;
		$desired_index = 0;
		
		foreach($arr as $index => $val){
			if($index == $cur_index){
				$desired_index = $prev_index;
				break;
			}
			$prev_index = $index;
		}
		if($desired_index === FALSE)
			$desired_index = self::get_last_key($arr);
		return $desired_index;
	}
	
	/* УЛУЧШЕННАЯ ДЕСЕРИАЛИЗАЦИЯ */
	static function unserialize($arr){
		$default = array();
		$output = array();
		if(is_array($arr)){return $arr;}
		if($arr){$output = unserialize(trim($arr));}
		if(is_array($output)){return $output;}
		else{return $default;}
	}
	
	/**
	 * КОНВЕРТИРОВАНИЕ МАССИВОВ
	 * $input - одномерный ассоциативный массив входных данных;
	 * $convert_rules - одномерный ассоциативный массив ключи которого - имена изменяемых полей, а значения - тип преобразования.
	**/
	static function convert($input, $convert_rules){

		if(is_array($input) && is_array($convert_rules)){
			foreach($convert_rules as $field => $operation){
				switch($operation){
					case 'unserialize': $input[$field] = self::unserialize($input[$field]); break;
					case 'timestamp2date': $input[$field] = YDate::timestamp2date($input[$field]); break;
					default: Error::fatal_error('Неверный тип преобразования: '.$operation);
				}
			}
		}
		/* $input - строка; $convert_rules - строка: тип преобразования */
		elseif(is_string($input) && is_string($convert_rules)){
			switch($convert_rules){
				case 'unserialize': $input = self::unserialize($input); break;
				case 'timestamp2date': $input = YDate::timestamp2date($input);	break;
				default: Error::fatal_error('Неверный тип преобразования: '.$convert_rules);
			}
		}else{
			Error::fatal_error('Неверные типы параметров: '.gettype($input).', '.gettype($convert_rules));
		}
		return $input;
	}
	
	// ПРЕОБРАЗОВАНИЕ СТРОКИ В АССОЦИАТИВНЫЙ МАССИВ
	static public function string2hash($string, $separator = ','){
		
		$string = (string)$string;
		if(!strlen($string))
			return array();
		
		$output = array();
		foreach(array_unique(explode($separator, $string)) as $item){
		
			$item = trim($item);
			if(!strlen($item))
				continue;
				
			if(strpos($item, '=')){
			
				$pair = explode('=', $item);
				if(!strlen($pair[0]))
					continue;
					
				$output[trim($pair[0])] = trim($pair[1]);
				
			}else{
				$output[$item] = 1;
			}
		}
		return $output;
	}
}

?>