<?php
class table {
	use observer;

	/**
	 * @var array
	 */
	private $table = [];
	/**
	 * @var int
	 */
	private $scrollsize = 0;
	/**
	 * @var mixed
	 */
	private $isForm = false;
	/**
	 * @var string
	 */
	private $formMethod = "";
	/**
	 * @var string
	 */
	private $formAction = "";
	/**
	 * @var array
	 */
	private $formFields = [];
	/**
	 * @var string
	 */
	private $formParent = "";
	/**
	 * @var array
	 */
	private $formAttr = [];
	/**
	 * @var array
	 */
	private $td = [];
	/**
	 * @var string
	 */
	private $tableClass = "";
	/**
	 * @var int
	 */
	private $dragType = 0;
	/**
	 * @var int
	 */
	private $maxCols = 0;
	/**
	 * @var int
	 */
	private $hasControls = 0;
	/**
	 * @var string
	 */
	private $formClass = "";
	/**
	 * @var string
	 */
	private $sqlTable = "";
	/**
	 * @var array
	 */
	private $sqlWhere = [];
	/**
	 * @var mixed
	 */
	private $db;
	/**
	 * @var int
	 */
	private $success = 0;
	/**
	 * @var mixed
	 */
	private $popupclose = true;

	/**
	 * @param array $config
	 */
	public function setup($config = []) {
		$this->db = $_SESSION["db"];

		$this->scrollsize       = $config["scrollsize"];
		$this->isForm           = isset($config["form"]);
		$this->formMethod       = $config["form"]["method"];
		$this->formAction       = $config["form"]["action"];
		$this->formClass        = $config["form"]["class"];
		$this->formParent       = $config["form"]["parent"];
		$this->sqlTable         = $config["form"]["sqltable"];
		$this->sqlWhere         = $config["form"]["sqlwhere"];
		$this->formAttr         = $config["form"]["attr"];
		$this->slide_to_element = $config["form"]["slide_to_element"];
		if ($config["form"]["popup"] != null && $config["form"]["popup"] == "1") {
			$this->popupclose = true;
		} else {
			$this->popupclose = false;
		}
		$this->td         = $config["td"];
		$this->tableClass = $config["class"];

		if ($config["dragable"] != null) {
			if (!is_array($this->tableClass)) {
				$this->tableClass = [];
			}

			array_push($this->tableClass, "dragable");
			$this->dragType = $config["dragable"]["type"];
		}

		if ($this->formAction != "") {
			$get = "";
			foreach ($_GET as $k => $v) {
				if ($get != "") {
					$get .= "&";
				}

				$get .= $k . "=" . $v;
			}

			if ($get != "") {
				$this->formAction .= "?" . $get;
			}
		}
	}

	public function controller() {
		if (($this->sqlTable != "" && ($_GET["source"] == $this->sqlTable) || $_GET["source"] == "")) {
			if ($_GET["source"] != "" && $_GET["startPos"] != "" && $_GET["endPos"] != "" && $_GET["startPos"] != $_GET["endPos"] && count($_POST) <= 0) {
				$where = "";

				if ($this->formParent != "") {
					if ($this->dragType) {
						$where = " AND is_deleted=0";
					}

					if (count($this->sqlWhere) > 0) {
						foreach ($this->sqlWhere as $key => $value) {
							$where .= " AND `$key`='$value'";
						}
					}

					$q = $this->db->query("SELECT " . str_replace("tbl_", "", $this->sqlTable) . "_id FROM " . $this->sqlTable . " WHERE " . $this->formParent . "=" . $_GET["parent"] . "$where ORDER BY sort ASC");
				} else {
					if ($this->dragType) {
						$where = " WHERE is_deleted=0";
					}

					if (count($this->sqlWhere) > 0) {
						foreach ($this->sqlWhere as $key => $value) {
							if ($where == "") {
								$where = " WHERE `$key`='$value'";
							} else {
								$where .= " AND `$key`='$value'";
							}
						}
					}

					$q = $this->db->query("SELECT " . str_replace("tbl_", "", $this->sqlTable) . "_id FROM " . $this->sqlTable . "$where ORDER BY sort ASC");
				}
				echo $this->db->error;
				$sort = [];
				while ($res = $q->fetch_row()) {
					array_push($sort, $res[0]);
				}

				$nsort = [];
				if ($_GET["endPos"] == -1) {
					array_push($nsort, $sort[$_GET["startPos"]]);
				}

				foreach ($sort as $k => $v) {
					if ($k == $_GET["endPos"] + 1 && $_GET["endPos"] != -1) {
						array_push($nsort, $sort[$_GET["startPos"]]);
					}
					array_push($nsort, $v);
				}

				if ($_GET["endPos"] + 1 == count($sort)) {
					array_push($nsort, $sort[$_GET["startPos"]]);
				}

				if ($_GET["endPos"] == -1) {
					unset($nsort[$_GET["startPos"] + 1]);
				} else if ($_GET["startPos"] > $_GET["endPos"]) {
					unset($nsort[$_GET["startPos"] + 1]);
				} else {
					unset($nsort[$_GET["startPos"]]);
				}

				$i = 0;
				foreach ($nsort as $v) {
					$this->db->query("UPDATE " . $this->sqlTable . " SET sort=$i WHERE " . str_replace("tbl_", "", $this->sqlTable) . "_id=" . $v);
					echo $this->db->error;
					$i++;
				}
			}

			if ($_GET["prmv"] != "") {
				$this->subscribe('beforePRMV');

				$q   = $this->db->query("DESCRIBE " . $this->sqlTable);
				$res = $q->fetch_row();

				if ($_GET["confirm"] == 1) {
					$this->db->query("DELETE FROM " . $this->sqlTable . " WHERE `" . $res[0] . "`=" . $_GET["prmv"]);
				} else {
					$url = "/cp/" . implode("/", $_SESSION["path"]) . "?";
					$i   = 0;
					foreach ($_GET as $k => $v) {
						if ($i > 0) {
							$url .= "&";
						}

						$url .= $k . "=" . $v;
						$i++;
					}
					$url .= "&confirm=1";

					$wrapper = "#wrapper";
					if ($this->formAttr["target"] != "") {
						$wrapper = $this->formAttr["target"];
					}

					echo cp::confirm(["title" => translation::get("confirm_delete"),
						"text"                    => translation::get("confirm_delete_text"),
						"confirm"                 => translation::get("yes"),
						"cancel"                  => translation::get("cancel"),
						"url"                     => $url,
						"wrapper"                 => $wrapper]);
				}

				$this->subscribe('afterPRMV');
			}

			if ($_GET["rmv"] != "") {
				$this->subscribe('beforeRMV');

				$q   = $this->db->query("DESCRIBE " . $this->sqlTable);
				$res = $q->fetch_row();

				if ($_GET["confirm"] == 1) {
					$this->db->query("UPDATE " . $this->sqlTable . " SET is_deleted=1 WHERE `" . $res[0] . "`=" . $_GET["rmv"]);
					echo $this->db->error;
				} else {
					$url = implode("/", $_SESSION["path"]) . "?";
					$i   = 0;
					foreach ($_GET as $k => $v) {
						if ($i > 0) {
							$url .= "&";
						}

						$url .= $k . "=" . $v;
						$i++;
					}
					$url .= "&confirm=1";

					if (substr($url, 0, 1) != "/") {
						$url = "/cp/" . $url;
					}

					echo cp::confirm(["title" => translation::get("confirm_delete"),
						"text"                    => translation::get("confirm_delete_text"),
						"confirm"                 => translation::get("yes"),
						"cancel"                  => translation::get("cancel"),
						"url"                     => $url]);
				}

				$this->subscribe('afterRMV');
			}

			if ($_GET["active"] != "") {
				$this->subscribe('beforeActive');

				$q   = $this->db->query("DESCRIBE " . $this->sqlTable);
				$res = $q->fetch_row();

				$hastimestamp = 0;
				while ($res1 = $q->fetch_row()) {
					if ($res1[0] == "timestamp") {
						$hastimestamp = 1;
						break;
					}
				}

				if ($hastimestamp) {
					$this->db->query("UPDATE " . $this->sqlTable . " SET is_active=1-is_active,timestamp=timestamp WHERE `" . $res[0] . "`=" . $_GET["active"]);
				} else {
					$this->db->query("UPDATE " . $this->sqlTable . " SET is_active=1-is_active WHERE `" . $res[0] . "`=" . $_GET["active"]);
				}
				echo $this->db->error;

				$this->subscribe('afterActive');
			}

			if ($this->sqlWhere != null) {
				$id    = 0;
				$where = "";
				foreach ($this->sqlWhere as $key => $value) {
					if ($where != "") {
						$where .= " AND ";
					}

					$where .= "`" . $key . "`=" . $value;
					$id = $value;
				}

				if ($this->formMethod == "post" && $this->sqlTable != "") {
					if ($_POST["dformsubmit"] != "") {
						$data = [];
						$q    = $this->db->query("DESCRIBE " . $this->sqlTable);
						while ($res = $q->fetch_row()) {
							if ($_POST[$res[0]] != "" || $res[1] == "tinyint(1)" || isset($_POST[$res[0]])) {
								if ($res[1] == "timestamp" || $res[1] == "datetime") {
									$data[$res[0]] = date("Y-m-d H:i:s", strtotime($_POST[$res[0]]));
								} else if ($res[1] == "date") {
									$data[$res[0]] = date("Y-m-d", strtotime($_POST[$res[0]] . " 00:00:01"));
								} else {
									$data[$res[0]] = $_POST[$res[0]];
								}
							}
						}

						$set = "";
						foreach ($data as $key => $value) {
							$value = $this->crop($value);

							if ($set != "") {
								$set .= ",";
							}

							$set .= "`$key`='$value'";
						}

						if ($id != "") {
							$q   = $this->db->query("SELECT " . str_replace("tbl_", "", $this->sqlTable) . "_id FROM " . $this->sqlTable . " WHERE $where");
							$res = $q->fetch_row();

							if ($res[0] == "") {
								$this->subscribe('beforeInsert');

								$this->db->query("INSERT " . $this->sqlTable . " SET $set");

								$this->subscribe('afterInsert');
							} else {
								$this->subscribe('beforeUpdate');

								$this->db->query("UPDATE " . $this->sqlTable . " SET $set WHERE $where");

								$this->subscribe('afterUpdate');
							}
						} else {
							$this->db->query("INSERT " . $this->sqlTable . " SET $set");
						}
						$this->success = 1;
						echo $this->db->error;
					}
				} else {
					if ($_GET["dformsubmit"] != "") {
						$data = [];
						$q    = $this->db->query("DESCRIBE " . $this->sqlTable);
						while ($res = $q->fetch_row()) {
							if ($_GET[$res[0]] != "" || $res[1] == "tinyint(1)" || isset($_GET[$res[0]])) {
								$data[$res[0]] = $_GET[$res[0]];
							}
						}

						$set = "";
						foreach ($data as $key => $value) {
							$value = $this->crop($value);
							if ($set != "") {
								$set .= ",";
							}

							$set .= "`$key`='$value'";
						}

						if ($id != "") {
							$q   = $this->db->query("SELECT " . str_replace("tbl_", "", $this->sqlTable) . "_id FROM " . $this->sqlTable . " WHERE $where");
							$res = $q->fetch_row();

							if ($res[0] == "") {
								$this->subscribe('beforeInsert');

								$this->db->query("INSERT " . $this->sqlTable . " SET $set");

								$this->subscribe('afterInsert');
							} else {
								$this->subscribe('beforeUpdate');

								$this->db->query("UPDATE " . $this->sqlTable . " SET $set WHERE $where");

								$this->subscribe('afterUpdate');
							}
						} else {
							$this->db->query("INSERT " . $this->sqlTable . " SET $set");
						}
						$this->success = 1;
					}
				}

				if ($id != "" && $id != 0) {
					$q = $this->db->query("SELECT * FROM " . $this->sqlTable . " WHERE " . $where);
					echo $this->db->error;
					$this->formFields = $q->fetch_assoc();
				}
			}
		}
	}

	/**
	 * @param $config
	 * @return mixed
	 */
	public function crop($config) {
		putenv('PATH=' . getenv('PATH') . ':/usr/local/bin');

		if (stristr($config, "crop:")) {
			$config = str_replace("crop:", "", $config);
			$config = explode(",", $config);

			$x       = $config[0];
			$y       = $config[1];
			$width   = $config[2];
			$height  = $config[3];
			$src     = ".." . $config[4];
			$tWidth  = $config[5];
			$tHeight = $config[6];

			$dir = str_replace("../assets/", "../assets/cache/$tWidth/$tHeight/", $src);

			$rcdir = explode("/", $dir);
			$sdir  = "";
			foreach ($rcdir as $k => $ndir) {
				$sdir .= $ndir . "/";
				if ($k >= 1 && count($rcdir) - 1 > $k) {
					@mkdir($sdir);
				}
			}

			$output     = "";
			$return_var = "";

			exec(config::CONVERTLOCATION . " \"" . $src . "\" -crop " . $width . "x" . $height . "+" . $x . "+" . $y . " \"" . $dir . "\" 2>&1", $output, $return_var);
			exec(config::CONVERTLOCATION . " \"" . $dir . "\" -resize " . $tWidth . "x" . $tHeight . " -quality 80 -density 72 \"" . $dir . "\" 2>&1", $output, $return_var);

			return str_replace("../", "/", $dir);
		} else {
			return $config;
		}
	}

	/**
	 * @param $config
	 * @return mixed
	 */
	public static function croppath($config) {
		if (stristr($config, "crop:")) {
			$config = str_replace("crop:", "", $config);
			$config = explode(",", $config);

			$x       = $config[0];
			$y       = $config[1];
			$width   = $config[2];
			$height  = $config[3];
			$src     = ".." . $config[4];
			$tWidth  = $config[5];
			$tHeight = $config[6];

			$dir = str_replace("../assets/", "../assets/cache/$tWidth/$tHeight/", $src);

			$rcdir = explode("/", $dir);
			$sdir  = "";
			foreach ($rcdir as $k => $ndir) {
				$sdir .= $ndir . "/";
				if ($k >= 1 && count($rcdir) - 1 > $k) {
					@mkdir($sdir);
				}
			}

			$output     = "";
			$return_var = "";

			return str_replace("../", "/", $dir);
		} else {
			return $config;
		}
	}

	/**
	 * @param $config
	 */
	public function add($config) {
		if ($this->maxCols < count($config["cols"])) {
			$this->maxCols = count($config["cols"]);
		}

		if (!is_array($config["style"])) {
			$config["style"] = [];
		}

		array_push($this->table, ["style" => $config["style"],
			"cols"                            => $config["cols"],
			"controls"                        => $config["controls"],
			"class"                           => $config["class"],
			"attr"                            => $config["attr"]]);

		if (isset($config["controls"])) {
			$this->hasControls = 1;
		}
	}

	/**
	 * @param $config
	 */
	public function addTitle($config) {
		if ($config == null) {
			$config = [];
		}

		if (!is_array($config["style"])) {
			$config["style"] = [];
		}

		$config["style"]["background-color"] = "#000";
		$config["style"]["border-color"]     = "#000";
		$config["style"]["color"]            = "#FFF";

		if (!is_array($config["class"])) {
			$config["class"] = [];
		}

		array_push($config["class"], "title");
		if ($config["controls"] != null) {
			$config["controls"]["style"] = "white";
		}

		$this->add($config);
	}

	/**
	 * @param $config
	 */
	public function addSubtitle($config) {
		if ($config == null) {
			$config = [];
		}

		$config["style"]["background-color"] = "#ddd";

		if (!is_array($config["class"])) {
			$config["class"] = [];
		}

		array_push($config["class"], "subtitle");

		$this->add($config);
	}

	/**
	 * @param $config
	 */
	public function addImageSelect($config) {
		ob_start();

		echo "<div class=\"cropper\">";

		echo "</div>";

		$attr = "";
		if ($config["attr"] != null) {
			foreach ($config["attr"] as $key => $value) {
				$attr .= " $key=\"$value\"";
			}
		}

		echo "<div class=\"fileselector\"$attr>";

		$media = new media();
		$media->imagepicker($config);

		echo "</div>";

		return ob_get_clean();
	}

	public function addImageSelectCustomController($config) {
		ob_start();

		echo "<div class=\"cropper\">";

		echo "</div>";		
		
		echo "<div class=\"fileselector\"$attr>";

		$media = new media();
		$media->imagepicker($config);

		echo "</div>";

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public function addVideoSelect($config) {
		ob_start();

		echo "<div class=\"cropper\">";

		echo "</div>";

		$attr = "";
		if ($config["attr"] != null) {
			foreach ($config["attr"] as $key => $value) {
				$attr .= " $key=\"$value\"";
			}
		}

		echo "<div class=\"fileselector\"$attr>";

		$media = new media();
		$media->videopicker($config);

		echo "</div>";

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public function addFileSelect($config) {
		ob_start();

		$attr = "";
		if ($config["attr"] != null) {
			foreach ($config["attr"] as $key => $value) {
				$attr .= " $key=\"$value\"";
			}
		}

		echo "<div class=\"fileselector\"$attr>";

		$media = new media();
		$media->filepicker($config);

		echo "</div>";

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public function addFileupload($config) {
		ob_start();

		$attr = "";
		if ($config["attr"] != null) {
			foreach ($config["attr"] as $key => $value) {
				$attr .= " $key=\"$value\"";
			}
		}

		echo "<div class=\"dragdropupload\"$attr>";
		echo "<div>";
		echo "<div>" . translation::get("dragdrop") . "</div>";
		echo "</div>";
		if ($config["target"] != "") {
			echo "<input type=\"hidden\" name=\"target\" value=\"" . $config["target"] . "\" />";
		}
		echo "<input type=\"file\" name=\"" . $config["name"] . "[]\" multiple />";

		echo "<div id=\"progress\"></div>";

		echo "</div>";

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public function addFormField($config) {
		ob_start();

		$style = "";
		if ($config["style"] != null) {
			$style = " style=\"" . $config["style"] . "\"";
		}

		$attr = "";
		if ($config["attr"] != null) {
			foreach ($config["attr"] as $key => $value) {
				$attr .= " $key=\"$value\"";
			}
		}

		if ($this->formFields[$config["name"]] != "") {
			$config["value"] = $this->formFields[$config["name"]];
		}

		$class = "";
		if ($config["type"] == "datetime") {
			if ($config["class"] != null) {
				array_push($config["class"], "datetime");
			} else {
				$config["class"] = ["datetime"];
			}
			$config["type"] = "text";

			$config["value"] = date("d.m.Y H:i", strtotime($config["value"]));
			if ($config["value"] == "01.01.1970 01:00") {
				$config["value"] = date("d.m.Y H:i");
			}

		} else if ($config["type"] == "date") {
			if ($config["class"] != null) {
				array_push($config["class"], "date");
			} else {
				$config["class"] = ["date"];
			}
			$config["type"] = "text";

			$config["value"] = date("d.m.Y", strtotime($config["value"]));
			if ($config["value"] == "01.01.1970") {
				$config["value"] = date("d.m.Y");
			}

		}

		if ($config["class"] != null) {
			$class = implode(" ", $config["class"]);
		}

		if ($class != "") {
			$class = " class=\"" . $class . "\"";
		}

		if ($config["type"] == "select") {
			echo "<select name=\"" . $config["name"] . "\"$attr $class>";
			foreach ($config["options"] as $key => $value) {
				$key = str_replace("\"", "&quot;", $key);

				echo "<option value=\"$key\"";
				if ($config["value"] == $key) {
					echo " selected=\"selected\"";
				}
				echo ">$value</option>";
			}
			echo "</textarea>";
		} else if ($config["type"] == "textarea") {
			echo "<textarea name=\"" . $config["name"] . "\"$attr $class>" . $config["value"] . "</textarea>";
		} else if ($config["type"] == "tinymce") {
			echo "<textarea tinymce=\"true\" name=\"" . $config["name"] . "\"$attr $class>" . $config["value"] . "</textarea>";
		} else {
			if ($config["type"] == "radio" || $config["type"] == "checkbox") {
				echo "<input";
				if ($config["value"] == $config["set"]) {
					echo " checked=\"true\"";
				}
				$config["value"] = str_replace("\"", "&quot;", $config["value"]);
				echo " type=\"" . $config["type"] . "\" name=\"" . $config["name"] . "\" value=\"" . $config["set"] . "\" $attr $class />";
			} else {
				$config["value"] = str_replace("\"", "&quot;", $config["value"]);
				echo "<input type=\"" . $config["type"] . "\" name=\"" . $config["name"] . "\" value=\"" . $config["value"] . "\" $style $attr $class />";
			}
		}

		return ob_get_clean();
	}

	/**
	 * @param $config
	 */
	public function auto($config) {
		$table  = $config["from"];
		$fields = $config["select"];
		$vars   = explode(",", $fields);
		foreach ($vars as $k => $v) {
			if (stristr($v, " as ")) {
				if (stristr($v, ")")) {
					if (stristr($v, "(")) {
						//skip
					} else {
						$k1 = $k - 1;
						while ($k1 >= 0) {
							$elm = $vars[$k1];
							unset($vars[$k1]);
							if (stristr($elm, "(")) {
								break;
							}

							$k1--;
						}
					}
				}

				$v        = explode(" as ", $v);
				$v        = $v[count($v) - 1];
				$vars[$k] = $v;
			}
		}
		$id = $config["id"];
		if ($id != "" && $fields != "" && $fields != "*") {
			$fields = $id . "," . $fields;
		}
		if ($config["order"] != "") {
			$order = "ORDER BY " . $config["order"];
		}

		if ($config["where"] != "") {
			$where = "WHERE " . $config["where"];
		}

		if (isset($config["controls"])) {
			$this->hasControls = 1;
		}

		$q = $this->db->query("SELECT $fields FROM $table $where $order");
		echo $this->db->error;
		while ($res = $q->fetch_assoc()) {
			$cols = [];
			foreach ($vars as $kv => $v) {
				if (trim($v) == "is_active") {
					if ($res[trim($v)]) {
						$res[trim($v)] = translation::get("yes");
					} else {
						$res[trim($v)] = translation::get("no");
					}
					$res[trim($v)] = "<a class=\"async\" href=\"" . $this->action . "?active=" . $res[$id] . "\">" . $res[trim($v)] . "</a>";
				}

				array_push($cols, $res[trim($v)]);
			}

			if ($this->maxCols < count($res) - 1) {
				$this->maxCols = count($res) - 1;
			}

			$controls = [];
			if (isset($config["controls"])) {
				foreach ($config["controls"] as $k => $v) {
					$controls[str_replace("{id}", $res[$id], $k)] = $v;
				}
			}

			array_push($this->table, ["style" => $style,
				"cols"                            => $cols,
				"controls"                        => $controls]);
		}
	}

	public function render() {
		ob_start();

		$class = "";
		if ($this->tableClass != null) {
			$class = " class=\"" . implode(" ", $this->tableClass) . "\"";
		}

		if ($this->isForm) {
			$fclass = "";
			if ($this->formClass != null) {
				$fclass = " class=\"" . implode(" ", $this->formClass) . "\"";
			}

			$attr = "";
			if ($this->formAttr != null) {
				foreach ($this->formAttr as $key => $value) {
					$attr .= " $key=\"$value\"";
				}
			}

			$slide_to_element = "";
			if ($this->slide_to_element == true) {
				$slide_to_element = "data-slide-to-element='" . $this->slide_to_element . "' ";
			}

			echo "<form enctype=\"multipart/form-data\" method=\"" . $this->formMethod . "\" source=\"" . $this->sqlTable . "\" action=\"" . $this->formAction . "\" $slide_to_element$fclass$attr>";
			if ($this->sqlTable != "") {
				echo "<input type=\"hidden\" name=\"dformsubmit\" value=\"true\" />";
			}

		}
		echo "<div class=\"table-wrapper\">";
		if ($this->scrollsize == 0) {
			echo "<table$class>";
		} else {
			echo "<table$class scrollsize=\"" . $this->scrollsize . "\">";
		}

		foreach ($this->table as $line) {
			$style = "";
			if ($line["style"] != null) {
				foreach ($line["style"] as $key => $value) {
					$style .= $key . ":" . $value . ";";
				}
			}
			$styletd = $style;
			if ($style != "") {
				$style = " style=\"$style\"";
			}

			$attr = "";
			if ($line["attr"] != null) {
				foreach ($line["attr"] as $key => $value) {
					$attr .= " $key=\"$value\"";
				}
			}

			if ($line["class"] != "") {
				echo "<tr class=\"" . implode(" ", $line["class"]) . "\"$attr>";
			} else {
				echo "<tr class=\"row\"$attr>";
			}

			if (!is_array($line["cols"])) {
				$line["cols"] = [];
			}
			foreach ($line["cols"] as $key => $col) {
				$styletd1 = $styletd;
				if (is_array($col)) {
					if ($col["style"] != null) {
						foreach ($col["style"] as $key1 => $value1) {
							$styletd1 .= $key1 . ":" . $value1 . ";";
						}
					}

					$col = $col["value"];
				}

				$width = "";
				if ($this->td[$key] != null) {
					$width = " width=\"" . $this->td[$key] . "\"";
				}

				$colspan = "";
				if ($key == count($line["cols"]) - 1) {
					if ($this->maxCols - count($line["cols"]) + 1 > 1) {
						$colspan = " colspan=\"" . ($this->maxCols - count($line["cols"]) + 1) . "\"";
					}

				}

				if ($colspan != "") {
					$width = "";
				}

				if ($styletd1 != "") {
					$styletd1 = " style=\"$styletd1\"";
				}

				echo "<td$colspan$styletd1$width>$col</td>";
			}

			if (count($line["controls"]) > 0) {
				echo "<td$style class=\"config\">";
				if ($line["controls"]["style"] != "") {
					echo "<div class=\"icon " . $line["controls"]["style"] . "\">";
				} else {
					echo "<div class=\"icon\">";
				}
				echo "<ul>";
				foreach ($line["controls"] as $key => $value) {
					if ($key == "style") {
						continue;
					}

					$target = "";
					if (is_array($value)) {
						$target = $value["target"];
						$value  = $value["name"];
						if ($target != "") {
							$target = " target=\"$target\"";
						}

					}
					echo "<li><a href=\"" . $key . "\"$target>" . $value . "</a></li>";
				}
				echo "</ul>";
				echo "</div>";
				echo "</td>";
			} else if ($this->hasControls) {
				echo "<td$style class=\"config\"></td>";
			}

			echo "</tr>";
		}

		echo "</table>";
		echo "</div>";
		if ($this->isForm) {
			echo "</form>";
		}

		if ($this->success == 1) {
			echo cp::message(["message" => translation::get("success"), "type" => "success", "popup" => $this->popupclose]);
		}

		return ob_get_clean();
	}
}
?>