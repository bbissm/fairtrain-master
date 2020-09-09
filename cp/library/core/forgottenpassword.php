<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class forgottenpassword extends module {
	/**
	 * @var mixed
	 */
	public static $auth = false;
	/**
	 * @var string
	 */
	protected $message = "";
	/**
	 * @var int
	 */
	protected $success = 0;

	public function setup() {

	}

	public function controller() {
		if ($_POST["ds_reset_email"] != "") {
			require 'library/phpmailer/Exception.php';
			require 'library/phpmailer/PHPMailer.php';
			require 'library/phpmailer/SMTP.php';

			$pwd      = crypt::gen(6);
			$password = password_hash($pwd, PASSWORD_DEFAULT);
			$token    = md5(config::SALT . $pwd);

			$q   = $this->db->query("SELECT cms_user_id FROM cms_user WHERE email='" . $_POST["ds_reset_email"] . "'");
			$res = $q->fetch_assoc();

			if ($res["cms_user_id"] != "") {
				$this->db->query("UPDATE cms_user SET token='$token',reset='$password' WHERE cms_user_id='" . $res["cms_user_id"] . "'");

				$mail = new PHPMailer(true);
				try {
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

					$mail->setFrom('no-reply@dimastersoftware.ch', 'DimasterSoftware CMS 3.0');
					$mail->addAddress($_POST["ds_reset_email"]);

					$mail->isHTML(true);
					$mail->Subject = translation::get("passwordreset");
					$mail->Body    = '<html>
				    					<body style="background-color:#FFF; padding: 10px; font-family: \'Century Gothic\',Arial; font-size: 14px;" bgcolor="#FFF">
				    						<table style="width:100%; height:100%; border-collapse: collapse;">
				    							<tr>
				    								<td style="width:600px;">
							    						<img src="' . config::PROTOCOL . '://' . $_SERVER["SERVER_NAME"] . '/cp/img/cms-logo-black.png" alt="DimasterSoftware" width="160" style="width:160px;" />
							    						<br /><br /><br />
							    						<p style="font-family: \'Century Gothic\',Arial; font-size: 14px;">' . translation::get("hello") . '</p>
							    						<p style="font-family: \'Century Gothic\',Arial; font-size: 14px;">' . translation::get("passwordresetinfo") . '</p>
							    						<p style="font-family: \'Century Gothic\',Arial; font-size: 14px;">' . $pwd . '<br /><a style="color:#0080d2;" href="' . config::PROTOCOL . '://' . $_SERVER["SERVER_NAME"] . '/cp/forgottenpassword?token=' . $token . '">' . translation::get("activate") . '</a></p>
							    						<p style="font-family: \'Century Gothic\',Arial; font-size: 14px;">' . translation::get("salutation") . '</p>
							    					</td>
							    					<td style="background-color:#FFF;">&nbsp;</td>
							    				</tr>
							    			</table>
				    					</body>
				    				  </html>';
					$mail->AltBody = translation::get("hello") . "\n\n" . translation::get("passwordresetinfo") . "\n\n" . $pwd . "\n" . config::PROTOCOL . '://' . $_SERVER["SERVER_NAME"] . '/cp/forgottenpassword?token=' . $token . "\n\n" . translation::get("salutation");

					$mail->send();
					$this->message = translation::get("sent");
					$this->success = 1;
				} catch (Exception $e) {
					$this->message = 'Mailer Error: ' . $mail->ErrorInfo;
					$this->success = -1;
				}
			}
		}
	}

	public function view() {
		if ($_GET["token"] != "") {
			$q   = $this->db->query("SELECT cms_user_id FROM cms_user WHERE token='" . $_GET["token"] . "'");
			$res = $q->fetch_assoc();

			if ($res["cms_user_id"] != "") {
				$this->db->query("UPDATE cms_user SET password=reset WHERE cms_user_id='" . $res["cms_user_id"] . "'");

				$table = new table();
				$table->addTitle(["cols" => [translation::get("forgottenpassword_success_title")]]);
				$table->add(["cols" => [translation::get("forgottenpassword_success_text") . "<br /><br /><a href=\"/cp/\">" . translation::get("tologin") . "</a>"]]);
				echo $table->render();
			} else {
				$table = new table();
				$table->addTitle(["cols" => [translation::get("forgottenpassword_error_title")]]);
				$table->add(["cols" => [translation::get("forgottenpassword_error_text")]]);
				echo $table->render();
			}
		} else {
			$table = new table();
			$table->setup(["form" => ["action" => "/cp/forgottenpassword", "method" => "post"]]);
			$table->addTitle(["cols" => [translation::get("forgottenpassword_title")]]);
			$table->add(["cols" => [$table->addFormfield(["name" => "ds_reset_email", "type" => "email", "attr" => ["placeholder" => translation::get("passwordreset_email"), "drequired" => true]])]]);
			$table->add(["cols" => [$table->addFormfield(["name" => "ds_reset", "type" => "submit", "value" => translation::get("passwordreset")])]]);
			echo $table->render();

			if ($this->success == 1) {
				echo cp::message(["message" => $this->message, "type" => "success"]);
			}
			if ($this->success == -1) {
				echo cp::message(["message" => $this->message, "type" => "error"]);
			}
		}
	}
}
?>