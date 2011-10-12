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
	
	// public function 
	
	public function hidden($name, $value){
		
		return '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
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