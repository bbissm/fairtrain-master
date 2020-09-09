<?php
class socialmedia_slider extends module {
	public function get($containerId) {
		$result = [];
		$q = $this->db->query("SELECT socialmedia_slider_id,
									  image,
									  sort,
									  is_deleted
									  FROM tbl_socialmedia_slider
									  WHERE 
									  	is_deleted = 0
									  ORDER BY sort ASC"); 
		while($res = $q->fetch_assoc()) {
			array_push($result, $res);
		} 		
		return $result;
	} 
} 
?>