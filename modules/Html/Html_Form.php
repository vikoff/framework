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
	
}