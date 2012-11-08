<?

class Admin_Model {
	
	// EXEC SQL (FORM SQL CONSOLE)
	public function execSql($sqls){
		
		$db = db::get();
		$sqls = preg_replace('/;\r\n/', ";\n", $sqls);
		$sqlsArr = explode(";\n", $sqls);
		$results = array();
		
		// формируем массив результатов с ключами sql, time, result, numrows
		foreach($sqlsArr as $sql){
			$sql = trim($sql);
			if(!empty($sql)){
				$sql = str_replace(array('\r', '\n'), array("\r", "\n"), $sql);
				$result = $db->getAll($sql);
				$results[] = array_merge($db->getLastQueryInfo(), array('result' => $result, 'numrows' => count($result)));
			}
		}

		return $results;
	}
	
	public function getTableData($table, $dbConnection = 'default'){

		$db = db::get($dbConnection);
		$structure = $db->describe($table);

		if (!$structure)
			return FALSE;
		
		$sortCols = array();
		foreach ($structure as $col)
			$sortCols[ $col['name'] ] = $col['name'];

		$sorter = new Sorter($structure[0]['name'], 'DESC', $sortCols);
		$paginator = new Paginator('sql', array('*', 'FROM '.$table.' ORDER BY '.$sorter->getOrderBy()), '~50', array(
			'dbConnection' => $db,
		));
		
		return array(
			'sortableLinks' => $sorter->getSortableLinks(),
			'structure' => $structure,
			'rows' => $db->getAll($paginator->getSql()),
			'pagination' => $paginator->getButtons(),
		);
	}

	public function makeFsSnapshot($scandir, $collapseDirs, $base = '/'){
		
		$dirs = array();
		$files = array();
		foreach(scandir($scandir) as $elm){
			
			if($elm == '.' || $elm == '..')
				continue;
			
			$mtime = date('Y-m-d H:i:s', filemtime($scandir.$elm));
			
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
	
	public function readModulesConfig(){
		
		$globalConfigFile = FS_ROOT.'config/modules.php';
		$modulesDir = FS_ROOT.'modules/';
		$modulesConfig = array();
		$log = array('error' => array(), 'info' => array());
		
		if (!file_exists($globalConfigFile)) {
			array_unshift($log['error'], '<b>Файл глобальной конфигурации отсутствует</b>');
			return $log;
		}

		if (!is_writeable($globalConfigFile)) {
			array_unshift($log['error'], '<span style="color: red; font-weight: bold;">Файл глобальной конфигурации не доступен для записи</span>');
			return $log;
		}

		foreach(scandir($modulesDir) as $elm){
			
			if($elm == '.' || $elm == '..' || !is_dir($modulesDir.$elm))
				continue;
			
			if(file_exists($modulesDir.$elm.'/config.php')){
				$config = include($modulesDir.$elm.'/config.php');
				if (!isset($config['name'])) {
					$log['error'][] = '<span style="color: red;">В конфиге модуля <b>'.$elm.'</b> отсутствует ключ <b>name</b></span>';
					continue;
				}
				$modulesConfig[ $config['name'] ] = $config;
				$log['info'][] = 'Прочитан конфиг модуля <b>'.$config['name'].'</b>';
			}
		}
		
		if (file_put_contents($globalConfigFile, "<?php\n\nreturn ".var_export($modulesConfig, 1).";\n\n?>"))
			$log['info'][] = 'Главный конфигурационный файл обновлен';
		else
			$log['error'][] = 'Не удалось обновить главный конфигурационный файл';

		return $log;
		// echo '<pre>'; print_r($modulesConfig); die;
			
	}
}

?>