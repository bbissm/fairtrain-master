<?php

class team extends module {
    public function getTeam()
    {
        $query = "SELECT leitung_id,image,name,phone,email,text FROM tbl_leitung WHERE extern = 0";
        $q = $this->db->query($query);
        return $q;
    }
}
?>