<?php
class container extends module {
	protected $parts = [];
	protected $list = [];
	protected $fk = "";
	protected $order = "";
	protected $id;
	protected $config;
	protected $skip;

	public function setup($config) {
		$this->parts = $config["parts"]; 
		$this->fk = $config["name"]; 
		$this->order = $config["order"];
		$this->skip = $config["skip"];
		$this->custom = $config["custom"];
	}

	public function load($config=[]) { 
		$this->config = $config;

		$limit = "";

		if((int)$config["count"]>0) {
			$limit = " LIMIT ".(int)$config["start"].",".(int)$config["count"];  
		}   

		if($this->order=="") {
			$this->order = "ORDER BY sort ASC";
		}

		$skip = "";
		if($this->skip!=null && $this->skip!="") {
			$skip = "AND `name`<>'".$this->skip."'";
		}

		if(stristr($this->fk,"%")) {
			$q = $this->db->query("SELECT cms_container_id,`key`,timestamp FROM cms_container WHERE `name` LIKE '".str_replace("_","\_",$this->fk)."' $skip ".$this->custom." AND is_deleted='0' ".$this->order.$limit); 
		} else {
			$q = $this->db->query("SELECT cms_container_id,`key`,timestamp FROM cms_container WHERE `name`='".$this->fk."' $skip ".$this->custom." AND is_deleted='0' ".$this->order.$limit); 
		}
		echo $this->db->error; 
		while($res = $q->fetch_assoc()) { 
			array_push($this->list, $res); 
		}
	}

	/*public function get() {
		$config = $_GET;

		$q = $this->db->query("SELECT cms_container_id,`key` FROM cms_container WHERE cms_container_id='".$config["id"]."'");
		$res = $q->fetch_assoc();
		$value = $res;  

		include $value["key"];
	}*/  

	public function attr() {
		if($_SESSION["login"]) { 
			echo " key=\"".$this->id."\" cms=\"true\""; 
		}
	} 

	public function controls($config) {
		if($_SESSION["login"]) {
			if(!stristr($this->order,"timestamp")) {
				echo "<div class=\"move\" style=\"display:none;\"></div>";
			}

			echo "<div class=\"controls\" style=\"display:none;\">";
				echo "<ul>";
				foreach($config as $key=>$value) { 
					if(is_array($value)) {
						echo "<li><a href=\"$key\" target=\"".$value["target"]."\">".$value["value"]."</a></li>";
					} else { 
						echo "<li><a href=\"$key\">$value</a></li>";
					}
				}
				echo "</ul>";
			echo "</div>";
		}
	}
 
	public function render() { 
		ob_start();

		if($_GET["path"]!="") {
			$_SESSION["path"]=explode("/",$_GET["path"]); 
		}

		if($_GET["load"]!="") { 
			if($_GET["arguments"]!="") {
				$_GET["arguments"] = unserialize(base64_decode($_GET["arguments"]));  
			}

			$this->setup(["name"=>$_GET["load"],"order"=>$_GET["order"],"skip"=>$_GET["skip"],"custom"=>$_GET["custom"]]);
			$this->load(["start"=>$_GET["start"],"count"=>$_GET["count"],"arguments"=>$_GET["arguments"]]); 
		}

		if($_SESSION["login"] && $_GET["load"]=="") {
			echo "<container-control parent=\"".$this->fk."\" style=\"display:none;\">";
			if(count($this->parts)>0) {
				echo "<div>".translation::get("add")."</div>"; 
			} else {
				echo "<div>&nbsp;</div>"; 
			}
			foreach($this->parts as $key=>$value) {
				echo "<button class=\"container-button\" key=\"".$key."\" container=\"".$this->fk."\">".$value."</button>";
			} 
			echo "</container-control>"; 

			echo "<container name=\"".$this->fk."\" path=\"".implode("/", $_SESSION["path"])."\" start=\"".$this->config["start"]."\" skip=\"".$this->skip."\" custom=\"".$this->custom."\" arguments=\"".base64_encode(serialize($this->config["arguments"]))."\" count=\"".$this->config["count"]."\" order=\"".$this->order."\">";  
			foreach($this->list as $value) { 
				$this->id = $value["key"];  
				$value["arguments"] = $this->config["arguments"];
				
				if($value["key"]!="") include $value["key"];  
			}
			echo "</container>";
		} else {  
			$_GET["load"]="";
			foreach($this->list as $value) {
				$this->id = $value["key"];
				$value["arguments"] = $this->config["arguments"];

				if($value["key"]!="") include $value["key"]; 
			}
		} 
 
		if($_GET["output"]!="") {
			echo ob_get_clean();
		} else {
			return ob_get_clean(); 
		}
	}
}
?>