<?

class Html_Form {
	
	/** имя модуля */
	const MODULE = 'html';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Html/templates/';
	
	private $_template = null;
	
	
	public static function create($template){
		
		return new Html_Form($template);
	}
	
	public function __construct($template){
		
		$this->_template = $template;
	}
	
	/**
	 * СГЕГЕРИРОВАТЬ HTML INPUT
	 * @param array $attrs - все параметры инпута вида 'параметр' => 'значение'
	 * @return string html input
	 */
	public static function input($attrs){
		
		if(!empty($attrs['value']))
			$attrs['value'] = htmlspecialchars($attrs['value']);
			
		$preparedAttrs = array();
		foreach($attrs as $k => &$v)
			$preparedAttrs[] = $k.'="'.$v.'"';
		
		return '<input '.implode(' ', $preparedAttrs).' />';
	}
	
	/**
	 * СГЕГЕРИРОВАТЬ HTML INPUT type="text"
	 * @param array $attrs - все параметры инпута вида 'параметр' => 'значение'
	 * @return string html input type=text
	 */
	public static function inputText($attrs){
		
		if(!empty($attrs['value']))
			$attrs['value'] = htmlspecialchars($attrs['value']);
			
		$preparedAttrs = array();
		foreach($attrs as $k => &$v)
			$preparedAttrs[] = $k.'="'.$v.'"';
		
		return '<input type="text" '.implode(' ', $preparedAttrs).' />';
	}
	
	/**
	 * СГЕГЕРИРОВАТЬ HTML INPUT type="checkbox"
	 * @param array $attrs - все параметры чекбокса вида 'параметр' => 'значение'
	 *                       ВАЖНО: параметр 'checked' нужно передавать в виде bool
	 * @return string html input type=checkbox
	 */
	public static function checkbox($attrs){
		
		if(!empty($attrs['checked']))
			$attrs['checked'] = 'checked';
		else
			unset($attrs['checked']);
			
		$preparedAttrs = array();
		foreach($attrs as $k => &$v)
			$preparedAttrs[] = $k.'="'.$v.'"';
		
		return '<input type="checkbox" '.implode(' ', $preparedAttrs).' />';
	}
	
	/**
	 * СГЕГЕРИРОВАТЬ HTML INPUT type="radio"
	 * @param array $attrs - все параметры радио-кнопки вида 'параметр' => 'значение'
	 *                       ВАЖНО: параметр 'checked' нужно передавать в виде bool
	 * @return string html input type=radio
	 */
	public static function radio($attrs){
		
		if(!empty($attrs['checked']))
			$attrs['checked'] = 'checked';
		else
			unset($attrs['checked']);
			
		$preparedAttrs = array();
		foreach($attrs as $k => &$v)
			$preparedAttrs[] = $k.'="'.$v.'"';
		
		return '<input type="radio" '.implode(' ', $preparedAttrs).' />';
	}
	
	/**
	 * СГЕНЕРИРОВАТЬ HTML SELECT
	 * @param array|false $selectAttrs - атрибуты тега selelect. Если FALSE, тогда генерируются только опции
	 * @param array $optionsArr - ассоциативный массив, $value => $title
	 *              или массив-список $title, $title
	 * @param string|null $active - выбранный элемент списка
	 * @param array $params - списко дополнительных параметров
	 *                     'keyEqVal' => bool - использовать value, такое же как и title
	 * @return string html select
	 */
	public static function select($selectAttrs, $optionsArr, $active = null, $params = array()){
		
		$options = '';
		foreach($optionsArr as $k => $v){
			$key = !empty($params['keyEqVal']) ? $v : $k;
			$options .= '<option value="'.$key.'"'.($key == $active ? ' selected="selected"' : '').'>'.$v.'</option>';
		}
		
		if(is_array($selectAttrs)){
			$select = '<select';
			foreach($selectAttrs as $k => $v)
				$select .= ' '.$k.'="'.$v.'"';
			$select .= '>'.$options.'</select>';
			
			return $select;
		}
		else{
			return $options;
		}
	}
	
	/**
	 * СГЕГЕРИРОВАТЬ HTML TEXTAREA
	 * @param array $attrs - все параметры инпута вида 'параметр' => 'значение', включая 'value'
	 * @return string html textarea
	 */
	public static function textarea($attrs){
		
		$value = '';
		if(isset($attrs['value'])){
			$value = htmlspecialchars($attrs['value']);
			unset($attrs['value']);
		}
			
		$preparedAttrs = array();
		foreach($attrs as $k => &$v)
			$preparedAttrs[] = $k.'="'.$v.'"';
		
		return '<textarea '.implode(' ', $preparedAttrs).'>'.$value.'</textarea>';
	}
	
}