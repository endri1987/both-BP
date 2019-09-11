<?php

define("BO_ENVIRONMENT", "AJAX_RESP_BO");
global $params_get;
$params_get = $_GET;
global $params_post;
$params_post = $_POST;

global $resources_url;
$resources_url = $_FILES;
global $paramsToControlByModule;
$paramsToControlByModule = $_REQUEST;
global $paramsToControlByModuleUntraited;
$paramsToControlByModuleUntraited = $_REQUEST;


INCLUDE_ONCE "../application_bo.php";

header("Content-Type: text/html; charset=" . APP_ENCODING);
INCLUDE_ONCE ASP_FRONT_PATH."php/BO/ajax_resp.php";

?>