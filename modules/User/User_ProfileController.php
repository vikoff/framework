<?

class User_ProfileController extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'User/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'view';
	
	public $methodResources = array(
		'display_view' => 'view',
		'display_registration' => 'public',
		'action_login' => 'public',
		'action_logout' => 'public',
	);
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	public function display_view($params = array()){
		
		echo 'hello';
	}
	
	public function display_registration($params = array()){
		
		$variables = $_POST;
		
		FrontendLayout::get()
			->setTitle('Регистрация')
			->setContentPhpFile(self::TPL_PATH.'registration.php', $variables)
			->render();
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
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
	public function action_logout($params = array()){
		
		CurUser::get()->logout();
		App::reload();
	}
	
}