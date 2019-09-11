<?
function FooterNavigationTemplate_onRender() {
	 global $session;

	IF ($session->Vars["thisMode"]=="_new")	
			WebApp::addVar("kushtdeaktivet", "");
	else 
			WebApp::addVar("kushtdeaktivet", " n1.active{{lang}} != '1' AND ");

	$kusht_deleted=" n1.state{{lang}} != 7 ";
	WebApp::addVar("kusht_deleted", "$kusht_deleted AND");

}
?>