<?php
class links extends module {
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
	private $linksleitung = [];
	public function setup() {

	}

	public function controller() {

		if ($_GET["edit"] != "") {

			if ($_POST["save"] != "") {
				$links_edit = "UPDATE tbl_links SET
									title='" . $_POST["title"] . "',
									street='" . $_POST["street"] . "',
									plz='" . $_POST["plz"] . "',
									location='" . $_POST["location"] . "',
									phone='" . $_POST["phone"] . "',
									webpage='" . $_POST["webpage"] . "',
									mobile='" . $_POST["mobile"] . "',
									praxis='" . $_POST["praxis"] . "',
									category_fk='" . $_POST["category_fk"] . "'
								WHERE
									links_id='" . $_GET["edit"] . "'";
				$q = $this->db->query($links_edit);
				echo $this->db->error;
				if ($q) {
					$this->success = 1;
				}
			}
		} elseif ($_GET["add"] != "") {
			if ($_POST["save"] != "") {
				// Insert links
				$insert = "INSERT INTO tbl_links (
					title,
					street,
					plz,
					location,
					phone,
					webpage,
					mobile,
					praxis,
                    category_fk
					)
					VALUES (
						'" . $_POST["title"] . "',
						'" . $_POST["street"] . "',
						'" . $_POST["plz"] . "',
						'" . $_POST["location"] . "',
						'" . $_POST["phone"] . "',
						'" . $_POST["webpage"] . "',
						'" . $_POST["mobile"] . "',
						'" . $_POST["praxis"] . "',
                        '" . $_POST["category_fk"] . "'
					)";
				$q = $this->db->query($insert);
				echo $this->db->error;

				if ($q) {
					$this->success = 1;
				}
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
		if ($_GET["add"] != "" || $_GET["edit"] != "") {
			$this->editLinks();
		} else {
			$this->showLinks();
		}
	}

	//editLinks
	public function editLinks() {
		$header = new header();
		$header->addTitle(translation::get("links_title"));
		$header->addParagraph(translation::get("links_text"));
		echo $header->render();

		// edit links
		$query = "SELECT title,street,plz,location,phone,webpage,mobile,praxis,category_fk FROM tbl_links WHERE links_id='" . $_GET["edit"] . "' AND is_deleted = 0 ";
		$q     = $this->db->query($query);
		$links = $q->fetch_assoc();

		//addCategoriesTolinks
		$query_cat = "SELECT links_category_id, lcl.title as title
						FROM tbl_links_category as lc
						LEFT JOIN tbl_links_category_lang as lcl
						on lc.links_category_id=lcl.links_category_fk
						WHERE lang_fk=" . $_SESSION["lang"]["key"] . "
					";
		$q_cat = $this->db->query($query_cat);
		echo $this->db->error;
		while ($res_cat = $q_cat->fetch_assoc()) {
			$categories[$res_cat["links_category_id"]] = $res_cat["title"];
		}
		$table = new table();
		$table->setup(["form" => ["method" => "post", "action" => "/cp/async/links/view"], "td" => [120]]);
		$table->addTitle(["cols" => [translation::get("edit")]]);

		$table->add(["cols" => ["Titel", $table->addFormField(["name" => "title", "value" => $links["title"], "type" => "text"])]]);
		$table->add(["cols" => ["Praxis", $table->addFormField(["name" => "praxis", "value" => $links["praxis"], "type" => "text"])]]);
		$table->add(["cols" => ["Strasse", $table->addFormField(["name" => "street", "value" => $links["street"], "type" => "text"])]]);
		$table->add(["cols" => ["PLZ", $table->addFormField(["name" => "plz", "value" => $links["plz"], "type" => "text"])]]);
		$table->add(["cols" => ["Ort", $table->addFormField(["name" => "location", "value" => $links["location"], "type" => "text"])]]);
		$table->add(["cols" => ["Telefon", $table->addFormField(["name" => "phone", "value" => $links["phone"], "type" => "text"])]]);
		$table->add(["cols" => ["Mobile", $table->addFormField(["name" => "mobile", "value" => $links["mobile"], "type" => "text"])]]);
		$table->add(["cols" => ["Webseite", $table->addFormField(["name" => "webpage", "value" => $links["webpage"], "type" => "text"])]]);
		$table->add(["cols" => ["Kategorie", $table->addFormField(["name" => "category_fk", "type" => "select", "value" => $links["category_fk"], "options" => $categories])]]);

		$table->add(["cols" => ["", $table->addFormfield(["name" => "save", "value" => translation::get("save"), "type" => "submit"])]]);
		echo $table->render();
	}

	public function showLinks() {
		$header = new header();
		$header->addTitle(translation::get("links_title"));
		$header->addParagraph(translation::get("links_text"));
		echo $header->render();
		$title = [
			translation::get("kategorie"),
			translation::get("title"),
			translation::get("praxis"),
			translation::get("street"),
			translation::get("plz"),
			translation::get("location"),
			translation::get("phone"),
			translation::get("mobile"),
			translation::get("webpage"),
		];
		$fields = [
			"title",
			"category_fk",
			"image",
			"text",

		];

		//SHOW links

		$table = new table();
		$table->setup(["dragable"=>["type"=>1],"td" => [120,0,0,0,40,40,40,40,0], "form" => ["sqltable" => "tbl_links","action" => "/cp/async/links/view"]]);
		$table->controller();
		$table->addTitle(["cols" => [translation::get("kommende_links")], "controls" => ["/cp/links?add=1" => translation::get("add")]]);
		$table->addSubtitle(["cols" => $title]);
		$q = $this->db->query("SELECT l.sort, links_id, l.title as title, lc.title as category, category_fk, street,plz,location,phone,webpage,mobile,praxis FROM tbl_links as l LEFT JOIN tbl_links_category as lc on l.category_fk=lc.links_category_id ORDER BY l.sort ASC");
		while ($res = $q->fetch_assoc()) {
			$table->add(["cols" => [
				$res["category"],
				$res["title"],
				$res["praxis"],
				$res["street"],
				$res["plz"],
				$res["location"],
				$res["phone"],
				$res["mobile"],
				$res["webpage"],

			],"order"=>"sort ASC",
				"controls"          => [
					"/cp/links?edit=" . $res["links_id"]            => translation::get("edit"),
					"/cp/async/links/view?prmv=" . $res["links_id"] => translation::get("rmv"),
				],
			]);
		}
		echo $table->render();
	}
}
?>
