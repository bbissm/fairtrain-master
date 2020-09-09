<?php
class config {
	CONST dbHost = "localhost";
	CONST dbUsername = "";
	CONST dbPassword = "";
	CONST dbName= "";

	CONST template = "";
	CONST protocol = "";
	CONST salt = "";
	CONST login = ;

	//backend
	CONST navigation = [
		"/cp/sitemap"        => "navigation_sitemap",
		"/cp/frontend"       => "navigation_content",
		"/cp/media"          => "navigation_media",
		"/cp/kurse"          => "navigation_kurse",
		"/cp/seminar"        => "navigation_seminar",
		"/cp/links"          => "navigation_links",
		"/cp/kursleitung"    => "navigation_kursleitung",
		"/cp/usermanagement" => "navigation_einstellungen",
	];

	CONST mediaPriority = ["keystone","bilder","audio","videos"];

	CONST convertLocation = "/usr/local/bin/convert"; 
	CONST sassPath = "/usr/local/bin/node_modules/node-sass/bin/node-sass";
	CONST imageCacheTime = 604800; 

	public static function escape() {
		foreach($_POST as $key=>$value) {
			$_POST[$key] = str_replace("'","\'",urldecode($value));
		}

		foreach($_GET as $key=>$value) {
			$_GET[$key] = str_replace("'","\'",urldecode($value));
		}
	}

	/**
	 * @param $filename
	 * @param $message
	 */
	public static function log($filename, $message) {
		$prefix   = "assets/log/";
		$filename = $prefix . $filename;
		$data     = file_get_contents($filename);
		if ($data != "") {
			$data .= "\n";
		}

		$data .= "[" . date("d.m.Y H:i:s") . "] " . $message;
		file_put_contents($filename, $data);
	}

	/**
	 * @param $arraySitemap
	 * @param $path
	 */
	public static function sitemap($arraySitemap, $path = "../") {
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
		foreach ($arraySitemap as $v) {
			$xml .= "<url>
      <loc>https://" . $_SERVER["HTTP_HOST"] . $v . "</loc>
      <lastmod>" . date("Y-m-d") . "</lastmod>
      <changefreq>always</changefreq>
   </url>";
		}
		$xml .= "
	</urlset>";
		file_put_contents($path . "sitemap.xml", $xml);
	}
}
?>
