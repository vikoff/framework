<?php
/**
 * Класс для работы с пагинацией
 * <code>
 *		$paginator = new Paginator('sql', array('*', "FROM users WHERE param='value'"));
 *		print_r(db::get()->getAll($paginator->getSql());
 * </code>
 * @using classes
 *		db
 * @using methods
 *		App::getHrefReplaced()
 */

class Paginator {
	
	
	///////////////////////////
	//////// ПАРАМЕТРЫ ////////
	///////////////////////////
	
	// использовать сокращенный список страниц (на указываемое число в каждую сторону)
	// чтобы показывать полный список страниц, установите значение в 0
	static private $_shortPagesBtn = 5;
	
	// число элементов на странице по умолчанию
	static private $_defaultItemsPerPage = 10;
	
	// допустимые варианты количества элементов на странице
	static public $itemsPerPageVariants = array('10' => 'по 10', '20' => 'по 20', '50' => 'по 50', '100' => 'по 100', 'all' => 'Все');
	
	// число элементов на странице
	private $_itemsPerPage = null;
	
	// флаг, можно ли пользователю менять кол-во элементов на странице
	private $_isItemsPerPageChangeable = FALSE;

	// имя GET параметра с номером страницы
	static private $_pageParamName = 'p';

	
	######## СВОЙСТВА ########
	
	// общее количество элементов
	private $_totalNumItems = null;
	
	// выражение LIMIT для SQL запроса
	private $_limit = FALSE;
	
	// массив-список из двух элементов, содержащий лимиты запроса
	private $_limitsArray = array();
	
	// исходный SQL запрос
	private $_sql = null;
	
	// html-кнопки перехода по страницам
	private $_buttons = array(
		'prev' => '',
		'pages' => '',
		'next' => '',
	);
	
	// html-тэги link перехода по страницам
	private $_linkTags = array(
		'start' => '',
		'prev' => '',
		'next' => '',
	);
	
	// общее число страниц
	private $_totalNumPages = 1;
	
	// номер текущей страницы (начиная с 1)
	private $_curPage = 1;

	private $_db = null;

	
	///////////////////////////
	//////// АКСЕССОРЫ ////////
	///////////////////////////
	
	static public function setShortPagesBtn($val){
		self::$_shortPagesBtn = (int)$val;
	}
	
	static public function setPageParamName($val){
		self::$_pageParamName = (string)$val;
	}
	
	////////////////////////////////////////////////
	//////// МЕТОДЫ ЗАГРУЗКИ ДАННЫХ В КЛАСС ////////
	////////////////////////////////////////////////
	
	public function __construct($type, $data, $itemsPerPage = null, $addit = array()){
		
		if(!in_array($type, array('sql', 'num'), true))
			trigger_error('Неизвестный тип входных данных ['.$type.']', E_USER_ERROR);
		
		if($type == 'sql' && !is_array($data))
			trigger_error('Тип входных данных [sql] подразумевает передачу данных в виде массива-списка', E_USER_ERROR);
		
		$this->setItemsPerPage($itemsPerPage);
		
		$this->_db = isset($addit['dbConnection']) ? $addit['dbConnection'] : db::get();
		
		if($type == 'sql')
			$this->_loadSql($data[0], $data[1]);
		if($type == 'num')
			$this->_loadNumItems($data);
			
		$this->_process();
	}
	
	// ЗАГРУЗИТЬ SQL ВЫРАЖЕНИЕ
	private function _loadSql($extractPart, $conditionPart){
	
		$this->_totalNumItems = $this->_db->fetchOne('SELECT COUNT(1) '.$conditionPart);
		$this->_sql = 'SELECT '.$extractPart.' '.$conditionPart;
	}
	
	// ЗАГРУЗИТЬ КОЛИЧЕСТВО ЭЛЕМЕНТОВ
	private function _loadNumItems($num){
	
		$this->_totalNumItems = (int)$num;
	}
	
	// УСТАНОВИТЬ КОЛИЧЕСТВО ЭЛЕМЕНТОВ НА СТРАНИЦЕ
	public function setItemsPerPage($num){
		
		// если передан null, принимаем значение сохраненное в сессии, или дефолтное
		if(is_null($num)){
			$this->_itemsPerPage = !empty($_SESSION['paginator-items-per-page'])
				? $_SESSION['paginator-items-per-page']
				: self::$_defaultItemsPerPage;
		}
		// если передано значение проверим его
		else{
			// определение значения и возможности его изменения
			$prefix = substr($num, 0, 1);
			if($prefix == '~' || $prefix == '='){
				$this->_isItemsPerPageChangeable = $prefix == '~';
				$num = substr($num, 1);
			}else{
				$this->_isItemsPerPageChangeable = FALSE;
			}
				
			if(!isset(self::$itemsPerPageVariants[$num]))
				trigger_error('Количество элементов на странице имеет недопустимое значение: '.$num, E_USER_ERROR);
			
			if($this->_isItemsPerPageChangeable)
				$this->_itemsPerPage = !empty($_SESSION['paginator-items-per-page'])
					? $_SESSION['paginator-items-per-page']
					: $num;
			else
				$this->_itemsPerPage = $num;
		}
	}
	
	
	/////////////////////////////////////////
	//////// МЕТОДЫ ПОЛУЧЕНИЯ ДАННЫХ ////////
	/////////////////////////////////////////

	// ПОЛУЧИТЬ ГОТОВОЕ SQL ВЫРАЖЕНИЕ
	public function getSql(){
		
		if(is_null($this->_sql))
			trigger_error('Невозможно получить SQL выражение.', E_USER_ERROR);
		
		return $this->_sql.$this->getLimitsSql();
	}
	
	// ПОЛУЧИТЬ ВЫРАЖЕНИЕ LIMIT ДЛЯ ЗАПРОСА К БД
	public function getLimitsSql(){

		return $this->_limit;
	}
	
	// ПОЛУЧИТЬ МАССИВ-СПИСОК ЛИМИТОВ ПАГИНАЦИИ
	public function getLimitsArray(){

		return $this->_limitsArray;
	}
	
	// ПОЛУЧИТЬ HTML КНОПКУ ПАГИНАЦИИ (ОДНУ)
	public function getButton($btn){
		
		if(!isset($this->_buttons[$btn]))
			trigger_error('Неверная кнопка пагинации "'.$btn.'".', E_USER_ERROR);
		
		return $this->_buttons[$btn];
	}
	
	// ПОЛУЧИТЬ HTML КНОПКИ ПАГИНАЦИИ (ВСЕ, В КОНТЕЙНЕРЕ)
	public function getButtons(){

		return ''
			.'<div class="pagination-num-form">'.($this->_isItemsPerPageChangeable ? 'Всего '.$this->_totalNumItems.' элементов. Отображать '.$this->_getItemsPerPageForm() : '').'</div>'
			.'<div class="pagination">'
				.$this->_buttons['prev']
				.$this->_buttons['pages']
				.$this->_buttons['next']
			.'</div>'
			;
	}
	
	private function _getItemsPerPageForm(){
		
		$options = '';
		foreach(self::$itemsPerPageVariants as $k => $v)
			$options .= '<option value="'.$k.'" '.($k == $this->_itemsPerPage ? 'selected="selected"' : '').'>'.$v.'</option>';
		
		return '
			<form class="inline" action="" method="post">
				'.FORMCODE.'
				<input type="hidden" name="action" value="user/paginator-set-items-per-page" />
				<select name="num" onchange="this.form.submit();">'.$options.'</select>
			</form>';
	}
	
	// ПОЛУЧИТЬ LINK-ТЕГ
	public function getLinkTag($tag){
		
		if(!isset($this->_linkTags[$tag]))
			trigger_error('Неверная кнопка пагинации "'.$tag.'".', E_USER_ERROR);
		
		return $this->_linkTags[$tag];
	}
	
	// ПОЛУЧИТЬ ВСЕ LINK-ТЕГИ
	public function getLinkTags(){

		return $this->_linkTags;
	}
	
	
	/////////////////////////
	//////// РАСЧЕТЫ ////////
	/////////////////////////

	// ВЫПОЛНИТЬ РАСЧЕТЫ
	private function _process(){

		if(is_null($this->_totalNumItems))
			trigger_error('Пагинация не инициализирована.', E_USER_ERROR);
		
		if($this->_itemsPerPage == 'all'){
		
			$this->_totalNumPages = 1;
			$this->_curPage = 1;
			$this->_limitsArray = array(0, 0);
			$this->_limit = '';
			
		}else{
			
			$this->_totalNumPages = ceil($this->_totalNumItems / $this->_itemsPerPage);
			
			$this->_curPage = isset($_GET[self::$_pageParamName]) ? (int)$_GET[self::$_pageParamName] : 1;
			if($this->_curPage > $this->_totalNumPages)
				$this->_curPage = $this->_totalNumPages;
			if($this->_curPage < 1)
				$this->_curPage = 1;
			
			// подсчет LIMIT выражения
			$this->_limitsArray = array(
				(($this->_curPage - 1) * $this->_itemsPerPage),
				$this->_itemsPerPage
			);
			
			$this->_limit = ' LIMIT '.$this->_limitsArray[0].', '.$this->_limitsArray[1];
		}
		
		// создание кнопок пагинации
		if($this->_totalNumPages > 1){
		
			$curUrl = App::getHrefReplaced(self::$_pageParamName, null);
			$urlPrefix = strpos($curUrl, '?') === FALSE ? '?' : '&';
		
			// создание кнопок назад/вперед и link-тэгов start/prev/next
			$this->_linkTags['start'] = $curUrl.$urlPrefix.self::$_pageParamName.'=1';
			if($this->_curPage > 1){
				$prevHref = $curUrl.$urlPrefix.self::$_pageParamName.'='.($this->_curPage - 1);
				$this->_linkTags['prev'] = $prevHref;
				$this->_buttons['prev'] = '<a class="prev" href="'.$prevHref.'">Назад</a>';
			}
			if($this->_curPage < $this->_totalNumPages){
				$nextHref = $curUrl.$urlPrefix.self::$_pageParamName.'='.($this->_curPage + 1);
				$this->_linkTags['next'] = $nextHref;
				$this->_buttons['next'] = '<a class="next" href="'.$nextHref.'">вперед</a>';
			}
				
			
			//создание списка страниц
			$markSkippedBefore = false;
			$markSkippedAfter = false;
			for($i = 1; $i <= $this->_totalNumPages; $i++){
			
				if(self::$_shortPagesBtn){
					// пропущенные страницы
					if($i != 1 && $i != $this->_totalNumPages && $i != $this->_curPage && abs($this->_curPage - $i) > self::$_shortPagesBtn){
						if($i < $this->_curPage){
							if(!$markSkippedBefore){
								$this->_buttons['pages'] .= '...';
								$markSkippedBefore = true;
							}
						}else{
							if(!$markSkippedAfter){
								$this->_buttons['pages'] .= '...';
								$markSkippedAfter = true;
							}
						}
						continue;
					}
				}
				if($this->_curPage == $i)
					$this->_buttons['pages'] .= '<span class="active">'.$i.'</span>';
				else
					$this->_buttons['pages'] .= '<a href="'.$curUrl.$urlPrefix.self::$_pageParamName.'='.$i.'" class="page">'.$i.'</a>';
			}
		}
		
	}
	
}
