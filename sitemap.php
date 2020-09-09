<?php
session_start();

require_once("library/core/autoload.php");
require_once("config.php");

$db = new mysqli(config::dbHost,config::dbUsername,config::dbPassword,config::dbName); 
$db->set_charset("utf8"); 

class sitemap {
	private $db;

	public function __construct(&$db) {
		$this->db = $db;
	}

	public function createSitemap(){
		$arraySitemap=array();
		$q = $this->db->query("SELECT cms_lang_id, short FROM cms_lang");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			sitemap::getArrayPageSitemap($arraySitemap,$res["cms_lang_id"],0,0,"/".$res["short"]);
		}

		sitemap::getArrayArticleSitemap($arraySitemap);

		config::sitemap($arraySitemap,"");
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
	
	private function getArrayArticleSitemap(&$array){
		$q = $this->db->query("SELECT cms_container_fk,permalink FROM tbl_feed_article WHERE is_active=1");
		echo $this->db->error;
		while($res = $q->fetch_assoc()) {
			if($res["permalink"]!=""){
				$array[]="/de/article/".$res["permalink"]."--".$res["cms_container_fk"];
			}
		}	
	}
}

$sitemap = new sitemap($db);
$sitemap->createSitemap();
?>