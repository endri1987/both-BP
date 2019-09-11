<?php
$EccERefID = "";
IF (isset($_GET["EccERefID"]) AND ($_GET["EccERefID"] != "")) {
	$EccERefID = $_GET["EccERefID"];
}
$EccEmode = "";
IF (isset($_GET["mode"]) AND ($_GET["mode"] != "")) {
	$EccEmode = $_GET["mode"];
}
$Eccsid = "";
IF (isset($_GET["sid"]) AND ($_GET["sid"] != "")) {
	$Eccsid = $_GET["sid"];
}
$mode = "";
IF (isset($_GET["mode"]) AND ($_GET["mode"] != "")) {
	$mode = $_GET["mode"];
}


define("BO_ENVIRONMENT", "APPLICATION_BO");
INCLUDE dirname(__FILE__)."/../application_bo.php";
INCLUDE_ONCE ASP_FRONT_PATH."php/BO/index_bo_app_inside.php";
WebApp::constructHtmlPage($tpl_file,$head_file,$messg_file);



?>