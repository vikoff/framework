<?php

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
	
	public static function settype(&$arr, $type){
		
		if(!is_array($arr))
			return;
		foreach($arr as &$v)
			if(is_array($v))
				self::settype($v, $type);
			else
				settype($v, $type);
	}
	
	public static function settypeReturn($arr, $type){
	
		if(!is_array($arr))
			return array();
		foreach($arr as &$v)
			if(is_array($v))
				$v = self::settypeReturn($v, $type);			
			else
				settype($v, $type);
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
	public static function getFirstKey($arr){
		if(!is_array($arr))
			return NULL;
		reset($arr);
		return key($arr);
	}
	
	// ПОЛУЧИТЬ ПОСЛЕДНИЙ КЛЮЧ В МАССИВЕ
	public static function getLastKey($arr){
		return (is_array($arr)) ? array_pop(array_keys($arr)) : 0;
	}
	
	// ПОЛУЧИТЬ СЛЕДУЮЩИЙ КЛЮЧ В МАССИВЕ
	public static function getNextIndex($cur_index, $arr){
	
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
	public static function getPrevIndex($cur_index, $arr){
		
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
			$desired_index = self::getLastKey($arr);
		return $desired_index;
	}
	
	/** УЛУЧШЕННАЯ ДЕСЕРИАЛИЗАЦИЯ */
	public static function unserialize($arr){
		
		$output = array();
		
		if(is_array($arr))
			return $arr;
			
		if($arr)
			$output = unserialize(trim($arr));
			
		return is_array($output) ? $output : array();
	}
}
