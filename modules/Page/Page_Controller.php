<?

class Page_Controller extends Controller{
	
	const DEFAULT_VIEW = 'main';
	
	const MODULE = 'Page';
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/Page/templates/';
	
	// методы, отображаемые по умолчанию
	protected $_defaultFrontendDisplay = 'view';
	protected $_defaultBackendDisplay = 'list';
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_view' 			=> PERMS_UNREG,
		
		'admin_display_list'	=> PERMS_ADMIN,
		'admin_display_new'		=> PERMS_ADMIN,
		'admin_display_edit'	=> PERMS_ADMIN,
		'admin_display_copy'	=> PERMS_ADMIN,
		'admin_display_delete'	=> PERMS_ADMIN,

		'action_publish'		=> PERMS_ADMIN,
		'action_unpublish'		=> PERMS_ADMIN,
		'action_save'			=> PERMS_ADMIN,
		'action_delete' 		=> PERMS_ADMIN,
	);
	
	protected $_title = 'Страницы';

	
	// ВЫПОЛНЕНИЕ ОТОБРАЖЕНИЯ
	public function performDisplay($method, $params){
		
		if($this->_adminMode){
		
			parent::performDisplay($method, $params);
			
		}else{
		
			// вместо метода передается идентификатор страницы
			if(strlen($method))
				array_unshift($params, $method);
			
			// а метод всегда только view
			$method = 'view';
			
			parent::performDisplay($method, $params);
		}
		
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		$pageAlias = getVar($params[0], self::DEFAULT_VIEW);
		$topMenuActiveItem = '';
		
		switch($pageAlias){
			case 'main': $topMenuActiveItem = 'main'; break;
			case 'contacts': $topMenuActiveItem = 'contacts'; break;
		}
		
		$variables = Page_Model::LoadByAlias($pageAlias)->GetAllFieldsPrepared();
		FrontendViewer::get()
			->setTitle($variables['title'])
			->setTopMenuActiveItem($topMenuActiveItem)
			->setContentSmarty(self::TPL_PATH.'view.tpl', $variables)
			->render();
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	/** DISPLAY LIST (ADMIN) */
	public function admin_display_list($params = array()){
		
		$collection = new PageCollection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendViewer::get()
			->prependTitle('Список страниц')
			->setLinkTags($collection->getLinkTags())
			->setContentSmarty(self::TPL_PATH.'admin_list.tpl', $variables);
	}
	
	// DISPLAY NEW (ADMIN)
	public function admin_display_new($params = array()){
		
		$pageTitle = 'Создание новой страницы';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => Page_Model::Create()->getValidator()->getJsRules(),
			'redirect'   => getVar($_POST['redirect']),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
	}
	
	// DISPLAY EDIT (ADMIN)
	public function admin_display_edit($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование страницы</span> '.$instance->getField('title');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle('Редактирование страницы')
			->setBreadcrumbs('add', array(null, 'Редактирование страницы'))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
		
	}
	
	/** DISPLAY COPY (ADMIN) */
	public function admin_display_copy($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);
		
		$pageTitle = 'Копирование страницы';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendViewer::get()
			->prependTitle($pageTitle)
			->setBreadcrumbs('add', array(null, $pageTitle))
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables);
		
	}
	
	// DISPLAY DELETE (ADMIN)
	public function admin_display_delete($params = array()){
		
		$instanceId = getVar($params[0], 0 ,'int');
		$instance = Page_Model::Load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		BackendViewer::get()
			->prependTitle('Удаление записи')
			->setBreadcrumbs('add', array(null, 'Удаление страницы'))
			->setContentSmarty(self::TPL_PATH.'delete.tpl', $variables);
		
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE (ADMIN) */
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
	
	/** ACTION PUBLISH (ADMIN) */
	public function action_publish($params = array()){
		
		$instance = Page_Model::Load(getVar($_POST['id'], 0, 'int'));
		$instance->publish();
		Messenger::get()->addSuccess('Страница "'.$instance->getField('title').'" опубликована');
		return TRUE;
	}
	
	/** ACTION UNPUBLISH (ADMIN) */
	public function action_unpublish($params = array()){
		
		$instance = Page_Model::Load(getVar($_POST['id'], 0, 'int'));
		$instance->unpublish();
		Messenger::get()->addSuccess('Страница "'.$instance->getField('title').'" скрыта');
		return TRUE;
	}
	
	/** ACTION DELETE (ADMIN) */
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
}

?>