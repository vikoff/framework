<?php

class Cmd {
	
	/**
	 * Метод разбирает переданные скрипту параметры в массив.
	 * @param  array $scheme массив именованых параметров вида
	 *         array('param1' => '-p --param1', 'param2' => '-k --param2 --param3')
	 * @return array разобранный массив параметров
	 */
	public static function parseArgs($scheme = array()) {
		global $argv;

		$argsParsed = array();
		$argvSliced = array_slice($argv, 1);
		$skipIndexes = array();

		foreach ($argvSliced as $index => $v) {

			if (isset($skipIndexes[$index]))
				continue;

			if (preg_match('/^(--\w+|-\w+)(=(.+))?$/', $v, $matches)) {
				$keyRaw = $matches[1];

				if (preg_match('/^-(\w\w+)$/', $keyRaw, $matches1)) {
					foreach (str_split($matches1[1]) as $key) {
						$argsParsed['-'.$key] = true;
					}
					continue;
				}

				$key = $keyRaw;
				$value = isset($matches[3]) ? $matches[3] : null;
				if (!$value && isset($argvSliced[$index + 1]) && $argvSliced[$index + 1]{0} != '-') {
					$value = $argvSliced[$index + 1];
					$skipIndexes[$index + 1] = true;
				}
				$argsParsed[$key] = $value === null ? true : $value;
			}
		}

		if ($scheme) {
			$output = array();
			foreach ($scheme as $param => $keys) {
				$keys = is_array($keys) ? $keys : explode(' ', $keys);
				foreach ($keys as $key) {
					if (isset($argsParsed[$key])) {
						$output[$param] = $argsParsed[$key];
						break;
					}
				}
				if (!isset($output[$param])) {
					$output[$param] = null;
				}
			}
		} else {
			$output = $argsParsed;
		}

		return $output;
	}

	public static function printLn($text) {
		echo date('Y-m-d H:i:s').' '.$text."\n";
	}
	
	public static function confirm($text, $default = null) {

		$y = $default === true ? 'Y' : 'y';
		$n = $default === false ? 'N' : 'n';
		$result = strtolower(self::readLn("$text [$y/$n]"));
		if (!strlen($result)) 
			return !is_null($default) ? $default : false;
		else
			return in_array($result, array('y', 'yes', 'д', 'да'));
	}
	
	public static function readLn($text) {
		echo  $text.': ';
		return trim(fgets(STDIN));
	}
}
 
