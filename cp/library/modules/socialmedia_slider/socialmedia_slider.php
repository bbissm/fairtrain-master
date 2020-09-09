<?php
class socialmedia_slider extends module { 
	public static $auth = true;
	public static $authLevel = [1,2,3,4];
	
	public function setup(){

	}

	public function controller(){

	}

	public function view(){
		if($_GET["add"] != "" || $_GET["edit"]!=""){
			$this->addSocialmedia_slider();
		} else {
			$this->showSocialmedia_slider();
		}
	}

	public function showSocialmedia_slider(){
		$config = $_GET;
		$header = new header();
		$header->addTitle(translation::get("socialmedia_slider_title"));
		$header->addParagraph(translation::get("socialmedia_slider_paragraph"));
		echo $header->render();

		$table = new table();
		// Welches MySql Table und welche Methode und alle Formularattribute ansprechen
		$table->setup(
			[
				"dragable" => true,
				"form" =>[
					//Läd in eine Lightbox
					"slide_to_element"=>true,
					"popup"=>true,
					"method"=>"post",
					"action"=>"cp/async/socialmedia_slider/view",
					"sqltable"=>"tbl_socialmedia_slider",
					"sqlwhere"=>[
						"cms_container_fk" => $config["id"]
					],
					"td"=>[0,120,120]
				]
			]
		);
		// Automatische Ausführung durch CMS (core/)
		$table->controller();
		$table->addTitle(["cols"=>["Slider verwalten"], "controls"=>["/cp/async/socialmedia_slider/view?id=".$config["id"]."&add=0"=>["name"=>translation::get("add")]]]);
		$table->addSubtitle(["cols"=>["Bild","Letztes Update"]]);
		$table->auto(["id"=>"socialmedia_slider_id","select"=>"image, last_updated","from"=>"tbl_socialmedia_slider","where"=>"cms_container_fk=".$config["id"],"order"=>"sort ASC","controls"=>["/cp/async/socialmedia_slider/view?id=".$config["id"]."&edit={id}"=>["target"=>"#popup_content","name"=>translation::get("edit")],"/cp/async/socialmedia_slider/view?id=".$config["id"]."&prmv={id}"=>["name"=>translation::get("rmv"),"target"=>"#popup_content"]]]);

		echo $table->render();
	}

	public function addSocialmedia_slider(){
		$config = $_GET;
        $date = date("d.m.Y");

        $header = new header();
        $header->addTitle(translation::get("socialmedia_slider_title"));
        $header->addParagraph(translation::get("socialmedia_slider_paragraph"));
        echo $header->render();

		$table = new table();
		$table->setup([
			"form"=>[
				"slide_to_element"=>true,
				"popup"=>true,
				"method"=>"post",
				"action"=>"/cp/async/socialmedia_slider/view",
				"sqltable"=>"tbl_socialmedia_slider",
				"sqlwhere"=>["socialmedia_slider_id"=>$config["edit"]],
				"attr"=>["target"=>"#popup_content"]
			],
			"td"=>[120]
		]);  
		$table->controller();
				$table->addTitle(["cols"=> ["Slider verwalten"], "controls"=>["/cp/async/socialmedia_slider/view?add=0"=>["name"=>translation::get("add"), "target"=>"#popup_content"]]]);
				$table->addSubtitle(["cols"=>["Allgemein"]]);
		$table->add(["cols"=>[translation::get("image_src"),$table->addFormField(["name"=>"image","type"=>"text","attr"=>["drequired"=>true]])]]);
				$table->addSubtitle(["cols"=>["Deutsch"]]);
				// $table->add(["cols"=>[translation::get("image_caption"),$table->addFormField(["name"=>"image_caption_de","type"=>"text","attr"=>["drequired"=>true]])]]);
				// $table->addSubtitle(["cols"=>["Französisch"]]);
				// $table->add(["cols"=>[translation::get("image_caption"),$table->addFormField(["name"=>"image_caption_fr","type"=>"text","attr"=>["drequired"=>true]])]]);
				// $table->addSubtitle(["cols"=>["Italienisch"]]);
				// $table->add(["cols"=>[translation::get("image_caption"),$table->addFormField(["name"=>"image_caption_it","type"=>"text","attr"=>["drequired"=>true]])]]);
				// $table->addSubtitle(["cols"=>["Englisch"]]);
				// $table->add(["cols"=>[translation::get("image_caption"),$table->addFormField(["name"=>"image_caption_en","type"=>"text","attr"=>["drequired"=>true]])]]);
				$table->addSubtitle(["cols"=>["Medienquelle"]]);
				$table->add(["cols"=>["",$table->addImageSelect(["target"=>"image","attr"=>["cropping"=>"16:16"]])]]);
				$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$config["id"]]),$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])." ".$table->addFormField(["name"=>"back","type"=>"button","value"=>translation::get("back"),"attr"=>["href"=>"/cp/async/socialmedia_slider/view?id=".$config["id"],"style"=>"margin-left:10px;"]])]]);
		echo $table->render();

	}

}
?>