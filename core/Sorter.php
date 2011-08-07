<?

class Sorter{
	
	/**
	 * Идентификатор поля для сортировки. Может совпадать с именем поля, а может нет.
	 * @var string
	 */
	private $_sortingField = '';
	
	/**
	 * Направление сортировки
	 * @var string ASC|DESC
	 */
	private $_sortingDirection = '';
	
	/**
	 * Выражение для вставки в ORDER BY в SQL запросе
	 * @var string
	 */
	private $_orderByStatement = '';
	
	/**
	 * Ассоциативный массив, где ключи - идентификаторы или имена полей,
	 * допустимых для сортировки, а значения - подписи кнопок сортировки
	 * Пара 'ключ - значение' может иметь два вида:
	 * 		1) 'sql_field_name' => 'Имя, отображаемое пользователю'
	 * 		2) 'field_identifier' => array('sql_field1 _DIR_, sql_field2 _DIR_', 'Имя, отображаемое пользователю')
	 * Во втором случае field_identifier - это значение, которое будет передаваться через url, а так же выполнять роль
	 * ключа в массиве Sorter::$_sortableLinks. _DIR_ конструкция, которая должна присутствовать в sql выражении,
	 * чтобы впоследствии быть замененной на реальное направление сортировки.
	 * @var array
	 */
	private $_sortingFieldsTitles = array();
	
	/**
	 * Ассоциативный массив кнопок сортировки
	 * ключ - идентификатор поля, значение - подпись кнопки
	 * @var null|array - возвращается всегда как массив
	 */
	private $_sortableLinks = null;
	
	
	/**
	 * КОНСТРУКТОР
	 * @param string $sortField - поле для сортировки по умолчанию (например 'id')
	 * @param string $sortDirection - направление сортировки по умолчанию (например 'ASC')
	 * @param array $sortFieldsTitles - массив допустимых полей для сортировки
	 * 		@see Sorter::$_sortingFieldsTitles
	 * @return Sorter
	 */
	public function __construct($sortField, $sortDirection, $sortFieldsTitles){
		
		$this->_sortingField = $sortField;
		$this->_sortingDirection = strtolower($sortDirection);
		$this->_sortingFieldsTitles = $sortFieldsTitles;
		
		$this->_parseQS();
	}
	
	/**
	 * PARSE QUERY STRING
	 * Получение поля и направления сортировки из Query String
	 * назначает поля $this->_sortingDirection, $this->_sortingField.
	 * @return void;
	 */
	private function _parseQS(){
		
		// если параметр $_GET['sort'] передан, пытаемся применить его
		if(!empty($_GET['sort'])){
			if($_GET['sort']{0} == '-'){
				$this->_sortingDirection = 'DESC';
				$_sortField = substr($_GET['sort'], 1);
			}else{
				$this->_sortingDirection = 'ASC';
				$_sortField = $_GET['sort'];
			}
			// если полученное поле сортировки допустимо - используем его
			if(array_key_exists($_sortField, $this->_sortingFieldsTitles)){
				$this->_sortingField = $_sortField;
				$this->_orderByStatement = is_array($this->_sortingFieldsTitles[$_sortField])
					? str_replace('_DIR_', $this->_sortingDirection, $this->_sortingFieldsTitles[$_sortField][0])
					: $this->_sortingField.' '.$this->_sortingDirection;
			}
			// если не допустимо - используем поля по умолчанию
			else{
				$this->_orderByStatement = $this->_sortingField.' '.$this->_sortingDirection;
			}
		}
		// если параметр $_GET['sort'] не передан, используем значения по умолчанию
		else{
			$this->_orderByStatement = $this->_sortingField.' '.$this->_sortingDirection;
		}
		
		// echo '<hr>'.$this->_orderByStatement.'<hr>';
	}
	
	/**
	 * GET ORDER BY
	 * @return string sql ORDER BY statement (например 'id DESC');
	 */
	public function getOrderBy(){
		
		return $this->_orderByStatement;
	}
	
	/**
	 * GET SORTABLE LINKS
	 * @return array ассоциативный массив ссылок сортировщиков
	 */
	public function getSortableLinks(){
		
		if(is_null($this->_sortableLinks)){
		
			$this->_sortableLinks = array();
			foreach($this->_sortingFieldsTitles as $field => $title){
				$title = is_array($title) ? $title[1] : $title;
				$titleSuffix = '';
				$dirSign = '';
				if($field == $this->_sortingField){
					if($this->_sortingDirection == 'ASC'){
						$dirSign = '-';
						$titleSuffix = '&nbsp;<span style="font-size: 10px;">▲</span>';
					}else{
						$dirSign = '';
						$titleSuffix = '&nbsp;<span style="font-size: 10px;">▼</span>';
					}
				}
				$this->_sortableLinks[$field] = '<a href="'.App::getHrefReplaced('sort', $dirSign.$field).'">'.$title.$titleSuffix.'</a>';
			}
		}
		
		return $this->_sortableLinks;
	}

}

?>