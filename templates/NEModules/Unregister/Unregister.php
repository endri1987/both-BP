<?
function Unregister_onRender() {
	global $session,$event;
 
	WebApp::addVar("idstemp","");
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		WebApp::addVar("idstempUNl",$session->Vars["idstemp"]);
	} 
 }
 ?>
