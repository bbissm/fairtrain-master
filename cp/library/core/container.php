<?php
class container extends module {
	protected $parts = [];
	protected $list = [];
	public static $auth = true;
	public static $authLevel = [1,2,3,4,5];

	public function add() {
		$config = $_GET; 

		$q = $this->db->query("SELECT sort FROM cms_container WHERE `name`='".$config["name"]."' AND is_deleted='0' ORDER BY sort DESC"); 
		$res = $q->fetch_row();
 
		$this->db->query("INSERT cms_container SET `key`='".$config["key"]."',sort=".((int)$res[0]+1).",`name`='".$config["name"]."'");	

		$q = $this->db->query("SELECT count(cms_container_id) FROM cms_container WHERE `name`='".$config["name"]."' AND is_deleted='0' ORDER BY sort DESC"); 
		$res = $q->fetch_row();

		if($_GET["position"]!="") { 
			$_GET["moveTo"] = $_GET["position"];
			$_GET["moveFrom"] = (int)$res[0]-1;  
			$_GET["container"] = $_GET["name"];  
			//print_r($_GET); 
			$this->move();  
		}
	}

	public function rmv($config=[]) { 
		if($_GET["id"]!="") {
			$config = $_GET;
		} 

		if($config["id"]!="") {
			$this->db->query("UPDATE cms_container SET is_deleted=1 WHERE cms_container_id=".$config["id"]);
			echo $config["id"];

			$q=$this->db->query("SELECT name, `key` FROM cms_container WHERE cms_container_id=".$config["id"]);
			$res=$q->fetch_assoc();
		} 
	}

	public function move() { 
		$where = "";

		$q = $this->db->query("SELECT cms_container_id FROM cms_container WHERE is_deleted='0' AND name='".$_GET["container"]."' ORDER BY sort ASC");
		echo $this->db->error; 
		$sort = [];  
		while($res = $q->fetch_row()) {
			array_push($sort, $res[0]); 
		}

		$nsort = [];
		if($_GET["moveTo"]==-1) {
			array_push($nsort, $sort[$_GET["moveFrom"]]);
		} 

		foreach($sort as $k=>$v) {
			if($k==$_GET["moveTo"]+1 && $_GET["moveTo"]!=-1) { 
				array_push($nsort, $sort[$_GET["moveFrom"]]);
			}
			array_push($nsort, $v); 
		}

		if($_GET["moveTo"]+1==count($sort)) {
			array_push($nsort, $sort[$_GET["moveFrom"]]);
		}   

		if($_GET["moveTo"]==-1) { 
			unset($nsort[$_GET["moveFrom"]+1]);
		} else if($_GET["moveFrom"]>$_GET["moveTo"]) {
			unset($nsort[$_GET["moveFrom"]+1]);
		} else {
			unset($nsort[$_GET["moveFrom"]]);
		} 

		$i = count($nsort);
		foreach($nsort as $v) {
			$this->db->query("UPDATE cms_container SET sort=$i WHERE cms_container_id=".$v); 
			echo $this->db->error;
			$i++; 
		}
	}
}
?>