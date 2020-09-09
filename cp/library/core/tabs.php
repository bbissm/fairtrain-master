<?php
class tabs {
	private $tabs = [];
	private $activeTab = 0;
	private $pushurl = "";

	public function setup($config) {
		if($config["active"]!="") {
			$this->activeTab = $config["active"];
		}
		if($config["push"]!="") {
			$this->pushurl=$config["push"]; 
		}
	}

	public function add($name,$content) {
		array_push($this->tabs, ["name"=>$name,"content"=>$content]);
	}

	public function render() {
		ob_start();

		echo "<div class=\"tabsystem\"";
		if($this->pushurl!="") { 
			echo " push=\"".$this->pushurl."\""; 
		}
		echo ">";
			
			echo "<div class=\"tabs\">";
				foreach($this->tabs as $key=>$value) {
					echo "<div class=\"tab";
					if($key==$this->activeTab) {
						echo " active";
					}
					echo "\">".$value["name"]."</div>";
				}
			echo "</div>";

			foreach($this->tabs as $key=>$value) {
				echo "<div class=\"tabcontent";
				if($key==$this->activeTab) {
					echo " active";
				}
				echo "\">"; 
					echo $value["content"];
				echo "</div>";
			}

		echo "</div>";
		
		return ob_get_clean();
	}
}
?> 