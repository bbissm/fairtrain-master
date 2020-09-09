<?php
class seminar extends module {
	/**
	 * @var mixed
	 */
	public static $auth = true;
	/**
	 * @var array
	 */

	public $success = 0;
	public static $authLevel = [1, 2, 3];

	/**
	 * @var array
	 */
	private $seminarleitung = [];
	public function setup() {

	}

	public function controller() {

		if ($_GET["seminar"] != "") {
			if ($_POST["save"] != "") {
				//Update seminar
				$date         = date("Y-m-d", strtotime($_POST["anmeldefrist"]));
				$seminar_edit = "UPDATE tbl_seminar SET
									passive='" . $_POST["passive"] . "',
									image='" . $_POST["image"] . "',
									anmeldefrist='" . $date . "',
									category_fk='" . $_POST["category_fk"] . "'
								WHERE
									seminar_id='" . $_GET["seminar"] . "'";

				$query = $this->db->query($seminar_edit);
				if($query){
					$this->success = 1;
				}
				foreach ($_POST as $key => $value) {

					if (strpos($key, 'lang_fk_') !== false) {

						$lang[] = $value;
					}
				}

				// print_r($_POST);
				foreach ($lang as $key) {
					$q = $this->db->query("SELECT * FROM tbl_seminar_lang WHERE seminar_fk='" . $_GET["seminar"] . "' AND lang_fk='" . $_POST["lang_fk_"] . $key . "'");

					if ($q->num_rows > 0) {

						$seminar_lang_edit = "UPDATE tbl_seminar_lang SET
							title='" . $_POST["title_" . $key] . "',
							text='" . $_POST["content_" . $key] . "',
							lernziele='" . $_POST["lernziele_" . $key] . "',
							zielgruppe='" . $_POST["zielgruppe_" . $key] . "',
							ort='" . $_POST["ort_" . $key] . "',
							kosten='" . $_POST["kosten_" . $key] . "',
							mit_hund='" . $_POST["mit_hund_" . $key] . "',
							ohne_hund='" . $_POST["ohne_hund_" . $key] . "',
							kursplaetze='" . $_POST["kursplaetze_" . $key] . "'
						WHERE
							seminar_fk='" . $_GET["seminar"] . "' AND
							lang_fk='" . $_POST["lang_fk_" . $key] . "'";
						// echo $seminar_lang_edit;
						// echo "update";
						// echo $seminar_lang_edit;
						echo $this->db->error;
						$query = $this->db->query($seminar_lang_edit);
						if($query){
							$this->success = 1;
						}

					} else {

						$seminar_lang_insert = "INSERT INTO tbl_seminar_lang (title,text,lernziele,zielgruppe,ort,kosten,mit_hund,ohne_hund,kursplaetze,lang_fk,seminar_fk) VALUES('" . $_POST["title_" . $key] . "','" . $_POST["content_" . $key] . "','" . $_POST["lernziele_" . $key] . "','" . $_POST["zielgruppe_" . $key] . "','" . $_POST["ort_" . $key] . "','" . $_POST["kosten_" . $key] . "','" . $_POST["mit_hund_" . $key] . "','" . $_POST["ohne_hund_" . $key] . "','" . $_POST["kursplaetze_" . $key] . "','" . $_POST["lang_fk_" . $key] . "','" . $_GET["seminar"] . "') ";
						// echo "insert<br>";
						echo "insert";
						echo $this->db->error;
						$query = $this->db->query($seminar_lang_insert);
						if($query){
							$this->success = 1;
						}
					}

				}
				
			}

		}
		if ($_GET["seminardatum"] != ""){
			//ADD seminardatu
			if ($_POST["save"] != ""){
				if ($_POST["time_from"] != "" && $_POST["date"] != "01.01.1970" && strtotime($_POST["date"]) >= strtotime(date("d.m.Y"))) {
					$date      = date("Y-m-d", strtotime($_POST["date"]));
					$time_from = date("H:i", strtotime($_POST["time_from"]));
					$time_to   = date("H:i", strtotime($_POST["time_to"]));
					$insert    = "INSERT INTO tbl_seminar_date (date,time_from, time_to, seminar_fk) VALUES ('" . $date . "','" . $time_from . "', '" . $time_to . "', '" . $_GET["seminardatum"] . "')";
					$this->db->query($insert);
					echo $this->db->error;
				} else {
					?>
					<script type="text/javascript">alert("Date should be greater than todays date.");</script>
				<?php
}
				//Edit seminardatum
				foreach ($_POST as $key => $value) {
					if (strpos($key, 'seminar_date') !== false) {
						$date_id[] = $value;
					}
				}
				foreach ($date_id as $key) {
					$edit_date      = date("Y-m-d", strtotime($_POST["edit_date_" . $key]));
					$edit_time_from = date("H:i", strtotime($_POST["edit_time_from_" . $key]));
					$edit_time_to   = date("H:i", strtotime($_POST["edit_time_to_" . $key]));
					$update         = "UPDATE tbl_seminar_date SET date='" . $edit_date . "',time_from='" . $edit_time_from . "',time_to='" . $edit_time_to . "',seminar_fk='" . $_GET["seminardatum"] . "' WHERE seminar_date_id='" . $key . "'";
					$this->db->query($update);
					echo $this->db->error;
				}
				//DELETE seminardatum
				if ($_GET["prmv"] != "") {
					$delete = "DELETE FROM tbl_seminar_date WHERE seminar_date_id='" . $_GET["prmv"] . "'";
					$this->db->query($delete);
				}
			}
		}

		if ($_GET["prmv"] != "") {
			$delete  = "UPDATE tbl_seminar SET is_deleted=1 WHERE seminar_id='" . $_GET["prmv"] . "'";
			$delete1 = "UPDATE tbl_seminar_lang SET is_deleted=1 WHERE seminar_fk='" . $_GET["prmv"] . "'";
			$this->db->query($delete);
			$this->db->query($delete1);
		}

		// ADD seminar
		if ($_GET["add"] != "") {
			if ($_POST["save"] != "") {
				$date   = date("Y-m-d", strtotime($_POST["anmeldefrist"]));
				$insert = "INSERT INTO tbl_seminar (
					passive,
					image,
					anmeldefrist,
					category_fk
					)
					VALUES (
						'" . $_POST["passive"] . "',
						'" . $_POST["image"] . "',
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
				$q   = $this->db->query("SELECT seminar_id FROM tbl_seminar ORDER by seminar_id DESC");
				$res = $q->fetch_assoc();
				foreach ($lang as $key) {
					$seminar_lang_insert = "INSERT INTO tbl_seminar_lang (title,text,lernziele,zielgruppe,ort,kosten,mit_hund,ohne_hund,kursplaetze,lang_fk,seminar_fk) VALUES('" . $_POST["title_" . $key] . "','" . $_POST["content_" . $key] . "','" . $_POST["lernziele_" . $key] . "','" . $_POST["zielgruppe_" . $key] . "','" . $_POST["ort_" . $key] . "','" . $_POST["kosten_" . $key] . "','" . $_POST["mit_hund_" . $key] . "','" . $_POST["ohne_hund_" . $key] . "','" . $_POST["kursplaetze_" . $key] . "','" . $_POST["lang_fk_" . $key] . "','" . $res["seminar_id"] . "') ";
					echo $this->db->error;
					$this->db->query($seminar_lang_insert);
				}
				echo $this->db->error;
			}
		}
		//editseminarleiter
		$seminarleiter = array();
		if ($_POST["save"] != "") {
			$this->db->query("DELETE FROM tbl_seminar_leitung WHERE seminar_fk='" . $_GET["seminar"] . "'");
			foreach ($_POST as $key => $value) {

				if (strpos($key, 'active_') !== false) {

					$seminarleiter[] = $value;
				}
			}
			foreach ($seminarleiter as $key) {
				$this->db->query("INSERT INTO tbl_seminar_leitung (seminar_fk,leitung_fk) VALUES ('" . $_GET["seminar"] . "', '" . $_POST["active_" . $key] . "')");
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
		if ($_GET["add"] != "" || $_GET["seminar"] != "") {
			$this->editSeminar();
		} elseif ($_GET["seminardatum"]) {
			$this->editSeminarDate();
		} elseif ($_GET["anmeldungen"]) {
			$this->showAnmeldungen();
		} elseif ($_GET["export"]) {
			$this->export();
		} else {
			$this->showSeminar();
		}

	}

	//editseminar
	public function editSeminar() {
		$header = new header();
		$header->addTitle(translation::get("seminar_title"));
		$header->addParagraph(translation::get("seminar_text"));
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
			"content",
			"category_fk",
		];

		// edit seminar
		$query = "SELECT passive,image,anmeldefrist,category_fk FROM tbl_seminar WHERE seminar_id='" . $_GET["seminar"] . "' AND is_deleted = 0 ";
		$q     = $this->db->query($query);
		echo $this->db->error;
		$seminar = $q->fetch_assoc();

		if ($_GET["add"] != "") {

		} elseif ($_GET["seminar"] != "") {
			$date = date("d.m.Y", strtotime($seminar["anmeldefrist"]));
		}

		//addCategoriesToseminar
		$query_cat = "SELECT seminar_category_id, title FROM tbl_seminar_category WHERE is_deleted = 0 ORDER BY title ASC";
		$q_cat     = $this->db->query($query_cat);
		while ($res_cat = $q_cat->fetch_assoc()) {
			$categories[$res_cat["seminar_category_id"]] = $res_cat["title"];
		}
		$table = new table();
		$table->setup(["form" => ["method" => "post", "action" => "/cp/async/seminar/view"], "td" => [120]]);
		$table->addTitle(["cols" => [translation::get("edit")]]);
		$passive = 0;
		if ($seminar["passive"] == 1) {
			$passive = 1;
		}

		//Language
		$language = "SELECT cms_lang_id, short, name FROM cms_lang";
		$q_lang   = $this->db->query($language);

		//language loop
		while ($lang = $q_lang->fetch_assoc()) {
			$seminar_lang     = "SELECT title, text FROM tbl_seminar_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND seminar_fk='" . $_GET["seminar"] . "'";
			$q_seminar_lang   = $this->db->query($seminar_lang);
			$res_seminar_lang = $q_seminar_lang->fetch_assoc();

			$table->addSubtitle(["cols" => [$lang["name"]]]);
			$table->add(["cols" => [$title[0], $table->addFormField(["name" => "title_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["title"], "type" => "text"])]]);
			$table->add(["cols" => [$title[2], $table->addFormField(["name" => "content_" . $lang["cms_lang_id"], "type" => "tinymce", "value" => $res_seminar_lang["text"]])
				. $table->addFormfield(["name" => "lang_fk_" . $lang["cms_lang_id"], "value" => $lang["cms_lang_id"], "type" => "hidden"])]]);

			//seminar lernziele
			$seminar_lang_lernziele     = "SELECT lernziele FROM tbl_seminar_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND seminar_fk='" . $_GET["seminar"] . "'";
			$q_seminar_lang_lernziele   = $this->db->query($seminar_lang_lernziele);
			$res_seminar_lang_lernziele = $q_seminar_lang_lernziele->fetch_assoc();

			$table->add(["cols" => [translation::get("lernziele"), $table->addFormField(["name" => "lernziele_" . $lang["cms_lang_id"], "type" => "tinymce", "value" => $res_seminar_lang_lernziele["lernziele"]])]]);


			// seminar details

			$seminar_lang     = "SELECT zielgruppe, ort,kosten,mit_hund,ohne_hund,kursplaetze FROM tbl_seminar_lang WHERE lang_fk='" . $lang["cms_lang_id"] . "' AND seminar_fk='" . $_GET["seminar"] . "'";
			$q_seminar_lang   = $this->db->query($seminar_lang);
			$res_seminar_lang = $q_seminar_lang->fetch_assoc();

			$table->add(["cols" => [translation::get("zielgruppe"), $table->addFormfield(["name" => "zielgruppe_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["zielgruppe"], "type" => "text"])]]);
			$table->add(["cols" => [translation::get("ort"), $table->addFormfield(["name" => "ort_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["ort"], "type" => "text"])]]);
			$table->add(["cols" => [translation::get("kosten") . ":", $table->addFormfield(["name" => "kosten_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["kosten"], "type" => "text"])]]);
			$table->add(["cols" => [translation::get("mit_hund"), $table->addFormfield(["name" => "mit_hund_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["mit_hund"], "type" => "text"])]]);
			$table->add(["cols" => [translation::get("ohne_hund"), $table->addFormfield(["name" => "ohne_hund_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["ohne_hund"], "type" => "text"])]]);
			$table->add(["cols" => [translation::get("kursplaetze"), $table->addFormfield(["name" => "kursplaetze_" . $lang["cms_lang_id"], "value" => $res_seminar_lang["kursplaetze"], "type" => "text"])]]);
		}
		$table->addSubtitle(["cols" => ["Allgemein"]]);
		$table->add(["cols" => [translation::get("image_src"), $table->addFormField(["name" => $fields[1], "value" => $seminar["image"], "type" => "text"])]]);
		$table->add(["cols" => [$title[1], $table->addImageSelect(["target" => "image", "attr" => ["targetWidth" => 1024, "targetHeight" => 1024]])]]);
		$table->add(["cols" => [$title[3], $table->addFormField(["name" => $fields[3], "type" => "select", "value" => $seminar["category_fk"], "options" => $categories])]]);
		$table->add(["cols" => [$title[4], $table->addFormField(["name" => "anmeldefrist", "type" => "date", "value" => $date])]]);
		$table->add(["cols" => [$title[5], $table->addFormField(["name" => "passive", "type" => "checkbox", "value" => $seminar["passive"], "set" => 1])]]);

		// SHOW seminarleitung
		//if ($_GET["seminar"] != "") {
			$table->addSubtitle(["cols" => [translation::get("seminarleiter_waehlen")]]);
			$query_leitung = "SELECT leitung_id, name, email FROM tbl_leitung";
			$q_leitung     = $this->db->query($query_leitung);

			$leitung = $this->db->query("SELECT leitung_fk, seminar_fk FROM tbl_seminar_leitung");

			while ($res_leitung = $q_leitung->fetch_assoc()) {
				$leitung = $this->db->query("SELECT leitung_fk, seminar_fk FROM tbl_seminar_leitung");

				$checked = 0;
				while ($res = $leitung->fetch_assoc()) {
					if ($res_leitung["leitung_id"] == $res["leitung_fk"] && $_GET["seminar"] == $res["seminar_fk"]) {
						$checked = $res_leitung["leitung_id"];
					}
				}
				$table->add([
					"cols" => [
						["value" => $res_leitung["name"]],
						["value" => $res_leitung["email"]],
						$table->addFormField(["name" => "active_" . $res_leitung["leitung_id"], "set" => $res_leitung["leitung_id"], "type" => "checkbox", "value" => $checked]),
					],
				]);
			}

		//if ($_GET["seminar"] != "") {
			// ADD seminardatum
			/*$seminar_title = [
				"time_from",
				"time_to",
			];

			$table->addSubtitle(["cols" => [translation::get("seminar_date_title")]]);
			$table->add(["cols" => [
				translation::get("date"),
				translation::get("Zeit von"),
				translation::get("Bis"),
			]]);

			$table->add(["cols" => [
				$table->addFormfield(["name" => "date", "value" => "", "type" => "date"]),
				$table->addFormfield(["name" => "time_from", "value" => "", "type" => "time"]),
				$table->addFormfield(["name" => "time_to", "value" => "", "type" => "time"]),

			]]);
			$q = $this->db->query("SELECT seminar_date_id, date,time_from, time_to FROM tbl_seminar_date WHERE seminar_fk='" . $_GET["seminar"] . "' AND date >= CURDATE()");
			echo $this->db->error;
			while ($res = $q->fetch_assoc()) {
				$date      = date("d.m.Y", strtotime($res["date"]));
				$time_from = date("H:i", strtotime($res["time_from"]));
				$time_to   = date("H:i", strtotime($res["time_to"]));
				$date_id   = $res["seminar_date_id"];
				$table->add([
					"cols"     => [
						$table->addFormfield(["name" => "edit_date_" . $date_id, "value" => $date, "type" => "date", "attr" => ["drequired" => true]]),
						$table->addFormfield(["name" => "edit_time_from_" . $date_id, "value" => $time_from, "type" => "time", "attr" => ["drequired" => true]]),
						$table->addFormfield(["name" => "edit_time_to_" . $date_id, "value" => $time_to, "type" => "time", "attr" => ["drequired" => true]]) . $table->addFormfield(["name" => "seminar_date_" . $date_id, "value" => $date_id, "type" => "hidden"]),
					],
					"controls" => [
						"/cp/async/seminar/view?seminar=" . $_GET["seminar"] . "&prmv=" . $date_id => translation::get("rmv"),
					],
				]);
			}*/
		//}

		$table->add(["cols" => ["", $table->addFormfield(["name" => "save", "value" => translation::get("save"), "type" => "submit"])]]);
		echo $table->render();
	}

	public function editSeminarDate()
	{
		$header = new header();
		$header->addTitle(translation::get("seminar_title"));
		$header->addParagraph(translation::get("seminar_text"));
		echo $header->render();
		
		$table = new table();
		$table->setup(["td" => [0, 0, 100], "form" => ["sqltable" => "tbl_seminar_date", "method"=>"post","action" => "/cp/async/seminar/view"]]);
		// ADD seminardatum
		$seminar_title = [
			"time_from",
			"time_to",
		];

		$table->addSubtitle(["cols" => [translation::get("seminar_date")]]);
		$table->add(["cols" => [
			translation::get("date"),
			translation::get("Zeit von"),
			translation::get("Bis"),
		]]);

		$table->add(["cols" => [
			$table->addFormfield(["name" => "date", "value" => "", "type" => "date"]),
			$table->addFormfield(["name" => "time_from", "value" => "", "type" => "time"]),
			$table->addFormfield(["name" => "time_to", "value" => "", "type" => "time"]),

		]]);
		$q = $this->db->query("SELECT seminar_date_id, date,time_from, time_to FROM tbl_seminar_date WHERE seminar_fk='" . $_GET["seminardatum"] . "' AND date >= CURDATE()");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$date      = date("d.m.Y", strtotime($res["date"]));
			$time_from = date("H:i", strtotime($res["time_from"]));
			$time_to   = date("H:i", strtotime($res["time_to"]));
			$date_id   = $res["seminar_date_id"];
			$table->add([
				"cols"     => [
					$table->addFormfield(["name" => "edit_date_" . $date_id, "value" => $date, "type" => "date", "attr" => ["drequired" => true]]),
					$table->addFormfield(["name" => "edit_time_from_" . $date_id, "value" => $time_from, "type" => "time", "attr" => ["drequired" => true]]),
					$table->addFormfield(["name" => "edit_time_to_" . $date_id, "value" => $time_to, "type" => "time", "attr" => ["drequired" => true]]) . $table->addFormfield(["name" => "seminar_date_" . $date_id, "value" => $date_id, "type" => "hidden"]),
				],
				"controls" => [
					"/cp/async/seminar/view?seminardatum=" . $_GET["seminardatum"] . "&prmv=" . $date_id => translation::get("rmv"),
				],
			]);
		}
		

		$table->add(["cols" => ["", $table->addFormfield(["name" => "save", "value" => translation::get("save"), "type" => "submit"])]]);
		echo $table->render();
	}

	public function showSeminar() {
		$header = new header();
		$header->addTitle(translation::get("seminar_title"));
		$header->addParagraph(translation::get("seminar_text"));
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

		//SHOW seminars

		$table = new table();
		$table->setup(["td" => [130, 180, 120, ], "form" => ["sqltable" => "tbl_seminar"]]);
		// $table->controller();
		$table->addTitle(["cols" => [translation::get("kommende_seminar")], "controls" => ["/cp/seminar?add=1" => translation::get("add")]]);
		$table->addSubtitle(["cols" => [$title[0], $title[1], $title[2], $title[3]]]);
		// $table->auto(["id"=>"seminar_id","select"=>implode(",",$fields),"from"=>"tbl_seminar","controls"=>["/cp/seminar?seminar={id}"=>translation::get("edit"),"/cp/async/seminar/view?prmv={id}"=>translation::get("rmv")]]);
		$q = $this->db->query("SELECT
						seminar_id,
						sl.title as title,
						sc.title as category,
						category_fk,
						image,
						sl.text as text
						FROM tbl_seminar as s
						LEFT JOIN tbl_seminar_category as sc
						on s.category_fk=sc.seminar_category_id
						LEFT JOIN tbl_seminar_lang as sl
						on sl.seminar_fk=s.seminar_id
						WHERE sl.lang_fk=1
						AND s.is_deleted=0");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$table->add(["cols" => [
				$res["title"],
				$res["category"],
				"<image src=\"" . $res["image"] . "\" width=\"200px\"/>",
				$res["text"]],
				"controls"          => [
					"/cp/seminar?seminar=" . $res["seminar_id"]          => translation::get("edit"),
					"/cp/seminar?seminardatum=" . $res["seminar_id"]          => translation::get("seminardatum"),
					"/cp/seminar?anmeldungen=" . $res["seminar_id"]      => translation::get("anmeldungen"),
					"/async/seminar/export?export=" . $res["seminar_id"] => translation::get("export_excel"),
					"/cp/async/seminar/view?prmv=" . $res["seminar_id"]  => translation::get("rmv"),
				],
			]);
		}
		echo $table->render();
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
		$table->addSubtitle(["cols" => ["lastname", "firstname", "plz", "ort", "strasse", "number", "mobile", "email", "dog", "hund_geschlecht", "kastration"]]);
		$query = "SELECT anmeldung_id,lastname,firstname,plz,location,street,number,mobile,email,dog,hund_geschlecht,kastration FROM tbl_anmeldung";
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

	public function showAnmeldungen() {
		$header = new header();
		$header->addTitle(translation::get("Anmeldungen"));
		$header->addParagraph(translation::get("seminar_text"));
		echo $header->render();

		$table = new table();
		$table->setup(["td" => [0, 0, 100], "form" => ["sqltable" => "tbl_anmeldung", "sqlwhere" => ["seminar_fk" => $_GET["anmeldungen"]]]]);
		$table->controller();
		$table->addTitle(["cols" => ["lastname", "firstname", "heimatort", "plz", "ort", "strasse", "number", "mobile", "email", "dog", "hund_geschlecht", "kastration"], "controls" => ["/async/seminar/export?export=" . $_GET["anmeldungen"] => translation::get("export_excel")]]);
		$q = $this->db->query("SELECT lastname,firstname,human_day,human_month,human_year,heimatort,plz,location,street,number,mobile,email,dog,hund_geschlecht,kastration FROM tbl_anmeldung WHERE seminar_fk='" . $_GET["anmeldungen"] . "'");
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
