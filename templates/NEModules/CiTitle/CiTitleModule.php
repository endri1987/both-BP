<?
function CiTitleModule_onRender() 
{
	global $session,$event,$sessUserObj;
	extract($event->args);

	WebApp::addVar("Ci_title","");

	require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");
	$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
	$tmpData = $workingCi->getGeneralProperties();

	if (isset($tmpData["ew_title"]) && $tmpData["ew_title"]!="") {
		WebApp::addVar("Ci_title",$tmpData["ew_title"]);
	}
	
	if (isset($sessUserObj->paramsDynamicCiId) && $sessUserObj->paramsDynamicCiId!="" && $sessUserObj->paramsDynamicCiId>0) {
	
	}
	
/*	echo "<textarea>";
	print_r($sessUserObj);
	echo "</textarea>";	*/
	
	
}
?>