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
	
	public function makeFsSnapshot($scandir, $collapseDirs, $base = '/'){
		
		$dirs = array();
		$files = array();
		foreach(scandir($scandir) as $elm){
			
			if($elm == '.' || $elm == '..')
				continue;
			
			$mtime = date('Y:m:d H-i-s', filemtime($scandir.$elm));
			
			if(is_dir($scandir.$elm))
				$dirs[] = array('dir' => $elm, 'mtime' => $mtime, 'size' => '');
			else
				$files[] = array('file' => $elm, 'mtime' => $mtime, 'size' => filesize($scandir.$elm));
		}
		
		foreach($dirs as $d){
			$dir = $d['dir'].'/';
			echo $base.$dir.'|'.$d['mtime']."\n";
			if (!isset($collapseDirs[$d['dir']]))
				$this->makeFsSnapshot($scandir.$dir, $collapseDirs, $base.$dir);
		}
		
		foreach($files as $f)
			echo $base.$f['file'].' |'.$f['mtime'].'|'.$f['size']."\n";
	}
}

?>