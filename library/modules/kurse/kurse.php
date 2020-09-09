<?php

class kurse extends module {
	/**
	 * @param  $courseId
	 * @return mixed
	 */
	public function getCourseOnly($courseId) {

		$q = $this->db->query("SELECT course_id, cl.title as title, cl.ort as ort, lektion_kosten, zehnerabo_kosten
									  FROM tbl_course as c LEFT JOIN tbl_course_lang as cl on cl.course_fk=c.course_id
									  WHERE course_id='" . $courseId . "' AND lang_fk='" . $_SESSION["lang"]["key"] . "' AND c.is_deleted=0");
		$res = $q->fetch_assoc();
		return $res;
	}

	/**
	 * @param  $config
	 * @return mixed
	 */
	public function get($config) {
		$q = $this->db->query("SELECT course_id
									  FROM tbl_course
									  WHERE cms_container_fk='" . $config["cms_container_id"] . "' AND is_deleted=0");
		$res = $q->fetch_assoc();
		return $res;
	}

	/**
	 * @param $container_fk
	 * @param $category_id
	 */
	public function getCourse($container_fk, $category_id = "") {

		echo "<div class=\"container\">";
		echo "<div class=\"row\">";
		$query_cat = "SELECT course_category_id, ccl.title as title FROM tbl_course_category as cc
						LEFT JOIN tbl_course_category_lang as ccl
						on cc.course_category_id=ccl.course_category_fk
						WHERE is_deleted = 0
						AND lang_fk=" . $_SESSION["lang"]["key"] . "
						ORDER BY course_category_id ASC";

		$q_cat = $this->db->query($query_cat);
		echo "<form id=\"kurse\" method=\"POST\">";
		while ($res_cat = $q_cat->fetch_assoc()) {
			echo "<label for=\"cat_" . $res_cat["course_category_id"] . "\" class=\"container-swipe text_brown\">";
			echo "<h3>" . $res_cat["title"] . "</h3>";
			echo "<img class=\"show-containers arrow-black\" src=\"/templates/web/img/brown-arrow.png\">";
			echo "<img class=\"show-containers arrow-white\" src=\"/templates/web/img/white-arrow-up.png\">";
			echo "<img class=\"show-containers arrow-white-down\" src=\"/templates/web/img/white-arrow.png\">";
			echo "<input class=\"cat_input\" id=\"cat_" . $res_cat["course_category_id"] . "\" name=\"cat_" . $res_cat["course_category_id"] . "\" type=\"checkbox\" style=\"visibility:hidden\" value=\"" . $res_cat["course_category_id"] . "\">";
			echo "</label>";
		}
		echo "</form>";
		echo "</div>";
	}

	public function getCourseAjax() {
		$category = array();
		$params   = array();

		foreach ($_POST as $key => $value) {
			parse_str($value, $params);

			foreach ($params as $k => $v) {

				$category[] = $v;

			}
		}
		$data = $this->getCourseContent($category);
		if ($data != null) {
			echo json_encode($data);
		}

	}

	/**
	 * @param array $category
	 */
	public function getCourseContent($category = array()) {

		if (count($category) > 0) {
			$query_course = "SELECT course_id, cl.title as title, date, image, cl.text as text, category_fk, constant FROM tbl_course as c LEFT JOIN tbl_course_date as cd on c.course_id=cd.course_fk LEFT JOIN tbl_course_lang as cl on cl.course_fk=c.course_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND passive=0 AND category_fk IN (" . implode(',', $category) . ") AND c.is_deleted=0 GROUP by course_id";
		} else {
			$query_course = "SELECT course_id, cl.title as title, date, image, cl.text as text, category_fk, constant FROM tbl_course as c LEFT JOIN tbl_course_date as cd on c.course_id=cd.course_fk LEFT JOIN tbl_course_lang as cl on cl.course_fk=c.course_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND passive=0 AND c.is_deleted=0 GROUP by course_id";
		}
		$q_course = $this->db->query($query_course);

		echo $this->db->error;
		echo "<div class=\"fadeout_containers\">";
		$course = array();
		if ($q_course->num_rows > 0) {
			while ($res = $q_course->fetch_assoc()) {
				if ($res["date"] >= date("Y-m-d") || $res["date"] == null || $res["constant"] != "") {
					echo "<div class=\"row_article beige\">";
					echo "<img src=\"" . $res["image"] . "\">";
					echo "<div class=\"article\">";
					echo "<h3>" . $res["title"] . "</h3>";
					echo $res["text"];
					$q1 = $this->db->query("SELECT cms_navigation_id FROM cms_navigation WHERE cms_navigation_fk=1");

					while ($res1 = $q1->fetch_assoc()) {
						$q_link   = $this->db->query("SELECT permalink, name FROM cms_navigation_page WHERE cms_navigation_fk=12 AND cms_lang_fk=" . $_SESSION["lang"]["key"]);
						$res_link = $q_link->fetch_assoc();
					}
					echo "<a href=\"/" . $_SESSION['lang']['short'] . "/" . $_SESSION["path"][1] . "/" . $res_link["permalink"] . "?id=" . $res["course_id"] . "\" class=\"button\">" . translation::get("kurs_details") . "</a>";
					echo "</div>";
					echo "</div>";
				}
			}
		}
		echo "</div>";
		echo "</div>";
	}

	/**
	 * @param  $categoryId
	 * @return mixed
	 */
	public function getCourseList($categoryId) {
		$data = array();
		$q    = $this->db->query("SELECT course_id, cl.title as title FROM tbl_course as c LEFT JOIN tbl_course_lang as cl on c.course_id=cl.course_fk WHERE cl.lang_fk='" . $_SESSION["lang"]["key"] . "' AND category_fk='" . $categoryId . "' AND c.is_deleted=0 ORDER BY passive ASC");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$data[] = $res;
		}
		return $data;
	}

	public function export() {
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=anmeldungen.xls");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);

		echo "<html>";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		echo "</head>";
		echo "<body>";
		$table = new table();
		$table->addSubtitle(["cols" => ["lastname", "firstname", "heimatort", "plz", "ort", "strasse", "number", "mobile", "email", "dog", "hund_geschlecht", "kastration"]]);
		$query = "SELECT anmeldung_id,lastname,firstname,heimatort,plz,location,street,number,mobile,email,dog,hund_geschlecht,kastration FROM tbl_anmeldung WHERE course_fk=" . $_GET["export"];
		$q     = $this->db->query($query);
		while ($res = $q->fetch_assoc()) {
			$hund_geschlecht = "";
			if ($res["hund_geschlecht"] == 2) {
				$hund_geschlecht = "Hündin";
			} elseif ($res["hund_geschlecht"] == 1) {
				$hund_geschlecht = "Rüde";
			}
			$kastration = "";
			if ($res["kastration"] == 2) {
				$kastration = "Kastriert";
			} elseif ($res["kastration"] == 1) {
				$kastration = "Nicht kastriert";
			}
			$table->add(["cols" => [
				$res["lastname"],
				$res["firstname"],
				$res["heimatort"],
				$res["plz"],
				$res["location"],
				$res["street"],
				$res["number"],
				$res["mobile"],
				$res["email"],
				$res["dog"],
				$hund_geschlecht,
				$kastration],
			]);
		}
		echo $table->render();
		echo "</body>";
		echo "</html>";
	}
}
?>
