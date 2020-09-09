<?php
error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);

session_start();

require_once("library/core/autoload.php");
require_once("../config.php");

config::escape();

if($_GET["logout"]!="") {
	$_SESSION=[];
	session_destroy();
}

$GLOBALS['hook'] = [];
$_SESSION["db"] = new mysqli(config::dbHost,config::dbUsername,config::dbPassword,config::dbName); 
$_SESSION["db"]->set_charset("utf8"); 
$_SESSION["path"] = explode("/",$_GET["cmspath"]);
unset($_GET["cmspath"]); 

auth::login();

$_SESSION["lang"] = ["key"=>1,"short"=>"de"];

if(!$_SESSION["login"]) { 
	cp::setup();
	include("template/login.php");
} else {
	if($_SESSION["path"][0]!="async") {
		cp::setup();
		include("template/index.php");
	} else {
		if($_SESSION["path"][1]!="") { 
			if($_SESSION["path"][1]::$auth==false || ($_SESSION["login"] && in_array($_SESSION["user"]["type"], $_SESSION["path"][1]::$authLevel))) {
				$module = new $_SESSION["path"][1]();
				$module->setup(json_decode(base64_decode($_GET["params"]),true));
				$module->controller();
				$method = $_SESSION["path"][2];
				$module->$method();
			}
		}
	} 
}
?>
