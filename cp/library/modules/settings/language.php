<?php
class language extends module {
	public static $auth = true;
	public static $authLevel = [1,2,3];
	
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

	public function view() {		
		$header = new header();
		$header->addTitle(translation::get("language_title"));
		$header->addParagraph(translation::get("language_text"));
		echo $header->render();

		$fields = [
			"short",
			"name",
			"is_default"
		];

		$title = [
			translation::get("language_short"), 
			translation::get("language_name"), 
			translation::get("language_is_default") 
		];

		if($_GET["edit"]!="" || $_GET["add"]!="") { 
			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/language/view","sqltable"=>"cms_lang","sqlwhere"=>["cms_lang_id"=>$_GET["edit"]]],"td"=>[120]]);
			$table->controller();
			$table->addTitle(["cols"=> [translation::get("language_table_title")]]);
			$table->add(["cols"=>[$title[0],$table->addFormField(["name"=>$fields[0],"type"=>"text","attr"=>["maxlength"=>5,"drequired"=>true]])]]);
			$table->add(["cols"=>[$title[1],$table->addFormField(["name"=>$fields[1],"type"=>"text","attr"=>["drequired"=>true]])]]); 
			$table->add(["cols"=>[$title[2],$table->addFormField(["name"=>$fields[2],"type"=>"checkbox","set"=>1])]]);
			$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		} else {
			$table = new table();  
			$table->setup(["td"=> [100,0,100],"form"=>["sqltable"=>"cms_lang"]]);
			$table->controller();
			$table->addTitle(["cols"=> [translation::get("language_table_title")],"controls"=>["/cp/language?add=1"=>translation::get("add")]]);
			$table->addSubtitle(["cols"=> $title]);
			$table->auto(["id"=>"cms_lang_id","select"=>implode(",",$fields),"from"=>"cms_lang","controls"=>["/cp/language?edit={id}"=>translation::get("edit"),"/cp/async/language/view?prmv={id}"=>translation::get("rmv")]]); 
		}

		echo $table->render();
	} 
}
?> 