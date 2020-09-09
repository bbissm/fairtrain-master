<?php
class cp {
	/**
	 * @var mixed
	 */
	private static $module = null;
	/**
	 * @var string
	 */
	private static $subnavigation = "";

	public static function setup() {
		if ($_SESSION["path"][0] == "") {
			$_SESSION["path"][0] = str_replace("/cp/", "", array_keys(self::getNavigationTree())[0]);
		}

		if ($_SESSION["path"][0] != "" && $_SESSION["path"][0] != "async") {
			if ($_SESSION["path"][0]::$auth == false || ($_SESSION["login"] && in_array($_SESSION["user"]["type"], $_SESSION["path"][0]::$authLevel))) {
				self::$module = new $_SESSION["path"][0]();
				self::$module->setup();
				self::$module->controller();
			}
		}
	}

	public static function appendHead() {
		ob_start();
		if (self::$module != null) {
			self::$module->htmlHead();
		}
		return ob_get_clean();
	}

	/**
	 * @return mixed
	 */
	public static function getNavigationTree() {
		$navigation = [];
		foreach (config::navigation as $class => $name) {
			$cl = explode("/", $class);
			$cl = $cl[2];
			if ($cl::$auth == false || ($_SESSION["login"] && in_array($_SESSION["user"]["type"], $cl::$authLevel))) {
				$navigation[$class] = $name;
			}
		}

		return $navigation;
	}

	public function login() {
		ob_start();

		if (self::$module != null) {
			self::$module->view();
		} else {
			if ($_GET["logout"] != "") {
				$table = new table();
				$table->addTitle(["cols" => [translation::get("logout")]]);
				$table->add(["cols" => [translation::get("logout_message")]]);
				echo $table->render();
			} else {
				$table = new table();
				$table->setup(["form" => ["method" => "post"]]);
				$table->addTitle(["cols" => ["<center><img id=\"logo-big\" src=\"/cp/img/cms-logo.png\" alt=\"Logo\" /></center>"]]);
				$table->add(["cols" => [$table->addFormfield(["name" => "ds_username", "type" => "email", "attr" => ["placeholder" => translation::get("mail"), "drequired" => true]])]]);
				$table->add(["cols" => [$table->addFormfield(["name" => "ds_password", "type" => "password", "attr" => ["placeholder" => translation::get("password"), "drequired" => true]])]]);
				$table->add(["cols" => [$table->addFormfield(["name" => "login", "type" => "submit", "value" => translation::get("login")])]]);
				$table->add(["cols" => [["style" => ["text-align" => "center"], "value" => "<a href=\"/cp/forgottenpassword\">" . translation::get("forgottenpassword") . "</a>"]]]);
				echo $table->render();

				if ($_POST["login"] != "") {
					echo self::message(["message" => translation::get("login_error"), "type" => "error"]);
				}
			}
		}

		return ob_get_clean();
	}

	public static function renderNavigation() {
		ob_start();
		echo "<ul>";
		echo "<li><a href=\"/cp/\"><img src=\"/cp/img/cms-logo-small.png\" alt=\"DimasterSoftware\" /></a></li>";
		foreach (config::navigation as $class => $name) {
			$cl = explode("/", $class);
			$cl = $cl[2];
			if ($cl::$auth == false || ($_SESSION["login"] && in_array($_SESSION["user"]["type"], $cl::$authLevel))) {
				echo "<li><a href=\"$class\">" . translation::get($name) . "</a></li>";
			}
		}
		echo "</ul>";
		echo "<a href=\"/cp/?logout=1\"><img id=\"logout\" src=\"/cp/img/power.png\" alt=\"logout\" /></a>";
		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public static function addSubnavigation($config) {
		ob_start();
		echo "<ul class=\"subnavigation\">";
		foreach ($config as $key => $value) {
			echo "<li><a href=\"$key\">" . $value . "</a></li>";
		}
		echo "</ul>";
		self::$subnavigation = ob_get_clean();
	}

	public static function subnavigation() {
		if (self::$subnavigation != "") {
			return " class=\"subnavigation\"";
		} else {
			return "";
		}
	}

	public static function renderSubnavigation() {
		return self::$subnavigation;
	}

	/**
	 * @param $config
	 */
	public static function message($config) {
		ob_start();

		echo "<script>dialog('" . str_replace("'", "\'", $config["message"]) . "','" . $config["type"] . "','" . (int) $config["popup"] . "');</script>";

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public static function confirm($config) {
		ob_start();

		if ($config["wrapper"] == "") {
			$config["wrapper"] = "#wrapper";
		}

		echo "<script>
			swal({
			  title: '" . str_replace("'", "\'", $config["title"]) . "',
			  text: '" . str_replace("'", "\'", $config["text"]) . "',
			  icon: 'warning',
			  buttons: true,
			  dangerMode: true,
			  confirmButtonColor: '#0080d2',
			  cancelButtonColor: '#c00'
			}).then((result) => {
			  if (result) {
			    $.get('" . $config["url"] . "',function(data) {
			    	$('" . $config["wrapper"] . "').html(data);
			    	setup();
					cms.bindContainer();
					fireResizeiFrame();
			    });
			  }
			});
		</script>";

		return ob_get_clean();
	}

	public static function renderView() {
		ob_start();
		self::$module->view();
		return ob_get_clean();
	}
}
?>
