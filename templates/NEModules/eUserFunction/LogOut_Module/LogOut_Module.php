<?
function LogOut_Module_onRender() {
	global $session;
 
	WebApp::addVar("idstempLogout","");
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		//$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);

		WebApp::addVar("idstempLogout",$session->Vars["idstemp"]);
	} 		
}
?>