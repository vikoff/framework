<?php

class TestGroup_Model extends ActiveRecord {
	
	/** имя модуля */
	const MODULE = 'test-group';
	
	/** таблица БД */
	const TABLE = 'test_groups';
	
	/** типы сохранения */
	const SAVE_CREATE   = 'create';
	const SAVE_EDIT     = 'edit';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	
	/** точка входа в класс (создание нового объекта) */
	public static function create(){
			
		return new TestGroup_Model(0, self::INIT_NEW);
	}
	
	/** точка входа в класс (загрузка существующего объекта) */
	public static function load($id){
		
		return new TestGroup_Model($id, self::INIT_EXISTS);
	}
	
	/** точка входа в класс (загрузка существующего объекта) */
	public static function forceLoad($id, $fieldvalues){
		
		return new TestGroup_Model($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	/** получить имя класса */
	public function getClass(){
		return __CLASS__;
	}
	
	/**
	 * дозагрузка данных
	 * выполняется после основной загрузки данных из БД
	 * и только для существующих объектов
	 * @param array &$data - данные полученные основным запросом
	 * @return void
	 */
	protected function _afterLoad(&$data){}
	
	/** подготовка данных к отображению */
	public function beforeDisplay($data){
	
		 $data['date_str'] = YDate::loadTimestamp($data['date'])->getStrDateShortTime();
		// $data['create_date_str'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		return $data;
	}
	
	/** получить экземпляр валидатора */
	public function getValidator($mode = self::SAVE_CREATE){
		
		$rules = array(
			'name' => array('strip_tags' => TRUE, 'length' => array('max' => 255), 'required' => TRUE)
		);
		
		$fields = array();
		switch($mode) {
			
			case self::SAVE_CREATE:
				$fields = array('name');
				break;
			
			case self::SAVE_EDIT:
				$fields = array('name');
				break;
			
			default: trigger_error('Неверный ключ валидатора', E_USER_ERROR);
		}
		
		$fieldsRules = array();
		foreach($fields as $f)
			$fieldsRules[$f] = $rules[$f];
			
		$validator = new Validator($fieldsRules);
		
		$validator->setFieldTitles(array(
			'id' => 'id',
			'name' => 'Название',
			'date' => 'Дата создания',
		));
		
		return $validator;
	}
		
	/** пре-валидация данных */
	public function preValidation(&$data, $saveMode = self::SAVE_DEFAULT){}
	
	/** пост-валидация данных */
	public function postValidation(&$data, $saveMode = self::SAVE_DEFAULT){
		
		// $data['author'] = CurUser::id();
		 $data['date'] = time();
		// if($this->isNewObj)
			// $data['create_date'] = time();
	}
	
	/** действие после сохранения */
	public function afterSave($data){
		
	}
	
	/** подготовка к удалению объекта */
	public function beforeDestroy(){
	
	}
	
}

class TestGroup_Collection extends ARCollection {
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'name' => 'Название',
		'date' => 'Дата создания',
	);
	
	
	/**
	 * @param array $filters
	 * @param array $options
	 * @return TestGroup_Collection
	 */
	public static function load($filters = array(), $options = array()){
			
		return new TestGroup_Collection($filters, $options);
	}
	
	/** конструктор */
	public function __construct($filters = array(), $options = array()){
		
		$this->_filters = $filters;
		$this->_options = $options;
	}

	/** получить список с постраничной разбивкой */
	public function getPaginated(){
		
		$where = $this->_getSqlFilter();
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.TestGroup_Model::TABLE.' '.$where.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->fetchAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TestGroup_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	/** получить список всех элементов */
	public function getAll(){
		
		$where = $this->_getSqlFilter();
		$data = db::get()->fetchAssoc('SELECT * FROM '.TestGroup_Model::TABLE.' '.$where, 'id');
		
		foreach($data as &$row)
			$row = TestGroup_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}

	/** получить список всех элементов */
	public function getList(){

		$where = $this->_getSqlFilter();
		$data = db::get()->fetchPairs('SELECT id, name FROM '.TestGroup_Model::TABLE.' '.$where);

		return $data;
	}

}
