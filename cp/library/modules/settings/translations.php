<?php
class translations extends module {
	private $langs = [];
	private $success = 0;
	public static $auth = true;
	public static $authLevel = [1,2,3];

	public function setup() {
		$l = $this->db->query("SELECT cms_lang_id,name,is_default FROM cms_lang ORDER BY is_default DESC, name ASC");
		while($lang = $l->fetch_assoc()) {	
			array_push($this->langs, $lang);
		}

		if($this->user["type"]==1) {
			cp::addSubnavigation([
				"/cp/usermanagement"=>translation::get("subnavigation_usermanagement"),
				"/cp/translations"=>translation::get("subnavigation_translation"),
				"/cp/language"=>translation::get("subnavigation_language"),
				"/cp/htaccess"=>translation::get("subnavigation_htaccess"),
				"/cp/sass"=>translation::get("subnavigation_sass")
			]);
		} else if($this->user["type"]<=2) {
			cp::addSubnavigation([
				"/cp/usermanagement"=>translation::get("subnavigation_usermanagement"),
				"/cp/translations"=>translation::get("subnavigation_translation"),
				"/cp/language"=>translation::get("subnavigation_language")
			]);
		}
	}

	public function htmlHead() {
		
	}
 
	public function controller() {
		if($_POST["save"]!="") {
			if($_POST["key"]!="") {
				foreach($this->langs as $lang) { 
					$_POST["value_S_".$lang["cms_lang_id"]] = urldecode($_POST["value_S_".$lang["cms_lang_id"]]);
					if($_POST["value_S_".$lang["cms_lang_id"]]!="") {
						$this->db->query("INSERT INTO cms_translation SET id='".$_POST["key"]."',value='".$_POST["value_S_".$lang["cms_lang_id"]]."',cms_lang_fk='".$lang["cms_lang_id"]."'");
					}
				}
			}

			foreach($_POST as $key=>$value) {
				$id = explode("_S_",$key);
				$key = $id[0];
				$lid = $id[1]; 
				if($key!="key" && $value!="" && $key!="value") {
					$value = urldecode($value);

					$q = $this->db->query("SELECT cms_translation_id,value FROM cms_translation WHERE id='$key' AND cms_lang_fk='$lid'");
					$res = $q->fetch_assoc();
					if($res["cms_translation_id"]!="") {
						$this->db->query("UPDATE cms_translation SET value='$value' WHERE id='$key' AND cms_lang_fk='$lid'");
					} else {
						$this->db->query("INSERT INTO cms_translation SET id='".$key."',value='".$value."',cms_lang_fk='".$lid."'");
					}
				}
			}  

			$this->success=1;
		} 

		if($_GET["rmv"]!="") { 
			$this->db->query("DELETE FROM cms_translation WHERE id='".$_GET["rmv"]."'");
		}
	}

	public function view() {
		$header = new header(); 
		$header->addTitle(translation::get("translation_title")); 
		$header->addParagraph(translation::get("translation_text"));
		echo $header->render();
 
 		$colsize = [200]; 
		$cols = [];
		array_push($cols, translation::get("translation_table_key"));
		foreach($this->langs as $lang) { 
			array_push($cols, $lang["name"]);
			array_push($colsize, 0); 
		}
		array_push($cols, "");
		array_push($colsize, 120); 

		$table = new table();
		$table->setup(["form"=> ["method"=>"post","action"=>"/cp/async/translations/view","class"=>["async"]],"td"=> $colsize]); 
		$table->addTitle(["cols"=> [translation::get("translation_table_title")]]);
		$table->addSubtitle(["cols"=> $cols]);
		
		$cols = [];
		array_push($cols,$table->addFormField(["type"=>"text","name"=>"key"]));
		foreach($this->langs as $lang) { 
			array_push($cols,$table->addFormField(["type"=>"textarea","name"=>"value_S_".$lang["cms_lang_id"]]));
		}
		array_push($cols,$table->addFormField(["type"=>"submit","name"=>"save","value"=>translation::get("translation_save")])); 

		$table->add(["cols"=> $cols]);

		$q = $this->db->query("SELECT id,value,DATE_FORMAT(last_updated,'%d.%m.%Y %H:%i:%s') as last_updated FROM cms_translation WHERE cms_lang_fk='".$this->langs[0]["cms_lang_id"]."' ORDER BY id ASC"); 
		while($res = $q->fetch_assoc()) {
			$cols = [];
			array_push($cols, $res["id"]);
			$i = 0;
			foreach($this->langs as $lang) { 
				$q1 = $this->db->query("SELECT id,value,DATE_FORMAT(last_updated,'%d.%m.%Y %H:%i:%s') as last_updated FROM cms_translation WHERE id='".$res["id"]."' AND cms_lang_fk='".$lang["cms_lang_id"]."' ORDER BY id ASC"); 
				$res1 = $q1->fetch_assoc(); 

				if($i==0) {
					array_push($cols, $table->addFormField(["type"=>"textarea","name"=>$res["id"]."_S_".$lang["cms_lang_id"],"value"=>$res1["value"],"attr"=>["drequired"=>true]]));
				} else {
					array_push($cols, $table->addFormField(["type"=>"textarea","name"=>$res["id"]."_S_".$lang["cms_lang_id"],"value"=>$res1["value"]]));
				}
				$i++;
			}
			array_push($cols, $res["last_updated"]);
 
			$table->add(["cols"=> $cols,
				"controls"=>["/cp/async/translations/view?rmv=".$res["id"]=>translation::get("rmv")]
			]);
		} 
		echo $table->render(); 

		if($this->success) {
			echo cp::message(["message"=>translation::get("success"),"type"=>"success"]);
		}
	}
}
?>