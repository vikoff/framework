<?

class Acl_Manager {
	
	private static $_instance = null;
	
	private $_userPermissions = array();
	
	
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Acl_Manager();
		
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
		);
		
		foreach($_data as $row)
			$this->_userPermissions[ $row['module'] ][ $row['resource'] ] = 1;
	}
	
	public function isResourceAllowed($module, $resource){
		
		return isset($this->_userPermissions[ $module ][ $resource ]);
	}
}

?>