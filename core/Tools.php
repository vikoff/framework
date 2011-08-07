<?

class Tools{
	
	/* БЕЗОПАСНЫЕ ТЕКСТ */
	
	static function safeText($text, $len = 254)
	{
		if(get_magic_quotes_gpc() || get_magic_quotes_runtime()){$text = stripslashes($text);}
		$len = intval($len);
		$output = trim($text);
		$output = htmlspecialchars($output, ENT_QUOTES);
		$output = substr($output, 0, $len);
		$output = addslashes($output);
		return $output;
	}
	
	static function strFixLen($str, $len, $fillChar = ' ', $fillAfter = FALSE){
		$str = (string)$str;
		$len = (int)$len;
		$fillChar = (string)(strlen($fillChar) ? $fillChar : ' ');
		$fillAfter = (bool)$fillAfter;
		
		if(strlen($str) > $len)
			return substr($str, 0, $len);
			
		while(strlen($str) < $len){
			$str = $fillAfter ? $str.$fillChar : $fillChar.$str;
		}
		return $str;
	}
	
	/* ПОЛУЧИТЬ ЧИСЛО ИЗ ЛЮБОЙ СТРОКИ, ГДЕ ЕСТЬ ЦИФРЫ */
	
	static function getint($str){
		return intval(preg_replace("/[^\d]/", "", $str));
	}
	
	/* УНИФИЦИРОВАННЫЕ РЕГУЛЯРНЫЕ ВЫРАЖЕНИЯ */

	static function getRegExp($param){
		$rus = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя';
		switch($param){
			case 'id':			return '/^[\d]{1,20}$/i';
			case 'password':	return '/^.{4,16}$/i';
			case 'email':		return '/^[\w'.$rus.'\.\-]{1,50}\@[\w'.$rus.'\.\-]{1,20}$/i';
		}
		die("Ошибка регулярных выражений");
	}
	
	/* ФУНКЦИЯ ОЧИСТКИ ДИРЕКТОРИИ ОТ ВСЕХ ФАЙЛОВ */
	
	static function clearDir($dirNam){
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
	
	static public function mkDir($dir){

		$dir = (string)$dir;
		
		if(is_dir($dir))
			return TRUE;
		
		return mkdir($dir, 0777, TRUE);
	}
	
	static function dir_avail($dir_name){
		
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
	
	/* ПРЕОБРАЗОВАНИЕ ЗНАЧЕНИЙ */
	
	static function convert_item($input, $convert_rules)
	{
		/*
		$input - одномерный ассоциативный массив входных данных;
		$convert_rules - одномерный ассоциативный массив ключи которого - имена изменяемых полей, а значения - тип преобразования.
		*/
		if(is_array($input) && is_array($convert_rules)){
			foreach($convert_rules as $field => $operation){
				switch($operation){
					case 'unserialize': 
						$input[$field] = unserialize($input[$field]);
						if(!is_array($input[$field])){$input[$field] = array();}
						break;
					case 'timestamp2date':
						$input[$field] = YDate::timestamp2date($input[$field]);
						break;
				}
			}
		}
		/* $input - строка; $convert_rules - строка: тип преобразования */
		elseif(!is_array($input) && is_string($convert_rules)){
			switch($convert_rules){
				case 'unserialize': 
					$input = unserialize($input);
					if(!is_array($input)){$input = array();}
					break;
				case 'timestamp2date':
					$input = YDate::timestamp2date($input);
					break;
			}
		}else{die("Invalid parametr for function 'Tools::convert_item': ".gettype($input).", ".gettype($convert_rules));}
		return $input;
	}
	
	/**** ПРОВЕРИТЬ ФОРМАТ ПАРАМЕТРА ****/
	
	static function check_format($value, $format){
		switch($format){
			case 'num':		if(is_numeric($value)){return true;} break;
			case 'num_n0':	if(is_numeric($value) && $value){return true;} break;
			case 'str':		if(is_string($value)){return true;} break;
			case 'str_n0':	if(is_string($value) && $value){return true;} break;
			default: die("Invalid Format: ".$format." (function check_format)");
		}
		return false;
	}
	
	/* ПОЛУЧИТЬ "ФАМИЛИЯ И.О." ИЗ "ФАМИЛИЯ ИМЯ ОТЧЕСТВО" */
	
	static function get_short_name($longname, $flags = '')
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
	
	static function truncate($str, $len = 128, $ending = '...'){
		
		$len = (int)$len;
		$output = '';
		$real_len = strlen($str);
		if($real_len <= $len){$output = $str;}
		else{$output = mb_substr($str, 0, $len, 'utf-8').' '.$ending;}
		return $output;
	}
	
	static function get_ext($filename){
		return strtolower(substr(strrchr($filename, '.'), 1));
	}
	
	static public function cp1251_to_utf8($txt){
	
		$in_arr = array(
			chr(208), chr(192), chr(193), chr(194),
			chr(195), chr(196), chr(197), chr(168),
			chr(198), chr(199), chr(200), chr(201),
			chr(202), chr(203), chr(204), chr(205),
			chr(206), chr(207), chr(209), chr(210),
			chr(211), chr(212), chr(213), chr(214),
			chr(215), chr(216), chr(217), chr(218),
			chr(219), chr(220), chr(221), chr(222),
			chr(223), chr(224), chr(225), chr(226),
			chr(227), chr(228), chr(229), chr(184),
			chr(230), chr(231), chr(232), chr(233),
			chr(234), chr(235), chr(236), chr(237),
			chr(238), chr(239), chr(240), chr(241),
			chr(242), chr(243), chr(244), chr(245),
			chr(246), chr(247), chr(248), chr(249),
			chr(250), chr(251), chr(252), chr(253),
			chr(254), chr(255)
		);

		$out_arr = array (
			chr(208).chr(160), chr(208).chr(144), chr(208).chr(145),
			chr(208).chr(146), chr(208).chr(147), chr(208).chr(148),
			chr(208).chr(149), chr(208).chr(129), chr(208).chr(150),
			chr(208).chr(151), chr(208).chr(152), chr(208).chr(153),
			chr(208).chr(154), chr(208).chr(155), chr(208).chr(156),
			chr(208).chr(157), chr(208).chr(158), chr(208).chr(159),
			chr(208).chr(161), chr(208).chr(162), chr(208).chr(163),
			chr(208).chr(164), chr(208).chr(165), chr(208).chr(166),
			chr(208).chr(167), chr(208).chr(168), chr(208).chr(169),
			chr(208).chr(170), chr(208).chr(171), chr(208).chr(172),
			chr(208).chr(173), chr(208).chr(174), chr(208).chr(175),
			chr(208).chr(176), chr(208).chr(177), chr(208).chr(178),
			chr(208).chr(179), chr(208).chr(180), chr(208).chr(181),
			chr(209).chr(145), chr(208).chr(182), chr(208).chr(183),
			chr(208).chr(184), chr(208).chr(185), chr(208).chr(186),
			chr(208).chr(187), chr(208).chr(188), chr(208).chr(189),
			chr(208).chr(190), chr(208).chr(191), chr(209).chr(128),
			chr(209).chr(129), chr(209).chr(130), chr(209).chr(131),
			chr(209).chr(132), chr(209).chr(133), chr(209).chr(134),
			chr(209).chr(135), chr(209).chr(136), chr(209).chr(137),
			chr(209).chr(138), chr(209).chr(139), chr(209).chr(140),
			chr(209).chr(141), chr(209).chr(142), chr(209).chr(143)
		);   

		$txt = str_replace($in_arr,$out_arr,$txt);
		return $txt;
	}
	
	static public function isWinServer(){
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
	public function basename($fullName){
	
		$fullName = str_replace('\\', '/', $fullName);
		if(substr($fullName, -1, 1) == '/')
			return '';
		$fullNameArr = explode('/', $fullName);
		return end($fullNameArr);
	}
}

?>