<?php
class translation {
	private static $db = null; 

	public static function get($key) {
		$q = $_SESSION["db"]->query("SELECT value FROM cms_translation WHERE id='".$key."' AND cms_lang_fk='".$_SESSION["lang"]["key"]."'");
		$res = $q->fetch_assoc();

		if(trim($res["value"])=="") {
			$res["value"]="[".$key."]";
		}  

		return $res["value"];
	}
}
?>