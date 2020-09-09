<?php
$obj          = new kurshighlight();
$data_course  = $obj->getCourse();
$data_seminar = $obj->getSeminar();
?>
<section class="actual_curses" <?php $this->attr();?>>
<?php
$this->controls(
	[
		// "/cp/async/kurshighlight/view?id=".$value["cms_container_id"] => ["value"=>translation::get("settings")],
		"/cp/async/container/rmv?id=" . $value["cms_container_id"] => ["value" => translation::get("rmv"), "target" => "destruct"],
	]
);
?>
<div class="container">
	<h2><?php echo translation::get("aktuelle_kurse"); ?></h2>
	<?php
$style = "space-between";
$count = count($data_course) + count($data_seminar);
if ($count < 4) {
	$style = "space-around";
}
?>
		<div class="row" style="<?php echo 'justify-content:' . $style; ?>">
			<?php foreach ($data_course as $key => $value) {
	?>
			<div class="container_img" style="background-image:url('<?php echo $value["image"]; ?>')">
				<!-- <img src="<?php echo $value["image"]; ?>"> -->
				<div class="half_container">
					<h3><?php echo $value["title"]; ?></h3>
					<?php

	if ($_SESSION["lang"]["short"] == "en") {
		$path1 = "courses/coursedetails";
	} else if ($_SESSION["lang"]["short"] == "de") {
		$path1 = "kurse/kursdetails";
	}
	echo "<a class=\"button\" href=\"/" . $_SESSION['lang']['short'] . "/" . $path1 . "?id=" . $value['course_id'] . "\">" . translation::get("kurs_details") . "</a>";
	?>

					<!-- <a class="button" href=" /  <  ? phpecho $_SESSION["lang"]["short"]$path1 . "?id=" . $value["course_id"];?>"><?php echo translation::get("kurs_details"); ?></a> -->
				</div>
			</div>
			<?php }?>

			<?php foreach ($data_seminar as $key => $value) {?>
				<div class="container_img" style="background-image:url('<?php echo $value["image"]; ?>')">
				<!-- <img src="<?php echo $value["image"]; ?>"> -->
				<div class="half_container">
					<h3><?php echo $value["title"]; ?></h3>
					<a class="button" href="/<?php echo $_SESSION["lang"]["short"] . "/seminar/seminardetails?id=" . $value["seminar_id"]; ?>"><?php echo translation::get("kurs_details"); ?></a>
				</div>
			</div>
			<?php }?>
		</div>
</div>
</section>
