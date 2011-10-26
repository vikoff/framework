<?php

class User_Controller extends Controller{
	
	/** имя модуля */
	const MODULE = 'user';
	
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'User/';
	
	// методы, отображаемые по умолчанию
	protected $_displayIndex = FALSE;
	
	protected $_proxy = array(
		'profile' => 'User_ProfileController',
	);
	
	// права на выполнение методов контроллера
	public $methodResources = array(
		
		'admin_display_list'	=> 'edit',
		'admin_display_view'	=> 'edit',
		'admin_display_create'	=> 'edit',
		'admin_display_edit'	=> 'edit',
		'admin_display_delete'	=> 'edit',

		'action_save_perms' 	=> 'edit',
		'action_delete' 		=> 'edit',
		'action_admin_create' 	=> 'edit',
		'action_admin_edit' 	=> 'edit',
		
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
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	// DISPLAY LIST (ADMIN)
	public function admin_display_list($params = array()){
		
		$collection = new UserCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('Список пользователей')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'admin_list.tpl', $variables);
	}
	
	// DISPLAY VIEW (ADMIN)
	public function admin_display_view($params = array()){
		
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
			
			BackendViewer::get()
				->prependTitle('Данные пользователя')
				->setContentSmarty(self::TPL_PATH.'view.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
		
	}
	
	// DISPLAY CREATE (ADMIN)
	public function admin_display_create($params = array()){
			
		$user = User::create();
		
		$levels = array();
		foreach(User::getPermsList() as $g)
			if($g > 0 && $g <= USER_AUTH_PERMS)
				$levels[$g] = User::getPermName($g);
			
		$variables = array_merge(Tools::unescape($_POST), array(
			'action' => 'registration',
			'jsRules' => $user->getValidator()->getJsRules(),
			'levels' => $levels,
		));
		
		BackendViewer::get()
			->setTitle('Создание нового пользователя')
			->setContentSmarty(self::TPL_PATH.'admin_edit.tpl', $variables);
	}
	
	// DISPLAY EDIT (ADMIN)
	public function admin_display_edit($params = array()){
			
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
		
		BackendViewer::get()
			->setTitle('Редактирование данных пользователя')
			->setContentSmarty(self::TPL_PATH.'admin_edit.tpl', $variables);
	}
	
	// DISPLAY DELETE (ADMIN)
	public function admin_display_delete($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0 ,'int');
			$instance = User::Load($instanceId);

			$variables = array_merge($instance->GetAllFieldsPrepared(), array(
				'instanceId' => $instanceId,
			));
			
			BackendViewer::get()
				->prependTitle('Удаление пользователя #'.$instanceId)
				->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
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
			BackendViewer::get()->error404();
		}

	}
	
	// ACTION DELETE (ADMIN)
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
			BackendViewer::get()->error404();
		}

	}
	
	// ACTION ADMIN CREATE
	public function action_admin_create($params = array()){
		
		$user = new User(0, TRUE);
		
		if($user->save(Tools::unescape($_POST))){
			Messenger::get()->addSuccess('Новый пользователь успешно создан');
			return TRUE;
		}else{
			Messenger::get()->addError('При регистрации нового пользователя возникли ошибки:', $user->getError());
			return FALSE;
		}
	}
	
	// ACTION ADMIN SAVE
	public function action_admin_edit($params = array()){
		
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
		catch(Exception $e){BackendViewer::get()->error404($e->getMessage());}
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
	
		echo User_Model::isLoginInUse(getVar($_GET['login'])) ? 'false' : 'true';
	}
	
}

?>