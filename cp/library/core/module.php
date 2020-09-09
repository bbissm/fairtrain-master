<?php
class module {
	use observer;

	Protected $db;
	Protected $user;
	Protected $parent;
	public static $auth = true;
	public static $authlevel = [];

	public function __construct() {
		$this->db = &$_SESSION["db"];
		$this->user = &$_SESSION["user"];
		$this->parent = $parent;
	}

	public function setup($config=[]) { 

	}

	public function htmlHead() {

	}

	public function controller() {

	}

	public function view() {

	}
}
?>