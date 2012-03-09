<?php
/**
 * 
 * 
 * @using:
 * 		const: CFG_SITE_NAME, FS_ROOT, WWW_ROOT, AJAX_MODE
 * 
 */
class Layout{
	
	
	protected $_layoutName = 'default';
	protected $_layoutDir = null;
	
	protected $_htmlTitle = '';
	protected $_htmlLinkTags = array();
	
	protected $_useAutoBreadcrumbs = FALSE;
	protected $_manualBreadcrumbs = array();
	
	protected $_contentLinks = array();
	
	protected $_htmlContent = '';
	
	protected $_layoutRender = 'auto';
	
	private static $_instance = null;
	
	
	/** ТОЧКА ВХОДА В КЛАСС (ПОЛУЧИТЬ ЭКЗЕМПЛЯР Layout) */
	public static function get(){
		
		if(is_null(self::$_instance))
			self::$_instance = new Layout();
		
		return self::$_instance;
	}
	
	/** КОНСТРУКТОР */
	protected function __construct(){
		
		$this->_layoutDir = 'layouts/'.$this->_layoutName.'/';
		$this->init();
	}
	
	protected function init(){}
	
	/** ПОЛУЧИТЬ ДИРЕКТОРИЮ ФАЙЛОВ МАКЕТА */
	public function getLayoutDir(){
		return $this->_layoutDir;
	}
	
	public function setTitle($title){
		
		$this->_htmlTitle = $title;
		return $this;
	}
	
	public function prependTitle($title, $separator = ' » '){
		
		$this->_htmlTitle = strlen($this->_htmlTitle)
			? $title.$separator.$this->_htmlTitle
			: $title.$this->_htmlTitle;
		return $this;
	}
	
	public function appendTitle($title, $separator = ' » '){
		
		$this->_htmlTitle = !empty($this->_htmlTitle)
			? $this->_htmlTitle.$separator.$title
			: $title;
		return $this;
	}
	
	public function setLinkTags($tags){
	
		foreach($tags as $tagname => $tagval)
			if(!empty($tagval))
				$this->_htmlLinkTags[$tagname] = $tagval;
				
		return $this;
	}
	
	public function autoBreadcrumbs($enable = TRUE){
		
		$this->_useAutoBreadcrumbs = (bool)$enable;
		return $this;
	}
	
	public function addBreadcrumb($title, $href = null){
		
		$this->_manualBreadcrumbs[] = array($href, $title);
		return $this;
	}
	
	public function href($url){
	
	return WWW_ROOT.(CFG_USE_SEF
		// http://site.com/controller/method?param=value
		? $url
		// http://site.com/index.php?r=controller/method&param=value
		: 'index.php'.(!empty($url) ? '?r='.str_replace('?', '&', $url) : ''));
	}
	
	public function addContentLink($href, $title){
		
		$this->_contentLinks[] = '<a href="'.$this->href($href).'">'.$title.'</a>';
		return $this;
	}
	
	/** ОЧИСТИТЬ КОНТЕНТ */
	public function clearContent(){
	
		$this->_htmlContent = '';
		return $this;
	}
	
	/** ЗАДАТЬ КОНТЕНТ */
	public function setContent($content){
	
		$this->_htmlContent .= $content;
		return $this;
	}
	
	/** ПОЛУЧИТЬ КОНТЕНТ ИЗ ПРОИЗВОЛЬНОГО ФАЙЛА (БЕЗ ИНТЕРПРЕТАЦИИ) */
	public function setContentHtmlFile($file){
		
		$this->_htmlContent .= $this->getContentHtmlFile($file);
		return $this;
	}
	
	/** ПОЛУЧИТЬ КОНТЕНТ ИЗ PHP-ФАЙЛА (С ИНТЕРПРЕТАЦИЕЙ) */
	public function setContentPhpFile($file, $variables = array()){
		
		$this->_htmlContent .= $this->getContentPhpFile($file, $variables);
		return $this;
	}
	
	public function setVariables($variables){
		
		foreach($variables as $k => $v)
			$this->$k = $v;
			
		return $this;
	}
	
	protected function _getTitleHTML(){
		
		return !empty($this->_htmlTitle)
			? $this->_htmlTitle.' - '.CFG_SITE_NAME
			: CFG_SITE_NAME;
	}
	
	protected function _getLinkTagsHTML(){
		
		$output = '';
		foreach($this->_htmlLinkTags as $rel => $href)
			$output = "\t".'<link rel="'.$rel.'" href="'.$href.'" />'."\n";
		
		return $output;
	}
	
	/** GET BASE HREF URL */
	protected function _getBaseHrefHTML(){
		
		return WWW_ROOT;
	}
	
	protected function _constructAutoBreadcrumbs(){
		
		return array();
	}
	
	/** GET BREADCRUMBS HTML */
	protected function _getBreadcrumbsHTML(){
		
		$all = $this->_useAutoBreadcrumbs
			? array_merge($this->_constructAutoBreadcrumbs(), $this->_manualBreadcrumbs)
			: $this->_manualBreadcrumbs;
			
		$breadcrumbs = array();
		$num = count($all);
		
		foreach($all as $index => $v)
			$breadcrumbs[] = is_null($v[0]) || ($index + 1) == $num
				? '<span class="item">'.$v[1].'</span>'
				: '<a class="item" href="'.$this->href($v[0]).'">'.$v[1].'</a>';
		
		return $num ? '<div class="breadcrumbs">'.implode('<span class="mediator"> » </span>', $breadcrumbs).'</div>' : '';
	}
	
	/** GET USER MESSAGE HTML */
	protected function _getUserMessagesHTML(){
	
		return Messenger::get()->getAll();
	}
	
	/** GET CONTENT LINKS HTML */
	protected function _getContentLinksHTML($separator = ' '){
		
		return !empty($this->_contentLinks)
			? '<div class="content-links">'.implode($separator, $this->_contentLinks).'</div>'
			: '';
	}
	
	/** GET HTML CONTENT */
	protected function _getContentHTML(){
		
		return $this->_htmlContent;
	}
	
	/** GET CLIENT STATISTICS LOADER HTML */
	protected function _getClientStatisticsLoaderHTML(){
	
		$stat = UserStatistics_Model::get();
		
		return $stat->checkClientSideStatistics()
			? $stat->getClientSideStatisticsLoader()
			: '';
	}
	
	/** RENDER ERROR PAGE */
	public function error($message = ''){
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Ошибка')
				->setContentPhpFile($this->_layoutDir.'error.php', array('message' => $message))
				->render();
		}
		exit();
	
		$variables = array(
			'message' => $message,
		);
		$this
			->setTitle('Ошибка')
			->setContentPhpFile($this->_layoutDir.'error.php', $variables)
			->render();
		exit();
	}
	
	/** RENDER ERROR 403 PAGE */
	public function error403($message = ''){
	
		header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden'); // 'HTTP/1.1 403 Forbidden'
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Доступ запрещен')
				->setContentPhpFile($this->_layoutDir.'error403.php', array('message' => $message))
				->render();
		}
		exit();
	}
	
	/** RENDER ERROR 404 PAGE */
	public function error404($message = ''){
	
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); // 'HTTP/1.1 404 Not Found'
		
		if(AJAX_MODE){
			echo $message;
		}else{
			$this
				->setTitle('Страница не найдена')
				->setContentPhpFile($this->_layoutDir.'error404.php', array('message' => $message))
				->render();
		}
		exit();
	}
	
	/**
	 * RENDER ALL
	 * @param string $type [auto|all|content|json]
	 */
	public function render($type = 'auto', $boolReturn = FALSE){
		
		// вывод json
		if ($type === 'json' || (AJAX_MODE && $type === 'auto'))
			return $this->_renderJSON($boolReturn);
		
		// вывод html без макета
		elseif ($type === 'content')
			return $this->_renderContent($boolReturn);
			
		// вывод html с макетом
		else
			return $this->_renderAll($boolReturn);
	}
	
	/**
	 * ВЫВЕСТИ/ВЕРНУТЬ ЭЛЕМЕНТЫ СТРАНИЦЫ В ФОРМАТЕ JSON
	 * @access protected
	 * @param bool $boolReturn - флаг, возвращать контент, или выводить
	 * @param void|string контент в формате json
	 */
	protected function _renderJSON($boolReturn){
		
		$data = array();
		$data['content'] = $this->_getContentHTML();
		
		$json = json_encode($data);
		
		if($boolReturn)
			return $json;
		else
			echo $json;
	}
	
	/**
	 * ВЫВЕСТИ/ВЕРНУТЬ КОНТЕНТ БЕЗ МАКЕТА
	 * @access protected
	 * @param bool $boolReturn - флаг, возвращать контент, или выводить
	 * @param void|string контент
	 */
	protected function _renderContent($boolReturn){
		
		if($boolReturn)
			return $this->_getContentHTML();
		else
			echo $this->_getContentHTML();
	}
	
	/**
	 * ВЫВЕСТИ/ВЕРНУТЬ КОНТЕНТ В МАКЕТЕ
	 * @access protected
	 * @param bool $boolReturn - флаг, возвращать контент, или выводить
	 * @param void|string контент
	 */
	protected function _renderAll($boolReturn){
		
		// $this->beforeRenderWithLayout();
		
		if($boolReturn)
			ob_start();
			
		include(FS_ROOT.$this->_layoutDir.'layout.php');
		
		if($boolReturn)
			return ob_get_clean();
	}
	
	/** АКСЕССОР ДЛЯ ШАБЛОНОВ */
	public function __get($name){
		
		return isset($this->$name) ? $this->$name : '';
	}
	
	public function __isset($name){
		
		return isset($this->$name);
	}
	
	/** ПОЛУЧИТЬ СОДЕРЖИМОЕ HTML ФАЙЛА */
	public function getContentHtmlFile($file){
		
		return file_get_contents($file);
	}
	
	/** ПОЛУЧИТЬ ПРОИНТЕРПРЕТИРОВАННОЕ СОДЕРЖИМОЕ PHP ФАЙЛА */
	public function getContentPhpFile($file, $variables = array()){
		
		foreach($variables as $k => $v)
			$this->$k = $v;
			
		ob_start();
		include(FS_ROOT.$file);
		
		foreach($variables as $k => $v)
			unset($this->$k);
		
		return ob_get_clean();
	}

}

?>