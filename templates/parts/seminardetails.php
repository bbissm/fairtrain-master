<?php

$obj_slider  = new heroslider();
$data_slider = $obj_slider->getDefault($value["cms_container_id"]);

$obj_seminardetails = new seminardetails();
$res_seminar        = $obj_seminardetails->getSeminar();
$q_date             = $obj_seminardetails->getDate();
$q_leitung          = $obj_seminardetails->getLeitung();

?>
<!-- schreibt cms attribute rein -->
<header>
		<div class="header-content">
		<div class="header-img slider-header slider">
			<?php foreach ($data_slider as $k => $v) {?>
				<div style="background-image: url('<?php echo $v['image']; ?>');">
					<div class="overlay">
						<h1><?php echo $v["text"]; ?></h1>
					</div>
				</div>
			<?php }?>
		</div>
	</div>
</header>

<?php
if ($res_seminar["text"] != "" || $q_date->num_rows > 0) {
	?>
<section>
	<div class="container kursdetails ">
		<h2><?php echo $res_seminar["title"]; ?></h2>
		<div class="row">
		<?php
if ($res_seminar["text"] != "") {
		echo "<div class=\"content\">";
		echo $res_seminar["text"];
		echo "</div>";
	}
	?>
	   <?php
if ($q_date->num_rows > 0) {
		?>
		<aside class="course_date">
			<table>
				<tr><th colspan="2"><h3><?php echo translation::get("kursdatum"); ?></h3></th></tr>
				<?php
$week      = [1 => translation::get("montag"), 2 => translation::get("dienstag"), 3 => translation::get("mittwoch"), 4 => translation::get("donnerstag"), 5 => translation::get("freitag"), 6 => translation::get("samstag"), 0 => translation::get("sonntag")];
		$date_from = array();
		while ($res_date = $q_date->fetch_assoc()) {
			$res_date["date"]      = date("d.m.Y", strtotime($res_date["date"]));
			$res_date["time_from"] = date("H:i", strtotime($res_date["time_from"]));
			$res_date["time_to"]   = date("H:i", strtotime($res_date["time_to"]));

			$date = date("w", strtotime($res_date["date"]));
			foreach ($week as $key => $day) {
				if ($date == $key) {
					$date_from = $day;
				}
			}
			echo "<tr><td><bold>" . $date_from . "</bold><p class='text_small'>" . $res_date["date"] . "</p></td><td><bold>" . $res_date["time_from"] . " - " . $res_date["time_to"] . "</bold><p class='text_small'></p></td></tr>";
		}
		?>
			</table>
		</aside>
		<?php }?>
		</div>
	</div>
	<?php
if ($res_seminar["anmeldefrist"] != "") {
		?>
		<?php
$q1 = $this->db->query("SELECT cms_navigation_id FROM cms_navigation WHERE cms_navigation_fk=3");

		while ($res1 = $q1->fetch_assoc()) {
			$q_link   = $this->db->query("SELECT permalink, name FROM cms_navigation_page WHERE cms_navigation_fk=14 AND cms_lang_fk=" . $_SESSION["lang"]["key"]);
			$res_link = $q_link->fetch_assoc();
		}
		?>
	<a class="button" style="margin-bottom: 20px;" href="/<?php echo $_SESSION['lang']['short'] . "/" . $_SESSION["path"][1] . "/" . $res_link["permalink"] . "?seminar=" . $_GET["id"]; ?>"> <?php echo translation::get("anmeldung"); ?></a>
		<?php
}

	if ($res_seminar["anmeldefrist"] != "1970-01-01") {
		echo "<p class=\"text_small\">" . translation::get("anmeldefrist_bis") . " " . $date = date('d.m.Y', strtotime($res_seminar['anmeldefrist'])) . "</p>";
	}
	?>

</section>

<?php }?>

<?php
if ($res_seminar["lernziele"] != "") {
	?>
<section class="beige lernziele">
	<div class="container ">
		<h2>Lernziele</h2>
		<div class="list_container row">
			<?php
echo $res_seminar["lernziele"];
	?>
		</div>
	</div>
</section>

<?php }?>

<?php

if ($res_seminar["zielgruppe"] != "" || $res_seminar["ort"] != "" || $res_seminar["mit_hund"] != "" || $res_seminar["ohne_hund"] != "") {
	?>
<section class="grey kurs_details">
	<div class="container centered ">
		<h2>Kurs Details</h2>
		<table class="kurs_details_table">
			<?php

	if ($res_seminar["zielgruppe"] != "") {
		echo "<tr><td><p><bold>" . translation::get("zielgruppe") . ":</bold></p></td><td><p>" . $res_seminar["zielgruppe"] . "</p></td></tr>";
	}
	if ($res_seminar["zielgruppe"] != "") {
		echo "<tr><td><p><bold>" . translation::get("ort") . ":</bold></p></td><td><p>" . $res_seminar["ort"] . "</p></td></tr>";
	}

	echo "<tr class=\"kosten\">";
	echo "<td>";

	echo "<div><strong><p>" . translation::get("kosten") . ":</p></strong></div>";
	echo "<div><p>" . $res_seminar["kosten"] . "</p></div>";
	echo "</td>";
	echo "<td>";
	if ($res_seminar["mit_hund"] != "") {
		echo "<div><p>" . translation::get("mit_hund") . ":</p></div>";
		echo "<div><p>" . $res_seminar["mit_hund"] . "</p></div>";

	}
	echo "</td>";
	echo "<td>";
	if ($res_seminar["ohne_hund"] != "") {
		echo "<div><p>" . translation::get("ohne_hund") . ":</p></div>";
		echo "<div><p>" . $res_seminar["ohne_hund"] . "</p></div>";

	}
	echo "</td>";

	echo "</tr>";
	if ($res_seminar["kursplaetze"] != "") {
		echo "<tr><td><p><bold>" . translation::get("kursplaetze") . ":</bold></p></td><td><p>" . $res_seminar["kursplaetze"] . "</p></td></tr>";
	}
	?>
		</table>
	</div>
</section>

<?php }?>

<?php

if ($q_leitung->num_rows > 0) {
	?>
<section class="leitung">
	<?php
echo "<div class=\"container  kursleitung_bio\">";
	echo "<h2>" . translation::get("kursleitung") . "</h2>";
	echo "<div class=\"row_article\">";
	while ($res_leitung = $q_leitung->fetch_assoc()) {
		echo "<div class=\"img\" style=\"background: url(" . $res_leitung["image"] . ")\"></div>";
		echo "<div class=\"article beige\">";
		echo "<h3>" . $res_leitung["name"] . "</h3>";
		echo $res_leitung["text"];
		echo "</div>";
	}
	echo "</div>";
	echo "</div>";
	?>
</section>
<?php }?>

<section class="button-container">
	<div class="centered_btn">
		<a class="button" href="javascript:history.go(-1)"><?php echo translation::get("back"); ?></a>
	</div>
</section>
