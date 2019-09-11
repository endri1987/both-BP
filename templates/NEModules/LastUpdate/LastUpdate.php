<?
function LastUpdate_onRender() {
	global $session,$event;
		
	$last_update_html = "";
	if ($session->Vars["contentType"] == "db") {
	
		$rs = WebApp::openRS("get_Last_update");

		$dataora_insupd = $rs->Field("dataora_insupd");

		$date_approve = $rs->Field("date_approve");
		$last_update_html = "$date_approve";
	}

	WebApp::addVar("last_update_html",$last_update_html);	

 }
 ?>