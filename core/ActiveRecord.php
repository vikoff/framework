<?php

class ActiveRecord {
	
	const INIT_NEW = 1;
	const INIT_EXISTS = 2;
	const INIT_EXISTS_FORCE = 3;
	const INIT_ANY = 4;

	public $id = null;
	public $pkField = 'id';
	public $tableName = null;
	
	public $isNewObj = null;
	public $isExistsObj = null;
	public $isNewlyCreated = FALSE;
	
	protected $_dbFieldValues = array();
	protected $_modifiedFields = array();
	protected $_fieldValuesForDisplay = array();
	protected $_hasPreparedFieldsValues = FALSE;
	
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
	
	/** ЗАГРУЗКА ДАННЫХ ИЗ БД */
	protected function _loadData(){
		
		$this->_dbFieldValues = $this->dbGetRow();
		
		if(!is_array($this->_dbFieldValues) || !count($this->_dbFieldValues))
			throw new Exception404($this->getConst('NOT_FOUND_MESSAGE'));
			
		$this->_afterLoad($this->_dbFieldValues);
		$this->_fieldValuesForDisplay = $this->beforeDisplay($this->_dbFieldValues);
		$this->_hasPreparedFieldsValues = TRUE;
	}
	
	/**
	 * ПРИНУДИТЕЛЬНАЯ ЗАГРУЗКА
	 *
	 * Загружает внешние данные в объект, заставляя его думать, что данные получены из БД.
	 * То есть если будет вызван метод save, то никаких изменений в БД не попадет.
	 * @param array $data - массив данных для загрузки
	 * @return void
	 */
	protected function _forceLoadData($data) {
		
		$this->_dbFieldValues = $data;
		
		if(!$this->id || !is_array($this->_dbFieldValues) || !count($this->_dbFieldValues)){
			var_dump($this->_dbFieldValues);
			throw new Exception404($this->getConst('NOT_FOUND_MESSAGE'));
		}
			
		$this->_afterForceLoad($this->_dbFieldValues);
		$this->_fieldValuesForDisplay = $this->beforeDisplay($this->_dbFieldValues);
		$this->_hasPreparedFieldsValues = TRUE;
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
	protected function _afterLoad(&$data){}
	
	/**
	 * ДОЗАГРУЗКА ДАННЫХ ПОСЛЕ ПРИНУДИТЕЛЬНОЙ ЗАГРУЗКИ
	 * выполняется после основной загрузки данных
	 * и только для существующих объектов
	 * @param array &$data - данные, уже переданные в объект
	 * @return void
	 */
	protected function _afterForceLoad(&$data){}
	
	/** ПОЛУЧИТЬ ЗНАЧЕНИЕ ПОЛЯ */
	public function getField($key){
		
		if(array_key_exists($key, $this->_dbFieldValues))
			return $this->_dbFieldValues[$key];
		
		if($this->isExistsObj)
			trigger_error('Поле "'.$key.'" не определено в таблице "'.$this->tableName.'"', E_USER_ERROR);
		else
			trigger_error('Невозможно вызвать метод self::getField() для нового объекта', E_USER_ERROR);
	}
	
	/** ПОЛУЧИТЬ МАССИВ ЗНАЧЕНИЙ ВСЕХ ПОЛЕЙ */
	public function getAllFields(){
		
		if($this->isNewObj)
			trigger_error('Невозможно вызвать метод self::getAllFields() для нового объекта', E_USER_ERROR);
			
		return $this->_dbFieldValues;
	}
	
	/** ПОЛУЧИТЬ ЗНАЧЕНИЕ ПОЛЯ, ПОДГОТОВЛЕННОЕ ДЛЯ ОТОБРАЖЕНИЯ */
	public function getFieldPrepared($key){
		
		if(!$this->_hasPreparedFieldsValues){
			$this->_fieldValuesForDisplay = $this->beforeDisplay($this->_dbFieldValues);
			$this->_hasPreparedFieldsValues = TRUE;
		}
			
		if(array_key_exists($key, $this->_fieldValuesForDisplay))
			return $this->_fieldValuesForDisplay[$key];
		
		if($this->isExistsObj)
			trigger_error('Неизвестное поле "'.$key.'"', E_USER_ERROR);
		else
			trigger_error('Невозможно вызвать метод self::getFieldPrepared() для нового объекта', E_USER_ERROR);
	}
	
	/** ПОЛУЧИТЬ МАССИВ ЗНАЧЕНИЙ ВСЕХ ПОЛЕЙ, ПОДГОТОВЛЕННЫХ ДЛЯ ОТОБРАЖЕНИЯ */
	public function getAllFieldsPrepared(){
		
		if($this->isNewObj)
			trigger_error('Невозможно вызвать метод self::getAllFieldsPrepared() для нового объекта', E_USER_ERROR);
		
		if(!$this->_hasPreparedFieldsValues){
			$this->_fieldValuesForDisplay = $this->beforeDisplay($this->_dbFieldValues);
			$this->_hasPreparedFieldsValues = TRUE;
		}
			
		return $this->_fieldValuesForDisplay;
	}

	/** УСТАНОВИТЬ ЗНАЧЕНИЕ ПОЛЯ */
	public function setField($field, $value){
		
		// выполнить, если значение не было задано, или же изменилось
		if(!array_key_exists($field, $this->_dbFieldValues) || $this->_dbFieldValues[$field] !== $value){
			$this->_dbFieldValues[$field] = $value;
			$this->_modifiedFields[$field] = true;
			$this->_hasPreparedFieldsValues = FALSE;
		}
		
		return $this;
	}
	
	/** УСТАНОВИТЬ ЗНАЧЕНИЕ ПОЛЕЙ */
	public function setFields($fields){
	
		foreach($fields as $field => $value){
			$this->_dbFieldValues[$field] = $value;
			$this->_modifiedFields[$field] = true;
		}
		$this->_hasPreparedFieldsValues = FALSE;
		
		return $this;
	}
	
	/** ПОДГОТОВКА ДАННЫХ К СОХРАНЕНИЮ */
	public function save($data){
		
		if($this->preValidation($data) === FALSE)
			return FALSE;
		
		$this->validation($data);
		if($this->hasError())
			return FALSE;
		
		if($this->postValidation($data) === FALSE)
			return FALSE;
			
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

			foreach($this->_dbFieldValues as $key => $val)
				$fields[$key] = $val;
			
			if(!count($fields))
				trigger_error('Нечего сохранять', E_USER_ERROR);
				
			$this->id = $this->_dbFieldValues[$this->pkField] = $this->dbInsert($fields);
			$this->_modifiedFields = array();
			$this->isExistsObj = TRUE;
			$this->isNewObj = FALSE;
			$this->isNewlyCreated = TRUE;
		}
		// существующий объект
		else{
			
			foreach($this->_modifiedFields as $k => $v)
				$fields[$k] = $this->_dbFieldValues[$k];
			
			if(!count($fields))
				return;
				
			$this->dbUpdate($fields);
			$this->_modifiedFields = array();
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
	
	/** ПОЛУЧИТЬ КОНСТАНТУ ИЗ КЛАССА-ПОТОМКА */
	public function getConst($name){
		
		return constant($this->getClass().'::'.$name);
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


class ARCollection{
	
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