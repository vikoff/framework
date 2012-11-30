<?php

class TestGroup_AdminController extends Controller {
	
	/** имя модуля */
	const MODULE = 'test-group';
	
	/** элемент, отображаемый во view по умолчанию */
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/TestGroup/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'list';
	
	/** ассоциация методов контроллера с ресурсами */
	public $methodResources = array(
		'display_list'     => 'admin_edit',
		'display_new'      => 'admin_edit',
		'display_edit'     => 'admin_edit',
		'display_copy'     => 'admin_edit',
		'display_delete'   => 'admin_edit',

		'action_save'      => 'admin_edit',
		'action_delete'    => 'admin_edit',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return User_Acl::get()->isResourceAllowed(self::MODULE, $resource);
	}
	
	/** ПОЛУЧИТЬ ИМЯ КЛАССА */
	public function getClass(){
		return __CLASS__;
	}
	
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	/** DISPLAY LIST */
	public function display_list(){
		
		$collection = new TestGroup_Collection();
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
		);
		
		BackendLayout::get()
			->prependTitle('Список элементов')
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables)
			->render();
	}
	
	/** DISPLAY NEW */
	public function display_new(){
		
		$pageTitle = 'Создание новой страницы';
		
		$variables = array_merge($_POST, array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => TestGroup_Model::create()->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->addBreadcrumb($pageTitle)
			->setContentPhpFile(self::TPL_PATH.'admin_edit.php', $variables)
			->render();
	}
	
	/** DISPLAY EDIT */
	public function display_edit($instanceId = null){
		
		$instanceId = (int)$instanceId;
		$instance = TestGroup_Model::load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование элемента</span> #'.$instance->getField('id');
	
		$variables = array_merge($instance->getAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle('Редактирование записи')
			->addBreadcrumb('Редактирование записи')
			->setContentPhpFile(self::TPL_PATH.'admin_edit.php', $variables)
			->render();
	}
	
	/** DISPLAY COPY */
	public function display_copy($instanceId = null){
		
		$instanceId = (int)$instanceId;
		$instance = TestGroup_Model::load($instanceId);
		
		$pageTitle = 'Копирование записи';
	
		$variables = array_merge($instance->getAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->addBreadcrumb($pageTitle)
			->setContentPhpFile(self::TPL_PATH.'admin_edit.php', $variables)
			->render();
	}
	
	/** DISPLAY DELETE */
	public function display_delete($instanceId = null){
		
		$instanceId = (int)$instanceId;
		$instance = TestGroup_Model::load($instanceId);

		$variables = array_merge($instance->getAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		BackendLayout::get()
			->prependTitle('Удаление записи')
			->addBreadcrumb('Удаление записи')
			->setContentPhpFile(self::TPL_PATH.'admin_delete.php', $variables)
			->render();
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE */
	public function action_save(){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new TestGroup_Model($instanceId);
		$saveMode = $instance->isNewObj ? TestGroup_Model::SAVE_CREATE : TestGroup_Model::SAVE_EDIT;
		
		if ($instance->save($_POST, $saveMode)) {
			Messenger::get()->addSuccess('Запись сохранена');
			$this->_redirectUrl = !empty($this->_redirectUrl)
				? preg_replace('/\(%([\w\-]+)%\)/e', '$instance->getField("$1")', $this->_redirectUrl)
				: null;
			return TRUE;
		} else {
			Messenger::get()->addError('Не удалось сохранить запись:', $instance->getError());
			return FALSE;
		}
	}
	
	/** ACTION DELETE */
	public function action_delete(){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = TestGroup_Model::load($instanceId);
	
		if ($instance->destroy()) {
			Messenger::get()->addSuccess('Запись удалена');
			return TRUE;
		} else {
			Messenger::get()->addError('Не удалось удалить запись:', $instance->getError());
			return FALSE;
		}

	}
	
}

?>