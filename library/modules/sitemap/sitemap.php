<?php
class sitemap {	
	public static function get($config=[]) {
		if($config["stage"]==null) {
			$config["stage"]=0; 
			$_SESSION["path"][0] = $_SESSION["lang"]["short"];
		}

		if($config["permalink"]!="") {
			$q = $_SESSION["db"]->query("SELECT cms_navigation_id 
										 FROM cms_navigation as n,
									  	 cms_navigation_page as p
									  	 WHERE p.cms_navigation_fk=cms_navigation_id
									  	 AND permalink='".$config["permalink"]."'");
			$res = $q->fetch_assoc();
			$config["stage"]=$res["cms_navigation_id"]; 
		}

		$nav = [];

		// print_r($_SESSION["lang"]["short"]);

		$q = $_SESSION["db"]->query("SELECT cms_navigation_id,
									 p.name as name,
									 p.permalink as permalink,
									 p.pagetitle as pagetitle,
									 p.og_title as og_title,
									 p.og_type as og_type,
									 p.og_image as og_image,
									 p.og_description as og_description,
									 p.og_site_name as og_site_name,
									 p.fb_app_id as fb_app_id,
									 p.keywords as keywords,
									 p.description as description,
									 p.author as author,
									 p.robots as robots,
									 p.is_active as is_active,
									 n.is_visible as is_visible,
									 n.is_footer as is_footer 
									 FROM cms_navigation as n,
									 cms_navigation_page as p
									 WHERE p.cms_navigation_fk=cms_navigation_id 
									 AND n.cms_navigation_fk='".$config["stage"]."' 
									 AND is_deleted=0
									 AND cms_lang_fk=".$config["lang"]."
									 ORDER BY sort ASC");
		// print_r($config); 
		
		echo $_SESSION["db"]->error;
		while($res = $q->fetch_assoc()) {
			array_push($nav, $res);
		}

		return $nav;
	}
}
?>