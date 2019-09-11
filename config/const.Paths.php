<?
//shikohet nese jemi nen https --------------------------------------------------------------------------------------------------------------
  $httpss = "";
  IF (ISSET($_SERVER["HTTPS"]) AND ($_SERVER["HTTPS"] == "on"))
     {
      $httpss = "s";
     }
//shikohet nese jemi nen https --------------------------------------------------------------------------------------------------------------


//E:\projects_114\ASP4v\ham\config\const.Paths.php

if (stristr($_SERVER["HTTP_HOST"], '.arkit'))
{
	define("APP_URL", 				"http".$httpss."://".$_SERVER["HTTP_HOST"]."/");
	define("APP_FRONT_URL",			"/");
	define("APP_URL_DOMAIN_NAME", 	"/");
	define("EASY_URL", "http".$httpss."://".$_SERVER["HTTP_HOST"]."/asp4_nobug/");
	
} else {
	define("APP_URL", 				"http".$httpss."://".$_SERVER["HTTP_HOST"]."/value_consulting/");
	define("APP_FRONT_URL",			"value_consulting/");
	define("APP_URL_DOMAIN_NAME", 	"value_consulting/");
}


define("BO_PATH",					"adm/");
define("BO_PATH_PHP",				"application_window.php");


define("AJAX_BO_PATH", 	APP_URL."adm/ajxrsp.php");


		//define("APP_STATE",                    "development");

//adresa e backoffice
define("APP_BACK_URL",				"admin/");
define("APP_ADMIN_PATH",			"admin/");

//case ASP
define("EASY_PATH",		"/var/www/html/asp4_nobug/");

define("APP_PHP_PATH",			APP_URL."include_php/");
define("INCLUDE_PATH",			EASY_PATH."include_php/");


//constants of the paths in the application
define("WEBAPP_PATH",			EASY_PATH."web_app/");

define("STYLE_DEFAULT",			APP_URL."include_css/default.css");
define("STYLE_DEFAULT_PATH",	APP_PATH."include_css/default.css");


/////rss
define("SYSTEM_NAME_DEFAULT",			"value_consulting");			// jep emrin e website-it
define("RSS_DESCRIPTION",        		"value_consulting");			// feed description:

define("SYSTEM_NAME_DEFAULT_EN",		"value_consulting");			// jep emrin e website-it
define("RSS_DESCRIPTION_EN",        	"by value_consulting");			// feed description:

define("RSS_LOGO_EXIST",          		"NO");						// in NO will not be displayed
define("RSS_LOGO_PATH",          		APP_URL."show_image.php?file_id=2");	// logo path
define("SPECIFIK_TOOLS",				APP_PATH."modules_back/tools/");

define("RC_FOLDER_NAME", 				"rc/");
define("RC_FOLDER_NAME_VERSION", 		"rc_version/");

define("RC_FOLDER_PATH", 				APP_PATH."rc/");
define("RC_FOLDER_PATH_VERSION", 		APP_PATH."rc_version/");

define("APP_URL_DOMAIN", "http".$httpss."://".$_SERVER["HTTP_HOST"]."/");
define("RC_FOLDER_URL", 				APP_URL_DOMAIN.APP_URL_DOMAIN_NAME."rc/");

define("MEMBERS_ORG_MAIN",	"6");

//Google API keys---------------------------------------------------
define("GMAP_JS_API_KEY",			"AIzaSyC4pmI93_AMcfBH4VSLN1LCX1phGC-ONek");
//---------------------------------------------------------------

?>