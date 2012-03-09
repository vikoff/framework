<?

class User_ProfileController extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/User/templates/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'view';
	
	public $methodResources = array(
		'display_view'            => 'view',
		'display_registration'    => 'public',
		'display_forget_password' => 'public',
		'display_edit'            => 'own-edit',
		'display_greeting'        => 'own-view',
		
		'action_login'            => 'public',
		'action_logout'           => 'public',
		'action_register'         => 'public',
		'action_forget_password'  => 'public',
		'action_change_password'  => 'own-edit',
		'action_edit'             => 'own-edit',
	);
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return User_Acl::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	public function display_view($params = array()) {
		
		echo 'hello';
	}
	
	/** DISPLAY REGISTRATION */
	public function display_registration() {
		
		$variables = $_POST;
		
		if (CurUser::get()->isLogged())
			App::redirect('');
			
		FrontendLayout::get()
			->setTitle('Регистрация')
			->setContentPhpFile(self::TPL_PATH.'registration.php', $variables)
			->render();
	}
	
	/** DISPLAY FORGET PASSWORD */
	public function display_forget_password() {
		
		if (CurUser::get()->isLogged())
			App::redirect('');
			
		FrontendLayout::get()
			->setTitle('Напомнить пароль')
			->setContentPhpFile(self::TPL_PATH.'forget_password.php')
			->render();
	}
	
	/** DISPLAY GREETING */
	public function display_greeting() {
	
		$variables = array();
		
		FrontendLayout::get()
			->setTitle('Добро пожаловать на сайт!')
			->setContentPhpFile(self::TPL_PATH.'greeting.php', $variables)
			->render();
	}
	
	/** DISPLAY EDIT */
	public function display_edit(){
			
		$user = CurUser::get();
		
		$data = $user->GetAllFieldsPrepared();
		$variables = array_merge($data, array(
			'birth' => YDate::loadDbDate(getVar($data['birthdate'])),
			'passwordMessage' => Messenger::get()->ns('password-change')->getAll(),
		));
		
		FrontendLayout::get()
			->setTitle('Редактирование личных данных')
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION REGISTER */
	public function action_register(){
		
		$user = CurUser::get();
		
		if($user->save($_POST, User_Model::SAVE_REGISTER)){
			$user->login($user->{CurUser::LOGIN_FIELD}, $_POST['password']);
			App::redirect('user/profile/greeting');
			return TRUE;
		}else{
			Messenger::get()->addError('При регистрации возникли ошибки:', $user->getError());
			return FALSE;
		}
	}
	
	/** ACTION LOGIN */
	public function action_login(){
		
		try{
			CurUser::get()->login(getVar($_POST['login']), getVar($_POST['pass']), getVar($_POST['remember']));
			App::reload();
			return TRUE;
		}catch(Exception $e){
			Messenger::get()->ns('login')->addError($e->getMessage());
			return FALSE;
		}
	}
	
	/** ACTION LOGOUT */
	public function action_logout(){
		
		CurUser::get()->logout();
		App::reload();
	}
	
	public function action_forget_password(){
		
		$email = getVar($_POST['email']);
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			Messenger::get()->addError('Неверный email адрес');
			return FALSE;
		}
		
		Messenger::get()->addSuccess('Новый пароль отправлен вам на email (пока в разработке)');
		return TRUE;
	}
	
	public static function action_edit(){
	
		$user = CurUser::get();
		// echo '<pre>'; print_r($_POST); die;
		if($user->save($_POST, User_Model::SAVE_EDIT)){
			Messenger::get()->addSuccess('Личные данные сохранены');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить данные:', $user->getError());
			return FALSE;
		}
	}

	public function action_change_password(){
		
		$user = CurUser::get();
		
		$oldPassword = getVar($_POST['old-password']);
		$newPassword = getVar($_POST['new-password']);
		$newPasswordConfirm = getVar($_POST['new-password-confirm']);
		$messenger = Messenger::get()->ns('password-change');
		
		if(!strlen($oldPassword) || !strlen($newPassword) || !strlen($newPasswordConfirm)){
			$messenger->addError('Заполните все поля');
			return FALSE;
		}
		
		$messenger = Messenger::get()->ns('password-change');
		
		if($user->setNewPassword($oldPassword, $newPassword, $newPasswordConfirm)){
			$messenger->addSuccess('Пароль обновлен.');
			return TRUE;
		}else{
			$messenger->addError('Не удалось обновить пароль:', $user->getError());
			return FALSE;
		}
	}
	
}