<?php
class Set {
	private $table = array();
	public function __construct($list) {
		$this->add($list);
	}
	public function add($list) {
		if(is_array($list)) {foreach($list as $element) if($element != '') $this->table[$element] = null;}
		else if($list != '') $this->table[$list] = null;
	}
	public function contains($value) {
		return array_key_exists($value, $this->table);
	}
	public function values() {
		$values = array_keys($this->table);
		sort($values);
		return $values;
	}
}
?>
