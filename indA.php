<?php
define("BO_ENVIRONMENT", "APPLICATION_BO");
INCLUDE dirname(__FILE__)."/application_bo.php";
INCLUDE_ONCE ASP_FRONT_PATH."php/BO/indA_bo_app.php";
WebApp::constructHtmlPage($tpl_file,$head_file,$messg_file);
?>