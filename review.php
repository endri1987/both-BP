<?php

define("BO_ENVIRONMENT", "CONTROLLER_REVIEW");
INCLUDE dirname(__FILE__)."/application.php";


INCLUDE_ONCE ASP_FRONT_PATH."php/BO/index_controller_review.php";
/****************
1. nese useri eshte i loguar eshte user BO aplikimi ka includuar ambjentin BO tek asp dhe nuk vjen fare ketu

2. nese useri nuk eshte loguar akoma
	
	do ti shfaqet logini
	
3. nese useri eshte loguar por nuk eshte user BO, duhet te vazhdoje navigimin si front end user, ose duhet ridiksionuar ne front me login????
		
******/

if ($session->Vars["ses_userid"]==2) {
	

	$dataFound = $VS->findHttpRelatedTargetPages();
	$VS->appRelatedInitialization();
	//if (isset($_GET["crd"]))
	$review_login="";
	if (isset($dataFound["level_0"]) && $dataFound["level_0"]!=""
		&& isset($dataFound["zone_failed_cords_pub"]) && $dataFound["zone_failed_cords_pub"]!="") {
		$redirectZoneInit = $dataFound["level_0"];
		$review_login = $dataFound["zone_failed_cords_pub"];	 
	} 

	if(defined('REVIEW_LOGIN') && REVIEW_LOGIN!="") 
		$review_login = REVIEW_LOGIN;	
	
	if ($review_login!="")
		WebApp::determine_state_general($review_login);
		
	if (isset($_GET["crd"]) && $_GET["crd"]!="") {
		$session->Vars["k_ini"] = $_GET["crd"];
	} elseif (!isset($session->Vars["k_ini"])) {
		$session->Vars["k_ini"] = $redirectZoneInit.",0,0,0,0";
	}
	
	$head_file 	= TPL_PATH."head.html";
	$head_file 	= "";
	$tpl_file  = TPL_PATH."MasterLogin.html";
} else {

		$VS->appRelatedInitialization($eccMode);
		$head_file 	= TPL_PATH."/head.html";
		$tpl_file 	= TPL_PATH."/main.html";
}


WebApp::collectHtmlPage();
WebApp::constructHtmlPage($tpl_file,$head_file,$messg_file);
WebApp::showHtmlPage();		

?>