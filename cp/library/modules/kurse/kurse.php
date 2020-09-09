<?php
class kurse extends module {
	/**
	 * @var mixed
	 */
	public static $auth = true;
	public $success = false;
	/**
	 * @var array
	 */
	public static $authLevel = [1, 2, 3];

	/**
	 * @var array
	 */
	private $kursleitung = [];
	public function setup() {

	}

	public function controller() {


		if ($_GET["kurs"] != "") {
			if ($_POST["save"] != "") {
				//Update Kurs
				$date        = date("Y-m-d", strtotime($_POST["anmeldefrist"]));
				$course_edit = "UPDATE tbl_course SET
									passive='" . $_POST["passive"] . "',
									image='" . $_POST["image"] . "',
									lektion_kosten='" . $_POST["lektion_kosten"] . "',
									zehnerabo_kosten='" . $_POST["zehnerabo_kosten"] . "',
									anmeldefrist='" . $date . "',
									category_fk='" . $_POST["category_fk"] . "'
								WHERE
									course_id='" . $_GET["kurs"] . "'";

				$query = $this->db->query($course_edit);



				foreach ($_POST as $key => $value) {

					if (strpos($key, 'lang_fk_') !== false) {

						$lang[] = $value;
					}
				}
				// print_r($_POST);
				foreach ($lang as $key) {
					$q = $this->db->query("SELECT * FROM tbl_course_lang WHERE course_fk='" . $_GET["kurs"] . "' AND lang_fk='" . $_POST["lang_fk_"] . $key . "'");

					if ($q->num_rows > 0) {
						$course_lang_edit = "UPDATE tbl_course_lang SET
							title='" . $_POST["title_" . $key] . "',
							text='" . $_POST["content_" . $key] . "',
							lernziele='" . $_POST["lernziele_" . $key] . "',
							zielgruppe='" . $_POST["zielgruppe_" . $key] . "',
							ort='" . $_POST["ort_" . $key] . "'
						WHERE
							course_fk='" . $_GET["kurs"] . "' AND
							lang_fk='" . $_POST["lang_fk_" . $key] . "'";
						// echo $course_lang_edit;
						// echo "update";
						echo $this->db->error;
						$query = $this->db->query($course_lang_edit);

					} else {

						$course_lang_insert = "INSERT INTO tbl_course_lang (title,text,lernziele,zielgruppe,ort,lang_fk,course_fk) VALUES('" . $_POST["title_" . $key] . "','" . $_POST["content_" . $key] . "','" . $_POST["lernziele_" . $key] . "','" . $_POST["zielgruppe_" . $key] . "','" . $_POST["ort_" . $key] . "','" . $_POST["lang_fk_" . $key] . "','" . $_GET["kurs"] . "') ";
						// echo "insert<br>";
						echo "insert";
						echo $this->db->error;
						$query = $this->db->query($course_lang_insert);
					}

				}
				
				if($query) {
					$this->success = 1;
				} else {
					$this->success = -1;
				}
				
			}

			
		}
		if($_GET["kursdatum"] != ""){
			if ($_POST["save"] != "") {
				//ADD Kursdatum
				if ($_POST["time_from"] != "" && $_POST["date"] != "01.01.1970" && strtotime($_POST["date"]) >= strtotime(date("d.m.Y"))) {
					$date      = date("Y-m-d", strtotime($_POST["date"]));
					//$time_from = date("H:i", strtotime($_POST["time_from"]));
					//$time_to   = date("H:i", strtotime($_POST["time_to"]));
					$time_from = $_POST["time_from"];
					$time_to = $_POST["time_to"];
					$insert    = "INSERT INTO tbl_course_date (date,time_from, time_to, course_fk,constant) VALUES ('" . $date . "','" . $time_from . "', '" . $time_to . "', '" . $_GET["kursdatum"] . "', '" . $_POST["constant"] . "')";
					echo $this->db->error;
					$query = $this->db->query($insert);
					if($query) {
						$this->success = 1;
					} else {
						$this->success = -1;
					}
				} else {
					?>
					<script type="text/javascript">alert("Date should be greater than todays date.");</script>
				<?php
				}
				//Edit Kursdatum
				foreach ($_POST as $key => $value) {
					if (strpos($key, 'course_date') !== false) {
						$date_id[] = $value;
					}
				}
				foreach ($date_id as $key) {
					$edit_date      = date("Y-m-d", strtotime($_POST["edit_date_" . $key]));
					//$edit_time_from = date("H:i", strtotime($_POST["edit_time_from_" . $key]));
					//$edit_time_to   = date("H:i", strtotime($_POST["edit_time_to_" . $key]));
					$edit_time_from = $_POST["edit_time_from_" . $key];
					$edit_time_to   = $_POST["edit_time_to_" . $key];

					$edit_constant  = $_POST["edit_constant_" . $key];
					$update         = "UPDATE tbl_course_date SET date='" . $edit_date . "',time_from='" . $edit_time_from . "',time_to='" . $edit_time_to . "',course_fk='" . $_GET["kursdatum"] . "',constant='" . $edit_constant . "' WHERE course_date_id='" . $key . "'";
					$query_update = $this->db->query($update);
					echo $this->db->error;

					if($query_update) {
						$this->success = 1;
					} else {
						$this->success = -1;
					}
				}
			}
			//DELETE Kursdatum
			if ($_GET["prmv"] != "") {
				$delete = "DELETE FROM tbl_course_date WHERE course_date_id='" . $_GET["prmv"] . "'";
				$this->db->query($delete);
			}
		}
		

		//DELETE kurse and kurse_lang
		if ($_GET["prmv"] != "") {
			$delete  = "UPDATE tbl_course SET is_deleted=1 WHERE course_id='" . $_GET["prmv"] . "'";
			$delete1 = "UPDATE tbl_course_lang SET is_deleted=1 WHERE course_fk='" . $_GET["prmv"] . "'";
			$this->db->query($delete);
			$this->db->query($delete1);
		}

		// ADD Kurs
		if ($_GET["add"] != "") {
			if ($_POST["save"] != "") {
				$date   = date("Y-m-d", strtotime($_POST["anmeldefrist"]));
				$insert = "INSERT INTO tbl_course (
					passive,
					image,
					lektion_kosten,
					zehnerabo_kosten,
					anmeldefrist,
					category_fk
					)
					VALUES (
						'" . $_POST["passive"] . "',
						'" . $_POST["image"] . "',
						'" . $_POST["lektion_kosten"] . "',
						'" . $_POST["zehnerabo_kosten"] . "',
						'" . $date . "',
						'" . $_POST["category_fk"] . "'
					)";

				$this->db->query($insert);
				echo $this->db->error;
				foreach ($_POST as $key => $value) {

					if (strpos($key, 'lang_fk_') !== false) {

						$lang[] = $value;
					}
				}
				$q   = $this->db->query("SELECT course_id FROM tbl_course ORDER by course_id DESC");
				$res = $q->fetch_assoc();
				foreach ($lang as $key) {
					$course_lang_insert = "INSERT INTO tbl_course_lang (title,text,lernziele,zielgruppe,ort,lang_fk,course_fk) VALUES('" . $_POST["title_" . $key] . "','" . $_POST["content_" . $key] . "','" . $_POST["lernziele_" . $key] . "','" . $_POST["zielgruppe_" . $key] . "','" . $_POST["ort_" . $key] . "','" . $_POST["lang_fk_" . $key] . "','" . $res["course_id"] . "') ";
					echo $this->db->error;
					$this->db->query($course_lang_insert);
				}
				echo $this->db->error;
			}
		}

		//editKursleiter

		$kursleiter = array();
		if ($_POST["save"] != "") {
			$this->db->query("DELETE FROM tbl_course_seminar_leitung WHERE course_fk='" . $_GET["kurs"] . "'");
			foreach ($_POST as $key => $value) {

				if (strpos($key, 'active_') !== false) {

					$kursleiter[] = $value;
				}
			}
			foreach ($kursleiter as $key) {
				$this->db->query("INSERT INTO tbl_course_seminar_leitung (course_fk,leitung_fk) VALUES ('" . $_GET["kurs"] . "', '" . $_POST["active_" . $key] . "')");
				$this->db->error;

			}
		}

		// Successmessage

		if ($this->success == 1) {

            echo cp::message(["message" => translation::get("success"), "type" => "success"]);

        } else if ($this->success == -1) {

            echo cp::message(["message" => translation::get("error"), "type" => "error"]);

        }
	}

	public function view() {
		if ($_GET["add"] != "" || $_GET["kurs"] != "") {
			$this->editCourse();
		} elseif ($_GET["anmeldungen"]) {
			$this->showAnmeldungen();
		} elseif ($_GET["export"]) {
			$this->export();
		} elseif ($_GET["kursdatum"]) {
			$this->editCourseDate();
		} else {
			$this->showCourse();
		}

	}

	//Edit course
	public function editCourse() {
		$header = new header();
		$header->addTitle(translation::get("kurse_title"));
		$header->addParagraph(translation::get("kurse_edit_text"));
		echo $header->render();

		$title = [
			translation::get("title"),
			translation::get("image"),
			translation::get("content"),
			translation::get("category"),
			translation::get("anmeldefrist"),
			translation::get("passive"),
		];
		$fields = [
			"title",
			"image",
			"text",
			"category_fk",
		];

		// $table = new table();
		// $table->setup(["td"=>[50,0]]);
		// $table->addTitle(["cols"=>["test"]]);
		// $table->add(["cols"=>["Dimo","<div style=\"width:100%;height:20px;background-color:#FFF;\"><div style=\"width:60%;height:20px;background-color:#0F0;\"></div></div>"]]);
		// echo $table->render();

		// edit Kurse
		$query = "SELECT passive,image,lektion_kosten,zehnerabo_kosten,anmeldefrist,category_fk FROM tbl_course WHERE course_id='" . $_GET["kurs"] . "' AND is_deleted = 0 ";
		$q     = $this->db->query($query);
		$kurse = $q->fetch_assoc();

		if ($_GET["kurs"] != "") {
			$date = date("d.m.Y", strtotime($kurse["anmeldefrist"]));
		}

		//addCategoriesToKurse
		$query_cat = "SELECT course_category_id, ccl.title as title FROM tbl_course_category as cc
						LEFT JOIN tbl_course_category_lang as ccl
						on cc.course_category_id=ccl.course_category_fk
						WHERE is_deleted = 0
						AND lang_fk=" . $_SESSION["lang"]["key"] . "
						ORDER BY course_category_id ASC";
		$q_cat = $this->db->query($query_cat);
		while ($res_cat = $q_cat->fetch_assoc()) {
			$categories[$res_cat["course_category_id"]] = $res_cat["title"];
		}
		$table = new table();
		$table->setup(["form" => ["method" => "post", "action" => "/cp/async/kurse/view"], "td" => [120]]);
		$table->addTitle(["cols" => [translation::get("kursdetails")]]);
		$passive = 0;
		if ($kurse["passive"] == 1) {
			$passive = 1;
		}
		//Language
		$language = "SELECT cms_lang_id, short, name FROM cms_lang";
		$q_lang   = $this->db->query($language);

		//language loop
		while ($lang = $q_lang->fetch_assoc()) {
			//Title and content

			$course_lang     = "SELECT title, text FROM tbl_course_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND course_fk='" . $_GET["kurs"] . "'";
			$q_course_lang   = $this->db->query($course_lang);
			$res_course_lang = $q_course_lang->fetch_assoc();

			$table->addSubtitle(["cols" => [$lang["name"]]]);
			$table->add(["cols" => [$title[0], $table->addFormField(["name" => "title_" . $lang["cms_lang_id"], "value" => $res_course_lang["title"], "type" => "text"])]]);
			$table->add(["cols" => [$title[2], $table->addFormField(["name" => "content_" . $lang["cms_lang_id"], "type" => "tinymce", "value" => $res_course_lang["text"]])
				. $table->addFormfield(["name" => "lang_fk_" . $lang["cms_lang_id"], "value" => $lang["cms_lang_id"], "type" => "hidden"])]]);

			//Lernziele

			$course_lang_lernziele     = "SELECT lernziele FROM tbl_course_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND course_fk='" . $_GET["kurs"] . "'";
			$q_course_lang_lernziele   = $this->db->query($course_lang_lernziele);
			$res_course_lang_lernziele = $q_course_lang_lernziele->fetch_assoc();

			$table->add(["cols" => [translation::get("lernziele"), $table->addFormField(["name" => "lernziele_" . $lang["cms_lang_id"], "type" => "tinymce", "value" => $res_course_lang_lernziele["lernziele"]])]]);

			// Kursdetails Zielgruppe und Ort

			$course_lang_details     = "SELECT zielgruppe,ort FROM tbl_course_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND course_fk='" . $_GET["kurs"] . "'";
			$q_course_lang_details   = $this->db->query($course_lang_details);
			$res_course_lang_details = $q_course_lang_details->fetch_assoc();

			$table->add(["cols" => ["Zielgruppe", $table->addFormfield(["name" => "zielgruppe_" . $lang["cms_lang_id"], "value" => $res_course_lang_details["zielgruppe"], "type" => "text"])]]);
			$table->add(["cols" => ["Ort", $table->addFormfield(["name" => "ort_" . $lang["cms_lang_id"], "value" => $res_course_lang_details["ort"], "type" => "text"])]]);
		}
		$table->addSubtitle(["cols" => ["Allgemein"]]);
		$table->add(["cols" => [translation::get("image_src"), $table->addFormField(["name" => $fields[1], "value" => $kurse["image"], "type" => "text"])]]);
		$table->add(["cols" => [$title[1], $table->addImageSelect(["target" => "image", "attr" => ["targetWidth" => 1024, "targetHeight" => 1024]])]]);
		$table->add(["cols" => [$title[3], $table->addFormField(["name" => $fields[3], "type" => "select", "value" => $kurse["category_fk"], "options" => $categories])]]);
		//$table->add(["cols" => [$title[4], $table->addFormField(["name" => "anmeldefrist", "type" => "date", "value" => $date])]]);
		$table->add(["cols" => [$title[5], $table->addFormField(["name" => "passive", "type" => "checkbox", "value" => $kurse["passive"], "set" => 1])]]);

		// SHOW Kursleitung
		
			$table->addSubtitle(["cols" => [translation::get("kursleiter_waehlen")]]);
			$query_leitung = "SELECT leitung_id, name, email FROM tbl_leitung";
			$q_leitung     = $this->db->query($query_leitung);

			$leitung = $this->db->query("SELECT leitung_fk, course_fk FROM tbl_course_seminar_leitung");

			while ($res_leitung = $q_leitung->fetch_assoc()) {
				$leitung = $this->db->query("SELECT leitung_fk, course_fk FROM tbl_course_seminar_leitung");

				$checked = 0;
				while ($res = $leitung->fetch_assoc()) {
					if ($res_leitung["leitung_id"] == $res["leitung_fk"] && $_GET["kurs"] == $res["course_fk"]) {
						$checked = $res_leitung["leitung_id"];
					}
				}
				$table->add([
					"cols"     => [
						["value" => $res_leitung["name"]],
						["value" => $res_leitung["email"]],
						$table->addFormField(["name" => "active_" . $res_leitung["leitung_id"], "set" => $res_leitung["leitung_id"], "type" => "checkbox", "value" => $checked]),
					],
				]);
			}
		
		$table->addSubtitle(["cols" => ["Kosten"]]);
		$table->add(["cols" => [translation::get("lektion_kosten"), $table->addFormfield(["name" => "lektion_kosten", "value" => $kurse["lektion_kosten"], "type" => "text"])]]);
		$table->add(["cols" => [translation::get("zehnerabo_kosten"), $table->addFormfield(["name" => "zehnerabo_kosten", "value" => $kurse["zehnerabo_kosten"], "type" => "text"])]]);

		if ($_GET["kurs"] != "") {
			
		}

		$table->add(["cols" => ["", $table->addFormfield(["name" => "save", "value" => translation::get("save"), "type" => "submit"])]]);
		echo $table->render();
	}

	public function editCourseDate()
	{
		$header = new header();
		$header->addTitle(translation::get("kursedate_title"));
		$header->addParagraph(translation::get("kursedate_edit_text"));
		echo $header->render();

		$table = new table();
		$table->setup(["td" => [100, 0, 40], "form" => ["method"=>"post","sqltable" => "tbl_course_date","action" => "/cp/async/kurse/view"]]);

		// ADD Kursdatum
			$kurs_title = [
				"time_from",
				"time_to",
			];

			$table->addSubtitle(["cols" => [translation::get("kurs_date")]]);
			$table->add(["cols" => [
				translation::get("Datum"),
				translation::get("time"),
				//translation::get("Bis"),
				translation::get("Kontinuierlich"),
			]]);

			$table->add(["cols" => [
				$table->addFormfield(["name" => "date", "value" => "", "type" => "date"]),
				//$table->addFormfield(["name" => "time_from", "value" => "", "type" => "time"]),
				$table->addFormfield(["name" => "time_from", "value" => "", "type" => "text"]),
				//$table->addFormfield(["name" => "time_to", "value" => "", "type" => "time"]),
				//$table->addFormfield(["name" => "time_to", "value" => "", "type" => "text"]),
				$table->addFormfield(["name" => "constant", "value" => "", "set" => "1", "type" => "checkbox"]),

			]]);
			$q = $this->db->query("SELECT course_date_id, date,time_from, time_to, course_date_id,constant FROM tbl_course_date WHERE course_fk='" . $_GET["kursdatum"] . "' AND (date >= CURDATE() OR constant=1) ORDER by date ASC");
			while ($res = $q->fetch_assoc()) {
				$date      = date("d.m.Y", strtotime($res["date"]));
				//$time_from = date("H:i", strtotime($res["time_from"]));
				//$time_to   = date("H:i", strtotime($res["time_to"]));
				$time_from = $res["time_from"];
				$time_to = $res["time_to"];
				$date_id   = $res["course_date_id"];
				if ($res["constant"] != 1) {
					unset($res["constant"]);
				}

				$table->add([
					"cols"     => [
						$table->addFormfield(["name" => "edit_date_" . $date_id, "value" => $date, "type" => "date", "attr" => ["drequired" => true]]),
						$table->addFormfield(["name" => "edit_time_from_" . $date_id, "value" => $time_from, "type" => "text", "attr" => ["drequired" => true]]),
						//$table->addFormfield(["name" => "edit_time_to_" . $date_id, "value" => $time_to, "type" => "text", "attr" => ["drequired" => true]]),
						$table->addFormfield(["name" => "edit_constant_" . $date_id, "value" => $res["constant"], "set" => 1, "type" => "checkbox"]) . $table->addFormfield(["name" => "course_date_" . $date_id, "value" => $date_id, "type" => "hidden"]),
					],
					"controls" => [
						"/cp/async/kurse/view?kursdatum=" . $_GET["kursdatum"] . "&prmv=" . $date_id => translation::get("rmv"),
					],
				]);
			}
			$table->add(["cols" => ["", $table->addFormfield(["name" => "save", "value" => translation::get("save"), "type" => "submit"])]]);

			echo $table->render();
	}

	public function showCourse() {
		$header = new header();
		$header->addTitle(translation::get("kurse_title"));
		$header->addParagraph(translation::get("kurse_text"));
		echo $header->render();
		$title = [
			translation::get("title"),
			translation::get("category"),
			translation::get("image"),
			translation::get("content"),

		];
		$fields = [
			"title",
			"category_fk",
			"image",
			"text",

		];

		//SHOW Courses

		$table = new table();
		$table->setup(["td" => [120, 180, 120, 0], "form" => ["sqltable" => "tbl_course"]]);
		// $table->controller();
		$table->addTitle(["cols" => [translation::get("upcoming_courses")], "controls" => ["/cp/kurse?add=1" => translation::get("add")]]);
		$table->addSubtitle(["cols" => [$title[0], $title[1], $title[2], $title[3]]]);
		// $table->auto(["id"=>"course_id","select"=>implode(",",$fields),"from"=>"tbl_course","controls"=>["/cp/kurse?kurs={id}"=>translation::get("edit"),"/cp/async/kurse/view?prmv={id}"=>translation::get("rmv")]]);
		$q = $this->db->query("SELECT course_id, cl.title as title, cc.title as category, category_fk, image, cl.text as text FROM tbl_course as c LEFT JOIN tbl_course_category as cc on c.category_fk=cc.course_category_id LEFT JOIN tbl_course_lang as cl on cl.course_fk=c.course_id WHERE cl.lang_fk=1 AND c.is_deleted=0");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$table->add(["cols" => [
				$res["title"],
				$res["category"],
				"<image src=\"" . $res["image"] . "\" width=\"200px\"/>",
				$res["text"]],
				"controls"          => [
					"/cp/kurse?kurs=" . $res["course_id"]             => translation::get("edit"),
					"/cp/kurse?kursdatum=" . $res["course_id"]             => translation::get("kursdatum"),
					"/cp/kurse?anmeldungen=" . $res["course_id"]      => translation::get("anmeldungen"),
					"/async/kurse/export?export=" . $res["course_id"] => translation::get("export_excel"),
					"/cp/async/kurse/view?prmv=" . $res["course_id"]  => translation::get("rmv"),
				],
			]);
		}
		echo $table->render();
	}

	public function showAnmeldungen() {
		$header = new header();
		$header->addTitle(translation::get("Anmeldungen"));
		$header->addParagraph(translation::get("kurse_text"));
		echo $header->render();

		$table = new table();
		$table->setup(["td" => [0, 0, 100], "form" => ["sqltable" => "tbl_anmeldung", "sqlwhere" => ["course_fk" => $_GET["anmeldungen"]]]]);
		$table->controller();
		$table->addTitle(["cols" => ["lastname", "firstname", "heimatort", "plz", "ort", "strasse", "number", "mobile", "email", "dog", "hund_geschlecht", "kastration"], "controls" => ["/async/kurse/export?export=" . $_GET["anmeldungen"] => translation::get("export excel")]]);
		$q = $this->db->query("SELECT lastname,firstname,human_day,human_month,human_year,heimatort,plz,location,street,number,mobile,email,dog,hund_geschlecht,kastration FROM tbl_anmeldung WHERE course_fk='" . $_GET["anmeldungen"] . "'");
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

	}
}
?>
