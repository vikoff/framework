<?php

/**
 *	класс ImageMaster предоставляет инструменты для копирования
 *	и редактирования изображений
 *	
 *	FullName = путь + имя + расширение
 *	Name 	 = имя + расширение
 *	NameOnly = имя
 *	Ext		 = расширение
 *
 */
class ImageMaster {
	
	// transform constants
	const T_PROPORT = 'proport';
	const T_CENTER  = 'fill';
	const T_CROP    = 'crop';
	
	// rotate constants
	const ROTATE_RIGHT = -90;
	const ROTATE_LEFT  = 90;
	const ROTATE_180   = 180;
	
	// flip constants
	const FLIP_HORIZONTAL = 'h';
	const FLIP_VERTICAL   = 'v';
	const FLIP_BOTH       = 'both';
	
	public static $validExtensions = array('1' => 'gif', '2' => 'jpeg', '3' => 'png');
	
	private $_path 		= '';
	private $_fullName	= '';
	private $_name 		= '';
	private $_nameOnly	= '';
	private $_ext 		= '';
	private $_mimeExt	= null;
	
	private $_isFileExists = FALSE;
	
	private $_reducedImgFullName = null;
	
	private $_exifData = null;
	
	private $_completedImages = 0;

	/**
	 * Конфигурация класса
	 * @var array
	 *		null|string 'imageMagick' - путь к библиотеке ImageMagick (или null чтобы использовать GD)
	 */
	private static $_config = array(
	
		'imageMagick' => null,
	);
	
	/**
	 * Испольозвать ли ImageMagick (иначе GD)
	 * @var bool
	 */
	private static $_useImageMagick = FALSE;
	
	
	/**
	 * Задать конфигурацию класса
	 * @param array $config - массив директива=>значение
	 * @return void;
	 */
	public static function config($config){
	
		foreach($config as $key => $val){
			if(array_key_exists($key, self::$_config)){
				self::$_config[$key] = $val;
			}else{
				trigger_error('Не удалось установить конфигурацию ImageMaster. Неизвестный ключ ['.$key.']', E_USER_ERROR);
			}
		}
		
		// флаг, использовать ли ImageMagick
		if(!is_null(self::$_config['imageMagick']))
			self::$_useImageMagick = TRUE;
	}
	
	/**
	 * Получить значение конфигурационной директивы, или весь массив конфигурации
	 * @param null|string $key
	 * @return array|string
	 */
	public static function getConfig($key = null){
		
		return is_null($key)
			? self::$_config
			: self::$_config[$key];
	}

	/**
	 * @param string $fullName - полный путь к изображению
	 * @param string $prettyName - реальное имя (для проверки валидности расширения)
	 * @return ImageMaster
	 */
	public static function load($fullName, $prettyName = ''){
		
		$instance = new ImageMaster($fullName, $prettyName);
		return $instance;
	}
	
	private function __construct($fullName, $prettyName = ''){
		
		$this->setFullName($fullName, $prettyName);
	}
	
	 /** задать полное имя изображения и проверить наличие файла */
	public function setFullName($fullName, $prettyName = ''){
		
		$this->_fullName = $fullName;
		$this->_path	 = dirname($this->_fullName).'/';
		$this->_name 	 = strlen($prettyName) ? $prettyName : $this->_getBasename($this->_fullName);
		$this->_ext 	 = $this->_getFileExt($this->_name);
		$this->_nameOnly = str_replace('.'.$this->_ext, '', $this->_name);
		
		if(strlen($this->_name) && file_exists($this->_fullName))
			
			$this->_isFileExists = TRUE;
		else
			throw new Exception('Исходное изображение не найдено');
			
	}
	
	public function getPath(){
		return $this->_path;
	}
	
	public function getFullName(){
		return $this->_fullName;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function getNameOnly(){
		return $this->_nameOnly;
	}
	
	public function getExt(){
		return $this->_ext;
	}
	
	public function getMimeExt(){
		
		if(is_null($this->_mimeExt)){
		
			list($w, $h, $type) = getimagesize($this->_fullName);
			$this->_mimeExt = $this->getExtByMimeIndex($type);
		}
		return $this->_mimeExt;
	}
	
	/** ПРОВЕРИТЬ, КОРРЕКТНЫЙ ЛИ ТИП ИЗОБРАЖЕНИЯ */
	public function checkImageFormat($checkExtension = FALSE){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
			
		// правильный ли MIME тип файла
		list($x, $y, $type) = getImageSize($this->_fullName);
		if(!in_array($type, array_keys(self::$validExtensions))){
			throw new Exception('Изображение имеет недопустимый формат. Разрешается PNG, JPEG или GIF');
		}
	
		if($checkExtension)
			if(!in_array(strtolower($this->_ext), array('jpg', 'jpeg', 'png', 'gif'), TRUE))
				throw new Exception('Изображение имеет недопустимый формат. Разрешается PNG, JPEG или GIF');
		
		return $this;
	}
	
	public function getExtByMimeIndex($mimeIndex){
		
		if(!isset(self::$validExtensions[$mimeIndex]))
			throw new Exception('Неверный тип изображения. Разрешается jpg, png или gif.');
		
		return self::$validExtensions[$mimeIndex];
	}
	
	/**
	* ПЕРЕМЕСТИТЬ ИЗОБРАЖЕНИЕ
	*
	* если задан путь с именем - переместить и переименовать
	* если задан путь без имени (заканчивающийся слешем) - переместить, оставив текущее имя
	* если прошло удачно, перемещенное изображение становится текущим
	*
	* @param string $dstFullName 
	* @return ImageMaster instance
	*/
	public function move($dstFullName, $forceMove = FALSE){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		if(file_exists($dstFullName)){
			if($forceMove)
				unlink($dstFullName);
			else
				throw new Exception('Перемещение невозможно. Конечный ['.$dstFullName.'] файл уже существует.');
		}
		
		// получение пути и имени конечного файла
		list($dstPath, $dstName) = $this->getPathAndName($dstFullName);
		
		// проверка конечной директории
		$this->checkDir($dstPath);
		
		// перемещение изображения
		if(@rename($this->_fullName, $dstPath.$dstName)){
		
			$this->setFullName($dstPath.$dstName);
		}else{
			throw new Exception('Не удалось переместить изображение');
		}
		
		return $this;
	}
	
	/**
	 * КОПИРОВАТЬ ИЗОБРАЖЕНИЕ
	 * если задан путь с именем - скопировать с заданным именем
	 * если задан путь без имени (заканчивающийся слешем) - скопировать, взяв текущее имя
	 * @param string $dstFullName - имя конечного файла
	 * @param bool $forceCopy - копировать принудительно (если файл с таким именем уже существует, удалит его)
	 * @param bool $focusOnSrcImg - оставить фокус на исходном изображении, иначе переместить фокус на новое
	 * @return ImageMaster instance
	 */
	public function copy($dstFullName, $forceCopy = FALSE, $focusOnSrcImg = FALSE){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		if(file_exists($dstFullName)){
			if($forceCopy)
				unlink($dstFullName);
			else
				throw new Exception('Копирование невозможно. Конечный файл ['.$dstFullName.'] уже существует.');
		}
		
		// получение пути и имени конечного файла
		list($dstPath, $dstName) = $this->getPathAndName($dstFullName);
		
		// проверка конечной директории
		$this->checkDir($dstPath);
		
		// копирование изображения
		if(@copy($this->_fullName, $dstPath.$dstName)){
			
			if(!$focusOnSrcImg)
				$this->setFullName($dstPath.$dstName);
		}else{
			throw new Exception('Не удалось скопировать изображение');
		}
		
		return $this;
	}
	
	/**
	 * РАЗВОРОТ ИЗОБРАЖЕНИЙ
	 * @param int $angle - угол поворота
	 * @param null|string $dstFullName - имя конечного изображения (если не задано, будет обработано текущее, иначе будет сделана копия)
	 * @param bool $focusOnSrcImg - имеет силу только если передан $dstFullName. Если TRUE, оставить исходное изображение текущим.
	 * @return ImageMaster instance
	 */
	public function rotate($angle, $bgColor = null, $dstFullName = null, $focusOnSrcImg = false){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		// сохраним имя оригинального файла, чтобы вернуть на него фокус при необходимости
		$originFullName = $this->_fullName;
		
		// если передан $dstFullName, значит надо сделать копию изображения
		// (при этом копия получает фокус, и разворачиваться будет она)
		if(!is_null($dstFullName))
			$this->copy($dstFullName);
	
		// получение параметров изображения
		list($srcWidth, $srcHeight, $srcType) = getimagesize($this->_fullName);
		
		// получение ресурса изображения
		switch ($srcType){	
			case 1: $srcImgRs = imagecreatefromgif($this->_fullName);  break;
			case 2: $srcImgRs = imagecreatefromjpeg($this->_fullName); break;
			case 3: $srcImgRs = imagecreatefrompng($this->_fullName);  break;
		}
		
		list($r, $g, $b) = self::colorHex2rgb(is_null($bgColor) ? '#FFF' : $bgColor);
		$bg = imagecolorallocate($srcImgRs, $r, $g, $b);
		
		$srcImgRs = imagerotate($srcImgRs, $angle, $bg);
		
		// сохранение изображения
		$this->_saveImgRs($srcType, $srcImgRs, $this->_fullName);
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		
		// если была создана копия изображения и задан флаг фокуса на исходное изображение
		// то установим фокус на исходное изображение
		if(!is_null($dstFullName) && $focusOnSrcImg)
			$this->setFullName($originFullName);
		
		return $this;
	}
	
	/**
	 * ОТРАЖЕНИЕ ИЗОБРАЖЕНИЯ
	 * @param const $mode self::[FLIP_HORIZONTAL|FLIP_VERTICAL|FLIP_BOTH]
	 * @param null|string $dstFullName - имя конечного изображения (если не задано, будет обработано текущее, иначе будет сделана копия)
	 * @param bool $focusOnSrcImg - имеет силу только если передан $dstFullName. Если TRUE, оставить исходное изображение текущим.
	 * @return ImageMaster instance
	 */
	public function flip($mode, $dstFullName = null, $focusOnSrcImg = false){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		// сохраним имя оригинального файла, чтобы вернуть на него фокус при необходимости
		$originFullName = $this->_fullName;
		
		// если передан $dstFullName, значит надо сделать копию изображения
		// (при этом копия получает фокус, и разворачиваться будет она)
		if(!is_null($dstFullName))
			$this->copy($dstFullName);
	
		// получение параметров изображения
		list($width, $height, $srcType) = getimagesize($this->_fullName);
		
		// получение ресурса изображения
		switch ($srcType){	
			case 1: $srcImgRs = imagecreatefromgif($this->_fullName);  break;
			case 2: $srcImgRs = imagecreatefromjpeg($this->_fullName); break;
			case 3: $srcImgRs = imagecreatefrompng($this->_fullName);  break;
		}

		$src_x      = 0;
		$src_y      = 0;
		$src_width  = $width;
		$src_height = $height;

		switch($mode){

			case self::FLIP_VERTICAL:
				$src_y      = $height;
				$src_height = -$height;
				break;

			case self::FLIP_HORIZONTAL:
				$src_x     = $width;
				$src_width = -$width;
				break;

			case self::FLIP_BOTH:
				$src_x      = $width;
				$src_y      = $height;
				$src_width  = -$width;
				$src_height = -$height;
				break;

			default:
				trigger_error('Неверный тип отражения изображения.', E_USER_ERROR);
		}

		$dstImgRs = imagecreatetruecolor($width, $height);

		imagecopyresampled($dstImgRs, $srcImgRs, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height);
		
		// сохранение изображения
		$this->_saveImgRs($srcType, $dstImgRs, $this->_fullName);
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		imagedestroy($dstImgRs);
		
		// если была создана копия изображения и задан флаг фокуса на исходное изображение
		// то установим фокус на исходное изображение
		if(!is_null($dstFullName) && $focusOnSrcImg)
			$this->setFullName($originFullName);
		
		return $this;
	}
	
	/**
	 * ТРЕБУЕТСЯ ЛИ ДЛЯ ИЗОБРАЖЕНИЯ EXIF-РАЗВОРОТ
	 * @return bool требуется ли разворот
	 */
	public function isNeedExifRotate(){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		return in_array($this->getExifOrientation(), array(2, 3, 4, 5, 6, 7, 8));
	}
	
	/**
	 * ПОЛУЧИТЬ ОРИЕНТАЦИЮ ИЗОБРАЖЕНИЯ ПО ДАННЫМ EXIF
	 * @return int ориентация изображения
	 */
	public function getExifOrientation(){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		// IMAGE MAGICK
		if(self::$_useImageMagick){
			$imPath = self::$_config['imageMagick'];
			return `{$imPath}identify -format %[EXIF:Orientation] {$this->_fullName}`;
		}
		
		// GD
		else{
			$this->_exifData = is_null($this->_exifData) ? @exif_read_data($this->_fullName) : $this->_exifData;
			if($this->_exifData){
				if(isset($this->_exifData['IFD0']['Orientation']))
					return $this->_exifData['IFD0']['Orientation'];
				elseif(isset($this->_exifData['Orientation']))
					return $this->_exifData['Orientation'];
				else
					return FALSE;
			}else{
				return FALSE;
			}
		}
	}
	
	/**
	 * РАЗВОРОТ ИЗОБРАЖЕНИЯ ПО EXIF ДАННЫМ
	 * @return ImageMaster instance
	 */
	public function exifRotate(){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
			
		// IMAGE MAGICK
		if(self::$_useImageMagick){
		
			$imPath = self::$_config['imageMagick'];
			`{$imPath}mogrify -auto-orient '{$this->_fullName}'`;
			
		}
		
		// GD
		else{
		
			switch($this->getExifOrientation()){
			
				case 1: // nothing
					break;

				case 2: // horizontal flip
					$this->flip(self::FLIP_HORIZONTAL);
					break;
				 
				case 3: // 180 rotate
					$this->rotate(self::ROTATE_180);
					break;

				case 4: // vertical flip
					$this->flip(self::FLIP_VERTICAL);
					break;

				case 5: // vertical flip + 90 rotate right
					$this->flip(self::FLIP_VERTICAL);
					$this->rotate(self::ROTATE_RIGHT);
					break;

				case 6: // 90 rotate right
					$this->rotate(self::ROTATE_RIGHT);
					break;

				case 7: // horizontal flip + 90 rotate right
					$this->flip(self::FLIP_HORIZONTAL);    
					$this->rotate(self::ROTATE_RIGHT);
					break;

				case 8: // 90 rotate left
					$this->rotate(self::ROTATE_LEFT);
					break;
			}
		}
		
		return $this;
	}
	
	/**
	 * НАЛОЖЕНИЕ ВОДЯНОГО ЗНАКА
	 * @param string $waterMarkImgSrc - путь к изображению водяного знака
	 * @param array $position - массив из двух элементов (по одному из пар [top|bottom], [left|right]), указывающий положение водяного знака
	 * @return ImageMaster instance
	 */
	public function watermark($waterMarkImgSrc, $position){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
	
		// получение параметров изображения
		list($width, $height, $srcType) = getimagesize($this->_fullName);
		
		// получение ресурса изображения
		$srcImgRs = $this->_getImgRs($srcType, $this->_fullName);
		
		// наложение водяного знака
		$srcImgRs = $this->_watermarkRs($srcImgRs, $waterMarkImgSrc, $position);
		
		// сохранение изображения
		$this->_saveImgRs($srcType, $srcImgRs, $this->_fullName);
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		
		return $this;
	}
	
	/**
	 * НАЛОЖЕНИЕ ВОДЯНОГО ЗНАКА на ресурс изображения
	 */
	private function _watermarkRs($srcImgRs, $waterMarkImgSrc, $position){
	
		// определение, правильно ли заданы координаты
		$p = $position + array('top' => null, 'bottom' => null, 'left' => null, 'right' => null);
		if((is_null($p['top']) && is_null($p['bottom'])) || (!is_null($p['top']) && !is_null($p['bottom'])) || (is_null($p['left']) && is_null($p['right'])) || (!is_null($p['left']) && !is_null($p['right'])))
			throw new Exception('Для наложения водяного знака укажите два значения позиции: одно из пары [top|bottom], и одно из пары [left|right]');
	
		// получение параметров изображения
		$width = imagesx($srcImgRs);
		$height = imagesy($srcImgRs);
		
		// получение параметров водяного знака
		list($wmWidth, $wmHeight, $wmType) = getimagesize($waterMarkImgSrc);
		
		// получение ресурса водяного знака
		$wmRs = $this->_getImgRs($wmType, $waterMarkImgSrc);
		
		$wmLeft = !is_null($p['left']) ? $p['left'] : $width - $p['right'] - $wmWidth;
		$wmTop  = !is_null($p['top']) ? $p['top'] : $height - $p['bottom'] - $wmHeight;
		
		// наложение водяного знака
		imagecopy($srcImgRs,
				  $wmRs,
				  $wmLeft, // dst_x
				  $wmTop,  // dst_y
			      0, 	   // src_x
			      0, 	   // src_y
			      $wmWidth,   // src_w
			      $wmHeight); // src_h
		
		// освобождение ресурсов
		imagedestroy($wmRs);
		
		return $srcImgRs;
	}
	
	/**
	 * РЕСАЙЗИТЬ ИЗОБРАЖЕНИЕ
	 * 
	 * @param string $dstFullNameNoExt - путь и имя (без расширения) конечного изображения
	 * @param array $copies - массив копий изображения array( array(width, height, prefix, transform-type), array(width, height, prefix, transform-type) )
	 * @param array $additParams - дополнительные параметры.
	 *        Допустимые значения:		
	 *        bool 'isFullNameSpecified' - флаг, говорящий о том, что имя конечного изображения указано с расширением, которое и надо использовать
	 */
	public function resize($dstFullNameNoExt, $copies, $additParams = array()){
	
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		// получение параметров исходного изображения
		list($srcWidth, $srcHeight, $srcType) = getimagesize($this->_fullName);
		$srcWidth  = (int)$srcWidth;
		$srcHeight = (int)$srcHeight;
		$srcType   = (int)$srcType;
		
		if(!$srcWidth || !$srcHeight || !$srcType){
			throw new Exception('Не удалось определить параметры изображения');
			return FALSE;
		}
	
		// получение пути и имени конечного файла
		// если полное передан параметр isFullNameSpecified, значит $dstFullNameNoExt уже содержит расширение файла,
		// иначе расширение будет добавлено автоматически, в зависимости от mime типа изображения.
		$_fullDstName = $dstFullNameNoExt.(!empty($additParams['isFullNameSpecified']) ? '' : '.'.$this->getExtByMimeIndex($srcType));
		list($dstPath, $dstName) = $this->getPathAndName($_fullDstName);
		$this->checkDir($dstPath);
		
		$validCopies = array();
		
		foreach($copies as $copy){
			
			if(!is_array($copy))
				trigger_error('Копии для преобразования должны быть переданы в виде массива массивов', E_USER_ERROR);
			
			$additImgParams = isset($copy[4]) && is_array($copy[4]) ? $copy[4] : array();
			
			$params = array(
				'width' 		=> (int)$copy[0],
				'height' 		=> (int)$copy[1],
				'prefix' 		=> (string)(!empty($copy[2]) ? $copy[2] : ''),
				'transformType'	=> (string)(!empty($copy[3]) ? $copy[3] : self::T_PROPORT),
				
				'canEnlarge' 	=> isset($additImgParams['canEnlarge']) ? $additImgParams['canEnlarge'] : FALSE,
				'bgColor' 		=> isset($additImgParams['bgColor']) ? $additImgParams['bgColor'] : 'transparent',
			);
			
			$ratio = (string)$this->_getRatio($srcWidth, $srcHeight, $params['width'], $params['height'], $params['transformType'], $params['canEnlarge']);
			$validCopies[$ratio][] = $params;
		}
		
		// выстраивание копий в порядке уменьшения размера
		// чтобы каждая следующая копия могла создаваться из предыдущей
		krsort($validCopies);
		
		// выполнение конвертирования в зависимости от типа преобразования
		foreach($validCopies as $ratio => $_copies){
			
			foreach($_copies as $copy){
				
				// определение параметров изображения
				$transformType = $copy['transformType'];
				$dstFullName = $dstPath.$copy['prefix'].$dstName;
				$dstImgRs = null;
				
				// IMAGE MAGICK
				if(self::$_useImageMagick){
					
					$imPath = self::$_config['imageMagick'];
					
					$size = $copy['width'].'x'.$copy['height'];
					$sizeSuffix = '';
					
					if(!$copy['canEnlarge'])
						$sizeSuffix = '>';
						
					// пропорциональное преобразование
					if($transformType == self::T_PROPORT){
						`{$imPath}convert '{$this->_fullName}' -thumbnail '{$size}{$sizeSuffix}' '{$dstFullName}'`;
					}
						
					// вписать изображение в прямоугольник заданного размера (заполнив пустое место фоном)
					if($transformType == self::T_CENTER){
						`{$imPath}convert '{$this->_fullName}' -thumbnail '{$size}{$sizeSuffix}' -background {$copy['bgColor']} -gravity center -extent {$size}  '{$dstFullName}'`;
					}
						
					// вписать изображение в прямоугольник заданного размера (обрезав края)
					if($transformType == self::T_CROP){
						`{$imPath}convert '{$this->_fullName}' -thumbnail '{$size}^>' -background {$copy['bgColor']} -gravity center -extent {$size}  '{$dstFullName}'`;
					}
					
				}
				
				// GD
				else{
					// пропорциональное преобразование
					if($transformType == self::T_PROPORT)
						$dstImgRs = $this->_resizeProport($dstFullName, $copy['width'], $copy['height'], $copy['canEnlarge']);
					
					// вписать изображение в прямоугольник заданного размера (заполнив пустое место фоном)
					elseif($transformType == self::T_CENTER)
						$dstImgRs = $this->_resizeCenter($dstFullName, $copy['width'], $copy['height'], $copy['bgColor'], $copy['canEnlarge']);
					
					// вписать изображение в прямоугольник заданного размера (обрезав края)
					elseif($transformType == self::T_CROP)
						$dstImgRs = $this->_resizeCrop($dstFullName, $copy['width'], $copy['height'], $copy['canEnlarge']);
						
					else
						trigger_error('Неизвестный тип преобразования.', E_USER_ERROR);
					
					// наложить водяной знак (если требуется)
					if(!empty($additParams['watermark']))
						$dstImgRs = $this->_watermarkRs($dstImgRs, $additParams['watermark'][0], $additParams['watermark'][1]);
			
					// сохранение изображения
					$this->_saveImgRs($srcType, $dstImgRs, $dstFullName);
				}
				
			}
		}
		
		return $this->_completedImages
			? $dstName
			: FALSE;
	
	}
	
	// ПОЛУЧИТЬ СООТНОШЕНИЕ МИНИАТЮРА / ОРИГИНАЛ
	private function _getRatio($srcWidth, $srcHeight, $dstWidth, $dstHeight, $transformType, $canEnlarge){
		
		if($transformType == self::T_PROPORT){
		
			$ratio = min($dstWidth / $srcWidth, $dstHeight / $srcHeight);
			
		}elseif($transformType == self::T_CENTER){
		
			$ratio = min($dstWidth / $srcWidth, $dstHeight / $srcHeight);
			
		}elseif($transformType == self::T_CROP){
		
			$ratio = max($dstWidth / $srcWidth, $dstHeight / $srcHeight);
			
		}else{
		
			trigger_error('Неверный transformType', E_USER_ERROR);
		}
		
		return $ratio > 1 && !$canEnlarge
			? 1
			: $ratio;
	}
	
	// _RESIZE PROPORT
	private function _resizeProport($dstFullName, $dstMaxWidth, $dstMaxHeight, $canEnlarge){
		
		$srcImgFullName = !is_null($this->_reducedImgFullName) ? $this->_reducedImgFullName : $this->_fullName;
	
		// получение параметров исходного изображения
		list($srcWidth, $srcHeight, $srcType) = getimagesize($srcImgFullName);
		
		// получение ресурса исходного изображения
		$srcImgRs = $this->_getImgRs($srcType, $srcImgFullName);

		// получение размеров конечного изображения
		$ratio = (float)min($dstMaxWidth / $srcWidth, $dstMaxHeight / $srcHeight);
		$ratio = $ratio > 1 && !$canEnlarge ? 1 : $ratio;
		
		$dstWidth  = $srcWidth * $ratio;
		$dstHeight = $srcHeight * $ratio;
		
		// var_dump($dstMaxHeight); die;
		
		// выполнение ресайза
		$dstImgRs = imagecreatetruecolor($dstWidth, $dstHeight);
		imagecopyresampled($dstImgRs, $srcImgRs, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);	//imagecopyresized
		
		$this->_reducedImgFullName = $dstFullName;
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		
		$this->_completedImages++;
		
		return $dstImgRs;
	}
	
	// _RESIZE CENTER
	private function _resizeCenter($dstFullName, $dstFullWidth, $dstFullHeight, $bgColor, $canEnlarge){
		
		$srcImgFullName = $this->_fullName;
	
		// получение параметров исходного изображения
		list($srcWidth, $srcHeight, $srcType) = getimagesize($srcImgFullName);
		
		// получение ресурса исходного изображения
		$srcImgRs = $this->_getImgRs($srcType, $srcImgFullName);

		// получение размеров конечного изображения
		$ratio = (float)min($dstFullWidth / $srcWidth, $dstFullHeight / $srcHeight);
		$ratio = $ratio > 1 && !$canEnlarge ? 1 : $ratio;
		
		$dstWidth  = $srcWidth * $ratio;
		$dstHeight = $srcHeight * $ratio;
		
		// var_dump($dstMaxHeight); die;
		
		// выполнение ресайза
		$dstImgRs = imagecreatetruecolor($dstFullWidth, $dstFullHeight);
		
		// прозрачный цвет не поддерживается GD
		$bgColor = $bgColor == 'transparent' ? 'FFFFFF' : $bgColor;
		
		list($r, $g, $b) = self::colorHex2rgb($bgColor);
		$bg = imagecolorallocate($dstImgRs, $r, $g, $b);
		
		imagefill($dstImgRs, 0, 0, $bg);
		
		imagecopyresampled($dstImgRs,
						   $srcImgRs,
						   ($dstFullWidth > $dstWidth) ? ($dstFullWidth - $dstWidth) / 2 : 0, //$dst_x
						   ($dstFullHeight > $dstHeight) ? ($dstFullHeight - $dstHeight) / 2 : 0, //$dst_y
						   0, // $src_x
						   0, // $src_y
						   $dstWidth,  //$dst_w
						   $dstHeight, //$dst_h
						   $srcWidth,  //$src_w
						   $srcHeight  //$src_h
						   );
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		
		$this->_completedImages++;
		
		return $dstImgRs;
	}
	
	// _RESIZE CROP
	private function _resizeCrop($dstFullName, $dstCanvasWidth, $dstCanvasHeight, $canEnlarge){
		
		$srcImgFullName = $this->_fullName;
	
		// получение параметров исходного изображения
		list($srcWidth, $srcHeight, $srcType) = getimagesize($srcImgFullName);
		
		// получение ресурса исходного изображения
		$srcImgRs = $this->_getImgRs($srcType, $srcImgFullName);

		// получение размеров конечного изображения
		$ratio = (float)max($dstCanvasWidth / $srcWidth, $dstCanvasHeight / $srcHeight);
		$ratio = $ratio > 1 && !$canEnlarge ? 1 : $ratio;
		
		$dstWidth  = $dstCanvasWidth / $ratio;
		$dstHeight = $dstCanvasHeight / $ratio;
		
		// выполнение ресайза
		$dstImgRs = imagecreatetruecolor($dstCanvasWidth, $dstCanvasHeight);
		
		imagecopyresampled($dstImgRs,
						   $srcImgRs,
						   0,  //$dst_x
						   0,  //$dst_y
						   ($srcWidth > $dstWidth)  ? ($srcWidth - $dstWidth) / 2    : 0, // $src_x
						   ($srcHeight > $dstHeight) ? ($srcHeight - $dstHeight) / 2 : 0, // $src_y
						   $dstCanvasWidth,  //$dst_w
						   $dstCanvasHeight, //$dst_h
						   $dstWidth,  //$src_w
						   $dstHeight  //$src_h
						   );
		
		// освобождение ресурсов
		imagedestroy($srcImgRs);
		
		$this->_completedImages++;
		
		return $dstImgRs;
	}
	
	public static function colorHex2rgb($color){
		
		$originColor = $color;
		
		if ($color[0] == '#')
			$color = substr($color, 1);

		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
									 $color[2].$color[3],
									 $color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			trigger_error('Не удалось определить цвет по идентификатру "'.$originColor.'"', E_USER_ERROR);

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}
	
	// УДАЛИТЬ ИЗОБРАЖЕНИЕ
	public function remove(){
		
		if(!$this->_isFileExists)
			throw new Exception('Файл изображения не найден');
		
		// удаление
		if(@unlink($this->_fullName)){
		
			$this->_isFileExists = FALSE;
		}else{
			throw new Exception('Не удалось удалить изображение');
		}
	}
	
	// ПРОВЕРКА И СОЗДАНИЕ ПАПКИ
	public function checkDir($dir){
		
		if(!is_dir($dir))
			if(!is_writable(dirname($dir)) || !@mkdir($dir, 0777, TRUE))
				throw new Exception('Невозможно создать целевую папку');
		
		if(!is_writable($dir))
			throw new Exception('Невозможно произвести запись в целевую папку "'.$dir.'" (права: '.substr(sprintf('%o', fileperms($dir)), -4).').');
		
		return $this;
	}
	
	/**
	 * ПОЛУЧЕНИЕ ПУТИ И ИМЕНИ ФАЙЛА
	 * если параметр $fullName - путь, оканчивающийся слешем, то в качестве имени возвращается имя текущего файла
	 * иначе как путь, так и имя берутся из $fullName.
	 * @param string $fullName
	 * @param bool   $separateName - флаг, указывающий что имя файла и расширение возвращаются раздельно
	 * @return array (path, name)|(path, name, ext)
	 */
	public function getPathAndName($fullName, $separateName = FALSE){
	
		$path = '';
		$name = '';
		$ext = '';
		
		// если параметр - путь, оканчивающийся слешем
		if(substr($fullName, -1, 1) == '/'){
			$path = $fullName;
			$name = $this->_name;
		}
		// если параметр - путь вместе с именем файла
		else{
			$path = dirname($fullName).'/';
			$name = $this->_getBasename($fullName);
		}
		
		// имя отдельно, расширение отдельно
		if($separateName){
		
			$ext = $this->_getFileExt($name);
			$name = str_replace('.'.$ext, '', $name);
			return array($path, $name, $ext);
			
		}
		// имя вместе с расширением
		else{
		
			return array($path, $name);
		}
	}
	
	// ПОЛУЧИТЬ РАСШИРЕНИЕ ФАЙЛА
	private function _getFileExt($filename){
		return strtolower(substr(strrchr($filename, '.'), 1));
	}
	
	/**
	 * ПОЛУЧИТЬ ИМЯ ФАЙЛА БЕЗ ПУТИ
	 * @param string $fullName - полное имя файла
	 * @return string - имя файла без пути
	 */
	private function _getBasename($fullName){

		$fullName = str_replace('\\', '/', $fullName);
		if(substr($fullName, -1, 1) == '/')
			return '';
		$fullNameArr = explode('/', $fullName);
		return end($fullNameArr);
	}
	
	/**
	 * ПОЛУЧИТЬ РЕСУРС ИЗОБРАЖЕНИЯ
	 * @param int $imgType - тип изображения
	 * @param string $imgFile - путь к изображению
	 * @return resource - ресурс изображения
	 */
	private function _getImgRs($imgType, $imgFile){
	
		switch ($imgType){	
			case 1: return imagecreatefromgif ($imgFile);
			case 2: return imagecreatefromjpeg($imgFile);
			case 3: return imagecreatefrompng ($imgFile);
			default: throw new Exception('Неподдерживаемый тип изображения: '.$imgType);
		}
	}
	
	/**
	 * СОХРАНИТЬ РЕСУРС ИЗОБРАЖЕНИЯ
	 * @param int $imgType - тип изображения
	 * @param imgRs - ресурс изображения
	 * @param string $imgFullName - полное имя изображения
	 * @return void
	 */
	private function _saveImgRs($imgType, $imgRs, $imgFullName){
		
		// сохранение изображения
		switch ($imgType){
			case 1: imagegif ($imgRs, $imgFullName);      break;
			case 2: imagejpeg($imgRs, $imgFullName, 100); break;
			case 3: imagepng ($imgRs, $imgFullName);      break;
			default: throw new Exception('Неподдерживаемый тип изображения: '.$imgType);
		}
	}
}
