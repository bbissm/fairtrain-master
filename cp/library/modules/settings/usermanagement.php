<?php
class usermanagement extends module {
	private $success;
	public static $auth = true;
	public static $authLevel = [1,2,3,4,5];

	public function setup() {
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

	public function controller() {
		if($this->user["type"]<=2) {
			if($_GET["add"]!="") {
				if($_POST["email"]!="" && $_POST["password"]!="") {
					$this->db->query("INSERT cms_user SET cms_user_type_fk='".$_POST["cms_user_type_fk"]."',email='".$_POST["email"]."',firstname='".$_POST["firstname"]."',lastname='".$_POST["lastname"]."',password='".password_hash($_POST["password"],PASSWORD_DEFAULT)."'");
					$this->success=1;
				}
			} else if($_GET["edit"]!="") {
				if($_POST["email"]!="" && $_POST["password"]=="") {
					$this->db->query("UPDATE cms_user SET cms_user_type_fk='".$_POST["cms_user_type_fk"]."',email='".$_POST["email"]."',firstname='".$_POST["firstname"]."',lastname='".$_POST["lastname"]."' WHERE cms_user_id='".$_GET["edit"]."'");
					$this->success=1;
				} else if($_POST["email"]!="" && $_POST["password"]!="") {
					$this->db->query("UPDATE cms_user SET cms_user_type_fk='".$_POST["cms_user_type_fk"]."',email='".$_POST["email"]."',firstname='".$_POST["firstname"]."',lastname='".$_POST["lastname"]."',password='".password_hash($_POST["password"],PASSWORD_DEFAULT)."' WHERE cms_user_id='".$_GET["edit"]."'");
					$this->success=1;
				}
			}
		} else {
			if($_POST["email"]!="" && $_POST["password"]=="") {
				$this->db->query("UPDATE cms_user SET email='".$_POST["email"]."',firstname='".$_POST["firstname"]."',lastname='".$_POST["lastname"]."' WHERE cms_user_id='".$this->user["id"]."'");
				$this->success=1;
			} else if($_POST["email"]!="" && $_POST["password"]==$_POST["retype"] && $_POST["password"]!="") {
				$this->db->query("UPDATE cms_user SET email='".$_POST["email"]."',firstname='".$_POST["firstname"]."',lastname='".$_POST["lastname"]."',password='".password_hash($_POST["password"],PASSWORD_DEFAULT)."' WHERE cms_user_id='".$this->user["id"]."'");
				$this->success=1;
			}
		}
	} 

	public function view() {
		$header = new header();
		$header->addTitle(translation::get("usermanagement_title"));
		$header->addParagraph(translation::get("usermanagement_text"));
		echo $header->render(); 

		$type = [];
		$q = $this->db->query("SELECT cms_user_type_id,name FROM cms_user_type ORDER BY cms_user_type_id ASC");
		while($res = $q->fetch_assoc()) {
			$type[$res["cms_user_type_id"]] = $res["name"];
		}

		if($this->user["type"]<=2) {
			if($_GET["edit"]!="" || $_GET["add"]!="") {
				if($_GET["add"]!="") {
					$_GET["edit"] = -1;		
				}
				$q = $this->db->query("SELECT email,firstname,lastname,cms_user_type_fk FROM cms_user WHERE cms_user_id='".$_GET["edit"]."'");
				$res = $q->fetch_assoc();

				$table = new table();
				$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/usermanagement/view"],"td"=>[120]]);
				$table->controller();
				$table->addTitle(["cols"=> [translation::get("settings")]]);
				$table->add(["cols"=>[translation::get("userlevel"),$table->addFormField(["name"=>"cms_user_type_fk","type"=>"select","options"=>$type,"value"=>$res["cms_user_type_fk"],"attr"=>["drequired"=>true]])]]);
				$table->add(["cols"=>[translation::get("firstname"),$table->addFormField(["name"=>"firstname","type"=>"text","value"=>$res["firstname"],"attr"=>["drequired"=>true]])]]);
				$table->add(["cols"=>[translation::get("lastname"),$table->addFormField(["name"=>"lastname","type"=>"text","value"=>$res["lastname"],"attr"=>["drequired"=>true]])]]); 
				$table->add(["cols"=>[translation::get("mail"),$table->addFormField(["name"=>"email","type"=>"email","value"=>$res["email"],"attr"=>["drequired"=>true]])]]);
				$table->add(["cols"=>[translation::get("password"),$table->addFormField(["name"=>"password","type"=>"password"])]]);
				$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);  
				echo $table->render();

				if($this->success==1) {  
					echo cp::message(["message"=>translation::get("success"),"type"=>"success"]);
				} else if($this->success==-1) {  
					echo cp::message(["message"=>translation::get("error"),"type"=>"error"]);
				}
			} else {
				$table = new table();
				$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/usermanagement/view","sqltable"=>"cms_user"],"td"=>[120,0,100]]);
				$table->controller();
				$table->addTitle(["cols"=> [translation::get("settings")],"controls"=>["/cp/usermanagement?add=1"=>translation::get("add")]]);
				$table->addSubtitle(["cols"=>[translation::get("email"),translation::get("userlevel"),translation::get("lastchanged")]]);
	 
				$q = $this->db->query("SELECT cms_user_id,email,cms_user_type_fk,DATE_FORMAT(timestamp,'%d.%m.%Y %H:%i') as timestamp FROM cms_user ORDER BY cms_user_type_fk ASC, email ASC");
				while($res = $q->fetch_assoc()) {
					if($this->user["id"]==$res["cms_user_id"]) {
						$table->add(["cols"=>[$res["email"],$type[$res["cms_user_type_fk"]],$res["timestamp"]],"controls"=>["/cp/usermanagement?edit=".$res["cms_user_id"]=>translation::get("edit")]]);
					} else {
						$table->add(["cols"=>[$res["email"],$type[$res["cms_user_type_fk"]],$res["timestamp"]],"controls"=>["/cp/usermanagement?edit=".$res["cms_user_id"]=>translation::get("edit"),"/cp/async/usermanagement/view?prmv=".$res["cms_user_id"]=>translation::get("rmv")]]);
					}
				}

				echo $table->render();
			}
		} else {
			$q = $this->db->query("SELECT email,firstname,lastname FROM cms_user WHERE cms_user_id='".$this->user["id"]."'");
			$res = $q->fetch_assoc();

			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/usermanagement/view"],"td"=>[120]]);
			$table->controller();
			$table->addTitle(["cols"=> [translation::get("settings")]]);
			$table->add(["cols"=>[translation::get("firstname"),$table->addFormField(["name"=>"firstname","type"=>"text","value"=>$res["firstname"],"attr"=>["drequired"=>true]])]]);
			$table->add(["cols"=>[translation::get("lastname"),$table->addFormField(["name"=>"lastname","type"=>"text","value"=>$res["lastname"],"attr"=>["drequired"=>true]])]]); 
			$table->add(["cols"=>[translation::get("mail"),$table->addFormField(["name"=>"email","type"=>"email","value"=>$res["email"],"attr"=>["drequired"=>true]])]]);
			$table->add(["cols"=>[translation::get("password"),$table->addFormField(["name"=>"password","type"=>"password"])]]);
			$table->add(["cols"=>[translation::get("retypepassword"),$table->addFormField(["name"=>"retype","type"=>"password"])]]);
			$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);  
			echo $table->render();

			if($this->success==1) {  
				echo cp::message(["message"=>translation::get("success"),"type"=>"success"]);
			}
		}
	} 
}
?> 