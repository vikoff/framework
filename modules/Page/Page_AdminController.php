<?

class Page_AdminController extends Controller{
	
	const MODULE = 'page';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Page/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		
		'display_list'       => PERMS_ADMIN,
		'display_new'        => PERMS_ADMIN,
		'display_edit'       => PERMS_ADMIN,
		'display_copy'       => PERMS_ADMIN,
		'display_delete'     => PERMS_ADMIN,

		'action_publish'		=> PERMS_ADMIN,
		'action_unpublish'		=> PERMS_ADMIN,
		'action_save'			=> PERMS_ADMIN,
		'action_delete' 		=> PERMS_ADMIN,
	);
	
	protected $_title = 'Страницы';
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list($params = array()){
		
		$collection = new Page_Collection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendLayout::get()
			->prependTitle('Список страниц')
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables)
			->render();
	}
	
	/** DISPLAY NEW */
	public function display_new($params = array()){
		
		$pageTitle = 'Создание новой страницы';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => Page_Model::Create()->getValidator()->getJsRules(),
			'redirect'   => getVar($_POST['redirect']),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	/** DISPLAY EDIT */
	public function display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование страницы</span> '.$instance->getField('title');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle('Редактирование страницы')
			->setBreadcrumbs('add', array(null, 'Редактирование страницы'))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
		
	}
	
	/** DISPLAY COPY */
	public function display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);
		
		$pageTitle = 'Копирование страницы';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	/** DISPLAY DELETE */
	public function display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array());
		
		BackendLayout::get()
			->prependTitle('Удаление записи')
			->setBreadcrumbs('add', array(null, 'Удаление страницы'))
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables)
			->render();
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE */
	public function action_save($params = array()){
		
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new Page_Model($instanceId);
		
		if($instance->Save($_POST)){
			Messenger::get()->addSuccess('Запись сохранена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION PUBLISH */
	public function action_publish($params = array()){
		
		$instance = Page_Model::Load(getVar($_POST['id'], 0, 'int'));
		$instance->publish();
		Messenger::get()->addSuccess('Страница "'.$instance->getField('title').'" опубликована');
		return TRUE;
	}
	
	/** ACTION UNPUBLISH */
	public function action_unpublish($params = array()){
		
		$instance = Page_Model::Load(getVar($_POST['id'], 0, 'int'));
		$instance->unpublish();
		Messenger::get()->addSuccess('Страница "'.$instance->getField('title').'" скрыта');
		return TRUE;
	}
	
	/** ACTION DELETE */
	public function action_delete($params = array()){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = Page_Model::Load($instanceId);
		
		// установить редирект на admin-list
		$this->setRedirectUrl('admin/content/page/list');
	
		if($instance->Destroy()){
			Messenger::get()->addSuccess('Страница удалена');
			return TRUE;
		}else{
			Messenger::get()->addError('Не удалось удалить страницу:', $instance->getError());
			// выполнить редирект принудительно
			$this->forceRedirect();
			return FALSE;
		}

	}
	
	
	////////////////////
	////// OTHER  //////
	////////////////////
	
	
	public function getClass(){
		return __CLASS__;
	}
	
}

?>