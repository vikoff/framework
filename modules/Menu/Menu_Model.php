<?php

class Menu_Model {
	
	const FILES_PATH = 'elements/menus/';
	
	private $_name = null;
	private $_data = array();
	private $_menu = array();
	
	public $items = array();
	public $activeItem = null;
	public $activeIndex = null;
	
	
	public function __construct($name, $data = array()){
		
		$this->_name = $name;
		$this->_data = $data;
		$this->_menu = include(FS_ROOT.self::FILES_PATH.$this->_name.'.php');
		$this->items = $this->_menu['items'];
		
		// поиск активного элемента
		for($i = 0, $len = count($this->items); $i < $len; $i++){
			if ($this->items[$i]['active']){
				$this->activeIndex = $i;
				$this->activeItem = &$this->items[$i];
				break;
			}
		}
	}
	
	public function getItems(){
		
		return $this->_menu['items'];
	}
}

?>