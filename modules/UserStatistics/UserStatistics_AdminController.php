<?php

class UserStatistics_AdminController extends Controller{
	
	const DEFAULT_VIEW = 1;
	
	/** путь к шаблонам (относительно FS_ROOT) */
	const TPL_PATH = 'modules/UserStatistics/templates/';
	
	/** метод, отображаемый по умолачанию */
	protected $_displayIndex = 'list';
	
	/** ассоциация методов контроллера с ресурсами */
	public $methodResources = array(
		'display_list'          => 'view',
		'display_view'			=> 'view',
		'ajax_save_client_side' => 'public',
	);
	
	
	/** ПРОВЕРКА ПРАВ НА ВЫПОЛНЕНИЕ РЕСУРСА */
	public function checkResourcePermission($resource){
		
		return TRUE;
	}
	
	/** ПОЛУЧИТЬ ИМЯ КЛАССА */
	public function getClass(){
		return __CLASS__;
	}
	
	
	///////////////////////////
	////// DISPLAY ADMIN //////
	///////////////////////////
	
	// DISPLAY LIST (ADMIN)
	public function display_list($params = array()){
		
		$collection = new UserStatistics_Collection($_GET);
		$variables = array(
			'collection' => $collection->getPaginated(),
			'pagination' => $collection->getPagination(),
			'sorters' => $collection->getSortableLinks(),
			'filters' => $collection->getFiltersLists(),
		);
		
		BackendLayout::get()
			->setLinkTags($collection->getLinkTags())
			->setContentPhpFile(self::TPL_PATH.'admin_list.php', $variables)
			->render();
	}
	
	// DISPLAY VIEW
	public function display_view($params = array()){
		
		try{
			$instanceId = getVar($params[0], 0, 'int');
			$variables = UserStatistics_Model::get()->getRowPrepared($instanceId);
			
			// echo '<pre>'; print_r($variables); die;
			
			BackendLayout::get()
				->prependTitle('Статистика посещений пользователя')
				->setContentPhpFile(self::TPL_PATH.'admin_view.php', $variables)
				->render();
		}
		catch(Exception $e){
			BackendViewer::get()->error404($e->getMessage());
		}
	}
	
	// DISPLAY DELETE (ADMIN)
	public function display_delete($params = array()){
	
		BackendViewer::get()->setContentPhpFile(self::TPL_PATH.'delete.php');
	}
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION DELETE (ADMIN)
	public function action_delete($params = array()){
		
		$expire = getVar($_POST['expire']);
		
		$expiredValues = array(
			'1day'   => 86400,
			'1week'  => 604800,
			'1month' => 2592000,
			'3month' => 7776000,
			'6month' => 15552000,
			'9month' => 23328000,
			'1year'  => 31536000);
			
		if(!isset($expiredValues[$expire])){
			Messenger::get()->addError('Неверный временной промежуток.');
			return FALSE;
		}
		
		UserStatistics::get()->deleteOldStatistics($expiredValues[$expire]);
		Messenger::get()->addSuccess('Старая статистика удалена.');
		return TRUE;
	}
	
}

?>