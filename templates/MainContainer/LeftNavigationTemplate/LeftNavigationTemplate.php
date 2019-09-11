<?

function LeftNavigationTemplate_onRender() {
	 global $session;

	WebApp::addVar("node_family", "0");
	IF ($session->Vars["thisMode"]=="_new") {
		$stateCondition=" 
			AND c.content{{lang}}{{thisMode}} IS NOT NULL
			AND n.description{{lang}}{{thisMode}} != 7";
		
	} else {
	
		$stateCondition=" 
			AND c.state{{lang}} not in (5,7)
			AND c.content{{lang}}{{thisMode}} IS NOT NULL
			AND n.active{{lang}} != '1'
			AND n.description{{lang}}{{thisMode}} IS NOT NULL ";
	}
	
	WebApp::addVar("stateCondition", " $stateCondition ");
	
}

?>