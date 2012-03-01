<?php

class Alias_Model extends ActiveRecord {
	
	/** имя модуля */
	const MODULE = 'alias';
	
	/** таблица БД */
	const TABLE = 'aliases';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new Alias_Model(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new Alias_Model($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new Alias_Model($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	/** ПОЛУЧИТЬ ИМЯ КЛАССА */
	public function getClass(){
		return __CLASS__;
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
	
	/** ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ */
	public function beforeDisplay($data){
	
		// $data['modif_date'] = YDate::loadTimestamp($data['modif_date'])->getStrDateShortTime();
		// $data['create_date'] = YDate::loadTimestamp($data['create_date'])->getStrDateShortTime();
		return $data;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator(){
		
		// инициализация экземпляра валидатора
		$validator = new Validator(array(
				'alias' => array('strip_tags' => TRUE, 'length' => array('max' => 255)),
				'path' => array('strip_tags' => TRUE, 'length' => array('max' => 255)),
			),
			array('required' => array('alias', 'path'))
		);
		
		$validator->setFieldTitles(array(
			'id' => 'id',
			'alias' => 'Псевдоним',
			'path' => 'Путь',
		));
		
		return $validator;
	}
		
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		// проверка псевдонима на совпадение с именем модуля
		if(App::get()->issetModule($data['alias'])){
			$this->setError('Псевдоним совпадает с именем подуля');
			return FALSE;
		}
		
		// проверка псевдонима на уникальность
		if(!$this->_isUnique($data['alias'])){
			$this->setError('Другая запись с таким псевдонимом уже существует');
			return FALSE;
		}
	}
	
	/** ПРОВЕРКА ПСЕВДОНИМА НА УНИКАЛЬНОСТЬ */
	private function _isUnique($alias){
		
		$db = db::get();
		$condition = ' WHERE alias='.$db->qe($alias);
		$condition .= $this->isNewObj ? '' : 'AND id != '.$this->id;
		return $db->getOne('SELECT COUNT(1) FROM '.self::TABLE.$condition) ? FALSE : TRUE;
	}
	
}

class Alias_Collection extends ARCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'path' => 'Реальный путь',
		'alias' => 'Псевдоним',
		'is_bound' => 'Связан по id',
	);
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load(){
			
		return new Alias_Collection();
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.Alias_Model::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = Alias_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ЭЛЕМЕНТОВ */
	public function getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.Alias_Model::TABLE, 'id', array());
		
		foreach($data as &$row)
			$row = Alias_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}
	
}

?>