<?

class User_ProfileController extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/User/templates/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'view';
	
	public $methodResources = array(
		'display_view' => 'view',
		'display_edit' => 'view',
		'display_registration' => 'public',
		'display_greeting' => 'view',
		
		'action_login' => 'public',
		'action_logout' => 'public',
		'action_register' => 'public',
	);
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	public function display_view($params = array()) {
		
		echo 'hello';
	}
	
	public function display_edit() {
		
		echo 'edit';
	}
	
	/** DISPLAY REGISTRATION */
	public function display_registration() {
		
		$variables = $_POST;
		
		FrontendLayout::get()
			->setTitle('Регистрация')
			->setContentPhpFile(self::TPL_PATH.'registration.php', $variables)
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
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION REGISTER */
	public function action_register(){
		
		$user = CurUser::get();
		
		if($user->save(Tools::unescape($_POST), User_Model::SAVE_REGISTER)){
			$user->login($user->getField(CurUser::LOGIN_FIELD), $_POST['password']);
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
	
}