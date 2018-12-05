<?php
class FrontData extends Data {
	public $pagename = '';
	public $config = null;
	public $models = null;
	private $db = null;
	public function __construct(&$db) {
		parent::__construct();
		$this->db = $db;
		$this->config = $this->db->config();
		$this->models = $this->db->models();
	}
}
?>
