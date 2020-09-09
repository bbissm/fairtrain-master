<?php

// error_reporting(E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR);


$data = [];

$langs = ["de", "fr", "it", "en"];

// $_SESSION["path"] = [];

$lang = "";
foreach ($langs as $val) {
	if ($_SESSION["path"][0] == $val) {
		$lang = $val;
		break;
	}
}

if ($lang != "") {
	$q   = $_SESSION["db"]->query("SELECT cms_lang_id FROM cms_lang WHERE short='" . $_SESSION["path"][0] . "'");
	$res = $q->fetch_assoc();

	$_SESSION["lang"] = ["key" => $res["cms_lang_id"], "short" => $_SESSION["path"][0]];
} else {
	if (!isset($_SESSION["lang"]["key"])) {
		/*$language = lang::detect($langs, "de");
		$location = lang::getLocation();
		$location = strtolower($location["country"]);*/

		$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		$language = in_array($language, $langs) ? $language : 'de';
		$lang     = $language;

		$_SESSION["path"][0] = $language;

		$q   = $_SESSION["db"]->query("SELECT cms_lang_id FROM cms_lang WHERE short='" . $_SESSION["path"][0] . "'");
		$res = $q->fetch_assoc();

		$_SESSION["lang"] = ["key" => $res["cms_lang_id"], "short" => $_SESSION["path"][0]];
	}
}

if ($lang != "" || $_SESSION["path"][0] == "") {
	$nav = sitemap::get(["stage" => 0, "lang" => $_SESSION["lang"]["key"]]);
	foreach ($nav as $key => $val) {
		if ($val["permalink"] == $_SESSION["path"][1] || $_SESSION["path"][1] == "" || $_SESSION["path"][1] == "article") {
			$data = $val;
			// file_put_contents("data.log", print_r($data));
			break;
		}
	}
}

if ($_SESSION["path"][2] == "") {
	$in  = 0;
	$nav = sitemap::get(["stage" => 0, "lang" => $_SESSION["lang"]["key"]]);
	foreach ($nav as $key => $val) {
		if ($val["permalink"] == $_SESSION["path"][1]) {
			$nav = sitemap::get(["stage" => $val["cms_navigation_id"], "lang" => $_SESSION["lang"]["key"]]);
			foreach ($nav as $val) {
				if ($val["is_active"] && !$val["is_visible"] && !$val["is_footer"]) {
					$_SESSION["path"][2] = $val["permalink"];
					$data                = $val;
					// file_put_contents("data.log", print_r($data));
					$in = 1;
					break;
				}
				if ($in) {
					break;
				}

			}
			break;
		}
	}
}

if (count($data) == 0) {
	header("HTTP/1.0 404 Not Found");
	foreach ($_SESSION["path"] as $key => $value) {
		if ($key > 0) {
			unset($_SESSION["path"][$key]);
		}
	}
	$_SESSION["path"][1] = "404";
}

$data["og_title"]       = str_replace("\"", "", $data["og_title"]);
$data["og_description"] = str_replace("\"", "", $data["og_description"]);
$data["og_description"] = str_replace("\n", " ", $data["og_description"]);

$data["pagetitle"]   = str_replace("\"", "", $data["pagetitle"]);
$data["description"] = str_replace("\"", "", $data["description"]);
$data["description"] = str_replace("\n", " ", $data["description"]);
?>
<!DOCTYPE HTML>
<!--
****
**** Concept, design and implementation by
**** DimasterSoftware GmbH
**** Sellenbueren 59a
**** 8143 Stallikon
**** Switzerland
**** http://www.dimastersoftware.ch
****
-->
<html lang="de">
<head>
	<title><?php echo $data["pagetitle"]; ?></title>

	<meta name="keywords" content="<?php echo $data["keywords"]; ?>" />
    <meta name="description" content="<?php echo $data["description"]; ?>" />
    <meta name="robots" content="index,follow" >
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
	<meta name="theme-color" content="#634E42">
	<meta name="author" content="" />
	<!-- BUG Fix -->
	<link rel="shortcut icon" href="">

	<meta property="og:title" content="<?php echo $data["og_title"]; ?>" />
	<meta property="og:site_name" content="<?php echo $data["og_site_name"]; ?>" />
	<meta property="og:url" content="<?php echo "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; ?>" />
	<meta property="og:description" content="<?php echo $data["og_description"]; ?>" />
	<meta property="og:image" content="<?php echo "https://" . $_SERVER["SERVER_NAME"] . $data["og_image"]; ?>" />

	<meta name="twitter:card" content="summary" />
	<meta name="twitter:site" content="@fairtrain.ch" />
	<meta name="twitter:title" content="<?php echo $data["og_title"]; ?>" />
	<meta name="twitter:description" content="<?php echo $data["og_description"]; ?>" />
	<meta name="twitter:image" content="<?php echo "https://" . $_SERVER["SERVER_NAME"] . $data["og_image"]; ?>" />

	<meta name="DC.title" content="<?php echo $data["pagetitle"]; ?>" />
	<meta name="DC.description" content="<?php echo $data["description"]; ?>" />
	<meta name="DC.publisher" content="Fairtrain" />
	<meta name="DC.rights" content="All rights reserved" />

    <link rel="icon" href="/favicon.png" type="image/png">
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" href="/favicon.png">

	<!-- JS Import-->
	<script src="/templates/web/js/library/jQuery.js"></script>
	<script src="/templates/web/js/library/jQuery.min.js"></script>

	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="/templates/web/slick-main/slick/slick.js"></script>

	<script src="/templates/web/leaflet/leaflet.js"></script>

	<!-- STYLES Import-->
	<link rel="stylesheet" href="/templates/web/leaflet/leaflet.css" />
	<!-- <link rel="stylesheet" type="text/css" href="/templates/web/slick-main/slick/slick.css"/>
	<link rel="stylesheet" type="text/css" href="/templates/web/slick-main/slick/slick-theme.css"/> -->

		<!-- angle arrows font-awesome -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->



   	<!-- Main FILES -->
   	<link href="/templates/web/css/main.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="/templates/web/js/main.js"></script>

	<?php
if (!isset($_SESSION["close_cookie"])) {
	?>
				<div id="cookie-banner">
					<span><?php echo translation::get("cookie"); ?> <a href="/<?php echo $_SESSION['lang']['short']; ?>/privacy" style="text-decoration: underline;" target="_blank">Datenschutzerklärung</a></span>
					<button id="cookie-close"><?php echo translation::get("accept"); ?></button>
				</div>
		<?php
}
?>

	<?php echo helper::cmsHead(); ?>
</head>
<body>
	<?php

include "parts/navigation.php";
$main = new main();
echo $main->render($data["cms_navigation_id"]);
include "parts/footer.php";

?>
<script type="text/javascript">
//<![CDATA[
var _gaq = [];

_gaq.push(
    ['_gat._anonymizeIp'],
    ['b._setAccount', 'UA-24162601-93'],
    ['b._setCustomVar', '1', 'websiteid', 's2ee1b26a36cf34c5'],
    ['b._setDomainName', 'none'],
    ['b._setAllowLinker', true],
    ['b._trackPageview']
);

(function() {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = 'https://www.google-analytics.com/ga.js';

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
})();
//]]>
</script>
</body>
</html>
