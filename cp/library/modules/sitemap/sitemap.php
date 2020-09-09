<?php
class sitemap extends module {
	private $success = 0;
	public static $auth = true;
	public static $authLevel = [1,2,3]; 

	public function htmlHead() {
		
	} 

	public function controller() { 
		if($_POST["save"]!="") {
			if($_GET["add"]!="") {
				$q = $this->db->query("SELECT sort+1 as sort FROM cms_navigation ORDER BY sort DESC LIMIT 0,1");
				$res = $q->fetch_assoc();

				$this->db->query("INSERT cms_navigation SET cms_navigation_fk='".$_GET["add"]."',is_visible='".(int)isset($_POST["is_visible"])."',is_footer='".(int)isset($_POST["is_footer"])."',sort='".$res["sort"]."'");
				$id = $this->db->insert_id;
				$q = $this->db->query("SELECT cms_lang_id,name FROM cms_lang ORDER BY is_default DESC,name ASC");
				while($res = $q->fetch_assoc()) {
					$this->db->query("INSERT cms_navigation_page SET name='".$_POST["name_S_".$res["cms_lang_id"]]."',permalink='".$_POST["permalink_S_".$res["cms_lang_id"]]."',is_active='".isset($_POST["is_active_S_".$res["cms_lang_id"]])."',cms_navigation_fk='$id',cms_lang_fk='".$res["cms_lang_id"]."'");
				
					$insert = "";
					sitemap::permalink($this->db->insert_id,$insert,$res["cms_lang_id"]);
					sitemap::add($insert,isset($_POST["is_active_S_".$res["cms_lang_id"]]));
					$this->createSitemap();
				}

				$this->success=1;
			} else if($_GET["edit"]!="") { 
				$this->db->query("UPDATE cms_navigation SET is_visible='".(int)isset($_POST["is_visible"])."',is_footer='".(int)isset($_POST["is_footer"])."' WHERE cms_navigation_id=".$_GET["edit"]);
				echo $this->db->error;
				$q = $this->db->query("SELECT cms_lang_id,name FROM cms_lang ORDER BY is_default DESC,name ASC");
				while($res = $q->fetch_assoc()) {
					$q1 = $this->db->query("SELECT cms_navigation_page_id FROM cms_navigation_page WHERE cms_navigation_fk=".$_GET["edit"]." AND cms_lang_fk='".$res["cms_lang_id"]."'");
					$res1 = $q1->fetch_assoc();

					$before = "";
					sitemap::permalink($_GET["edit"],$before,$res["cms_lang_id"]);



					if($res1["cms_navigation_page_id"]!=null && $res1["cms_navigation_page_id"]!="") {
						$this->db->query("UPDATE cms_navigation_page SET name='".$_POST["name_S_".$res["cms_lang_id"]]."',permalink='".$_POST["permalink_S_".$res["cms_lang_id"]]."',is_active='".(int)isset($_POST["is_active_S_".$res["cms_lang_id"]])."' WHERE cms_navigation_fk=".$_GET["edit"]." AND cms_lang_fk='".$res["cms_lang_id"]."'");
					} else {
						$this->db->query("INSERT cms_navigation_page SET name='".$_POST["name_S_".$res["cms_lang_id"]]."',permalink='".$_POST["permalink_S_".$res["cms_lang_id"]]."',is_active='".(int)isset($_POST["is_active_S_".$res["cms_lang_id"]])."',cms_navigation_fk='".$_GET["edit"]."',cms_lang_fk='".$res["cms_lang_id"]."'");
					}

					$after = "";
					sitemap::permalink($_GET["edit"],$after,$res["cms_lang_id"]);
				
					sitemap::change($before,$after,isset($_POST["is_active_S_".$res["cms_lang_id"]]));
					$this->createSitemap();
				} 

				$this->success=1;
			}

			echo $this->db->error;
		}
		
		if($_GET["rmv"]!="") {
			$q = $this->db->query("SELECT cms_lang_id,name FROM cms_lang ORDER BY is_default DESC,name ASC");
			while($res = $q->fetch_assoc()) {
				$rmv = "";
				sitemap::permalink($_GET["rmv"],$rmv,$res["cms_lang_id"]);
				sitemap::rmv($rmv);
				$this->createSitemap();
			}
		}
		
		if($_GET["active"]!="") {
			$this->db->query("UPDATE cms_navigation_page SET is_active=1-is_active WHERE cms_navigation_fk='".$_GET["active"]."' AND cms_lang_fk='".$_SESSION["lang"]["key"]."'");
			unset($_GET["active"]);
			$this->createSitemap();
		}

		if($_GET["visible"]!="") {
			$this->db->query("UPDATE cms_navigation SET is_visible=1-is_visible WHERE cms_navigation_id='".$_GET["visible"]."'");
		}

		if($_GET["footer"]!="") {
			$this->db->query("UPDATE cms_navigation SET is_footer=1-is_footer WHERE cms_navigation_id='".$_GET["footer"]."'");
		}

		if($_GET["pushid"]!="") {
			$_SESSION["tabs"]=$_GET["pushid"];
		}
	}

	public function view() {
		$header = new header(); 
		$header->addTitle(translation::get("sitemap_title"));
		$header->addParagraph(translation::get("sitemap_text"));
		echo $header->render();

		if($_GET["seo"]!="") { 
			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/sitemap/view","sqltable"=>"cms_navigation_page","sqlwhere"=>["cms_navigation_page_id"=>$_GET["seo"]]],"td"=>[120]]);
			$table->controller();
			$table->addTitle(["cols"=> [translation::get("sitemap_seo_title")]]); 
			
			$table->addSubtitle(["cols"=> [translation::get("sitemap_meta_subtitle")]]);
			$table->add(["cols"=> [translation::get("sitemap_pagetitle"),$table->addFormField(["name"=>"pagetitle","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_keywords"),$table->addFormField(["name"=>"keywords","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_description"),$table->addFormField(["name"=>"description","type"=>"textarea"])]]);
			$table->add(["cols"=> [translation::get("sitemap_author"),$table->addFormField(["name"=>"author","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_robots"),$table->addFormField(["name"=>"robots","type"=>"text"])]]);
			
			$table->addSubtitle(["cols"=> [translation::get("sitemap_seo_subtitle")]]);
			$table->add(["cols"=> [translation::get("sitemap_og_title"),$table->addFormField(["name"=>"og_title","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_og_type"),$table->addFormField(["name"=>"og_type","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_og_image"),$table->addFormField(["name"=>"og_image","type"=>"text"])]]);
			$table->add(["cols"=>["",$table->addImageSelect(["target"=>"og_image","attr"=>["cropping"=>"120:63","targetWidth"=>1200,"targetHeight"=>630]])]]);  
			$table->add(["cols"=> [translation::get("sitemap_og_description"),$table->addFormField(["name"=>"og_description","type"=>"textarea"])]]);
			$table->add(["cols"=> [translation::get("sitemap_og_site_name"),$table->addFormField(["name"=>"og_site_name","type"=>"text"])]]);
			$table->add(["cols"=> [translation::get("sitemap_fb_app_id"),$table->addFormField(["name"=>"fb_app_id","type"=>"text"])]]);	
			
			$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
			echo $table->render();
		} else if($_GET["add"]!="" || $_GET["edit"]!="") {
			if($_GET["edit"]!="") {
				$q = $this->db->query("SELECT is_visible,is_footer FROM cms_navigation WHERE cms_navigation_id='".$_GET["edit"]."'");
				echo $this->db->error;
				$res2 = $q->fetch_assoc();

				if($res2["is_visible"]!=1) unset($res2["is_visible"]);
				if($res2["is_footer"]!=1) unset($res2["is_footer"]);  
			} 

			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/sitemap/view"],"td"=>[120]]);
			$table->controller();
			$table->addTitle(["cols"=> [translation::get("sitemap_title")]]);
			
			$q = $this->db->query("SELECT cms_lang_id,name FROM cms_lang ORDER BY is_default DESC,name ASC");
			while($res = $q->fetch_assoc()) {
				if($_GET["edit"]!="") {
					$q1 = $this->db->query("SELECT name,permalink,is_active FROM cms_navigation_page WHERE cms_navigation_fk=".$_GET["edit"]." AND cms_lang_fk=".$res["cms_lang_id"]);
					$res1 = $q1->fetch_assoc();
					if($res1["is_active"]!=1) unset($res1["is_active"]);
				}
 
				$table->addSubtitle(["cols"=>[$res["name"]]]);
				$table->add(["cols"=>[translation::get("pagename"),$table->addFormField(["name"=>"name_S_".$res["cms_lang_id"],"type"=>"text","value"=>$res1["name"],"attr"=>["perma"=>"permalink_S_".$res["cms_lang_id"]]])]]);
				$table->add(["cols"=>[translation::get("permalink"),$table->addFormField(["name"=>"permalink_S_".$res["cms_lang_id"],"type"=>"text","value"=>$res1["permalink"]])]]);
				$table->add(["cols"=>[translation::get("active"),$table->addFormField(["name"=>"is_active_S_".$res["cms_lang_id"],"type"=>"checkbox","value"=>$res1["is_active"],"set"=>1])]]);
			}	 
 			
 			$table->addSubtitle(["cols"=>[translation::get("general")]]);
			$table->add(["cols"=>[translation::get("visible"),$table->addFormField(["name"=>"is_visible","type"=>"checkbox","value"=>$res2["is_visible"],"set"=>1])]]);	
			$table->add(["cols"=>[translation::get("footer"),$table->addFormField(["name"=>"is_footer","type"=>"checkbox","value"=>$res2["is_footer"],"set"=>1])]]);	
			$table->add(["cols"=>["",$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
			echo $table->render();
		} else {
			$tabs = new tabs();
			$tabs->setup(["active"=>$_SESSION["tabs"],"push"=>"/cp/async/sitemap/view"]);

			$i = 0;
			$q = $this->db->query("SELECT cms_lang_id,name FROM cms_lang ORDER BY is_default DESC,name ASC");
			while($res = $q->fetch_assoc()) {
				$table = new table();
				$table->setup(["dragable"=>["type"=>1],"td"=>[0,40,40,40],"form"=>["action"=>"/cp/async/sitemap/view","sqltable"=>"cms_navigation","parent"=>"cms_navigation_fk"]]); 
				if($_SESSION["tabs"]==$i) $table->controller();
				$table->addTitle(["cols"=> [translation::get("sitemap_title")],"controls"=>["/cp/sitemap?add=0&lang=".$res["cms_lang_id"]=>translation::get("add")]]);
				$table->addSubtitle(["cols"=> [translation::get("description"),translation::get("active"),translation::get("footer"),translation::get("visible")]]);
				$this->navigation($table,0,0,$res["cms_lang_id"]);
				$tabs->add($res["name"],$table->render());
				$i++;
			}
			echo $tabs->render();
		}

		if($this->success==1) {
			echo cp::message(["message"=>translation::get("success"),"type"=>"success"]);
		} 
	}

	public function navigation(&$table,$stage,$fk,$lang) {
		$q = $this->db->query("SELECT cms_navigation_id,
										  n.cms_navigation_fk as cms_navigation_fk,
										  p.name as name, 
										  p.is_active as is_active,
										  is_visible,
										  is_footer,
										  cms_navigation_page_id
										  FROM cms_navigation as n,
										  cms_navigation_page as p
										  WHERE p.cms_navigation_fk=cms_navigation_id 
										  AND n.cms_navigation_fk='$fk' 
										  AND is_deleted=0
										  AND cms_lang_fk=$lang
										  ORDER BY sort ASC");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			if($res["is_active"]==1) { 
				$res["is_active"]=translation::get("yes");
			} else {
				$res["is_active"]=translation::get("no");
			}

			if($res["is_visible"]==1) {
				$res["is_visible"]=translation::get("yes"); 
			} else {
				$res["is_visible"]=translation::get("no");
			}

			if($res["is_footer"]==1) {
				$res["is_footer"]=translation::get("yes"); 
			} else {
				$res["is_footer"]=translation::get("no");
			}

			$row = [];
			$row["style"]=[];

			if($res["is_visible"]==translation::get("yes")) {
				$row["style"]["background-color"]="#DDD";
			}
 
			if($res["is_active"]!=translation::get("yes")) {
				$row["style"]["color"]="rgba(0,0,0,0.5)";
			}

			$res["is_active"] = "<a class=\"async\" href=\"/cp/async/sitemap/view?active=".$res["cms_navigation_id"]."\">".$res["is_active"]."</a>";
			$res["is_visible"] = "<a class=\"async\" href=\"/cp/async/sitemap/view?visible=".$res["cms_navigation_id"]."\">".$res["is_visible"]."</a>";
			$res["is_footer"] = "<a class=\"async\" href=\"/cp/async/sitemap/view?footer=".$res["cms_navigation_id"]."\">".$res["is_footer"]."</a>";
			
			$row["cols"] = ["<div style=\"padding-left:".($stage*20)."px;\">".$res["name"]."</div>",$res["is_active"],$res["is_footer"],$res["is_visible"]];
			$row["controls"] = ["/cp/sitemap?edit=".$res["cms_navigation_id"]."&lang=".$lang=>translation::get("edit"),"/cp/sitemap?add=".$res["cms_navigation_id"]."&lang=".$lang=>translation::get("addsubnav"),"/cp/sitemap?seo=".$res["cms_navigation_page_id"]."&lang=".$lang=>translation::get("metadata"),"/cp/async/sitemap/view?rmv=".$res["cms_navigation_id"]."&lang=".$lang=>translation::get("rmv")];
			$row["attr"] = ["parent"=>$res["cms_navigation_fk"]];
			
			$table->add($row); 
			$this->navigation($table,$stage+1,$res["cms_navigation_id"],$lang);
		}
	}
 
	public static function permalink($parent,&$link,$lang) { 
		$q = $_SESSION["db"]->query("SELECT cms_navigation_id,
										  n.cms_navigation_fk as cms_navigation_fk,
										  permalink
										  FROM cms_navigation as n,
										  cms_navigation_page as p
										  WHERE p.cms_navigation_fk=cms_navigation_id 
										  AND n.cms_navigation_id='$parent' 
										  AND is_deleted=0
										  AND cms_lang_fk=$lang
										  ORDER BY sort ASC");
		echo $_SESSION["db"]->error;
		$res = $q->fetch_assoc();

		if($link!="") {
			$link = $res["permalink"]."/".$link;
		} else {
			$link = $res["permalink"];
		}

		if($res["cms_navigation_fk"]!="" && $res["cms_navigation_fk"]!=0) {
			sitemap::permalink($res["cms_navigation_fk"],$link,$lang);
		} else {
			$q = $_SESSION["db"]->query("SELECT short FROM cms_lang WHERE cms_lang_id=$lang");
			$res = $q->fetch_assoc();

			$link = "/".$res["short"]."/".$link;
		}
	}

	public static function add($src,$state) {
		$_SESSION["db"]->query("INSERT cms_sitemap SET permalink='$src',is_active=".(int)$state);
	}

	public static function change($src,$dest,$state) {
		$src1 = str_replace("/","_",substr($src,1));
		$dest1 = str_replace("/","_",substr($dest, 1));

		$q = $_SESSION["db"]->query("SELECT cms_container_id,name FROM cms_container WHERE name LIKE '".$src1."%' ORDER BY name ASC");
		echo $_SESSION["db"]->error;
		while ($res = $q->fetch_assoc()) {
			$res["name"] = str_replace($src1,$dest1,$res["name"]);

			$_SESSION["db"]->query("UPDATE cms_container SET name='".$res["name"]."' WHERE cms_container_id=".$res["cms_container_id"]);
			echo $_SESSION["db"]->error;
		}

		$q = $_SESSION["db"]->query("SELECT cms_sitemap_id,permalink,is_active FROM cms_sitemap WHERE permalink LIKE '".$src."' ORDER BY permalink ASC");
		echo $_SESSION["db"]->error;
		$res = $q->fetch_assoc();

		if($res["cms_sitemap_id"]=="") {
			sitemap::add($dest,$state);
		} else {
			$i = 0;
			$q = $_SESSION["db"]->query("SELECT cms_sitemap_id,permalink,is_active FROM cms_sitemap WHERE permalink LIKE '".$src."%' ORDER BY permalink ASC");
			echo $_SESSION["db"]->error;
			while($res = $q->fetch_assoc()) {
				$res["permalink"] = str_replace($src,$dest,$res["permalink"]);
				if($i==0) {
					$_SESSION["db"]->query("UPDATE cms_sitemap SET permalink='".$res["permalink"]."',is_active=$state WHERE cms_sitemap_id=".$res["cms_sitemap_id"]);
				} else {
					$_SESSION["db"]->query("UPDATE cms_sitemap SET permalink='".$res["permalink"]."' WHERE cms_sitemap_id=".$res["cms_sitemap_id"]);
				}
				// echo $_SESSION["db"]->error;
				$i++;
			}
		}
	}

	public static function rmv($src) {
		$_SESSION["db"]->query("DELETE FROM cms_sitemap WHERE permalink LIKE '".$src."%'");
	}

	public function createSitemap(){
		$arraySitemap=array();
		$q = $this->db->query("SELECT cms_lang_id, short FROM cms_lang");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			sitemap::getArrayPageSitemap($arraySitemap,$res["cms_lang_id"],0,0,"/".$res["short"]);
		}

		config::sitemap($arraySitemap);
	}

	private function getArrayPageSitemap(&$array,$lang,$fk,$stage,$link){
		$array[]=$link;
		$q = $this->db->query("SELECT cms_navigation_id,permalink
										  FROM cms_navigation as n,
										  cms_navigation_page as p
										  WHERE p.cms_navigation_fk=cms_navigation_id 
										  AND n.cms_navigation_fk='$fk' 
										  AND p.is_active=1
										  AND is_deleted=0
										  AND cms_lang_fk=$lang
										  ORDER BY sort ASC");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			sitemap::getArrayPageSitemap($array,$lang,$res["cms_navigation_id"],$stage+1,$link."/".$res["permalink"]);
		}
	}
}
?>