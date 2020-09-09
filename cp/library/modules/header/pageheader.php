<?php
class pageheader extends module { 
	public static $auth = true;
	public static $authLevel = [1,2,3];
	
	public function edit() {
		if($_GET["h1"]!="") {
			$q = $this->db->query("SELECT header_id FROM tbl_header WHERE cms_container_fk='".$_GET["h1"]."'");
			$res = $q->fetch_assoc();

			if($res["header_id"]!="") {
				$this->db->query("UPDATE tbl_header SET title='".strip_tags($_POST["text"])."',timestamp=timestamp WHERE cms_container_fk='".$_GET["h1"]."'");
			} else {
				$this->db->query("INSERT tbl_header SET title='".strip_tags($_POST["text"])."',color='blue',timestamp='".date("Y-m-d H:i:s")."',cms_container_fk='".$_GET["h1"]."'");
			}
		}

		if($_GET["p"]!="") { 
			$q = $this->db->query("SELECT header_id FROM tbl_header WHERE cms_container_fk='".$_GET["p"]."'");
			$res = $q->fetch_assoc();

			if($res["header_id"]!="") {
				$this->db->query("UPDATE tbl_header SET preview='".strip_tags($_POST["text"])."',timestamp=timestamp WHERE cms_container_fk='".$_GET["p"]."'");
			} else {
				$this->db->query("INSERT tbl_header SET preview='".strip_tags($_POST["text"])."',color='blue',timestamp='".date("Y-m-d H:i:s")."',cms_container_fk='".$_GET["p"]."'");
			}
		}
	}

	public function settings() { 
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("header_title"));
		$header->addParagraph(translation::get("header_text")); 
		echo $header->render(); 
 
		$table = new table();
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/pageheader/settings","sqltable"=>"tbl_header","sqlwhere"=>["cms_container_fk"=>$config["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller(); 
		$table->addTitle(["cols"=> [translation::get("header_title")]]);
		$table->add(["cols"=>[translation::get("color"),$table->addFormField(["name"=>"color","type"=>"select","options"=>["blue"=>translation::get("blue"),"yellow"=>translation::get("yellow"),"red"=>translation::get("red")]])]]);
		$table->add(["cols"=>[translation::get("titel"),$table->addFormField(["name"=>"title","type"=>"text","attr"=>["maxlength"=>150,"drequired"=>true]])]]);
		$table->add(["cols"=>[translation::get("text"),$table->addFormField(["name"=>"preview","type"=>"textarea","attr"=>["maxlength"=>450]])]]);  
		$table->add(["cols"=>[translation::get("center"),$table->addFormField(["name"=>"center","type"=>"checkbox","set"=>1])]]);  
		$table->add(["cols"=>[translation::get("channels"),$table->addFormField(["name"=>"channel","type"=>"checkbox","set"=>1])]]);  
		$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$_GET["id"]]),$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		echo $table->render();    
	} 
}
?>