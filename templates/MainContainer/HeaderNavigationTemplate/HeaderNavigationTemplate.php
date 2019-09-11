<?
function HeaderNavigationTemplate_onRender() {
	 global $session;

	IF ($session->Vars["thisMode"]=="_new")
			WebApp::addVar("kushtdeaktivet", "");
	else
			WebApp::addVar("kushtdeaktivet", " n.active{{lang}} != '1' AND ");
	$kusht_deleted=" n.state{{lang}} != 7 ";
	WebApp::addVar("kusht_deleted", "$kusht_deleted AND");
}
?>
