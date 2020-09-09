<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class form extends module {

	public function setCookieAccept() {
		$_SESSION["close_cookie"] = 1;

	}

	/**
	 * @param  $config
	 * @return mixed
	 */
	public function get($config) {
		$q = $this->db->query("SELECT form_hardcoded_id
									  FROM tbl_form_hardcoded
									  WHERE cms_container_fk='" . $config["container"] . "'");
		$res = $q->fetch_assoc();
		return $res;
	}

	public function anmelden() {

		foreach ($_POST as $key => $value) {
			parse_str($value, $params);
		}
		if ($params["privacy_policy"] != "" &&
			$params["dog"] != "" &&
			$params["firstname"] != "" &&
			$params["lastname"] != "" &&
			$params["day"] != "" &&
			$params["month"] != "" &&
			$params["year"] != "" &&
			$params["rasse"] != "" &&
			$params["hund_geschlecht"] != "" &&
			$params["kastration"] != "" &&
			$params["chip"] != "" &&
			$params["geschlecht"] != "" &&
			$params["street"] != "" &&
			$params["number"] != "" &&
			$params["plz"] != "" &&
			$params["location"] != "" &&
			$params["mobile"] != "" &&
			$params["email"] != ""
		) {

			$obj_course = new kurse();
			$course     = $obj_course->getCourseOnly($params["course_fk"]);
			//Mail abschicken
			$mailBody = "";

			$mailBody = "<html>";
			$mailBody .= "<body>";
			$mailBody .= "<p style=\"font-family: sans-serif;\">Sie haben sich erfolgreich für den Kurs " . $course["title"] . " angemeldet.</p>";
			$mailBody .= "<br /> <p style=\"font-family: sans-serif;\"><strong>Der Kurs findet statt in:</strong> " . $course["ort"] . "</p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Die Lektionkosten:</strong> " . $course["lektion_kosten"] . ".-</p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Die Zehner-Abo Kosten:</strong> " . $course["zehnerabo_kosten"] . ".-</p>";

			$mailBody .= " <p style=\"font-family: sans-serif;\">Dann finden die nächsten Kurse statt:</p>";
			$query_date = "SELECT date,time_from, time_to FROM tbl_course_date WHERE course_fk='" . $params["course_fk"] . "' ORDER BY date ASC, time_from ASC ";
			$q_date     = $this->db->query($query_date);
			echo $this->db->error;

			while ($res_date = $q_date->fetch_assoc()) {
				$res_date["date"]      = date("d.m.Y", strtotime($res_date["date"]));
				$res_date["time_from"] = date("H:i", strtotime($res_date["time_from"]));
				$res_date["time_to"]   = date("H:i", strtotime($res_date["time_to"]));
				$date                  = date("w", strtotime($res_date["date"]));
				$date_from             = array();
				if ($date == 1) {
					$date_from = "Montag";
				} elseif ($date == 2) {
					$date_from = "Dienstag";
				} elseif ($date == 3) {
					$date_from = "Mittwoch";
				} elseif ($date == 4) {
					$date_from = "Donnerstag";
				} elseif ($date == 5) {
					$date_from = "Freitag";
				} elseif ($date == 6) {
					$date_from = "Samstag";
				} elseif ($date == 7) {
					$date_from = "Sonntag";
				}
				$mailBody .= "<br /><tr><td><strong>" . $date_from . ", " . $res_date["date"] . "</strong></td><td> " . $res_date["time_from"] . " - " . $res_date["time_to"] . "</td></tr>";
			}

			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Wir freuen uns, Sie bei uns demnächst willkommen zu heissen.</strong></p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Besten Dank und freundliche Grüsse</strong></p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Ihr Fairtrain Team!</strong></p>";
			$mailBody .= "</body>";
			$mailBody .= "</html>";

			if (strlen($params["email"]) > 0) {
				require 'library/phpmailer/Exception.php';
				require 'library/phpmailer/PHPMailer.php';
				require 'library/phpmailer/SMTP.php';

				$mail            = new PHPMailer(true);
				$mail->SMTPDebug = 0;
				$mail->isSMTP();
				$mail->Host       = 'smtp.virtualtec.ch';
				$mail->SMTPAuth   = true;
				$mail->Username   = 'sender@live.dimaster.ch';
				$mail->Password   = 'kmlktfkr';
				$mail->SMTPSecure = 'tls';
				$mail->Port       = 587;
				$mail->CharSet    = "UTF-8";
				$mail->WordWrap   = 50;

				$mail->setFrom("martin.ivanenko@hotmail.com", 'Fairtrain');

				$mail->addAddress($params["email"]);

				$mail->isHTML(true);
				$mail->Subject = "Fairtrain Kursanmeldung";
				$mail->Body    = $mailBody;
				$mail->send();
			}
			$insert = "INSERT INTO tbl_anmeldung (
				dog,
				firstname,
				lastname,
				human_day,
				human_month,
				human_year,
				heimatort,
				day,
				month,
				year,
				rasse,
				hund_geschlecht,
				kastration,
				chip,
				geschlecht,
				street,
				number,
				plz,
				location,
				mobile,
				email,
				message,
				course_fk,
				seminar_fk
				) VALUES (
				'" . $params["dog"] . "',
				'" . $params["firstname"] . "',
				'" . $params["lastname"] . "',
				'" . $params["human_day"] . "',
				'" . $params["human_month"] . "',
				'" . $params["human_year"] . "',
				'" . $params["heimatort"] . "',
				'" . $params["day"] . "',
				'" . $params["month"] . "',
				'" . $params["year"] . "',
				'" . $params["rasse"] . "',
				'" . $params["hund_geschlecht"] . "',
				'" . $params["kastration"] . "',
				'" . $params["chip"] . "',
				'" . $params["geschlecht"] . "',
				'" . $params["street"] . "',
				'" . $params["number"] . "',
				'" . $params["plz"] . "',
				'" . $params["location"] . "',
				'" . $params["mobile"] . "',
				'" . $params["email"] . "',
				'" . $params["message"] . "',
				'" . $params["course_fk"] . "',
				'" . $params["seminar_fk"] . "'
				)";
			$stmt = $this->db->query($insert);
		}

	}

	public function contact() {
		foreach ($_POST as $key => $value) {
			parse_str($value, $params);
			if ($params["privacy_policy"] != "" && $params["firstname"] != "" && $params["lastname"] != "" && $params["email"] != "") {
				$insert = "INSERT INTO tbl_contact (
					firstname,
					lastname,
					email,
					message
					) VALUES (
					'" . $params["firstname"] . "',
					'" . $params["lastname"] . "',
					'" . $params["email"] . "',
					'" . $params["message"] . "'
					)";
				$stmt = $this->db->query($insert);

			}
			//Mail abschicken
			$mailBody = "";

			$mailBody = "<html>";
			$mailBody .= "<body>";
			$mailBody .= "<p style=\"font-family: sans-serif;\">Auf der Webseite von Fairtrain hat jemand das Kontaktformular ausgefüllt. Hier sehen Sie die, vom Benutzer ausgefüllten Felder.</p>";

			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Vorname:</strong> " . $params["firstname"] . "</p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Nachname:</strong> " . $params["lastname"] . "</p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Email:</strong> " . $params["email"] . "</p>";
			$mailBody .= " <p style=\"font-family: sans-serif;\"><strong>Nachricht:</strong> " . $params["message"] . "</p>";

			$mailBody .= "</body>";
			$mailBody .= "</html>";

			if (strlen($params["email"]) > 0) {
				require 'library/phpmailer/Exception.php';
				require 'library/phpmailer/PHPMailer.php';
				require 'library/phpmailer/SMTP.php';

				$mail            = new PHPMailer(true);
				$mail->SMTPDebug = 0;
				$mail->isSMTP();
				$mail->Host       = 'smtp.virtualtec.ch';
				$mail->SMTPAuth   = true;
				$mail->Username   = 'sender@live.dimaster.ch';
				$mail->Password   = 'kmlktfkr';
				$mail->SMTPSecure = 'tls';
				$mail->Port       = 587;
				$mail->CharSet    = "UTF-8";
				$mail->WordWrap   = 50;

				$mail->setFrom($params["email"], 'Fairtrain');

				$mail->addAddress("martin.ivanenko@hotmail.de");

				$mail->isHTML(true);
				$mail->Subject = "Fairtrain Kontakt";
				$mail->Body    = $mailBody;
				$mail->send();
			}
		}
	}

	/**
	 * @param  $form_id
	 * @return mixed
	 */
	public function getFormTemplate($form_id) {
		$q   = $this->db->query("SELECT selected FROM tbl_form_hardcoded WHERE cms_container_fk = '" . $form_id . "' ORDER BY form_hardcoded_id DESC LIMIT 0,1");
		$res = $q->fetch_assoc();
		return $res["selected"];
	}

	public function enterMailChimp() {
		$vars = array("FNAME" => $_POST["name"]);
		$this->getMailchimp("c062cfe2fd", $_POST["email"], $vars);
	}

	/**
	 * @param $form_id
	 * @param $email
	 * @param $merge_vars
	 * @param $apiKey
	 */
	public function getMailchimp($form_id, $email, $merge_vars, $apiKey = 0) {
		$apiKey    = $apiKey == 0 ? "4039e8aae419f992892de74cbb57245a-us14" : $apiKey;
		$MailChimp = new MailChimp($apiKey);
		$result    = $MailChimp->call('lists/subscribe', array(
			'id'                => $form_id,
			'email'             => array('email' => $email), //array('email'=>$_GET["email"]),
			'merge_vars'        => $merge_vars, //array('FNAME'=>$_GET["vorname"],'LNAME'=>$_GET["name"]),
			'double_optin'      => false,
			'update_existing'   => true,
			'replace_interests' => false,
			'send_welcome'      => false,
		));
	}
}
?>
