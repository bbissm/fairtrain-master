<?php
class kurshighlight extends module {
	/**
	 * @return mixed
	 */
	public function getCourse() {
		$result = [];
		$query  = "SELECT course_id,
				  cl.title as title,
				  image,
				  c.is_deleted as is_deleted
				  FROM tbl_course as c
				  LEFT JOIN tbl_course_lang as cl
				  on c.course_id=cl.course_fk
				  WHERE
				  	c.is_deleted = 0
					  AND lang_fk = '" . $_SESSION["lang"]["key"] . "'
				  ORDER BY RAND()
				  LIMIT 2";
		$q = $this->db->query($query);
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			array_push($result, $res);
		}

		return $result;
	}

	/**
	 * @return mixed
	 */
	public function getSeminar() {
		$result = [];
		$q      = $this->db->query("SELECT seminar_id,
                                      sl.title as title,
									  image,
									  s.is_deleted as is_deleted
									  FROM tbl_seminar as s
									  LEFT JOIN tbl_seminar_lang as sl
									  on s.seminar_id=sl.seminar_fk
									  WHERE
									  	s.is_deleted = 0
									  	AND lang_fk='" . $_SESSION["lang"]["key"] . "'
									  ORDER BY RAND()
									  LIMIT 2");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			array_push($result, $res);
		}

		return $result;
	}
}
?>
