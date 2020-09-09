<?php
$obj  = new form();
$data = $obj->get(["container" => $value["cms_container_id"]]);

$course  = new kurse();
$seminar = new seminar();

if ($_GET["kurs"] != "") {
	$q           = $this->db->query("SELECT course_category_id FROM tbl_course_category as cc LEFT JOIN tbl_course as c on c.category_fk=cc.course_category_id WHERE course_id=" . $_GET["kurs"]);
	$res         = $q->fetch_assoc();
	$course_list = $course->getCourseList($res["course_category_id"]);
} else if ($_GET["seminar"] != "") {

	$q = $this->db->query("SELECT seminar_category_id FROM tbl_seminar_category as sc LEFT JOIN tbl_seminar as s on s.category_fk=sc.seminar_category_id WHERE seminar_id=" . $_GET["seminar"]);
	echo $this->db->error;
	$res         = $q->fetch_assoc();
	$course_list = $seminar->getSeminarList($res["seminar_category_id"]);
}
?>
<div <?php $this->attr();?>>
		<?php $this->controls([
	"/cp/async/form/settings?id=" . $value["cms_container_id"] => ["value" => translation::get("settings")],
	"/cp/async/container/rmv?id=" . $value["cms_container_id"] => ["value" => translation::get("rmv"), "target" => "destruct"],
]);
$sel = $obj->getFormTemplate($value["cms_container_id"]);
if ($sel == 1) {

	?>
					<section class="beige kontaktformular">
						<div class="container">
							<h2><?php echo translation::get("anmeldeformular"); ?></h2>

						<div>
							<form style="flex-direction: column; align-items: center;" id="contact_form" class="kontaktformular" method="POST">
								<ul class="wrapper">
								<li class="form-row">
									<label for="firstname"><p><?php echo translation::get("vorname"); ?>: </p></label>
									<input type="text" name="firstname" id="firstname"  required>
								</li>
								<li class="form-row">
									<label for="lastname"><p><?php echo translation::get("nachname"); ?>: </p></label>
									<input type="text" name="lastname" id="lastname"  required>
								</li>
								<li class="form-row">
									<label for="email"><p><?php echo translation::get("email"); ?>: </p></label>
									<input type="email" name="email" id="email"  required>
								</li>
								<li class="form-row">
									<label for="message"><p><?php echo translation::get("nachricht"); ?>: </p></label>
									<textarea type="text" name="message" id="message" ></textarea>
								</li>
								<li class="form-row">
									<?php
echo "Es gilt die <input type=\"checkbox\" name=\"privacy_policy\" required><a target=\"_BLANK\” class=\"text_small\" href=\"/" . $_SESSION['lang']['short'] . "/privacy\">Datenschutzerklärung</a>";
	?>
								</li>
								</ul>
								<!-- <div class="centered_btn"><a href="javascript:history.go(-1)"><button class="btn">Absenden</button></a></div> -->
								<div class="centered_btn"><input type="submit" value="<?php echo translation::get("sign_up_submit"); ?>" name="contact_submit" id="contact_submit" class="button"></div>

							</form>
						</div>
						</div>
					</section>
				<?php
} else if ($sel == 2) {
	?>
				<section class="beige"><div class="container centered">
					<h2><?php echo translation::get("offer_selection"); ?></h2>
					<div class="checkbox_container">
						<ul class="styled_ul">
							<?php
foreach ($course_list as $key) {
		$checked = "";

		if ($_GET["kurs"] != "") {

			if ($key["course_id"] == $_GET["kurs"]) {
				$checked = "checked=\"checked\"";
			}
			echo "<div class=\"radio\"><input name=\"radio_button\" class=\"course\" value=\"" . $key["course_id"] . "\" type=\"radio\" " . $checked . "><li><p>" . $key["title"] . "</p></li></div>";
		} elseif ($_GET["seminar"]) {
			if ($key["seminar_id"] == $_GET["seminar"]) {
				$checked = "checked=\"checked\"";
			}
			echo "<div class=\"radio\"><input name=\"radio_button\" class=\"course\" value=\"" . $key["seminar_id"] . "\" type=\"radio\" " . $checked . "><li><p>" . $key["title"] . "</p></li></div>";

		}
	}
	?>

						</ul>
					</div>
				</section>


				<form  style="margin-top:75px;" id="anmeldung_form" class="anmeldung-form" method="POST">
					<?php
if ($_GET["kurs"] != "") {
		echo "<input type=\"hidden\" id=\"course\" name=\"course_fk\" value=\"" . $_GET["kurs"] . "\">";
	} elseif ($_GET["seminar"] != "") {
		echo "<input type=\"hidden\" id=\"seminar\" name=\"seminar_fk\" value=\"" . $_GET["seminar"] . "\">";}
	?>
					<div class="form-side">
						<h2><?php echo translation::get("Data dog"); ?></h2>
						<div class="wrapper">
							<div class="form-row">
								<label for="dog"><p><?php echo translation::get("name"); ?>: </p></label>
								<input type="text" name="dog" id="dog"  required>
							</div>
							<div class="form-row inline">
								<label><p><?php echo translation::get("birthday"); ?>: </p></label>
								<div>
									<input type="text" name="day" id="day" placeholder="<?php echo translation::get('day'); ?>" required>
									<input type="text" name="month" id="month" placeholder="<?php echo translation::get('month'); ?>" required>
									<input type="text" name="year" id="year" placeholder="<?php echo translation::get('year'); ?>" required>
								</div>
							</div>
							<div class="form-row">
								<label for="rasse"><p><?php echo translation::get("rasse"); ?>: </p></label>
								<input type="text" name="rasse" id="rasse"  required>
							</div>
							<div class="form-row radio">
								<label for="geschlecht"><p><?php echo translation::get("geschlecht"); ?>: </p></label>
								<div>
									<div>
										<input type="radio" id="hund_geschlecht" name="hund_geschlecht" value="1"> <label><?php echo translation::get("ruede"); ?></label>
									</div>
									<div>
										<input type="radio" id="hund_geschlecht" name="hund_geschlecht" value="2"> <label><?php echo translation::get("huendin"); ?></label>
									</div>
								</div>

							</div>
							<div class="form-row radio">
								<label for="kastration"><p><?php echo translation::get("kastration"); ?>: </p></label>
								<div>
									<div>
										<input type="radio" name="kastration" id="kastration" value="2"> <label><?php echo translation::get("kastriert"); ?>
										</label>
									</div>
									<div>
										<input type="radio" name="kastration" id="kastration" value="1"> <label><?php echo translation::get("nicht_kastriert"); ?>
										</label>
									</div>
								</div>
							</div>
							<div class="form-row">
								<label for="chip"><p><?php echo translation::get("chip-nr"); ?>.: </p></label>
								<input type="text" name="chip" id="chip"  required>
							</div>
						</div>
					</div>

					<div class="form-side">
						<h2><?php echo translation::get("Data human"); ?></h2>
						<div class="wrapper">
							<div class="form-row radio">
								<label for="geschlecht"><p><?php echo translation::get("anrede"); ?>: </p></label>
								<div>
									<div>
										<input type="radio" id="geschlecht" name="geschlecht" value="1"> <label><?php echo translation::get("herr"); ?>
										</label>
									</div>
									<div>
										<input type="radio" id="geschlecht" name="geschlecht" value="2"> <label><?php echo translation::get("frau"); ?>
										</label>
									</div>
								</div>
							</div>
							<div class="form-row">
								<label for="firstname"><p><?php echo translation::get("firstname"); ?>: </p></label>
								<input type="text" name="firstname" id="firstname"  required>
							</div>
							<div class="form-row">
								<label for="lastname"><p><?php echo translation::get("lastname"); ?>: </p></label>
								<input type="text" name="lastname" id="lastname"  required>
							</div>
							<div class="form-row birthday inline" style="display:none;">
								<!-- for js -->
								<label ><p><?php echo translation::get("birthday"); ?>: </p></label>
								<div>
									<input type='text' name='human_day' id='human_day' placeholder="day">
									<input type='text' name='human_month' id='human_month' placeholder="month">
									<input type='text' name='human_year' id='human_year' placeholder="year">
								</div>
							</div>
							<div class="form-row heimatort" style="display:none;">
								<!-- for js -->
								<label for="heimatort"><p><?php echo translation::get("heimatort"); ?>: </p></label>
								<input type='text' name='heimatort' id='heimatort'>
							</div>
							<div class="form-row street">
								<label for="street"><p><?php echo translation::get("street"); ?>/<?php echo translation::get("nr"); ?>.: </p></label>
								<div>
									<input type="text" name="street" id="street"  required>
									<input type="text" name="number" id="number"  required>
								</div>
							</div>
							<div class="form-row plz">
								<label for="plz"><p><?php echo translation::get("plz"); ?> <?php echo translation::get("ort"); ?>: </p></label>
								<div>
									<input type="text" name="plz" id="plz"  required>
									<input type="text" name="location" id="location"  required>
								</div>
							</div>
							<div class="form-row">
								<label for="mobile"><p><?php echo translation::get("mobile"); ?>: </p></label>
								<input type="text" name="mobile" id="mobile"  required>
							</div>
							<div class="form-row">
								<label for="email"><p><?php echo translation::get("email"); ?>: </p></label>
								<input type="email" name="email" id="email"  required>
							</div>
						</div>
					</div>

					<div class="form_bottom">
						<div class="form-row">
							<label for="message"><p><?php echo translation::get("nachricht"); ?>: </p></label>
							<textarea type="text" name="message" id="message"  required></textarea>
						</div>
							<div class="form-row centered flex">
								<!-- <input type="checkbox" id="privacy_policy" name="privacy_policy" required> <label for="privacy_policy" ><p class="text_small">Es gilt die Datenschutzerklärung</p></label><br> -->
								<?php
echo "<input type=\"checkbox\" id=\"privacy_policy\" name=\"privacy_policy\" required><label for=\"privacy_policy\"><a target=\"_BLANK\” class=\"text_small\" style=\"text-decoration:underline;\" href=\"/" . $_SESSION['lang']['short'] . "/privacy\">" . translation::get("privacy_policy") . "</a></label><br>";
	?>
							</div>
							<div class="centered_btn"><input type="submit" value="<?php echo translation::get("sign_up_submit"); ?>" id="anmeldung_submit" name="anmeldung_submit" class="button"></div>
					</div>
				</form>
				<script>addFieldToForm();</script>
				<?php
} else {
	?>
				<section class="kontakt">
						<div class="container">
							<h2><?php echo translation::get("kontaktformular"); ?></h2>

						<div>
							<form style="flex-direction: column; align-items: center;" id="contact_form" class="kontaktformular" method="POST">
								<ul class="wrapper">
								<li class="form-row">
									<label for="firstname"><p><?php echo translation::get("sign_up_prename"); ?>: </p></label>
									<input type="text" name="firstname" id="firstname"  required>
								</li>
								<li class="form-row">
									<label for="lastname"><p><?php echo translation::get("lastname"); ?>: </p></label>
									<input type="text" name="lastname" id="lastname"  required>
								</li>
								<li class="form-row">
									<label for="email"><p><?php echo translation::get("email"); ?>: </p></label>
									<input type="email" name="email" id="email"  required>
								</li>
								<li class="form-row">
									<label for="message"><p><?php echo translation::get("nachricht"); ?>: </p></label>
									<textarea type="text" name="message" id="message" ></textarea>
								</li>
								<li class="form-row flex">
									<?php
echo "<input type=\"checkbox\" name=\"privacy_policy\" required><a target=\"_BLANK\” class=\"text_small\" href=\"/" . $_SESSION['lang']['short'] . "/privacy\">" . translation::get("privacy_policy") . "</a>";
	?>
								</li>
								</ul>
								<!-- <div class="centered_btn"><a href="javascript:history.go(-1)"><button class="btn">Absenden</button></a></div> -->
								<div class="centered_btn"><input type="submit" value="<?php echo translation::get("sign_up_submit"); ?>" name="contact_submit" id="contact_submit" class="button"></div>

							</form>
						</div>
						</div>
					</section>
					<?php
}
?>
</div>

