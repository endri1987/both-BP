<?php

define("BO_ENVIRONMENT", "APPLICATION");

INCLUDE dirname(__FILE__)."/../application_bo.php";


INCLUDE_ONCE ASP_FRONT_PATH."php/BO/index_bo_app.php";

$VS->appRelatedInitialization($EccEmode);

if (isset($session->Vars["EccEmode"]) &&  $session->Vars["EccEmode"] =="play") {
	$tpl_file  = TPL_PATH."userLecturePreview.html";
} elseif (isset($session->Vars["EccEmode"]) && $session->Vars["EccEmode"]=="DropZone") {
	$tpl_file  = TPL_PATH."mainLectureDropZone.html";
} elseif (isset($session->Vars["EccEmode"]) && ($session->Vars["EccEmode"]=="Review" || $session->Vars["EccEmode"]=="ReviewEdit")) {
	 $tpl_file  = TPL_PATH."mainLecturePreview.html";
} else { 

	IF (isset($_GET["mode"]) AND ($_GET["mode"] != ""))
	   {$mode = SUBSTR($_GET["mode"], 0, 10);}
	ELSE
	   {$mode = "";}

	if (isset ($mode) && ($mode=="simple" || $mode=="play")) {
			  $tpl_file  = TPL_PATH."mainLecturePreview.html";
			  $session->Vars["mode"] = $mode;
	} elseif (isset ($mode) && ($mode=="act" || $mode=="play" || $mode=="alone" || $mode=="simple" || $mode=="print" || $mode=="email" || $mode=="cards" || $mode=="wb")) {	
		//wb -ketu parsohet nje nem i caktuar, direkt webboxi i tij parametri=idnemin
			if ($mode=="simple") {
				$head_file = "";
				$session->Vars["callBox"] = "y";
			}
			$tpl_file  = NEMODULES_PATH."popUp/popUp.html";
			$session->Vars["mode"] = $mode;
			if ($mode=="email") {
				WebApp::getGlobalVarFromTransition ($mi_var="");
			}
			if ($mode=="email" && isset($session->Vars["sendthis"]) && $session->Vars["sendthis"]==1) 
			   {
				include NEMODULES_PATH."popUp/index.php";
			   }
		  }		   
}


WebApp::constructHtmlPage($tpl_file,$head_file,$messg_file);



?>