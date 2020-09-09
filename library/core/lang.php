<?php
class lang {
	public static function detect($allowed_languages, $default_language, $lang_variable = null, $strict_mode = true) {
		if ($lang_variable === null) {
			$lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}
		
		if (empty($lang_variable)) {
			return $default_language;
		}
		
		$accepted_languages = preg_split('/,\s*/', $lang_variable);
		
		$current_lang = $default_language;
		$current_q = 0;
		
		foreach ($accepted_languages as $accepted_language) {
			$res = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)'.
			'(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);
			
			if (!$res) {
				continue;
			}
			
			$lang_code = explode ('-', $matches[1]);
			
			if (isset($matches[2])) {
				$lang_quality = (float)$matches[2];
			} else {
				$lang_quality = 1.0;
			}
			
			while (count ($lang_code)) {
				if (in_array (strtolower (join ('-', $lang_code)), $allowed_languages)) {
					if ($lang_quality > $current_q) {
						$current_lang = strtolower (join ('-', $lang_code));
						$current_q = $lang_quality;
						break;
					}
				}
				if ($strict_mode) {
					break;
				}
				array_pop ($lang_code);
			}
		}
		
		return $current_lang;
	}

    public static function getLocation() {
         if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		$ip=$_SERVER['REMOTE_ADDR'];
		}
		$result = self::locateWithFreegeoip($ip);
		if ($result == 0) {
			$result = self::locateWithGeoPlugin($ip);
		}
		return $result;
	}
	
	public static function locateWithGeoPlugin($ip) {
		try {
			$json = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
			$countryCode = $json->geoplugin_countryCode;
			$longitude = $json->geoplugin_longitude;
			$latitude = $json->geoplugin_latitude;
			return array("country" => $countryCode,
						"longitude" => $longitude,
						"latitude" => $latitude,
						"from" => "geoplugin");
		} catch (Exception $e) {
			config::log("geolocation.log","Geoplugin.net blocked connection!");
			return 0;
		}
	}

	public static function locateWithFreegeoip($ip) {
		try {
			$json = json_decode(file_get_contents("http://freegeoip.net/json/".$ip));
			$countryCode = $json->country_code;
			$longitude = $json->longitude;
			$latitude = $json->latitude;
			return array("country" => $countryCode,
						"longitude" => $longitude,
						"latitude" => $latitude,
						"from" => "freegeoip");
		} catch (Exception $e) {
			config::log("geolocation.log","Freegeoip blocked connection!");
			return 0;
		}
	}
}
?>