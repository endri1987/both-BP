<?

require_once(INCLUDE_PATH.'intServices/collector.Data.List.class.php');
require_once(INC_PATH."personalization.functionality.class.php");
function Collector_onRender() {
	global $session,$event;


	$termSearch	= "";
	WebApp::addVar("backToListIcon","");
	$statusNem = '0';	//0-list of searchin with term(, 1- abstract, nqse abstracti do trajtohet nga ky nem )
	$targeted_page = "";

	$ILC_x = new collectorDataListClass();
	$ILC_x->InitClass($session->Vars["idstemp"]);
	$ILC_x->ConstructDataList();

	if ($ILC_x->slogan_title!="") {
		WebApp::addVar("dp_slogan_title","yes");
		WebApp::addVar("slogan_title",$ILC_x->slogan_title);
	}

	if ($ILC_x->slogan_description!="") {
		WebApp::addVar("dp_slogan_description","yes");
		WebApp::addVar("slogan_description",$ILC_x->slogan_description);
	}
	
	WebApp::addVar("include_default","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/".$ILC_x->templateFileName."\"/>");
	WebApp::addVar("HeaderNavT","no");
	if (isset($ILC_x->templateHeaderNav) && $ILC_x->templateHeaderNav>=0) {
		WebApp::addVar("HeaderNavT","yes");
		if ($ILC_x->templateHeaderNav>0 && $ILC_x->templateHeaderFileName!="")
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/".$ILC_x->templateHeaderFileName."\"/>");
		else
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/navigation_default.html\"/>");
	}
	
	WebApp::addVar("FooterNavT","no");
	if (isset($ILC_x->templateFooterNav) && $ILC_x->templateFooterNav>=0) {
		WebApp::addVar("FooterNavT","yes");
		if ($ILC_x->templateFooterNav>0 && $ILC_x->templateFooterFileName!="")
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/".$ILC_x->templateFooterFileName."\"/>");
		else
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/navigation_default.html\"/>");
	}

// echo $ILC_x->templateFileName."ILC_x<textarea>";
// print_r($ILC_x);
// echo "</textarea>";

}

?>