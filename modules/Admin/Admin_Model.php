<?

class Admin_Model {
	
	// EXEC SQL (FORM SQL CONSOLE)
	public function execSql($sqls){
		
		$db = db::get();
		$sqls = preg_replace('/;\r\n/', ";\n", $sqls);
		$sqlsArr = explode(";\n", $sqls);
		$results = array();
		
		$db->enableErrorHandlingMode();
		
		foreach($sqlsArr as $sql){
			$sql = trim($sql);
			if(!empty($sql))
				$results[] = $db->getAll($sql, array());
		}
		
		$db->disableErrorHandlingMode();
		
		return $results;
	}
}

?>