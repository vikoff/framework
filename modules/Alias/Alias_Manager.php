<?php

class Alias_Manager {
	
	/**
	 * ПОЛУЧИТЬ РЕАЛЬНЫЙ ПУТЬ ПО ПСЕВДОНИМУ
	 * Вызывается из класса Request, чтобы прозрачно подменить псевдоним реальным путем.
	 * @param string $alias - псевдоним для поиска
	 * @return string|null - возвращает найденный путь, или null, если ничего не найдено
	 */
	public static function getPath($alias){
		
		$db = db::get();
		return $db->fetchOne('SELECT path FROM '.Alias_Model::TABLE.' WHERE alias='.$db->qe($alias));
	}
}

?>