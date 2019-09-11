<?
function newsletterModule_onRender() {
	global $session,$event;
 
	WebApp::addVar("idstemp","");
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		WebApp::addVar("idstempNl",$session->Vars["idstemp"]);
	} 
 }
 ?>
