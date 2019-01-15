<?php
class FrontData extends Data {
	private $db = null;
	public function __construct(&$db) {
		parent::__construct();
		$this->db = $db;
	}
}
?>
