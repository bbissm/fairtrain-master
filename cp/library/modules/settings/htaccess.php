<?php
class htaccess extends module {
	public static $auth = true;
	public static $authLevel = [1];

	public function setup() {
		cp::addSubnavigation([
			"/cp/usermanagement"=>translation::get("subnavigation_usermanagement"),
			"/cp/translations"=>translation::get("subnavigation_translation"),
			"/cp/language"=>translation::get("subnavigation_language"),
			"/cp/htaccess"=>translation::get("subnavigation_htaccess"),
			"/cp/sass"=>translation::get("subnavigation_sass")
		]);
	}

	public function controller() {
		if($_POST["save"]!="") {
			file_put_contents("../.htaccess", $_POST["htaccess"]);
			$this->success=1;
		}
	}

	public function view() {		
		$header = new header();
		$header->addTitle(translation::get("htaccess_title"));
		$header->addParagraph(translation::get("htaccess_text"));
		echo $header->render();
		
		$table = new table();
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/htaccess/view"]]);
		$table->addTitle(["cols"=> [translation::get("htaccess_title")]]);
		$table->add(["cols"=>[$table->addFormField(["attr"=>["style"=>"height:500px"],"name"=>"htaccess","type"=>"textarea","value"=>file_get_contents("../.htaccess")])]]);
		$table->add(["cols"=>[$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		echo $table->render();

		if($this->success) {
			echo cp::message(["message"=>translation::get("success"),"type"=>"success"]);
		}
	} 
}
?> 