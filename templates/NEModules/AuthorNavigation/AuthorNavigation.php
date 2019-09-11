<?
function AuthorNavigation_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

INCLUDE(APP_PATH."templates/NEModules/AuthorNavigation/AuthorNavigation_grid.php");

function AuthorNavigation_onRender() 
{
//$starts = WebApp::get_formatted_microtime();
	global $session,$event,$global_cache_dynamic,$cacheDyn;
	extract($event->args);
	
    INCLUDE(APP_PATH."templates/NEModules/AuthorNavigation/AuthorNavigationFunc.php");


//$totals = WebApp::get_formatted_microtime() - $starts;
//echo $debugTime = "<br>inside AuthorNavigation END->".round($totals, 2).":totals";

   // function get_CiTitleToUrl($content_id, $lng_id=1, $title="", $filename="", $koord_level_node_param="",$with_https="", $returnWhat="full_path",$returnModeInfo="onlyPath")	
   // function get_CiMainTitleOfNodeToUrl($koord, $lng_id=1,$with_https="")

	$linkToHomeOfZoneCrd = $session->Vars["level_0"].",0,0,0,0";
	$linkToHomeOfZoneHref = "javascript:GoTo('thisPage?event=none.ch_state(k=".$linkToHomeOfZoneCrd.")')";

	IF ($global_cache_dynamic == "Y") {
		$linkToHomeOfZoneHref = $cacheDyn->get_CiMainTitleOfNodeToUrl($linkToHomeOfZoneCrd, $session->Vars["ln"]);
	}

	WebApp::addVar("linkToHomeOfZoneHref",$linkToHomeOfZoneHref);

}