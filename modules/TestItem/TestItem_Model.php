<?php

class TestItem_Model extends ActiveRecord {
	
	/** имя модуля */
	const MODULE = 'test-item';
	
	/** таблица БД */
	const TABLE = 'test_items';
	
	/** типы сохранения */
	const SAVE_CREATE   = 'create';
	const SAVE_EDIT     = 'edit';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	const IMG_PATH = 'images/content/test_items/';

	
	/** точка входа в класс (создание нового объекта) */
	public static function create(){
			
		return new TestItem_Model(0, self::INIT_NEW);
	}
	
	/** точка входа в класс (загрузка существующего объекта) */
	public static function load($id){
		
		return new TestItem_Model($id, self::INIT_EXISTS);
	}
	
	/** точка входа в класс (загрузка существующего объекта) */
	public static function forceLoad($id, $fieldvalues){
		
		return new TestItem_Model($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
		$data['img_src'] = $data['img'] ? WWW_ROOT . self::IMG_PATH . $data['img'] : null;
		$data['thumb_src'] = $data['img'] ? WWW_ROOT . self::IMG_PATH . 'thumb_'.$data['img'] : null;
		return $data;
	}
	
	/** получить экземпляр валидатора */
	public function getValidator($mode = self::SAVE_CREATE) {
		
		$rules = array(
			'group_id' => array('settype' => 'int', 'required' => TRUE),
			'name' => array('strip_tags' => TRUE, 'length' => array('max' => 255), 'required' => TRUE),
			'description' => array('strip_tags' => TRUE, 'length' => array('max' => 65535))
		);
		
		$fields = array();
		switch($mode) {
			
			case self::SAVE_CREATE:
				$fields = array('group_id', 'name', 'description');
				break;
			
			case self::SAVE_EDIT:
				$fields = array('group_id', 'name', 'description');
				break;
			
			default: trigger_error('Неверный ключ валидатора', E_USER_ERROR);
		}
		
		$fieldsRules = array();
		foreach($fields as $f)
			$fieldsRules[$f] = $rules[$f];
			
		$validator = new Validator($fieldsRules);
		
		$validator->setFieldTitles(array(
			'id' => 'id',
			'group_id' => 'Группа',
			'name' => 'Название',
			'img' => 'Изображение',
			'description' => 'Описание',
			'date' => 'Дата создания',
		));
		
		return $validator;
	}
		
	/** пре-валидация данных */
	public function preValidation(&$data, $saveMode = self::SAVE_DEFAULT){

		$this->checkImage();
	}
	
	/** пост-валидация данных */
	public function postValidation(&$data, $saveMode = self::SAVE_DEFAULT){

		$this->saveImage();
		$data['date'] = time();
	}
	
	/** действие после сохранения */
	public function afterSave($data){
		
	}
	
	/** подготовка к удалению объекта */
	public function beforeDestroy(){
	
	}

	/** проверка изображения */
	public function checkImage() {

		$this->additData['image'] = null;

		// если изображения нет
		if (empty($_FILES['img']['name'])) {
			if ($this->isNewObj)
				$this->setError('Изображение не загружено');
			return;
		}

		$path = FS_ROOT.self::IMG_PATH;

		try {
			// создание экземпляра картинки и выполнение проверок
			$this->additData['image'] = ImageMaster::load($_FILES['img']['tmp_name'], $_FILES['img']['name'])
				->checkImageFormat($withExt = TRUE)
				->checkDir($path);

			// если новая картинка загружена, а объект существующий и была старая картинка
			// то старую надо удалить
			if ($this->isExistsObj && $this->img) {
				Tools::unlink($path.$this->img);
				Tools::unlink($path.'thumb_'.$this->img);
			}
		}
		catch (Exception $e) {
			$this->setError($e->getMessage());
		}
	}

	 /** сохранение изображения */
	public function saveImage() {

		if (!empty($this->additData['image'])) {
			try {
				$path = FS_ROOT.self::IMG_PATH;
				$copies = array(
					array(138, 98, 'thumb_', ImageMaster::T_CENTER),
					array(640, 480, '', ImageMaster::T_PROPORT),
				);
				$resultImg = $this->additData['image']
					->checkDir($path)
					->resize($path.$this->id, $copies);

				$this->setField('img', $resultImg)->_save();
			}
			catch(Exception $e){trigger_error($e->getMessage(), E_USER_ERROR);}
		}
	}

	public function deleteImg() {

		$path = FS_ROOT.self::IMG_PATH;
		if ($this->img) {
			Tools::unlink($path.$this->img);
			Tools::unlink($path.'thumb_'.$this->img);
			$this->setField('img', null);
			$this->_save();
		}
	}
}

class TestItem_Collection extends ARCollection {
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'group_id' => 'Группа',
		'name' => 'Название',
		'img' => 'Изображение',
		'description' => 'Описание',
		'date' => 'Дата создания',
	);
	
	
	/** точка входа в класс */
	public static function load($filters = array(), $options = array()){
			
		return new TestItem_Collection($filters, $options);
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
		$paginator = new Paginator('sql', array('*', 'FROM '.TestItem_Model::TABLE.' '.$where.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->fetchAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = TestItem_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	/** получить список всех элементов */
	public function getAll(){
		
		$where = $this->_getSqlFilter();
		$data = db::get()->fetchAssoc('SELECT * FROM '.TestItem_Model::TABLE.' '.$where, 'id', array());
		
		foreach($data as &$row)
			$row = TestItem_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}
	
}
