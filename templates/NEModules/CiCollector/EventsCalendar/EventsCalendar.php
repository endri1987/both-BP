<?
require_once(INCLUDE_PATH.'intServices/collector.Data.List.class.php');
function EventsCalendar_onRender() {
	global $session,$event;

	//$tm_start = array_sum(explode(' ', microtime()));
	//$objectPropWorkgroup = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));

	
	$termSearch	= "";
	WebApp::addVar("backToListIcon","");
	$statusNem = '0';	//0-list of searchin with term(, 1- abstract, nqse abstracti do trajtohet nga ky nem )
	$targeted_page = "";
	

	WebApp::addVar("fln",str_ireplace("Lng","",$session->Vars["lang"]));


	if (!isset($session->Vars["rIDset"]))	$session->Vars["rIDset"] = "";
	if (!isset($session->Vars["uIDset"]))	$session->Vars["uIDset"] = "";
	
	$ILC_x = new collectorDataListClass();
		$ILC_x->InitClass();
		$ILC_x->initCalendar();
	$ILC_x->ConstructDataList();
	


	WebApp::addVar("include_default","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/".$ILC_x->templateFileName."\"/>");
	WebApp::addVar("EventsCalendarMainTemplate","".$ILC_x->templateFileName."");
	if ($ILC_x->slogan_title!="") {
		WebApp::addVar("dp_slogan_title","yes");
		WebApp::addVar("slogan_title",$ILC_x->slogan_title);
	}

	if ($ILC_x->slogan_description!="") {
		WebApp::addVar("dp_slogan_description","yes");
		WebApp::addVar("slogan_description",$ILC_x->slogan_description);
	}
	$objId="";
	$type_doc="";

	if (isset($ILC_x->objNem) && $ILC_x->objNem!= "")
		$objId = $ILC_x->objNem;

	if (isset($ILC_x->type_doc) && $ILC_x->type_doc!= "")
		$type_doc = $ILC_x->type_doc;

	WebApp::addVar("objIdType",$objId.$type_doc);



	WebApp::addVar("include_calendar_flag","no");
	if (isset($ILC_x->templateCalendarType) && $ILC_x->templateCalendarType>=0) {
		WebApp::addVar("include_calendar_flag","yes");
		if ($ILC_x->templateCalendarType>0 && $ILC_x->templateCalendarFileName!="")
			WebApp::addVar("include_Calendar","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/".$ILC_x->templateCalendarFileName."\"/>");
		else
			WebApp::addVar("include_Calendar","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/calendar_default.html\"/>");
	}

	

	WebApp::addVar("HeaderNavT","no");
	if (isset($ILC_x->templateHeaderNav) && $ILC_x->templateHeaderNav>=0) {
		WebApp::addVar("HeaderNavT","yes");
		if ($ILC_x->templateHeaderNav>0 && $ILC_x->templateHeaderFileName!="")
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/".$ILC_x->templateHeaderFileName."\"/>");
		else
			WebApp::addVar("include_HeaderNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/navigation_default.html\"/>");
	}
	

	
	WebApp::addVar("FooterNavT","no");
	if (isset($ILC_x->templateFooterNav) && $ILC_x->templateFooterNav>=0) {
		WebApp::addVar("FooterNavT","yes");
		if ($ILC_x->templateFooterNav>0 && $ILC_x->templateFooterFileName!="")
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/".$ILC_x->templateFooterFileName."\"/>");
		else
		WebApp::addVar("include_FooterNav","<Include SRC=\"{{NEMODULES_PATH}}CiCollector/EventsCalendar/navigation_default.html\"/>");
	}
	
	WebApp::addVar("objNemCal",$ILC_x->objNem);
	// echo "<textarea>";
	// print_r($ILC_x);
	// echo "</textarea>";
	
}

?>