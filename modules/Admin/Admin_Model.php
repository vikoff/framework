<?

class Admin_Model {
	
	// EXEC SQL (FORM SQL CONSOLE)
	public function execSql($sqls){
		
		$db = db::get();
		$sqls = preg_replace('/;\r\n/', ";\n", $sqls);
		$sqlsArr = explode(";\n", $sqls);
		$results = array();
		
		$db->enableErrorHandlingMode();
		
		// формируем массив результатов с ключами sql, time, result, numrows
		foreach($sqlsArr as $sql){
			$sql = trim($sql);
			if(!empty($sql)){
				$result = $db->getAll($sql);
				$results[] = array_merge($db->getLastQueryInfo(), array('result' => $result, 'numrows' => count($result)));
			}
		}
		
		$db->disableErrorHandlingMode();
		
		return $results;
	}
}

?>