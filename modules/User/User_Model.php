<?

class User_Model extends ActiveRecord{
	
	const VALIDATION_ADMIN_CREATE = 'adm-create';
	const VALIDATION_ADMIN_EDIT   = 'adm-edit';
	const VALIDATION_REGISTER     = 'reg';
	
	// пол
	const GENDER_FEMALE 	= 'f';
	const GENDER_MALE		= 'm';
	
	const TABLE = 'users';
	
	const NOT_FOUND_MESSAGE = 'Пользователь не найден';

	
	/** ТОЧКА ВХОДА В КЛАСС (СОЗДАНИЕ НОВОГО ОБЪЕКТА) */
	public static function create(){
			
		return new User_Model(0, self::INIT_NEW);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function load($id){
		
		return new User_Model($id, self::INIT_EXISTS);
	}
	
	/** ТОЧКА ВХОДА В КЛАСС (ЗАГРУЗКА СУЩЕСТВУЮЩЕГО ОБЪЕКТА) */
	public static function forceLoad($id, $fieldvalues){
		
		return new User_Model($id, self::INIT_EXISTS_FORCE, $fieldvalues);
	}
	
	/** СЛУЖЕБНЫЙ МЕТОД (получение констант из родителя) */
	public function getConst($name){
		return constant(__CLASS__.'::'.$name);
	}
	
	/** ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ */
	public function beforeDisplay($data){
		
		$data['fio'] = $this->getName();
		$data['role_str'] = '';
		$data['regdate'] = YDate::loadTimestamp(getVar($data['regdate']))->getStrDateShortTime();
		
		return $data;
	}

	/** ПОЛУЧИТЬ ЭКЗЕМПЛЯР ВАЛИДАТОРА */
	public function getValidator($mode = self::VALIDATION_REGISTER){
		
		$rules = array(
			'login' 	 => array('required' => TRUE, 'length' => array('min' => '2', 'max' => '255')),
			'email' 	 => array('required' => TRUE, 'length' => array('max' => '100'), 'email' => true),
			'password' 	 => array('required' => TRUE, 'length' => array('min' => '5', 'max' => '100'), 'hash' => 'sha1'),
			'password_confirm'	=> array('compare' => 'password', 'hash' => 'sha1', 'unsetAfter' => TRUE),
			'surname' 	 => array('required' => TRUE, 'length' => array('max' => '255')),
			'name' 		 => array('required' => TRUE, 'length' => array('max' => '255')),
			'role_id' 	 => array('required' => TRUE, 'settype' => 'int'),
			'captcha' 	 => array('captcha' => isset($_SESSION['captcha']) ? $_SESSION['captcha'] : ''),
		);
		
		$fields = array();
		switch($mode) {
			
			case self::VALIDATION_ADMIN_CREATE:
				$fields = array('login', 'password', 'password_confirm', 'email', 'surname', 'name', 'role_id');
				break;
			
			case self::VALIDATION_ADMIN_EDIT:
				$fields = array('email', 'surname', 'name', 'role_id');
				break;
				
			case self::VALIDATION_REGISTER:
				$fields = array();
				break;
		}
		
		$fieldsRules = array();
		foreach($fields as $f)
			$fieldsRules[$f] = $rules[$f];
			
		$validator = new Validator($fieldsRules, array('strip_tags' => '*'));
		
		$validator->setFieldTitles(array(
			'login' 			=> 'Логин',
			'email' 			=> 'email-адрес',
			'password' 			=> 'пароль',
			'password_confirm' 	=> 'подтверждение пароля',
			'surname' 			=> 'фамилия',
			'name' 				=> 'имя',
			'role_id' 			=> 'роль',
		));
		
		return $validator;
	}
	
	public function preValidation(&$data){
		
		$data['birthdate'] = !empty($data['birth']) ? YDate::loadArray($data['birth'])->getDbDate() : '0000-00-00';
	}
	
	public function postValidation(&$data){
		
		if($this->isNewObj){
			
			if(!empty($data['email']) && self::isEmailInUse($data['email'])){
				$this->setError('Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="'.href('profile/forget-password').'">восстановления учетной записи</a>');
				return FALSE;
			}
			
			if (empty($data['role_id']))
				$data['role_id'] = 1;
				
			$data['active'] = '1';
			$data['regdate'] = time();
		}
	}
	
	public function afterSave($data){
		
	}
	
	/** ПОЛУЧИТЬ ИМЯ ПОЛЬЗОВАТЕЛЯ */
	public function getName($name = null){
		
		$outputArr = array();
		
		if(is_null($name))
			$name = 'fio';
		
		for($i = 0; $i < strlen($name); $i++){
			if($name{$i} == 'f')
				$outputArr[] = $this->getField('surname');
			elseif($name{$i} == 'i')
				$outputArr[] = $this->getField('name');
			elseif($name{$i} == 'o')
				$outputArr[] = ''; //$this->getField('patronymic');
			else
				trigger_error('Неизвестный код имени: "'.$name{$i}.'"', E_USER_ERROR);
		}
		$output = trim(implode(' ', $outputArr));
		return strlen($output) ? $output : $this->getField('login');
	}
	
	/** УСТАНОВИТЬ НОВЫЙ ПАРОЛЬ */
	public function setNewPassword($oldPassword, $newPassword, $newPasswordConfirm){
		
		if($newPassword != $newPasswordConfirm){
			$this->setError('Пароль и подтверждение не совпадают');
			return FALSE;
		}
		
		if(sha1($oldPassword) != db::get()->getOne('SELECT password FROM '.self::TABLE.' WHERE id='.$this->getField('id'), '')){
			$this->setError('Вы неверно ввели старый пароль');
			return FALSE;
		}
		
		db::get()->update(self::TABLE, array('password' => sha1($newPassword)), 'id='.$this->getField('id'));
		return TRUE;
	}
	
	/** УСТАНОВИТЬ НОВЫЙ УРОВЕНЬ ПРАВ */
	public function setPerms($newPerms){
	
		if(!in_array($newPerms, self::getPermsList())){
			$this->setError('Неверный идентификатор пользовательских прав "'.$newPerms.'"');
			return FALSE;
		}
		
		if($this->getField('level') > USER_AUTH_PERMS){
			$this->setError('Невозможно изменять права пользователю, с правами выше текущего.');
			return FALSE;
		}
		
		if($newPerms == 0 || $newPerms > USER_AUTH_PERMS){
			$this->setError('Невозможно присвоить пользователю уровень прав "'.self::getPermName($newPerms).'"');
			return FALSE;
		}
		
		$this
			->setField('level', $newPerms)
			->_save();
		
		return TRUE;
	}
	
	/** ЗАНЯТ ЛИ EMAIL */
	static public function isEmailInUse($email){
	
		$db = db::get();
		return $db->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE email='.$db->qe($email));
	}
	
	/** ЗАНЯТ ЛИ ЛОГИН */
	static public function isLoginInUse($login){
		
		$db = db::get();
		return $db->getOne('SELECT COUNT(1) FROM '.self::TABLE.' WHERE login='.$db->qe($login));
	}
	
	/** ПРОВЕРИТЬ, ИМЕЕТ ЛИ ПОЛЬЗОВАТЕЛЬ УКАЗАННЫЕ ПРАВА */
	static public function hasPerm($perm){

		return USER_AUTH_PERMS >= $perm;
	}

	/** ПОЛУЧИТЬ СПИСОК ВОЗМОЖНЫХ ПРАВ ПОЛЬЗОВАТЕЛЕЙ */
	static public function getPermsList(){
		return array(PERMS_UNREG, PERMS_REG, PERMS_MODERATOR, PERMS_ADMIN, PERMS_SUPERADMIN, PERMS_ROOT);
	}
	
	/** ПОЛУЧИТЬ ТЕКСТОВОЕ НАЗВАНИЕ ПРАВ ПОЛЬЗОВАТЕЛЯ */
	static public function getPermName($perm){
		switch($perm){
			case PERMS_UNREG:
				return 'Гость';
			case PERMS_REG:
				return 'Пользователь';
			case PERMS_MODERATOR:
				return 'Модератор';
			case PERMS_ADMIN:
				return 'Администратор';
			case PERMS_SUPERADMIN:
				return 'Суперадминистратор';
			case PERMS_ROOT:
				return 'ROOT';
			default:
				trigger_error('Неверная группа пользователей: '.$perm, E_USER_ERROR);
		}
	}
	
	/** ТЕКСТОВОЕ ЗНАЧЕНИЕ ПАРАМЕТРА "ПОЛ ПОЛЬЗОВАТЕЛЯ" */
	static public function getGenderString($gender){
		if($gender == self::GENDER_FEMALE)
			return 'женщина';
		elseif($gender == self::GENDER_MALE)
			return 'мужчина';
		else
			return 'не указан';
	}

}


class User_Collection extends ARCollection{

	protected $_sortableFieldsTitles = array(
		'id' => 'id',
		'login' => 'Логин',
		'email' => 'email',
		'fio' => array('surname _DIR_, name _DIR_', 'ФИО'),
		'role_str' => array('role_id', 'Роль'),
		'regdate' => 'Дата регистрации',
	);
	
	/** ПОЛУЧИТЬ СПИСОК С ПОСТРАНИЧНОЙ РАЗБИВКОЙ */
	public function getPaginated(){
		
		$sorter = new Sorter('id', 'DESC', $this->_sortableFieldsTitles);
		$paginator = new Paginator('sql', array('*', 'FROM '.User_Model::TABLE.' ORDER BY '.$sorter->getOrderBy()), 50);
		
		$data = db::get()->getAll($paginator->getSql(), array());
		
		foreach($data as &$row)
			$row = User_Model::forceLoad($row['id'], $row)->getAllFieldsPrepared();
		
		$this->_sortableLinks = $sorter->getSortableLinks();
		$this->_pagination = $paginator->getButtons();
		$this->_linkTags = $paginator->getLinkTags();
		
		return $data;
	}
	
}

?>