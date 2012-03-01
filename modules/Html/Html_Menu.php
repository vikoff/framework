<?php

class Html_Menu {
	
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
		
		// поиск активного элемента
		foreach($this->_menu['items'] as $item){
			
			if (isset($item['display']) && $item['display'] === FALSE)
				continue;
				
			$this->items[] = $item;
			
			if ($item['active']){
				$index = count($this->items) - 1;
				$this->activeIndex = $index;
				$this->activeItem = &$this->items[$index];
			}
		}
	}
	
	public function getItems(){
		
		return $this->items;
	}
}

?>