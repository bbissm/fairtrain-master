<?php

class kursdetails extends module {

    /**
     * @return mixed
     */
    public function getCourse() {
        $query_course = "SELECT cl.title as title, cl.text as text, cl.lernziele as lernziele, cl.zielgruppe as zielgruppe, cl.ort ort, lektion_kosten,zehnerabo_kosten FROM tbl_course as c LEFT JOIN tbl_course_lang as cl on cl.course_fk=c.course_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND course_id='" . $_GET["id"] . "' AND c.is_deleted=0";
        $q_course     = $this->db->query($query_course);
        $res_course   = $q_course->fetch_assoc();
        return $res_course;

    }

    /**
     * @return mixed
     */
    public function getDate() {
        $query_date = "SELECT date,time_from, time_to,constant FROM tbl_course_date WHERE course_fk='" . $_GET["id"] . "' AND (date >= CURDATE() OR constant=1) ORDER BY date ASC";
        $q_date     = $this->db->query($query_date);
        file_put_contents("data.log", $query_date);
        return $q_date;
    }

    /**
     * @return mixed
     */
    public function getLeitung() {
        $q = $this->db->query("SELECT image,name,phone,email FROM tbl_leitung as l INNER JOIN tbl_course_seminar_leitung as csl on l.leitung_id=csl.leitung_fk WHERE course_fk='" . $_GET["id"] . "' ");
        echo $this->db->error;
        return $q;
    }
}

?>
