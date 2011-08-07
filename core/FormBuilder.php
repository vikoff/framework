<?

class FormBuilder {
	
	public static function printSelectOptions($options, $active = null, $params = array()){
		
		$output = '';
		foreach($options as $k => $v){
			
			$key = !empty($params['keyEqVal']) ? $v : $k;
			$output .= '<option value="'.$key.'"'.($key == $active ? ' selected="selected"' : '').'>'.$v.'</option>';
		}
			
		return $output;
	}
}

?>