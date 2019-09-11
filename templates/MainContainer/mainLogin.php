<? 
function mainLogin_onRender() {

	global $session, $sessUserObj, $event,$VS;	

	$zone_id = "14,15,16,31,32";
	$zone_mode_right = "multi";
	
	$zone_id_left = "7,8,9";
	$zone_mode_left = "multi";
	
	include_once ASP_FRONT_PATH."php/find.region.SI.php";
	
	$obj_siInRegion = new siInRegionS($zone_id,$zone_mode_right,"no");
	$cnt_related = $obj_siInRegion->cnt_related;
	if ($cnt_related>0) {
		WebApp::addGlobalVar("exist_right","yes");
	} else {
		WebApp::addGlobalVar("exist_right","no");
	}

	
	$obj_siInRegion = new siInRegionS($zone_id_left,$zone_mode_left,"no");
	$cnt_related = $obj_siInRegion->cnt_related;
	if ($cnt_related>0) {
		WebApp::addGlobalVar("exist_left","yes");
	} else {
		WebApp::addGlobalVar("exist_left","no");
	}
	
   
	require_once(INCLUDE_AJAX_PATH . "/CiManagerFe.class.php");
    $getPublishedHtmlForMainCi = "yes";
	$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
	$workingCi->getMainDocumentCiToGrid();	
  
}