<?php

class CsvParser_Model {
	
	protected $isFound = null;
	protected $hfile;
	protected $filename;
	protected $lseparate = ";";
	protected $affected = 0;
	private $_fields = array();

	
	/**
	 * ИНИЦИАЛИЗАЦИЯ ДАННЫХ, ПЕРЕМЕЩЕНИЕ УКАЗАТЕЛЯ В ФАЙЛЕ НА НУЖНУЮ СТРОКУ
	 * 
	 * @param: array(
	 * 		file			- имя CSV файла для парсинга
	 * 		lstart = 0		- номер строки, с которой надо начинать обработку (первая строка имеет номер 0)
	 * 		lseparete = ';'	- символ-разделитель в CSV файле
	 */
	public function __construct($file, $startline = 0, $separator = ';'){
		
		$lstart = (int)$startline;
		$this->lseparete = $separator;
		$this->filename = $file;

		if(($this->hfile = fopen($this->filename, 'r')) !== false){
				
			$this->isFound = TRUE;
			
			// установить указатель на начальную строку 
			for($i = 0; $i < $lstart; $i++)
				fgetcsv($this->hfile, 4096, $this->lseparete);
				
		}else{
			$this->isFound = FALSE;
			trigger_error('Не удалось открыть файл', E_USER_NOTICE);
		}
	}
	
	
	public function __destruct(){
		fclose($this->hfile);
	}
	
	/**
	 * ЗАГРУЖАЕТ МАССИВ ПОЛЕЙ, ОБРАБАТЫВАЕМЫХ В CSV ФАЙЛЕ
	 *
	 * @param $fields:array - массив ключи которого - имена полей (для бд), значения - номера столбцов из CSV файла (нумерация столбцов с 1)
	 */
	public function SetFields($fields) {

		foreach($fields as $key => $val){
			if($val){$fields[$key] = $val - 1;}
			else{unset($fields[$key]);}
		}
		$this->_fields = $fields;
	}

	public function GetAffected() {
		return $this->affected;
	}
	
	public function getOneLine(){
	
		$line = fgetcsv($this->hfile, 4096, $this->lseparete);
		if(!$line){
			return FALSE;
		}else{
			return $line;
		}
	}
	
	public function getAll(){
	
		$data = array();
		while(($row = $this->csvLine2hash($this->getOneLine())) !== false)
			$data[] = $row;
		return $data;
	}
	
	public function csvLine2hash($line){
		
		if(!is_array($line))
			return FALSE;
			
		$data = array();
		foreach($this->_fields as $fildName => $colIndex)
			$data[$fildName] = isset($line[$colIndex]) ? $line[$colIndex] : '';
			
		$this->prepareLine($data);
		return $data;
	}

	public function parseLine(){
		
		return $this->csvLine2hash($this->getOneLine());
	}
	
	public function isFound(){
		
		return $this->isFound;
	}
	
	public function prepareLine(&$line){
		
		if(isset($line['date'])){
			
			$arr = explode('.', $line['date']);
			$arr = array_reverse($arr);
			$line['date'] = implode('-', $arr);
		}
	}
	
}
?>
