<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class links extends module {
	public function get($config) {
		$q = $this->db->query("SELECT links_id
									  FROM tbl_links
									  WHERE cms_container_fk='".$config["cms_container_id"]."'"); 
		$res = $q->fetch_assoc(); 
		return $res; 
	} 
	public function getLinks($container_fk,$category_id=""){
	echo "<div class=\"container\">";
	$query_cat = "SELECT links_category_id, lcl.title as title
					FROM tbl_links_category as lc 
					LEFT JOIN tbl_links_category_lang as lcl 
					on lc.links_category_id=lcl.links_category_fk
					WHERE lang_fk=".$_SESSION["lang"]["key"]."
				";		
		$q_cat = $this->db->query($query_cat);
		echo "<form class=\"form grey filter\" id=\"links\" method=\"POST\">";
			echo "<div class=\"filter-header\">";
					echo "<h2 class=\"center\">Links</h2>";
					echo "<div class=\"centered_btn\">";
					// echo "<fieldset id=\"cat\">";
					while($res = $q_cat->fetch_assoc()){	
						echo "<label for=\"cat_".$res["links_category_id"]."\" class=\"text_brown btn button\">";
							echo $res["title"];
						echo "</label>";
						// echo "<input class=\"cat_input\" id=\"cat_".$res["links_category_id"]."\" name=\"cat_".$res["links_category_id"]."\" type=\"checkbox\" style=\"visibility:hidden\" value=\"".$res["links_category_id"]."\">";
						echo "<input class=\"cat_input\" id=\"cat_".$res["links_category_id"]."\" name=\"cat_".$res["links_category_id"]."\" type=\"checkbox\" style=\"visibility:hidden\" value=\"".$res["links_category_id"]."\">";

					}
					// echo "</fieldset>";
					echo "</div>";
			echo "</div>";
        echo "</form>";
	}

	public function getLinksAjax()
	{
		$category = array();
		$params = array();
		foreach($_POST as $key => $value){	
			parse_str($value, $params);
			foreach($params as $k => $v){
				$category[] = $v;
			}
		}
        $data = $this->getLinksContent($category);
        if($data != null){
			echo json_encode($data);
		}
		
	}
	public function getLinksContent($category=array()){
		if(count($category) > 0){
			$query_links = "SELECT title,street,plz,location,phone,webpage,category_fk,praxis,mobile FROM tbl_links WHERE category_fk IN (".implode(',',$category).")";
		}else{
			$query_links = "SELECT title,street,plz,location,phone,webpage,category_fk,praxis,mobile FROM tbl_links";
		}
		$q_links = $this->db->query($query_links);
		echo $this->db->error;
		$links = array();
		if($q_links->num_rows > 0){
            while($res = $q_links->fetch_assoc()){
				echo "<div class=\"article\">";
					if($res["praxis"] != ""){
						echo "<h3>".$res["praxis"]."</h3>";
					}
					if($res["title"] != ""){
						echo "<p>".$res["title"]."</p>";
					}
					if($res["street"] != ""){
						echo "<p>".$res["street"]."</p>";
					}
					if($res["location"] != ""){
						echo "<p>".$res["plz"]." ".$res["location"]."</p>";
					}
					
						echo "<p class=\"underline\" style=\"margin-top: 25px;\"><a>".$res["phone"]."</a></p>";
					
					if($res["mobile"] != ""){
						echo "<p class=\"underline\"><a>".$res["mobile"]."</a></p>";
					}
					if($res["webpage"] != ""){
						echo "<p class=\"underline\"><a href=\"".$res["webpage"]."\">".$res["webpage"]."</a></p>";
					}
                echo "</div>";
            }
		}
	}

	
}
?>
