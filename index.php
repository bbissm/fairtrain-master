<?php
error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);
session_start();

require_once("library/core/autoload.php");
require_once("config.php");

config::escape();
if(config::login && $_SESSION["login"]==false) {
	header("location: /cp");
	exit(0);
}

$GLOBALS['hook'] = [];
$_SESSION["db"] = new mysqli(config::dbHost,config::dbUsername,config::dbPassword,config::dbName); 
$_SESSION["db"]->set_charset("utf8"); 
$_SESSION["path"] = explode("/",$_GET["cmspath"]);
unset($_GET["cmspath"]); 

if($_SESSION["path"][0]!="async" && config::template!="") {
	include("templates/".config::template."/index.php");
} else {
	if($_SESSION["path"][1]!="") {
		$module = new $_SESSION["path"][1]();
		$module->setup(json_decode(base64_decode($_GET["params"]),true));
		$method = $_SESSION["path"][2];
		$module->$method();
	}
}
?>
