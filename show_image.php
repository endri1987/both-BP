<?php
IF (ISSET($_GET["file_id"]) AND ($_GET["file_id"] != "") && $_GET["file_id"]>0) {
	$app_path = dirname(__FILE__);
	DEFINE("APP_PATH",		$app_path."/");
    
    INCLUDE (APP_PATH."config/const.Paths.php");
    INCLUDE (APP_PATH."config/const.DB.php");
    INCLUDE (APP_PATH."config/const.Config.php");
    
    define("ASP_FRONT_PATH",    EASY_PATH."inc/");
   
   INCLUDE ASP_FRONT_PATH."php/show_sl_resource.php";
}