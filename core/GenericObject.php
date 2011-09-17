<?php

class GenericObject{
	
	const INIT_NEW = 1;
	const INIT_EXISTS = 2;
	const INIT_EXISTS_FORCE = 3;
	const INIT_ANY = 4;

	public $id = null;
	public $pkField = 'id';
	public $tableName = null;
	
	public $isNewObj = null;
	public $isExistsObj = null;

	protected $dbFieldValues = array();
	protected $modifiedFields = array();
	protected $fieldValuesForDisplay = array();
	
	/**
	 * Экземпляр валидатора
	 * используется в потомках
	 * @var null|Validator-instance
	 */
	protected $validator = null;
	protected $_errors = array();
	
	protected $additData = array();
	
	/**
	 * КОНСТРУКТОР
	 * Инициирует объект в зависимости от $initType
	 * @throws Exception404 - если объект не найден
	 * @throws Exception403 - если доступ к объекту запрещен
	 * @param int $id - id записи в БД
	 * @param const $initType - тип инициализации
	 * @param array $data - данные для принудительной загрузки (forceLoad)
	 */
	public function __construct($id = 0, $initType = self::INIT_ANY, $data = array()){
		
		$this->tableName = $this->getConst('TABLE');
		
		switch($initType){
			
			// загрузка нового объекта
			case self::INIT_NEW:
			
				$this->id = 0;
				$this->isNewObj = TRUE;
				$this->isExistsObj = FALSE;
				break;
			
			// загрузка существующего объекта
			case self::INIT_EXISTS:
			
				$this->id = (int)$id;
				$this->isExistsObj = TRUE;
				$this->isNewObj = FALSE;
				$this->_loadData();
				$this->_accessCheck();
				break;
			
			// принудительная загрузка существующего объекта
			case self::INIT_EXISTS_FORCE:
				
				$this->id = (int)$id;
				$this->isExistsObj = TRUE;
				$this->isNewObj = FALSE;
				$this->_forceLoadData($data);
				break;
				
			// загрузка нового/существующего объекта в зависимости от id
			case self::INIT_ANY:
			
				$this->id = (int)$id;
				// существующий объект
				if($this->id){ 
					$this->isExistsObj = TRUE;
					$this->isNewObj = FALSE;
					$this->_loadData();
					$this->_accessCheck();
				}
				// новый объект
				else{
					$this->isNewObj = TRUE;
					$this->isExistsObj = FALSE;
				}
				break;
			
			default: trigger_error('Неверный тип инициализации конструктора', E_USER_ERROR);
		}
		
	}  
	
	/** 
	 * ПОЛУЧИТЬ КОНСТАНТУ КЛАССА НАСЛЕДНИКА
	 * @param string $name - имя константы
	 * @return string - значение константы
	 */
	public function getConst($name){
		trigger_error('Метод <b>getConst</b> не определен в классе наследнике GenericObject', E_USER_ERROR);
	}
	
	/** ЗАГРУЗКА ДАННЫХ ИЗ БАЗЫ ДАННЫХ */
	private function _loadData(){
		
		$this->dbFieldValues = $this->dbGetRow();
		
		if(!is_array($this->dbFieldValues) || !count($this->dbFieldValues))
			throw new Exception404($this->getConst('NOT_FOUND_MESSAGE'));
			
		$this->afterLoad($this->dbFieldValues);
		$this->fieldValuesForDisplay = $this->beforeDisplay($this->dbFieldValues);
	}
	
	/**
	 * ПРИНУДИТЕЛЬНАЯ ЗАГРУЗКА
	 *
	 * Загружает внешние данные в объект, заставляя его думать, что данные получены из БД.
	 * То есть если будет вызван метод save, то никаких изменений в БД не попадет.
	 * @param array $data - массив данных для загрузки
	 * @return void
	 */
	private function _forceLoadData($data) {
		
		$this->dbFieldValues = $data;
		
		if(!$this->id || !is_array($this->dbFieldValues) || !count($this->dbFieldValues))
			throw new Exception404($this->getConst('NOT_FOUND_MESSAGE'));
			
		$this->fieldValuesForDisplay = $this->beforeDisplay($this->dbFieldValues);
	}
	
	/**
	 * ПРОВЕРКА ВОЗМОЖНОСТИ ДОСТУПА К ОБЪЕКТУ
	 * Вызывается автоматически при загрузке существующего объекта
	 * В случае запрета доступа генерирует нужное исключение
	 */
	protected function _accessCheck(){}
	
	/**
	 * ДОЗАГРУЗКА ДАННЫХ
	 * выполняется после основной загрузки данных из БД
	 * и только для существующих объектов
	 * @param array &$data - данные полученные основным запросом
	 * @return void
	 */
	protected function afterLoad(&$data){}
	
	/** ПОЛУЧИТЬ ЗНАЧЕНИЕ ПОЛЯ */
	public function getField($key){
		
		if(array_key_exists($key, $this->dbFieldValues))
			return $this->dbFieldValues[$key];
		
		if($this->isExistsObj)
			trigger_error('Поле "'.$key.'" не определено в таблице "'.$this->tableName.'"', E_USER_ERROR);
		else
			trigger_error('Невозможно вызвать метод self::getField() для нового объекта', E_USER_ERROR);
	}
	
	/** ПОЛУЧИТЬ МАССИВ ЗНАЧЕНИЙ ВСЕХ ПОЛЕЙ */
	public function getAllFields(){
		
		if($this->isNewObj)
			trigger_error('Невозможно вызвать метод self::getAllFields() для нового объекта', E_USER_ERROR);
			
		return $this->dbFieldValues;
	}
	
	/** ПОЛУЧИТЬ ЗНАЧЕНИЕ ПОЛЯ, ПОДГОТОВЛЕННОЕ ДЛЯ ОТОБРАЖЕНИЯ */
	public function getFieldPrepared($key){
		
		if(array_key_exists($key, $this->fieldValuesForDisplay))
			return $this->fieldValuesForDisplay[$key];
		
		if($this->isExistsObj)
			trigger_error('Неизвестное поле "'.$key.'"', E_USER_ERROR);
		else
			trigger_error('Невозможно вызвать метод self::getFieldPrepared() для нового объекта', E_USER_ERROR);
	}
	
	/** ПОЛУЧИТЬ МАССИВ ЗНАЧЕНИЙ ВСЕХ ПОЛЕЙ, ПОДГОТОВЛЕННЫХ ДЛЯ ОТОБРАЖЕНИЯ */
	public function getAllFieldsPrepared(){
		
		if($this->isNewObj)
			trigger_error('Невозможно вызвать метод self::getAllFieldsPrepared() для нового объекта', E_USER_ERROR);
			
		return $this->fieldValuesForDisplay;
	}

	/** УСТАНОВИТЬ ЗНАЧЕНИЕ ПОЛЯ */
	public function setField($field, $value){
		
		// выполнить, если значение не было задано, или же изменилось
		if(!array_key_exists($field, $this->dbFieldValues) || $this->dbFieldValues[$field] !== $value){
			$this->dbFieldValues[$field] = $value;
			$this->modifiedFields[$field] = true;
		}
		
		return $this;
	}
	
	/** УСТАНОВИТЬ ЗНАЧЕНИЕ ПОЛЕЙ */
	public function setFields($fields){
	
		foreach($fields as $field => $value){
			$this->dbFieldValues[$field] = $value;
			$this->modifiedFields[$field] = true;
		}
		
		return $this;
	}
	
	/** ПОДГОТОВКА ДАННЫХ К СОХРАНЕНИЮ */
	public function save($data){
		
		$this->preValidation($data);
		
		$this->validation($data);
		if($this->hasError())
			return FALSE;
		
		$this->postValidation($data);
		if($this->hasError())
			return FALSE;
				
		$this->setFields($data);
		$this->_save();
		$this->afterSave($data);
		return $this->id;
	}
	
	/** СОХРАНЕНИЕ ОБЪЕКТА */
	protected function _save(){
	
		$fields = array();
		
		// новый объект
		if($this->isNewObj){

			foreach($this->dbFieldValues as $key => $val)
				$fields[$key] = $val;
			
			if(!count($fields))
				trigger_error('Нечего сохранять', E_USER_ERROR);
				
			$this->id = $this->dbFieldValues[$this->pkField] = $this->dbInsert($fields);
			$this->modifiedFields = array();
			$this->isExistsObj = TRUE;
			$this->isNewObj = FALSE;
		}
		// существующий объект
		else{
			
			foreach($this->modifiedFields as $k => $v)
				$fields[$k] = $this->dbFieldValues[$k];
			
			if(!count($fields))
				return;
				
			$this->dbUpdate($fields);
			$this->modifiedFields = array();
		}
	}
	
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){}
	
	/**
	 * ВАЛИДАЦИЯ ДАННЫХ
	 * Производит некоторые валидационные преобразования.
	 * Если данные не прошли валидацию, сохраняет ошибки в стандартный контейнер
	 * @param &$data - массив данных для валидации
	 * @return void
	 */
	public function validation(&$data){
		
		$validator = $this->getValidator();
		$data = $validator->validate($data);
		
		if($validator->hasError())
			$this->setError($validator->getError());
	}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){}
	
	/** ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ */
	public function beforeDisplay($data){
		
		return $data;
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){}
	
	/** УДАЛЕНИЕ ОБЪЕКТА */
	public function destroy(){

		if($this->isNewObj)
			trigger_error('Удалить вновьсозданный объект невозможно', E_USER_ERROR);
			
		$this->beforeDestroy();
		$this->dbDelete();
		return TRUE;
	}
	
	public function setError($error){
		$this->_errors[] = $error;
	}
	
	public function getError(){
		return implode('<br />', $this->_errors);
	}
	
	public function hasError(){
		return count($this->_errors);
	}
	
	
	#### ОПЕРАЦИИ С БАЗОЙ ДАННЫХ ####
	
	public function dbGetRow(){
		return db::get()->getRow("SELECT * FROM ".$this->tableName." WHERE ".$this->pkField."='".$this->id."'");
	}
	
	public function dbInsert($fields){
		return db::get()->insert($this->tableName, $fields);
	}
	
	public function dbUpdate($fields){
		return db::get()->update($this->tableName, $fields, $this->pkField."='".$this->id."'");
	}
	
	public function dbDelete(){
		return db::get()->delete($this->tableName, $this->pkField."='".$this->id."'");
	}
	
}


class GenericObjectCollection{
	
	protected $_pagination = '';
	protected $_linkTags = array();
	protected $_sortableFieldsTitles = array();
	protected $_sortableLinks = array();
	
	
	// ПОЛУЧИТЬ SORTABLE LINKS
	public function getSortableLinks(){
	
		return $this->_sortableLinks;
	}
	
	// ПОЛУЧИТЬ КНОПКИ ПЕРЕКЛЮЧЕНИЯ СТРАНИЦ
	public function getPagination(){
	
		return $this->_pagination;
	}
	
	// ПОЛУЧИТЬ LINK ТЭГИ
	public function getLinkTags(){
	
		return $this->_linkTags;
	}
	
}

?>