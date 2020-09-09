<?php
class auth {
	public static function login() {
		$username = $_POST["ds_username"];
		$password = $_POST["ds_password"];

		$q = $_SESSION["db"]->query("SELECT cms_user_id,email,password,cms_user_type_fk FROM cms_user");
		while($res = $q->fetch_row()) {
			if(strtolower($res[1])==strtolower($username) && password_verify($password, $res[2])) {
				$_SESSION["login"]=true;
				$_SESSION["user"] = ["id"=>$res[0],"email"=>$res[1],"type"=>$res[3]];

				break;
			} 
		}
	}
}
?>