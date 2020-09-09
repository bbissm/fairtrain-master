<?php
//test
class heroslider extends module {
	public function get($containerId) {
		$result = [];
		$q = $this->db->query("SELECT title,
									  image,
									  text,
									  sort,
									  no_overlay,
									  is_deleted
									  FROM tbl_heroslider
									  WHERE cms_container_fk='".$containerId."'
									  ORDER BY sort ASC"); 
		while($res = $q->fetch_assoc()) {
			array_push($result, $res);
		} 		

		return $result;
	} 
	public function getDefault() {
		$result = [];
		$q = $this->db->query("SELECT title,
									  image,
									  text,
									  sort,
									  is_deleted,
									  no_overlay
									  FROM tbl_heroslider
									  ORDER BY sort ASC
									  LIMIT 3"); 
		while($res = $q->fetch_assoc()) {
			array_push($result, $res);
		} 		

		return $result;
	} 
} 
?>