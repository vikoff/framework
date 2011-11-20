<?php

class User_Acl {
	
	const TABLE = 'user_acl';
	
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
		
		$db = db::get();
		$_data = $db->getAll('SELECT module, resource FROM '.self::TABLE.' WHERE role_id='.USER_AUTH_ROLE_ID);
		
		foreach($_data as $row)
			$this->_userPermissions[ $row['module'] ][ $row['resource'] ] = 1;
	}
	
	public function isResourceAllowed($module, $resource){
		
		if (CurUser::get()->isRoot())
			return TRUE;
		
		if ($resource == 'public')
			return TRUE;
			
		return isset($this->_userPermissions[ $module ][ $resource ]);
	}
	
	public function getResourcesList(){
		
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
			if (!isset($rules[ $rule['module'] ][ $rule['resource'] ]))
				$rules[ $rule['module'] ][ $rule['resource'] ] = array();
			$rules[ $rule['module'] ][ $rule['resource'] ][ $rule['role_id'] ] = TRUE;
		}
		
		return $rules;
	}
	
	public function saveRules($rulesRaw){
		
		$db = db::get();
		$db->truncate(self::TABLE);
		
		foreach($rulesRaw as $row => $enable){
			list($module, $resource, $role_id) = explode('|', $row) + array('','','');
			$db->insert(self::TABLE, array(
				'module' => $module,
				'resource' => $resource,
				'role_id' => $role_id,
			));
		}
		
		return TRUE;
	}
	
	public function copyRules($fromRoleId, $toRoleId){
		
		$db = db::get();
		$db->getOne('
			INSERT INTO '.self::TABLE.'
			SELECT '.$db->qe($toRoleId).' AS role_id, module, resource FROM '.self::TABLE.' WHERE role_id='.(int)$fromRoleId.'
		');
		return TRUE;
	}
	
	public function deleteRole($role_id){
		
		db::get()->delete(self::TABLE, 'role_id='.(int)$role_id);
		return TRUE;
	}
	
}

?>