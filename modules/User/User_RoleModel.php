<?php

class User_RoleModel extends ActiveRecord {
	
	/** имя модуля */
	const MODULE = 'user';
	
	/** таблица БД */
	const TABLE = 'user_roles';
	
	/** флаг, выделяющий роль для гостей */
	const FLAG_GUEST = 1;
	/** флаг, выделяющий роль для зарегистрировавшихся пользователей */
	const FLAG_REG = 2;
	
	/** типы сохранения */
	const SAVE_CREATE   = 'create';
	const SAVE_EDIT     = 'edit';
	
	const NOT_FOUND_MESSAGE = 'Страница не найдена';

	protected $_serialization = array('data' => array('description'));
	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new User_RoleModel(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new User_RoleModel($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new User_RoleModel($id, self::INIT_EXISTS_FORCE, $fieldvalues);
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
		
		$data['flag_str'] = ' - ';
		switch ($data['flag']) {
			case self::FLAG_GUEST: $data['flag_str'] = 'роль для гостей'; break;
			case self::FLAG_REG: $data['flag_str'] = 'роль для зарегистрировавшихся'; break;
		}
		return $data;
	}
	
	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator($mode = self::SAVE_CREATE){
		
		$rules = array(
			'title' => array('required' => TRUE, 'strip_tags' => TRUE, 'length' => array('max' => 255)),
			'level' => array('settype' => 'int'),
			'flag' => array('settype' => 'int'),
			'description' => array('strip_tags' => TRUE, 'length' => array('max' => 65535)),
		);
			
		$validator = new Validator($rules);
		
		$validator->setFieldTitles(array(
			'id' => 'id',
			'title' => 'Заголовок',
			'level' => 'Уровень',
			'flag' => 'Флаг',
			'description' => 'Описание',
		));
		
		return $validator;
	}
		
	/** ПРЕ-ВАЛИДАЦИЯ ДАННЫХ */
	public function preValidation(&$data){
		
		if ($this->isNewObj)
			$this->additData['copy_role'] = getVar($data['copy_role'], 0, 'int');
	}
	
	/** ПОСТ-ВАЛИДАЦИЯ ДАННЫХ */
	public function postValidation(&$data){
		
		// проверка level
		if ($data['level'] < 0 || $data['level'] > 49){
			$this->setError('Уровень должен быть числом от 1 до 49');
			return FALSE;
		}
		
		// если флаг указан, очистим его у других записей
		if (!empty($data['flag']))
			db::get()->update(self::TABLE, array('flag' => 0), 'flag='.$data['flag']);
	}
	
	/** ДЕЙСТВИЕ ПОСЛЕ СОХРАНЕНИЯ */
	public function afterSave($data){
		
		// скопировать права доступа указанной роли
		if ($this->isNewlyCreated && $this->additData['copy_role']){
			try{
				User_Acl::get()->copyRules($this->additData['copy_role'], $this->id);
				$role = User_RoleModel::load($this->additData['copy_role'])->title;
				Messenger::get()->addInfo('Правила доступа скопированы с роли <b>'.$role.'</b>');
			} catch (Exception $e){
				Messenger::get()->addError('роль #'.$this->additData['copy_role'].' не найдена');
			}
		}
	}
	
	/** ПОДГОТОВКА К УДАЛЕНИЮ ОБЪЕКТА */
	public function beforeDestroy(){
		
		User_Acl::get()->deleteRole($this->id);
	}
	
}

class User_RoleCollection extends ARCollection{
	
	/**
	 * поля, по которым возможна сортировка коллекции
	 * каждый ключ должен быть корректным выражением для SQL ORDER BY
	 * var array $_sortableFieldsTitles
	 */
	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'title' => 'Роль',
		'level' => 'Уровень',
		'flag' => 'Флаг',
	);
	
	public $roles = array();
	
	private static $_instance = null;
	
	
	/** ТОЧКА ВХОДА В КЛАСС */
	public static function load(){
		
		if (is_null(self::$_instance))
			self::$_instance = new User_RoleCollection();
			
		return self::$_instance;
	}
	
	public function __construct(){
		$this->roles = $this->_getAll();
	}

	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('level', 'ASC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.User_RoleModel::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = User_RoleModel::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
	private function _getAll(){
		
		$data = db::get()->getAllIndexed('SELECT * FROM '.User_RoleModel::TABLE.' ORDER BY level', 'id', array());
		
		foreach($data as &$row)
			$row = User_RoleModel::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		return $data;
	}
	
	/** ПОЛУЧИТЬ СПИСОК ВСЕХ ЭЛЕМЕНТОВ */
	public function getAll(){
		
		return $this->roles;
	}
	
	public function getTitle($roleId){
		
		return isset($this->roles[$roleId])
			? $this->roles[$roleId]['title']
			: '';
	}
	
	public function getRole($id, $key = null) {
		
		// ROOT
		if ($id == -1)
			$role = array('id' => -1, 'title' => 'ROOT', 'level' => 50);
		else
			$role = isset($this->roles[$id])
				? $this->roles[$id]
				: array('id' => 0, 'title' => 'ROLE NOT FOUND', 'level' => 0);
		
		return $key ? $role[$key] : $role;
	}
	
	public function getGuestRole($key = null) {
		
		$role = array('id' => 0, 'title' => 'ROLE NOT FOUND', 'level' => 0);
		foreach ($this->roles as $r) {
			if ($r['flag'] == User_RoleModel::FLAG_GUEST) {
				$role = $r;
				break;
			}
		}
		
		return $key ? $role[$key] : $role;
	}
	
	public function getRegRole($key = null) {
		
		$role = array('id' => 0, 'title' => 'ROLE NOT FOUND', 'level' => 0);
		foreach ($this->roles as $r) {
			if ($r['flag'] == User_RoleModel::FLAG_REG) {
				$role = $r;
				break;
			}
		}
		
		return $key ? $role[$key] : $role;
	}
	
	/** ПОЛУЧИТЬ СПИСОК РОЛЕЙ ВИДА array(id => title) */
	public function getList(){
		
		return db::get()->getColIndexed('SELECT id, title FROM '.User_RoleModel::TABLE.' ORDER BY level', 'id', array());
	}
}

?>