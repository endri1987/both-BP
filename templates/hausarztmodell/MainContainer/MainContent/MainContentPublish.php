<?
require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");

function MainContentPublish_onRender() {
	global $session,$event,$atributeMainCi;


    $workingCi = new CiManagerFe($cis, $session->Vars["lang"]);
    $workingCi->parseDocumentToDisplayPreviewMode();
    $prop = array();
    if (isset($workingCi->properties_structured["DC"]) && count($workingCi->properties_structured["DC"]) > 0)
        $prop = array_merge($prop, $workingCi->properties_structured["DC"]);

    if (isset($workingCi->properties_structured["EX"]) && count($workingCi->properties_structured["EX"]) > 0)
        $prop = array_merge($prop, $workingCi->properties_structured["EX"]);

    $gridDProp["data"][0] = $prop;
    $gridDProp["AllRecs"] = count($gridDProp["data"]);


    WebApp::addVar("CiPropGrid" . $cis, $gridDProp);

    if (isset($workingCi->properties_structured["TiContent"]) && $workingCi->properties_structured["TiContent"] != "") {
        $gridD["data"][0]['templateHtml'] = $workingCi->properties_structured["TiContent"];
        $gridD["AllRecs"] = count($gridD["data"]);
        WebApp::addVar("TemplateGrid" . $cis, $gridD);
    }




	
	if (isset($session->Vars["contentId"]) && $session->Vars["contentId"] > 0) {
		$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
		$workingCi->parseDocumentToDisplay();	

		while(list($key,$value) = each ($workingCi->properties_parsed)) {
			WebApp::addVar($key,$value);
		}		
		$contentToDisplay = $workingCi->contentToDisplay;	
		$atributeMainCi = $workingCi->properties_parsed;

		WebApp::addGlobalVar("contentToDisplayHtml",$contentToDisplay);	
	} else {
		WebApp::addGlobalVar("contentToDisplayHtml","");	
	}

	
}

?>