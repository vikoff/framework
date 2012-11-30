<?php

class Tools {
	
	/* ПОЛУЧИТЬ ЧИСЛО ИЗ ЛЮБОЙ СТРОКИ, ГДЕ ЕСТЬ ЦИФРЫ */
	public static function getint($str){
		return intval(preg_replace("/[^\d]/", "", $str));
	}
	
	/* УНИФИЦИРОВАННЫЕ РЕГУЛЯРНЫЕ ВЫРАЖЕНИЯ */
	public static function getRegExp($param){
		$rus = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя';
		switch($param){
			case 'id':			return '/^[\d]{1,20}$/i';
			case 'password':	return '/^.{4,16}$/i';
			case 'email':		return '/^[\w'.$rus.'\.\-]{1,50}\@[\w'.$rus.'\.\-]{1,20}$/i';
		}
		die("Ошибка регулярных выражений");
	}
	
	/* ФУНКЦИЯ ОЧИСТКИ ДИРЕКТОРИИ ОТ ВСЕХ ФАЙЛОВ */
	public static function clearDir($dirNam){
		if(!is_dir($dirNam)){return false;}
		chdir($dirNam);
		$files = scandir($dirNam);
		foreach($files as $f){
			if($f == '.' || $f == '..'){continue;}
			if(is_dir($f)){continue;}
			if(file_exists($f)){unlink($f);}
		}
		return true;
	}
	
	// УДАЛЕНИЕ ФАЙЛА ИЛИ ФАЙЛОВ
	public static function unlink($filesArr){
		
		foreach((array)$filesArr as $file)
			if(file_exists($file))
				@unlink($file);
	}
	
	public static function mkDir($dir){

		$dir = (string)$dir;
		
		if(is_dir($dir))
			return TRUE;
		
		return mkdir($dir, 0777, TRUE);
	}
	
	public static function dir_avail($dir_name){
		
		if(!$dir_name){
			Error::fatal_error('Пустое имя папки');
			return false;
		}
		if(!is_dir($dir_name))
			mkdir($dir_name);
		
		if(is_dir($dir_name) && is_writable($dir_name))
			return true;
		
		else{
			Error::fatal_error('Невозможно создать папку');
			return false;
		}
	}
	
	/* ПОЛУЧИТЬ "ФАМИЛИЯ И.О." ИЗ "ФАМИЛИЯ ИМЯ ОТЧЕСТВО" */
	public static function get_short_name($longname, $flags = '')
	{
		$output = '';
		$longname = trim($longname);
		
		if(!($space_index = strpos($longname, ' '))){return $longname;}
		// фамилия
		$output .= substr($longname, 0, $space_index);
		
		$full_fisrt_name = substr($longname, $space_index + 1);
		// имя
		if(strlen($full_fisrt_name)){$output .= ' '.substr($full_fisrt_name, 0, 1).'.';}
		else{return $output;}
		
		if(!($space_index = strpos($full_fisrt_name, ' '))){return $output;}
		// отчество
		$output .= ' '.substr($full_fisrt_name, $space_index + 1, 1).'.';
		
		return $output;
	}
	
	public static function truncate($str, $len = 128, $ending = '...'){
		
		$len = (int)$len;
		$output = '';
		$real_len = strlen($str);
		if($real_len <= $len){$output = $str;}
		else{$output = mb_substr($str, 0, $len, 'utf-8').' '.$ending;}
		return $output;
	}
	
	public static function get_ext($filename){
		return strtolower(substr(strrchr($filename, '.'), 1));
	}
	
	public static function isWinServer(){
		return isset($_SERVER['WINDIR']) ? TRUE : FALSE;
	}
	
	public static function getCountriesList($default){
		
		$countries = require FS_ROOT.'includes/countries/counrties.php';
		$output = '';
		foreach($countries as $c)
			$output .= '<option value="'.$c.'" '.($default == $c ? 'selected' : '').'>'.$c.'</option>';
		return $output;
	}
	
	public static function formatByteSize($bytesize, $toArray = FALSE){
		
		$output = array('value' => 0, 'units' => '');
		
		if($bytesize < 1048576)
			$output = array('value' => round($bytesize / 1024, 2), 'units' => 'КБ');
		elseif($bytesize < 1073741824)
			$output = array('value' => round($bytesize / 1048576, 2), 'units' => 'МБ');
		else
			$output = array('value' => round($bytesize / 1073741824, 2), 'units' => 'ГБ');
		
		return $toArray
			? $output
			: $output['value'].' '.$output['units'];
	}
	
	/**
	 * АНАЛОГ ВСТРОЕННОЙ ФУНКЦИИ BASENAME
	 * В отличии от встроенной функции,
	 * корректно определяет имя, даже если в нем содержатся пробелы.
	 * Если переданное имя оканчивается слешем, то оно интерпретируется как путь,
	 * и, соответственно, возвращается пустое имя.
	 * @param string $fullName - полное имя файла (включая путь)
	 * @return string имя файла без пути
	 */
	public static function basename($fullName){
	
		$fullName = str_replace('\\', '/', $fullName);
		if(substr($fullName, -1, 1) == '/')
			return '';
		$fullNameArr = explode('/', $fullName);
		return end($fullNameArr);
	}

	/** УБРАТЬ СЛЕШИ MAGIC_QUOTES */
	public static function unescape($data){
		
		if (!get_magic_quotes_gpc())
			return $data;
		
		if (is_array($data))
			foreach($data as &$v)
				$v = is_scalar($v) ? stripslashes($v) : self::unescape($v);
		else
			$data = stripslashes($data);
		
		return $data;
	}
	
	public static function sendDownloadHeaders($filename){
		
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: cache');
		header('Content-type: application/download');
		header('Content-Disposition: attachment; filename='.$filename);
	}

}
