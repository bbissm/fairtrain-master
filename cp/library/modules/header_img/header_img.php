<?php
class header_img extends module { 
	public static $auth = true;
	public static $authLevel = [1,2,3,4];
	
	public function settings() {
		$config = $_GET;

		$header = new header();
		$header->addTitle(translation::get("img_title"));
		$header->addParagraph(translation::get("img_text")); 
		echo $header->render();
 
		$table = new table();
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/header_img/settings","sqltable"=>"tbl_header_img","sqlwhere"=>["cms_container_fk"=>$config["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller(); 
		$table->addTitle(["cols"=> [translation::get("img_title")]]);
		$table->add(["cols"=>[translation::get("img"),$table->addFormField(["name"=>"img_path","type"=>"text"])]]);
		$table->add(["cols"=>["",$table->addImageSelect(["target"=>"img_path","attr"=>["maxWidth"=>1920,"maxHeight"=>800]])]]); 
		//$table->add(["cols"=>[translation::get("html"),$table->addFormField(["name"=>"html","type"=>"textarea"])]]);
		$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$_GET["id"]]),$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		echo $table->render();   
	} 
}
?>