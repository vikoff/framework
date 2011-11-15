<?php

class TestItem_AdminController extends Controller {
	
	/** имя модуля */
	const MODULE = 'testItem';
	
	/** элемент, отображаемый во view по умолчанию */
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/TestItem/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'list';
	
	/** ассоциация методов контроллера с ресурсами */
	public $methodResources = array(
		'display_list'    => 'edit',
		'display_new'     => 'edit',
		'display_edit'    => 'edit',
		'display_copy'    => 'edit',
		'display_delete'  => 'edit',

		'action_save'     => 'edit',
		'action_delete'   => 'edit',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return Acl_Manager::get()->isResourceAllowed(self::MODULE, $resource);
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
		
		$collection = new TestItem_Collection();
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
		
		$variables = array_merge(Tools::unescape($_POST), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => TestItem_Model::create()->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->addBreadcrumb(array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	/** DISPLAY EDIT */
	public function display_edit($uid = null){
		
		$instanceId = (int)$uid;
		$instance = TestItem_Model::load($instanceId);
		
		$pageTitle = '<span style="font-size: 14px;">Редактирование элемента</span> #'.$instance->getField('id');
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle('Редактирование записи')
			->addBreadcrumb(array(null, 'Редактирование записи'))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	/** DISPLAY COPY */
	public function display_copy($uid = null){
		
		$instanceId = (int)$uid;
		$instance = TestItem_Model::load($instanceId);
		
		$pageTitle = 'Копирование записи';
	
		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => 0,
			'pageTitle'  => $pageTitle,
			'validation' => $instance->getValidator()->getJsRules(),
		));
		
		BackendLayout::get()
			->prependTitle($pageTitle)
			->addBreadcrumb(array(null, $pageTitle))
			->setContentPhpFile(self::TPL_PATH.'edit.php', $variables)
			->render();
	}
	
	/** DISPLAY DELETE */
	public function display_delete($uid = null){
		
		$instanceId = (int)$uid;
		$instance = TestItem_Model::load($instanceId);

		$variables = array_merge($instance->GetAllFieldsPrepared(), array(
			'instanceId' => $instanceId,
		));
		
		BackendLayout::get()
			->prependTitle('Удаление записи')
			->addBreadcrumb(array(null, 'Удаление записи'))
			->setContentPhpFile(self::TPL_PATH.'delete.php', $variables)
			->render();
	}
	

	////////////////////
	////// ACTION //////
	////////////////////
	
	/** ACTION SAVE */
	public function action_save(){
		
		$instanceId = getVar($_POST['id'], 0, 'int');
		$instance = new TestItem_Model($instanceId);
		$saveMode = $instance->isNewObj ? TestItem_Model::SAVE_CREATE : TestItem_Model::SAVE_EDIT;
		
		if ($instance->save(Tools::unescape($_POST), $saveMode)) {
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
		$instance = TestItem_Model::load($instanceId);
	
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