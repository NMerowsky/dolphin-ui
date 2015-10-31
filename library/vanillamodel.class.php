<?php
class VanillaModel extends SQLQuery {
	protected $_model;

	function __construct() {
		
		global $inflect;

		$this->connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
		$this->_model = get_class($this);
		echo $this->_model;
		$this->_table = strtolower($inflect->pluralize($this->_model));
	}

	function __destruct() {
	}
}
