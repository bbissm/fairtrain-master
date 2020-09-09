<?php
class teamdetails extends module { 
	private $linklist = [];
	public static $auth = true;
	public static $authLevel = [1,2,3];
	
	public function update() {
		if($_GET["id"]!="") {
			
			$query = "SELECT teamdetails_id, type, mitglied_fk FROM tbl_teamdetails WHERE mitglied_fk=".$_GET["id"]." AND type=".$_GET["type"]." AND lang_fk=".$_GET["lang_fk"]."";
			$q = $this->db->query($query);

			if($q->num_rows > 0){
				$update ="UPDATE tbl_teamdetails SET type='".$_GET["type"]."',text='".$_POST["html"]."' WHERE mitglied_fk='".$_GET["id"]."' AND type='".$_GET["type"]."' AND lang_fk='".$_GET["lang_fk"]."'";
				$this->db->query($update);
			}else{
				$this->db->query("INSERT tbl_teamdetails SET type='".$_GET["type"]."',text='".$_POST["html"]."',mitglied_fk='".$_GET["id"]."',lang_fk='".$_GET["lang_fk"]."'");
				echo $this->db->error;
			}
			
			echo $this->db->error;
			$res = $q->fetch_assoc();	
		} 
	}
}
?>