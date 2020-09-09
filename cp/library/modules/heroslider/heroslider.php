<?php
//Test
class heroslider extends module { 
	public static $auth = true;
	public static $authLevel = [1,2,3,4];
	public $success = 0;
	public function setup(){

	}

	public function controller(){
		
		if(!isset($_GET["add"]) && !isset($_GET["edit"])){
			if(isset($_POST["save"])){
				$overlay = 0;
				if($_POST["no_overlay"] != ""){
					$overlay = $_POST["no_overlay"];
				}
				$stmt = "UPDATE tbl_heroslider SET no_overlay=".$overlay." WHERE cms_container_fk='".$_GET["id"]."'";
				$query = $this->db->query($stmt);

				if($query){
					$this->success = 1;
				}
 			}
		}
				// Successmessage

		if ($this->success == 1) {

            echo cp::message(["message" => translation::get("success"), "type" => "success"]);

        } else if ($this->success == -1) {

            echo cp::message(["message" => translation::get("error"), "type" => "error"]);

        }
	}

	public function view(){
		if($_GET["add"] != "" || $_GET["edit"]!=""){
			$this->addHeroslider();
		} else {
			$this->showHeroslider();
		}
	}

	public function showHeroslider(){
		/*if($_GET["prmv"] != ""){
            $delete = "DELETE FROM tbl_heroslider WHERE heroslider_id=".$_GET["prmv"];
            $this->db->query($delete);
		}*/

		$config = $_GET;
		$header = new header();
		$header->addTitle(translation::get("heroslider_title"));
		$header->addParagraph(translation::get("heroslider_paragraph"));
		echo $header->render();	

		$table = new table();
		$table->setup(["td"=> [120],"form"=>["attr"=>["target"=>"#popup_content"],"action"=>"/cp/async/heroslider/view","method"=>"post","sqltable"=>"tbl_heroslider","sqlwhere"=>["cms_container_fk"=>$config["id"]]]]);
			$q = $this->db->query("SELECT no_overlay FROM tbl_heroslider WHERE cms_container_fk=".$config["id"]);
			$res = $q->fetch_assoc();
			$table->addTitle(["cols"=>[translation::get("overlay")]]);
			$table->addSubtitle(["cols"=>[translation::get("overlay_text")]]);
			$table->add(["cols"=>["Kein Overlay",$table->addFormField(["name"=>"no_overlay","type"=>"checkbox","set"=>1,"value"=>$res["no_overlay"]])]]);
			$table->add(["cols"=>[$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);

		echo $table->render();

		$table = new table();
		// Welches MySql Table und welche Methode und alle Formularattribute ansprechen
		$table->setup(["dragable"=>["type"=>1],"td"=> [120],"form"=>["attr"=>["target"=>"#popup_content"],"action"=>"/cp/async/heroslider/view","sqltable"=>"tbl_heroslider","sqlwhere"=>["cms_container_fk"=>$config["id"]]]]);
		// Automatische Ausführung durch CMS (core/)
		$table->controller();

		$table->addTitle(["cols"=>[translation::get("heroslider_verwalten")], "controls"=>["/cp/async/heroslider/view?id=".$config["id"]."&add=0"=>["name"=>translation::get("add"), "target"=>"#popup_content"]]]);
		$table->addSubtitle(["cols"=>["Text","Letztes Update"]]);

		$q = $this->db->query("SELECT heroslider_id, text, image, last_updated, no_overlay, sort FROM tbl_heroslider WHERE cms_container_fk=".$config["id"]." AND is_deleted=0 ORDER BY sort ASC");
		while($res = $q->fetch_assoc()){
			$table->add(["cols"=>[$res["text"],"<image src=\"".$res["image"]."\" width=\"200px\"/>",$res["last_updated"],$res["sort"]],"order"=>"sort ASC",
				"controls"=>[
					"/cp/async/heroslider/view?id=".$config["id"]."&edit=".$res["heroslider_id"].""=>["name"=>translation::get("edit"),"target"=>"#popup_content"],
					"/cp/async/heroslider/view?id=".$config["id"]."&prmv=".$res["heroslider_id"].""=>["name"=>translation::get("rmv"),"target"=>"#popup_content"]
				]
			]);
		}
		echo $table->render();


		
		
	}

	public function addHeroslider(){
		$config = $_GET;
        $date = date("d.m.Y");

        $header = new header();
        $header->addTitle(translation::get("heroslider_title"));
        $header->addParagraph(translation::get("heroslider_paragraph"));
        echo $header->render();

		$table = new table();
		$table->setup([
			"dragable"=>["type"=>1],
			"form"=>[
				"slide_to_element"=>true,
				"popup"=>true,
				"method"=>"post",
				"action"=>"/cp/async/heroslider/view",
				"sqltable"=>"tbl_heroslider",
				"sqlwhere"=>["heroslider_id"=>$config["edit"]],
				"attr"=>["target"=>"#popup_content"]
			],
			"td"=>[120]
		]);  
		$table->controller();
				$table->addTitle(["cols"=> [translation::get("heroslider_verwalten")]]);
				$table->addSubtitle(["cols"=>["Allgemein"]]);
				$table->add(["cols"=>[translation::get("image_src"),$table->addFormField(["name"=>"image","type"=>"text","attr"=>["drequired"=>true]])]]);
				$table->add(["cols"=>[translation::get("image_alt"),$table->addFormField(["name"=>"text","type"=>"text"])]]);
				$table->addSubtitle(["cols"=>["Medienquelle"]]);
				$table->add(["cols"=>["",$table->addImageSelectCustomController(["target"=>"image","attr"=>["cropping"=>"16:9","targetWidth"=>1920,"targetHeight"=>1080]])]]);
				$table->add(["cols"=>[
					$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$config["id"]]),
					$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])." ".$table->addFormField(["name"=>"back","type"=>"button","value"=>translation::get("back"),"attr"=>["href"=>"/cp/async/heroslider/view?id=".$config["id"],"style"=>"margin-left:10px;"]])
				]]);
		echo $table->render();

	}

}
?>
