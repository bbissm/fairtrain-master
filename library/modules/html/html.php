<?php
class html extends module {
	public function get($config) {
		$q = $this->db->query("SELECT html_id,
									  html,
									  background
									  FROM tbl_html
									  WHERE cms_container_fk='".$config["container"]."'"); 
		$res = $q->fetch_assoc(); 

		if($res["html"]=="") { 
			$res = ["html_id"=>"-1"];
			$res = ["html"=>""];
		}   
 
		return $res; 
	} 

	public function getCourseName($courseId) {
		$q = $this->db->query("SELECT cc.title as title FROM tbl_course_category as cc LEFT JOIN tbl_course as c on c.category_fk=cc.course_category_id WHERE course_id='".$courseId."'");
		$res = $q->fetch_assoc();
		return $res["title"];
	}
}
?>