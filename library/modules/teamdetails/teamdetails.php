<?php

class teamdetails extends module{

    public function get() {
        $array = array();
        // $query = "SELECT teamdetails_id,
		// 							  type,
		// 							  tl.text
		// 							  FROM tbl_teamdetails as t
        //                               LEFT JOIN tbl_teamdetails_lang as tl
        //                               on t.teamdetails_id=tl.teamdetails_fk
        //                               WHERE mitglied_fk='".$_GET["mitglied"]."'
        //                               AND lang_fk=".$_SESSION["lang"]["key"]."
        //                               ";
        $query = "SELECT teamdetails_id,
									  type,
									  text
									  FROM tbl_teamdetails as t
                                      WHERE mitglied_fk='".$_GET["mitglied"]."'
                                      AND lang_fk=".$_SESSION["lang"]["key"]."
                                      ";
        $q = $this->db->query($query);

        while($res = $q->fetch_assoc()){
            $array[] = $res;
        }
        echo $this->db->error;
 
		
 
		return $array;
    }
    
    public function getTeamdetails()
    {   
        $query = "SELECT * FROM tbl_leitung WHERE leitung_id='".$_GET["mitglied"]."'";
        $q = $this->db->query($query);
        return $q;
    }
}



?>