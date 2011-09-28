<?

class User_ProfileController extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'profile';
	protected $_defaultBackendDisplay = 'content';
	
	public $methodResources = array(
		'action_login' => 'public',
	);
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
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
}