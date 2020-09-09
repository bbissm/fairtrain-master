<?php
class main extends module {
	/**
	 * @param $navi_id
	 */
	public function render($navi_id) {
		if ($_GET["path"] != "" && $_SESSION["path"][0] == "async") {
			$_GET["path"]     = substr($_GET["path"], 1);
			$_SESSION["path"] = explode("/", $_GET["path"]);
			unset($_GET["path"]);
		}

		echo "<main>";
		$container = new container();
		$container->setup(["parts" => [
			"templates/web/parts/html.php"              => "Content",
			"templates/web/parts/form.php"              => "Formular",
			"templates/web/parts/heroslider.php"        => "Hero Slider",
			"templates/web/parts/impressionen.php"      => "Impressionen",
			"templates/web/parts/kurshighlight.php"     => "Aktuelle Kurse",
			// "templates/web/parts/socialmedia_slider.php"=>"Socialmedia Slider",
			"templates/web/parts/partner_netzwerk.php"  => "Partner Netzwerk",
			"templates/web/parts/kurse.php"             => "Kurse",
			"templates/web/parts/seminar.php"           => "Seminar",
			"templates/web/parts/team.php"              => "Team",
			"templates/web/parts/links.php"             => "Links",
			"templates/web/parts/contact_locations.php" => "Kontakt Orte",
		],
			"name"                     => "main_" . implode("_", $_SESSION["path"]),
			"id"                       => 0]);
		$container->load([]);

		$q1 = $this->db->query("SELECT cms_navigation_id FROM cms_navigation WHERE cms_navigation_fk=1");

		while ($res1 = $q1->fetch_assoc()) {

			$q_link   = $this->db->query("SELECT permalink, name FROM cms_navigation_page WHERE cms_navigation_fk=14 AND cms_lang_fk=" . $_SESSION["lang"]["key"]);
			$res_link = $q_link->fetch_assoc();
			//echo "<pre>";
			//print_r($res_link);
			//echo "</pre>";
		}
		if ($navi_id == 1 && $_SESSION["path"][2] != "" && $_SESSION["path"][2] != $res_link["permalink"]) {
			include "templates/web/parts/kursdetails.php";
		} else if ($navi_id == 3 && $_SESSION["path"][2] != "" && $_SESSION["path"][2] != "anmeldung" && $_SESSION["path"][2] != "register") {
			include "templates/web/parts/seminardetails.php";
		} else if ($navi_id == 4 && $_SESSION["path"][2] != "") {
			include "templates/web/parts/teamdetails.php";
		} else {
			echo $container->render();
			echo "</main>";
		}

	}

	public function pageTitle() {
		$path = $_GET["path"];
		if (strpos($path, "--") != false) {
			$id  = explode("--", $path);
			$id  = $id[count($id) - 1];
			$q   = $this->db->query("SELECT title FROM tbl_feed_article WHERE cms_container_fk='" . $id . "' AND is_active=1");
			$res = $q->fetch_assoc();
			echo $res["title"] . " | Fairtrain";
		} else {
			$q   = $this->db->query("SELECT pagetitle FROM cms_navigation_page WHERE cms_navigation_fk='" . $_SESSION["nav"] . "' AND cms_lang_fk='" . $_SESSION["lang"]["key"] . "'");
			$res = $q->fetch_assoc();
			if ($res["pagetitle"] == "") {
				echo "Fairtrain";
			} else {
				echo $res["pagetitle"];
			}
		}
	}
}
?>
