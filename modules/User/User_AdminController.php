<?php

class User_AdminController extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/User/templates/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = 'list';
	
	// права на выполнение методов контроллера
	public $methodResources = array(
		
		'display_list'		=> 'edit',
		'display_view'		=> 'edit',
		'display_create'	=> 'edit',
		'display_edit'		=> 'edit',
		'display_delete'	=> 'edit',

		'action_save_perms' => 'edit',
		'action_delete' 	=> 'edit',
		'action_create' 	=> 'edit',
		'action_edit' 		=> 'edit',
		
		'ajax_generate_password' => 'public',
		'ajax_check_login_unique' => 'public',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	public function getClass(){
		return __CLASS__;
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY LIST
	public function display_list($params = array()){
		
		$collection = new User_Collection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendLayout::get()
			->prependTitle('Список пользователей')
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables)
			->render();
	}
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = User::Load($instanceId);
			
			$userPerms = $instance->getField('level');
			$perms = array('allowEdit' => FALSE, 'list' => '', 'curTitle' => User::getPermName($userPerms));
			if(USER_AUTH_PERMS >= $userPerms){
				$perms['allowEdit'] = TRUE;
				foreach(User::getPermsList() as $perm)
					if($perm > 0 && $perm <= USER_AUTH_PERMS)
						$perms['list'] .= '<option value="'.$perm.'" '.($perm == $userPerms ? 'selected="selected" style="color: blue;"' : '').'>'.User::getPermName($perm).'</option>';
			}
			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
				'perms' => $perms,
			));
			
			BackendLayout::get()
				->prependTitle('Данные пользователя')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables);
		}
		catch(Exception $e){
			BackendLayout::get()->error404($e->getMessage());
		}
		
	}
	
	// DISPLAY CREATE
	public function display_create($params = array()){
			
		$user = User_Model::create();
			
		$variables = array_merge(Tools::unescape($_POST), array(
			'action' => 'registration',
			'jsRules' => $user->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->setTitle('Создание нового пользователя')
			->setContentPhpFile(self::TPL_PATH.'admin_create.php', $variables)
			->render();
	}
	
	// DISPLAY EDIT
	public function display_edit($params = array()){
			
		$instanceId = getVar($params[0], 0 ,'int');
		$user = User::Load($instanceId);
		
		if($user->getField('level') > USER_AUTH_PERMS)
			throw new Exception('Недостаточно прав для редактирования данных пользователя '.$user->getName());
			
		$levels = array();
		foreach(User::getPermsList() as $g)
			if($g > 0 && $g <= USER_AUTH_PERMS)
				$levels[$g] = User::getPermName($g);
			
		$variables = array_merge($user->GetAllFieldsPrepared(), array(
			'action' => 'edit',
			'jsRules' => $user->getValidator()->getJsRules(),
			'levels' => $levels,
		));
		
		BackendLayout::get()
			->setTitle('Редактирование данных пользователя')
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
	}
	
	// DISPLAY DELETE
	public function display_delete($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = User::Load($instanceId);

			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
			));
			
			BackendLayout::get()
				->prependTitle('Удаление пользователя #'.$instanceId)
				->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		}
		catch(Exception $e){
			BackendLayout::get()->error404($e->getMessage());
		}
		
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION SAVE PERMS
	public function action_save_perms($params = array()){
		
		$instanceId = getVar($_POST['instance-id'], 0, 'int');
		
		try{
			$instance = User::Load($instanceId);
		
			if($instance->setPerms(getVar($_POST['level'], 0, 'int'))){
				Messenger::get()->addSuccess('Пользователь получил новые права');
				return TRUE;
			}else{
				Messenger::get()->addError('Не удалось установить новые права для пользователя:', $instance->getError());
				return FALSE;
			}
		}
		catch(Exception $e){
			BackendLayout::get()->error404();
		}

	}
	
	// ACTION DELETE
	public function action_delete($params = array()){
		
		$instanceId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		
		try{
			$instance = User::Load($instanceId);
		
			$this->setRedirectUrl(App::href('admin/users/list'));
		
			if($instance->Destroy()){
				Messenger::get()->addSuccess('Пользователь удален');
				return TRUE;
			}else{
				Messenger::get()->addError('Не удалось удалить пользователя.');
				$this->forceRedirect();
				return FALSE;
			}
		}
		catch(Exception $e){
			BackendLayout::get()->error404();
		}

	}
	
	// ACTION ADMIN CREATE
	public function action_create($params = array()){
		
		$user = new User(0, TRUE);
		
		if($user->Save($_POST)){
			Messenger::get()->addSuccess('Новый пользователь успешно создан');
			return TRUE;
		}else{
			Messenger::get()->addError('При регистрации нового пользователя возникли ошибки:', $user->getError());
			return FALSE;
		}
	}
	
	// ACTION ADMIN SAVE
	public function action_edit($params = array()){
		
		// echo '<pre>'; print_r($_POST); die;
		try{
			$user = User::load(getVar($_POST['id']), TRUE);
			
			if($user->Save($_POST)){
				Messenger::get()->addSuccess('Данные пользователя сохранены');
				return TRUE;
			}else{
				Messenger::get()->addError('При сохранении данных пользователя возникли ошибки:', $user->getError());
				return FALSE;
			}
		}
		catch(Exception $e){BackendLayout::get()->error404($e->getMessage());}
	}
	
	//////////////////
	////// AJAX //////
	//////////////////
	
	// AJAX GENERATE PASSWORD
	public function ajax_generate_password($params = array()){
		
		$len = rand(8, 12);
		echo substr(md5(md5(microtime().mt_rand())), 2, $len);
	}
	
	// ПРОВЕРКА ЛОГИНА НА УНИКАЛЬНОСТЬ
	public function ajax_check_login_unique($params = array()){
	
		echo User::isLoginInUse(getVar($_GET['login'])) ? 'false' : 'true';
	}
	
}

?>