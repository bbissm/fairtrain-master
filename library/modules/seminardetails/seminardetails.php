<?php

class seminardetails extends module {
    /**
     * @return mixed
     */
    public function getSeminar() {

        $query = "SELECT sl.title as title, sl.text as text, sl.lernziele as lernziele, sl.zielgruppe as zielgruppe, sl.ort ort, sl.kosten as kosten, sl.mit_hund as mit_hund, sl.ohne_hund as ohne_hund, sl.kursplaetze as kursplaetze, anmeldefrist FROM tbl_seminar as s LEFT JOIN tbl_seminar_lang as sl on sl.seminar_fk=s.seminar_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND seminar_id='" . $_GET["id"] . "' AND s.is_deleted='0'";
        $q     = $this->db->query($query);
        echo $this->db->error;
        $res = $q->fetch_assoc();
        return $res;
    }

    /**
     * @return mixed
     */
    public function getDate() {
        $query = "SELECT date,time_from, time_to FROM tbl_seminar_date WHERE seminar_fk='" . $_GET["id"] . "' ORDER BY date ASC, time_from ASC ";
        $q     = $this->db->query($query);
        return $q;
    }

    /**
     * @return mixed
     */
    public function getLeitung() {
        $q = $this->db->query("SELECT image,name,phone,email,text FROM tbl_leitung as l INNER JOIN tbl_seminar_leitung as sl on l.leitung_id=sl.leitung_fk WHERE seminar_fk='" . $_GET["id"] . "' ORDER BY sl.last_updated ASC LIMIT 1 ");
        echo $this->db->error;
        return $q;
    }
}

?>
