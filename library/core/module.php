<?php
class module {
	use observer;
	
	Protected $db;
	Protected $user;
	Protected $parent;

	public function __construct() {
		$this->db = &$_SESSION["db"];
		$this->user = &$_SESSION["user"];
		$this->parent = $parent;
	}

	public function setup($config=[]) {

	}

	public function controller() {

	}


}
?>