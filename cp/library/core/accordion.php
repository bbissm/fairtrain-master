<?php
class accordion {
	private $accordion = [];
	private $accordionClass = [];
	private $accordionAttr = [];
	private $hasControls = false;

	public function setup($config) {
		if($config["class"]!=null) {
			$this->accordionClass = $config["class"]; 
		}

		$this->accordionAttr = $config["attr"]; 
	}

	public function add($config) {
		if($config["controls"]!=null) {
			$this->hasControls = true;
		}
		array_push($this->accordion,["title"=>$config["title"],
									"content"=>$config["content"],
									"class"=>$config["class"],
									"style"=>$config["style"],
									"attr"=>$config["attr"],
									"active"=>$config["active"], 
									"controls"=>$config["controls"]]);
	} 

	public function render() { 
		ob_start();

		$class = "";
		$class = implode(" ", $this->accordionClass);
		if($class!="") $class=" ".$class;

		$attr = "";
		if($this->accordionAttr!=null) {
			foreach($this->accordionAttr as $key=>$value) {
				$attr.=" $key=\"$value\""; 
			}
		}

		echo "<table class=\"accordion".$class."\"$attr>"; 
		foreach($this->accordion as $entry) {
			if($entry["active"]) {
				$class = " active";
			} else {
				$class = "";
			}
			if($entry["class"]!=null) {
				$class = implode(" ", $entry["class"]);
				if($class!="") $class = " ".$class;
			}

			$attr = "";
			if($entry["attr"]!=null) {
				foreach($entry["attr"] as $key=>$value) {
					$attr.=" $key=\"$value\"";
				}
			}

			$style = "";
			if($entry["style"]!=null) {
				foreach($entry["style"] as $key=>$value) {
					$style.=$key.":".$value.";";
				} 
			} 
			if($style!="") $style=" style=\"$style\"";

			echo "<tr class=\"accordion_title$class\"$attr$style><td>".$entry["title"]."</td>";

			if(count($entry["controls"])>0) {
				echo "<td$style class=\"config\">";
					if($entry["controls"]["style"]!="") {
						echo "<div class=\"icon ".$entry["controls"]["style"]."\">"; 
					} else {
						echo "<div class=\"icon\">";
					}
						echo "<ul>";
							foreach($entry["controls"] as $key=>$value) {
								if($key=="style") continue;
								echo "<li><a href=\"".$key."\">".$value."</a></li>";
							}
						echo "</ul>";
					echo "</div>";
				echo "</td>";
			} else if($this->hasControls) {
				echo "<td class=\"config\"></td>";
			}

			echo "</tr>";
			echo "<tr class=\"accordion_row";
			if($entry["active"]) {
				echo " active"; 
			}
			echo "\"><td";
			if($this->hasControls) {
				echo " colspan=\"2\"";
			}
			echo ">".$entry["content"]."</td></tr>";
		}
		echo "</table>";
	
		return ob_get_clean();
	}
}
?>