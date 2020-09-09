<?php
class crypt {
	public static function gen($len) {
		$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!_-$?&+";
		$pwd = "";

		for($i=0;$i<$len;$i++) {
			$pwd.=$str[rand(0,strlen($str)-1)];
		}

		return $pwd;
	}
}
?>