<nav class="nav">
	<?php

$active = 0;
$nav    = sitemap::get(["stage" => 0, "lang" => $_SESSION["lang"]["key"]]);
?>
	<div role='navigation' id="nav-box" class="nav-box">
		<div id="top-nav" class="top-nav">
			<?php
echo "<a href=\"/" . $_SESSION["lang"]["short"] . "/home\"><img src=\"/templates/web/img/logo.png\"></a>";
?>
			<ul class="desktop-list">
			<?php
foreach ($nav as $key => $value) {
	if ($value["is_active"] && !$value["is_visible"] && !$value["is_footer"]) {
		echo "<li";
		if ($_SESSION["path"][1] == $value["permalink"] || $_SESSION["path"][1] == "") {
			$_SESSION["path"][1] = $value["permalink"];
			$active              = $value["cms_navigation_id"];
			echo " class=\"active\"";
			$_SESSION["nav"] = $active;
		}
		echo ">";
		echo "<a href=\"/" . $_SESSION["lang"]["short"] . "/" . $value["permalink"] . "\">" . $value["name"] . "</a>";
		echo "</li>";
	}
}
?>
			</ul>
			<div class="topnav-logos">
				<!-- <a href="/"><img src="/templates/web/img/icon-fb.png"/></a> -->
				<a href="https://www.instagram.com/fairtrain_fur_hund_und_mensch/" target="_BLANK"><img src="/templates/web/img/icon-insta.png"/></a>
			</div>
			<div class="topnav-lng">
				<a href="/de" <?php if ($_SESSION["path"][0] == "de") {?>class="active"<?php }?>>DE</a>
				<a href="/en" <?php if ($_SESSION["path"][0] == "en") {?>class="active"<?php }?>>EN</a>
			</div>
			<a href="javascript:void(0)" id="toggle-nav"><img src="/templates/web/img/menu-icon-mobile.png"></a>
		</div>
		<ul class="nav-list hide" id="nav-list">
		<?php
foreach ($nav as $key => $value) {
	if ($value["is_active"] && !$value["is_visible"] && !$value["is_footer"]) {
		echo "<li";
		if ($_SESSION["path"][1] == $value["permalink"] || $_SESSION["path"][1] == "") {
			$_SESSION["path"][1] = $value["permalink"];
			$active              = $value["cms_navigation_id"];
			echo " class=\"active\"";
			$_SESSION["nav"] = $active;
		}
		echo ">";
		echo "<a href=\"/" . $_SESSION["lang"]["short"] . "/" . $value["permalink"] . "\">" . $value["name"] . "</a>";
		echo "</li>";
	}
}
?>
		</ul>
		<div id="bottom-nav" class="bottom-nav hide">
			<div class="link_logo">
				<a href="/"><img src="/templates/web/img/icon-fb.png"/></a>
				<a href="/"><img src="/templates/web/img/icon-insta.png"/></a>
			</div>
			<div class="link_lng">
				<a href="/de" <?php if ($_SESSION["path"][0] == "de") {?>class="active"<?php }?>>DE</a>
				<a href="/en" <?php if ($_SESSION["path"][0] == "en") {?>class="active"<?php }?>>EN</a>
			</div>
		</div>
	</div>
</nav>
