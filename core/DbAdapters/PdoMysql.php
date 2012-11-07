<?php

class DbAdapter_PdoMysql extends DbAdapter_PdoAbstract {

	protected function _getPdoInstance() {

		$options = array();

		if ($this->_encoding)
			$options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$this->_encoding}";

		$dsn = "mysql:host={$this->connHost}";
		if ($this->connDatabase)
			$dsn .= ";dbname={$this->connDatabase}";

		return new PDO($dsn, $this->connUser, $this->connPass, $options);
	}

	/** выбрать базу данных */
	public function selectDb($db) {

		$this->query('USE '.$db);
	}

}

?>