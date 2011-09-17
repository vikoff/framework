<?

class User_ProfileController extends Controller{
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'profile';
	protected $_defaultBackendDisplay = 'content';
	
	public $permissions = array(
		'action_login' => PERMS_UNREG,
	);
	
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