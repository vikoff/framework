<?php

/**
 * ПОЛУЧИТЬ ЗНАЧЕНИЕ ПЕРЕМЕННОЙ
 * @param mixed &$varname - имя переменной (может быть не определена)
 * @param mixed $defaultVal - значение по умолчанию (возвращается, если переменная не определена)
 * @param string $type - тип, принудительно присваеваемый переменной, если она определена
 * @return mixed - полученное значение
 */
function getVar(&$varname, $defaultVal = '', $type = ''){

	if(!isset($varname))
		return $defaultVal;
	
	if(strlen($type))
		settype($varname, $type);
	
	return $varname;
}

function href($href){
	
	return WWW_ROOT.(CFG_USE_SEF
		// http://site.com/controller/method?param=value
		? $href
		// http://site.com/index.php?r=controller/method&param=value
		: 'index.php'.(!empty($href) ? '?r='.str_replace('?', '&', $href) : ''));
}

