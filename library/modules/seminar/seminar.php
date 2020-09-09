<?php

class seminar extends module {
	/**
	 * @param  $config
	 * @return mixed
	 */
	public function get($config) {
		$q = $this->db->query("SELECT seminar_id
									  FROM tbl_seminar
									  WHERE cms_container_fk='" . $config["cms_container_id"] . "'");
		$res = $q->fetch_assoc();
		return $res;
	}

	/**
	 * @param $container_fk
	 * @param $category_id
	 */
	public function getSeminar($container_fk, $category_id = "") {
		echo "<div class=\"container\">";
		$query_cat = "SELECT seminar_category_id, title FROM tbl_seminar_category";
		$q_cat     = $this->db->query($query_cat);
		echo "<form class=\"beige form filter\" id=\"seminar\" method=\"POST\">";
		echo "<div class=\"filter-header \">";
		echo "<h2 class=\"center\">Filter</h2>";
		echo "<div class=\"centered_btn\">";
		while ($res = $q_cat->fetch_assoc()) {
			echo "<label for=\"cat_" . $res["seminar_category_id"] . "\" class=\"text_brown button btn\">";
			echo $res["title"];
			echo "</label>";
			echo "<input class=\"cat_input\" id=\"cat_" . $res["seminar_category_id"] . "\" name=\"cat_" . $res["seminar_category_id"] . "\" type=\"checkbox\" style=\"visibility:hidden\" value=\"" . $res["seminar_category_id"] . "\">";
		}
		echo "</div>";
		echo "</div>";
		echo "</form>";
	}

	public function getSeminarAjax() {
		$category = array();
		$params   = array();

		foreach ($_POST as $key => $value) {
			parse_str($value, $params);

			foreach ($params as $k => $v) {

				$category[] = $v;

			}
		}
		$data = $this->getSeminarContent($category);
		if ($data != null) {
			echo json_encode($data);
		}

	}

	/**
	 * @param array $category
	 */
	public function getSeminarContent($category = array()) {
		if (count($category) > 0) {
			$query_seminar = "SELECT seminar_id, sl.title as title, date, image, sl.text as text, category_fk FROM tbl_seminar as s LEFT JOIN tbl_seminar_date as sd on s.seminar_id=sd.seminar_fk LEFT JOIN tbl_seminar_lang as sl on sl.seminar_fk=s.seminar_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND passive=0 AND category_fk IN (" . implode(',', $category) . ") AND s.is_deleted=0 GROUP by seminar_id";
		} else {
			$query_seminar = "SELECT seminar_id, sl.title as title, date, image, sl.text as text, category_fk FROM tbl_seminar as s LEFT JOIN tbl_seminar_date as sd on s.seminar_id=sd.seminar_fk LEFT JOIN tbl_seminar_lang as sl on sl.seminar_fk=s.seminar_id WHERE lang_fk='" . $_SESSION["lang"]["key"] . "' AND s.is_deleted=0 AND passive=0 GROUP by seminar_id";
		}
		$q_seminar = $this->db->query($query_seminar);
		echo $this->db->error;

		$seminar = array();
		if ($q_seminar->num_rows > 0) {
			while ($res = $q_seminar->fetch_assoc()) {

				if ($res["date"] >= date("Y-m-d") || $res["date"] == null) {
					echo "<div class=\"row_article beige\">";
					echo "<img src=\"" . $res["image"] . "\">";
					echo "<div class=\"article\">";
					echo "<h3>" . $res["title"] . "</h3>";
					$q_leitung = $this->db->query("SELECT name FROM tbl_leitung WHERE leitung_id=" . $res["leitung_fk"]);
					echo $res["text"];
					echo "<a href=\"/" . $_SESSION['lang']['short'] . "/seminar/seminardetails?id=" . $res["seminar_id"] . "\" class=\"button\">" . translation::get("kurs_details") . "</a>";
					echo "</div>";
					echo "</div>";
				}
			}
		}

	}

	/**
	 * @param  $categoryId
	 * @return mixed
	 */
	public function getSeminarList($categoryId) {
		$data  = array();
		$query = "SELECT seminar_id, sl.title as title FROM tbl_seminar as s LEFT JOIN tbl_seminar_lang as sl on s.seminar_id=sl.seminar_fk WHERE sl.lang_fk=" . $_SESSION['lang']['key'] . " AND category_fk=" . $categoryId . " AND s.is_deleted=0 ORDER BY passive ASC";
		$q     = $this->db->query($query);
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$data[] = $res;
		}

		return $data;
	}

	public function export() {
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
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
		$query = "SELECT anmeldung_id,lastname,firstname,heimatort,plz,location,street,number,mobile,email,dog,hund_geschlecht,kastration FROM tbl_anmeldung WHERE seminar_fk=" . $_GET["export"];
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
