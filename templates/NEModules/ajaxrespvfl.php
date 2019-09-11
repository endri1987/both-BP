<?php

/*echo "<textarea>";
print_r($_GET);
echo "</textarea>";*/

define("APPLICATION_STATE", "FE");	//BO;FE
header("Content-Type: text/html; charset=UTF-8");
include_once "../../application.php";


// KY KONTROLL BEHET PER POPUPIP PASI I KA ARDHUR UNI HEREN E PARE, DUHET TE GJENDET TIPI DHE USERI
$last_state_Sql = "SELECT tip, ses_userid FROM transition
					WHERE ID_S = '".$_GET["uni"]."'
					  AND tool_id in (-2)
					  AND process_id in (-2)";

$rs_last_state = WebApp::execQuery($last_state_Sql);
if(!$rs_last_state->EOF()) {
	$session->Vars["tip"]		= $rs_last_state->Field("tip");
	$session->Vars["ses_userid"]	= $rs_last_state->Field("ses_userid");
	$session->Vars["uni"]		= $uni;
} else {

	$session->Vars["tip"]		= 2;
	$session->Vars["ses_userid"]	= 2;
}

require_once(INCLUDE_PATH."ValidateState.class.php");
$VS = new ValidateState();
if (isset($_REQUEST["cntid"])) WebApp::determine_state_general($_REQUEST["cntid"]);
require_once(INC_PATH.'personalization.functionality.class.php');

switch ($_REQUEST["ap_process"])
{

	
	case "getCalendarEvents":
			$code=getCalendarEventsInSpecifiedDate();
			print_r($code);
		break;
	case "getCalendarMEvents":
			$code=getCalendarEventsSpecifiedMonth();
			print_r($code);
		break;

}


function getCalendarEventsSpecifiedMonth() {

	global $session;
	
		require_once(INCLUDE_PATH.'intServices/collector.Data.List.class.php');
	
	
		if (isset($_REQUEST["cLln"]) && $_REQUEST["cLln"]!="") {
			$session->Vars["rIDset"] = "Lng".$_REQUEST["cLln"];
		} 
		
		if (isset($_REQUEST["fu"]) && $_REQUEST["fu"]!="") {
			$session->Vars["ses_userid"] = $_REQUEST["fu"];
		} 	
		
		if (isset($_REQUEST["fTP"]) && $_REQUEST["fTP"]!="") {
			$session->Vars["tip"] = $_REQUEST["fTP"];
		} 
		
		require_once(INCLUDE_PATH.'intServices/li.Data.List.class.php');
		$ILC_x = new collectorDataListClass();
	
		$ILC_x->InitClass($_REQUEST["idstemp"]);
		if (isset($_REQUEST["fCalByMonth"]) && $_REQUEST["fCalByMonth"]!="") {
			$tmpM = explode("_",$_REQUEST["fCalByMonth"]);
			$ILC_x->initCalendar($tmpM[0]."-".$tmpM[1]."-01",'specifiedMonth');
			
			
			
			//WebApp::addVar("minCaldate",		$tmpM[0]."-".($tmpM[1]*1)."-1");
		//	WebApp::addVar("maxCaldate",	$tmpM[0]."-".($tmpM[1]*1)."-31");
			
			
			
			
		} else {
			$ILC_x->initCalendar();
		}
	
			$ILC_x->ConstructDataList();
	
			$currenCaldate = date("d").".".($tmpM[1]*1).".".$tmpM[0];
			WebApp::addVar("currenCaldate",	$currenCaldate);
	
	
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
	
		
	
		
		WebApp::addVar("objNemCal",$ILC_x->objNem);
		
		
		//$f_name = NEMODULES_PATH."CiCollector/EventsCalendar/".$ILC_x->templateFileName."";
	//echo	$ILC_x->templateFileName."------<br>";
		$tempFile  = preg_replace("/\.html/","",$ILC_x->templateFileName);
	//echo	"<br>".$tempFile;	
		$tempFile  = $tempFile."MonthView.html";
	//echo	"<br>".$tempFile;	
		
		$f_name = NEMODULES_PATH."CiCollector/EventsCalendar/".$tempFile;
		$tplY 			= new WebBox("buildNavX");
		$message_file_sh 	= "Shqip";
		$message_file 		= APP_PATH."templates/".$message_file_sh.".mesg";
		$tplY->parse_msg_file($message_file);
		extract($GLOBALS["tplVars"]->Vars[0]);				
			
	
		WebApp::collectHtmlPage();
		WebApp::constructHtmlPage($f_name,'undefined',$message_file);
		$content=WebApp::getHtmlPage();	
		
		
		$regex_el2="/<form name=\"WebAppForm\".*<\/html>/is";
		if (preg_match($regex_el2, $content, $el_start_properti_arr2)) {
			$contentForm=rawurldecode(preg_replace ("#".rawurlencode($el_start_properti_arr2[0])."#i","",rawurlencode($content)));
		}	
		
		
	return $contentForm;

}

function getCalendarEventsInSpecifiedDate () {


	require_once(INCLUDE_PATH.'intServices/collector.Data.List.class.php');

	$ILC_x = new collectorDataListClass();
	$ILC_x->InitClass($_REQUEST["idstemp"]);

	$ILC_x->distinctIDS = $_REQUEST["cis"];

	$all_idsT = explode(",",$_REQUEST["cis"]);
	$ILC_x->CountItems = count($all_idsT);
	$ILC_x->recPages = $ILC_x->CountItems;


	if ($ILC_x->CountItems==-1) {
		WebApp::addVar("AllRecs","error");
		$this->error_code = 5;

	} elseif ($ILC_x->CountItems==0) {

		WebApp::addVar("AllRecs","empty");
		$ILC_x->error_code = 2;

	} else {

		$ILC_x->error_code = 1;
		WebApp::addVar("AllRecs","full");

		$ILC_x->CreateLimitToSql();
		$ILC_x->listDataArticles();
	}


	if (isset($ILC_x->error_code_description[$ILC_x->error_code]))
		WebApp::addVar("error_code_to_html", $ILC_x->error_code_description[$ILC_x->error_code]);
	else
		WebApp::addVar("error_code_to_html", "SomeErrorOccurred");


	WebApp::addVar("gridDataSrc", $ILC_x->gridDataSrc);

	WebApp::addVar("rp",$ILC_x->recPages);
	WebApp::addVar("rpp",$ILC_x->NrPage);


	WebApp::addVar("CountItems",$ILC_x->CountItems);
	WebApp::addVar("TotPage",$ILC_x->TotPage);
	WebApp::addVar("NrPage",$ILC_x->NrPage);
	WebApp::addVar("FromRecs",$ILC_x->FromRecs);
	WebApp::addVar("ToRecs",$ILC_x->ToRecs);
	WebApp::addVar("previewsPage",$ILC_x->previewsPage);
	WebApp::addVar("recPage",$ILC_x->recPages);
	WebApp::addVar("nextPage",$ILC_x->nextPage);


	WebApp::addVar("HeaderNavT","no");
	if (isset($ILC_x->templateHeaderNav) && $ILC_x->templateHeaderNav>=0) {
		WebApp::addVar("HeaderNavT","yes");
		if ($ILC_x->templateHeaderNav>0 && $ILC_x->templateHeaderFileName!="")
			WebApp::addVar("include_HeaderNav","<Include SRC=\"".NEMODULES_PATH."CiCollector/EventsCalendar/".$ILC_x->templateHeaderFileName."\"/>");
		else
			WebApp::addVar("include_HeaderNav","<Include SRC=\"".NEMODULES_PATH."CiCollector/EventsCalendar/navigation_default.html\"/>");
	}


	WebApp::addVar("FooterNavT","no");
	if (isset($ILC_x->templateFooterNav) && $ILC_x->templateFooterNav>=0) {
		WebApp::addVar("FooterNavT","yes");
		if ($ILC_x->templateFooterNav>0 && $ILC_x->templateFooterFileName!="")
		WebApp::addVar("include_FooterNav","<Include SRC=\"".NEMODULES_PATH."CiCollector/EventsCalendar/".$ILC_x->templateFooterFileName."\"/>");
		else
		WebApp::addVar("include_FooterNav","<Include SRC=\"".NEMODULES_PATH."CiCollector/EventsCalendar/navigation_default.html\"/>");
	}

	$ajax_Path = str_ireplace(".html","AjaxDetails.html",$ILC_x->templateFileName);
	$f_name = NEMODULES_PATH."CiCollector/EventsCalendar/".$ajax_Path;


	$tplY 			= new WebBox("buildNavX");
	$message_file_sh 	= "Shqip";
	$message_file 		= APP_PATH."templates/".$message_file_sh.".mesg";
	$tplY->parse_msg_file($message_file);
	extract($GLOBALS["tplVars"]->Vars[0]);


	WebApp::collectHtmlPage();
	WebApp::constructHtmlPage($f_name,'undefined',$message_file);
	$content=WebApp::getHtmlPage();


	$regex_el2="/<form name=\"WebAppForm\".*<\/html>/is";
	if (preg_match($regex_el2, $content, $el_start_properti_arr2)) {
		$contentForm=rawurldecode(preg_replace ("#".rawurlencode($el_start_properti_arr2[0])."#i","",rawurlencode($content)));
	}
	return $contentForm;
}


?>