<?php
$obj_slider  = new heroslider();
$data_slider = $obj_slider->getDefault($value["cms_container_id"]);

$obj_kursdetails = new kursdetails();
$res_course      = $obj_kursdetails->getCourse();
$q_date          = $obj_kursdetails->getDate();
$q_leitung       = $obj_kursdetails->getLeitung();

?>

<!-- schreibt cms attribute rein -->
<header >
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
$obj_content = new html();
$obj_content->setup();
$data_content = $obj_content->get(["container" => $value["cms_container_id"]]);
if ($data_content["html"] == "" || $data_content["html"] == "undefined") {
	$data_content["html"] = "Inhalt";
}
?>
<?php
if ($res_course["text"] != "" || $q_date->num_rows > 0) {
	?>
<section>
	<div class="container kursdetails ">
		<h2><?php echo $res_course["title"]; ?></h2>
		<div class="row">
        <div class="content" >
			<?php echo $res_course["text"]; ?>
		</div>
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
			$date                  = date("w", strtotime($res_date["date"]));

			foreach ($week as $key => $day) {
				if ($date == $key) {
					$date_from = $day;
				}
			}
			if ($res_date["constant"] == 1) {
				$course_date = " ";
				// $s = "s";
			} else {
				$course_date = "<p class='text_small'>" . $res_date["date"] . "</p>";
				// $s = "";
			}
			//echo "<tr><td><bold>" . $date_from . "</bold>" . $course_date . "</td><td><bold>" . $res_date["time_from"] . " - " . $res_date["time_to"] . "</bold><p class='text_small'></p></td></tr>";
			echo "<tr><td><bold>" . $date_from . "</bold>" . $course_date . "</td><td><bold>" . $res_date["time_from"] . "</bold><p class='text_small'></p></td></tr>";
		}
		?>
			</table>
		</aside>
		<?php }?>
		</div>
	</div>
	<?php
$q1 = $this->db->query("SELECT cms_navigation_id FROM cms_navigation WHERE cms_navigation_fk=1");

	while ($res1 = $q1->fetch_assoc()) {
		$q_link   = $this->db->query("SELECT permalink, name FROM cms_navigation_page WHERE cms_navigation_fk=14 AND cms_lang_fk=" . $_SESSION["lang"]["key"]);
		$res_link = $q_link->fetch_assoc();
	}
	?>
	<a class="button" style="margin-bottom: 20px;" href="/<?php echo $_SESSION['lang']['short'] . "/" . $_SESSION["path"][1] . "/" . $res_link["permalink"] . "?kurs=" . $_GET["id"]; ?>"> <?php echo translation::get("anmeldung"); ?></a>
	<?php

	//if ($res_course["anmeldefrist"] != "1970-01-01") {
		//echo "<p class=\"text_small\">" . translation::get("anmeldefrist_bis") . " " . $date = date('d.m.Y', strtotime($res_course['anmeldefrist'])) . "</p>";
	//}
	?>
</section>
<?php }?>

<?php
if ($res_course["lernziele"] != "") {
	?>
	<section class="beige lernziele">
	<div class="container ">
		<h2>Lernziele</h2>
		<div class="list_container row">

				<?php
echo $res_course["lernziele"];
	?>

		</div>
	</div>
</section>

<?php }?>

<?php

if ($res_course["zielgruppe"] != "" || $res_course["ort"] != "" || $res_course["mit_hund"] != "" || $res_course["ohne_hund"] != "") {
	?>
<div class="grey kurs_details_section">
<section class="">
	<div class="container centered ">
		<h2><?php echo translation::get("kurs_details"); ?></h2>
		<table class="kurs_details_table">
			<tr><td><p><bold><?php echo translation::get("zielgruppe"); ?>:</bold></p></td><td><p><?php echo $res_course["zielgruppe"]; ?></p></td></tr>
			<tr><td><p><bold><?php echo translation::get("ort"); ?>:</bold></p></td><td><p><?php echo $res_course["ort"]; ?></p></td></tr>
			<?php
echo "<tr class=\"kosten\">";
	echo "<td>";
	if ($res_course["kosten"] != "") {
		echo "<div><strong><p>" . translation::get("kosten") . ":</p></strong></div>";
	}
	echo "</td>";

	echo "<td>";
	if ($res_course["lektion_kosten"] != "") {
		echo "<div><p>" . translation::get("lektion_kosten") . ":</p></div>";
		echo "<div><p>" . $res_course["lektion_kosten"] . "</p></div>";

	}
	echo "</td>";
	echo "<td>";
	if ($res_course["zehnerabo_kosten"] != "") {
		echo "<div><p>" . translation::get("zehnerabo_kosten") . ":</p></div>";
		echo "<div><p>" . $res_course["zehnerabo_kosten"] . "</p></div>";

	}
	echo "</td>";
	echo "</tr>";

	?>
		</table>
	</div>
</section>
</div>
<?php }?>

<?php

if ($q_leitung->num_rows > 0) {
	?>
<section class="leitung">
	<?php
echo "<div class=\"container  kursleitung\">";
	echo "<h2>" . translation::get("kursleitung") . "</h2>";
	echo "<div class=\"row\">";
	while ($res_leitung = $q_leitung->fetch_assoc()) {
		echo "<div class=\"container_img2\">";
		echo "<div class=\"background-img\">";
		echo "<img src=\"" . $res_leitung["image"] . "\">";
		echo "</div>";
		echo "<div class=\"half_container\">";
		echo "<h3>" . $res_leitung["name"] . "</h3>";
		echo "<a><p style=\"text-decoration:none;\">" . $res_leitung["phone"] . "</p></a><br>";
		echo "<a href=\"mailto:" . $res_leitung["email"] . "\"><p>" . $res_leitung["email"] . "</p></a>";
		echo "</div>";
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
