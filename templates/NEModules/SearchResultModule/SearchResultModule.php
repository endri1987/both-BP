<?
require_once(INCLUDE_PATH.'intServices/Internal.SearchResult.Module.class.php');
function SearchResultModule_onRender() {
	global $session,$event;
	$LP = new InternalSearchResultModule("union");


	global $global_cache_dynamic,$cacheDyn;
	$hrefSearchResult = "";
	IF ($global_cache_dynamic == "Y") {
		$hrefSearchResult = $cacheDyn->get_CiTitleToUrl($session->Vars["contentId"], SUBSTR($session->Vars["lang"], -1));
	}	   
	WebApp::addVar("hrefSearchResultNext",$hrefSearchResult);
	WebApp::addVar("tid","");
}