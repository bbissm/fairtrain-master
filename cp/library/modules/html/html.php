<?php
class html extends module { 
	private $linklist = [];
	public static $auth = true;
	public static $authLevel = [1,2,3];

	public function settings() {
		$header = new header();
		$header->addTitle(translation::get("color_background"));
		echo $header->render();

		$table = new table(); 
		$table->setup(["form"=>["slide_to_element"=>"true","popup" => "true","method"=>"post","action"=>"/cp/async/html/settings","sqltable"=>"tbl_html","sqlwhere"=>["cms_container_fk"=>$_GET["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller();  
		
		$table->add(["cols"=>[translation::get("color"),$table->addFormField(["name"=>"background","type"=>"select","options"=>[0=>translation::get("white"),1=>translation::get("grey"),2=>translation::get("beige")]])]]);
		$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		echo $table->render();
	}
	
	public function update() {
		if($_GET["id"]!="") {
			$q = $this->db->query("SELECT html_id FROM tbl_html WHERE cms_container_fk='".$_GET["id"]."'");
			$res = $q->fetch_row(); 
			if($res[0]=="") {  
				$this->db->query("INSERT tbl_html SET html='".$_POST["html"]."',cms_container_fk='".$_GET["id"]."'");
			} else {
				$this->db->query("UPDATE tbl_html SET html='".$_POST["html"]."' WHERE cms_container_fk='".$_GET["id"]."'"); 
			
			} 
			echo $this->db->error;
		} 
	}

	public function file() {
		$this->linklist = [""=>translation::get("select")]; 
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("hyperlink_title"));
		$header->addParagraph(translation::get("hyperlink_text")); 
		echo $header->render();

		$table = new table(); 
		$table->addTitle(["cols"=> [translation::get("hyperlink_title")]]);
		$table->add(["cols"=>[translation::get("file"),$table->addFormField(["name"=>"url","type"=>"hidden"]).$table->addFileSelect(["target"=>"url"])]]);
		echo $table->render();    
	}  

	public function image() {
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("image_title"));
		$header->addParagraph(translation::get("image_text")); 
		echo $header->render();

		$table = new table(); 
		$table->setup(["td"=>[120]]);
		$table->controller();  

		$table->addTitle(["cols"=> [translation::get("image_title")]]);
		$table->add(["cols"=>[translation::get("file"),$table->addFormField(["name"=>"url","type"=>"hidden"]).$table->addImageSelect(["target"=>"url","attr"=>["maxWidth"=>1920,"maxHeight"=>800]])]]);
		echo $table->render();     
	}  

	public function linkList() {
		$this->linklist = [""=>translation::get("select")]; 
		$this->navigation(0,0,$_SESSION["lang"]["key"],"/".$_SESSION["lang"]["short"]."/");

		echo json_encode($linklist);
	}

	public function table() {
		$this->linklist = [""=>translation::get("select")]; 
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("table_title"));
		$header->addParagraph(translation::get("table_text")); 
		echo $header->render();

		$table = new table(); 
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/html/table","sqltable"=>"tbl_html","sqlwhere"=>["cms_container_fk"=>$config["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller();  

		$table->addTitle(["cols"=> [translation::get("table_title")]]);
		$table->add(["cols"=>[translation::get("cols"),$table->addFormField(["name"=>"cols","type"=>"text"])]]); 
		$table->add(["cols"=>[translation::get("rows"),$table->addFormField(["name"=>"rows","type"=>"text"])]]);
		$table->add(["cols"=>[translation::get("class"),$table->addFormField(["name"=>"class","type"=>"select","options"=>["regular"=>"","greyboxed"=>"greyboxed","grey"=>"grey","accordeon"=>"accordeon","red"=>"red","tworow"=>"two row"]])]]); 
		$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$_GET["id"]]),$table->addFormField(["name"=>"add","type"=>"button","value"=>translation::get("add")])]]);
		echo $table->render();     
	} 

	public function copyNoFormat() {
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("copyNoFormat_title"));
		$header->addParagraph(translation::get("copyNoFormat_text")); 
		echo $header->render();

		$table = new table(); 
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/html/copyNoFormat","sqltable"=>"tbl_feed_article","sqlwhere"=>["cms_container_fk"=>$config["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller();  

		$table->addTitle(["cols"=> [translation::get("copyNoFormat_title")]]);
		$table->add(["cols"=>[translation::get("text"),$table->addFormField(["name"=>"text","type"=>"textarea"])]]); 
		$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$_GET["id"]]),$table->addFormField(["name"=>"add","type"=>"button","value"=>translation::get("add")])]]);
		echo $table->render();    
	}  

	public function navigation($stage,$fk,$lang,$link) {
		$q = $this->db->query("SELECT cms_navigation_id,
										  n.cms_navigation_fk as cms_navigation_fk,
										  p.name as name, 
										  p.is_active as is_active,
										  is_visible,
										  cms_navigation_page_id,
										  permalink
										  FROM cms_navigation as n,
										  cms_navigation_page as p
										  WHERE p.cms_navigation_fk=cms_navigation_id 
										  AND n.cms_navigation_fk='$fk' 
										  AND is_deleted=0
										  AND cms_lang_fk=$lang
										  ORDER BY sort ASC");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			$offset = "";
			for($i=0;$i<$stage;$i++) {
				$offset .= "--";
			}
			if($offset!="") $offset.=" ";
			$this->linklist[$link.$res["permalink"]] = $offset.$res["name"];

			$this->navigation($stage+1,$res["cms_navigation_id"],$lang,$link.$res["permalink"]."/");
		}
	}
}
?>