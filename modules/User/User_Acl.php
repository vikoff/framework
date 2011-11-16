<?php

class User_Acl {
	
	const TABLE = 'user_access';
	
	private static $_instance = null;
	
	private $_userPermissions = array();
	
	
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new User_Acl();
		
		return self::$_instance;
	}
	
	public function __construct(){
		
		$this->_loadUserPermissions();
	}
	
	private function _loadUserPermissions(){
		
		// $db = db::get();
		// $_data = $db->getAll('SELECT module, resource FROM acl WHERE role='.$db->qe(USER_AUTH_ROLE));
		
		// DEBUG
		$_data = array(
			array('module' => 'page', 'resource' => 'view'),
			array('module' => 'user', 'resource' => 'public'),
		);
		
		foreach($_data as $row)
			$this->_userPermissions[ $row['module'] ][ $row['resource'] ] = 1;
	}
	
	public function isResourceAllowed($module, $resource){
		
		if(CurUser::get()->isRoot())
			return TRUE;
			
		return isset($this->_userPermissions[ $module ][ $resource ]);
	}
	
	public function getResourcesList(){
		
		// echo '<pre>'; print_r(Config::get()->getModulesConfig()); die;
		$list = array();
		foreach(App::get()->getModulesConfig() as $module => $conf){
			if (!isset($conf['resources']))
				trigger_error('Модуль <b>'.$module.'</b> не имеет ключа <b>resources</b> в конфигурационном файле.', E_USER_ERROR);
			foreach($conf['resources'] as $name => $title){
				$list[] = array(
					'module' => $module,
					'module_title' => $conf['title'],
					'resource' => $name,
					'resource_title' => $title
				);
			}
		}
		
		// echo '<pre>'; print_r($list); die;
		return $list;
	}
	
	public function getAllAccessRules(){
		
		$rules = array();
		foreach(db::get()->getAll('SELECT * FROM '.self::TABLE) as $rule){
			if (!isset($rules[ $rule['module'] ]))
				$rules[ $rule['module'] ] = array();
			$rules[ $rule['module'] ][ $rule['resource'] ] = TRUE;
		}
		
		return $rules;
	}
}

?>