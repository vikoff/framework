<?

class SmartyPlugins{
	
	/**
	 * FUNCTION A
	 * 
	 * Функция генерирует атрибут href или же весь тег <a> (зависит - передан ли параметр 'text')
	 * @param array $params
	 * 		string 'href' - обязательный параметр, ссылка вида 'controller/action?param=value&param2=val2'
	 * 		string 'text' - текст ссылки. Если передан, будет сгенерирована вся ссылка.
	 * @return string значение атрибута href или весь тег ссылки
	 */
	public static function function_a($params){
		
		$href = App::href(getVar($params['href']));
		$text = getVar($params['text']);
		
		unset($params['href']);
		unset($params['text']);
		
		if(empty($text))
			return $href;
		
		$attrsStr = '';
		foreach($params as $k => $v)
			$attrsStr .= ' '.$k.'="'.$v.'"';
			
		return '<a href="'.$href.'"'.$attrsStr.'>'.$text.'</a>';
	}
	
	////////////////////////////////////////
	////// PREFILTER TRIM_WHITE_SPACE //////
	////////////////////////////////////////
	
	public static function trimwhitespace($source, &$smarty){

		// Pull out the script blocks
		preg_match_all("!<script[^>]*?>.*?</script>!is", $source, $match);
		$_script_blocks = $match[0];
		$source = preg_replace("!<script[^>]*?>.*?</script>!is",
							   '@@@SMARTY:TRIM:SCRIPT@@@', $source);

		// Pull out the pre blocks
		preg_match_all("!<pre[^>]*?>.*?</pre>!is", $source, $match);
		$_pre_blocks = $match[0];
		$source = preg_replace("!<pre[^>]*?>.*?</pre>!is",
							   '@@@SMARTY:TRIM:PRE@@@', $source);
		
		// Pull out the textarea blocks
		preg_match_all("!<textarea[^>]*?>.*?</textarea>!is", $source, $match);
		$_textarea_blocks = $match[0];
		$source = preg_replace("!<textarea[^>]*?>.*?</textarea>!is",
							   '@@@SMARTY:TRIM:TEXTAREA@@@', $source);

		// remove all leading spaces, tabs and carriage returns NOT
		// preceeded by a php close tag.
		$source = trim(preg_replace(array('/\s+/'), ' ', $source));

		// replace textarea blocks
		self::trimwhitespace_replace("@@@SMARTY:TRIM:TEXTAREA@@@",$_textarea_blocks, $source);

		// replace pre blocks
		self::trimwhitespace_replace("@@@SMARTY:TRIM:PRE@@@",$_pre_blocks, $source);

		// replace script blocks
		self::trimwhitespace_replace("@@@SMARTY:TRIM:SCRIPT@@@",$_script_blocks, $source);

		return $source;
	}

	public static function trimwhitespace_replace($search_str, $replace, &$subject) {
		$_len = strlen($search_str);
		$_pos = 0;
		for ($_i=0, $_count=count($replace); $_i<$_count; $_i++)
			if (($_pos=strpos($subject, $search_str, $_pos))!==false)
				$subject = substr_replace($subject, $replace[$_i], $_pos, $_len);
			else
				break;

	}

	
	////////////////////////////////////////
	////// PREFILTER TRIM_WHITE_SPACE //////
	////////////////////////////////////////

	public static function escape_script($tpl_source, &$smarty){
	
		$regex = '/(<script.*>)(.*)(<\/script>)/sU'; 
		$new_source = preg_replace_callback($regex, 
			array('SmartyPlugins', '_escape_script'), 
			$tpl_source); 
		return $new_source; 
	}	
	
	public static function _escape_script($matches){
		
		$openTag  = & $matches[1];
		$script   = & $matches[2]; 
		$closeTag = & $matches[3];
		
		$script = preg_replace('/{{/', '{/literal}{', $script); 
		$script = preg_replace('/}}/', '}{literal}', $script); 

		$result = $openTag.'{literal}'.$script.'{/literal}'.$closeTag;
		return $result; 
	} 
}

?>