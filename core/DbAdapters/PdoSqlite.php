<?php

class DbAdapter_PdoSqlite extends DbAdapter_PdoAbstract {

	protected function _getPdoInstance() {
		return new PDO("sqlite:{$this->connDatabase}");
	}

	/** выбрать базу данных */
	public function selectDb($db){}

    public function quoteFieldName($field){
        return '"'.$field.'"';
    }

}
