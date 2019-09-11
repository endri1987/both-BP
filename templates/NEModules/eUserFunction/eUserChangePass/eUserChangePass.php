<?
function eUserChangePass_onRender() {
	global $session,$event;

	$inc_modul_template = "changePasswordTemplate.html";
    
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

  		$objectNemProperties = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
        
 
		$template_id_sel=$objectNemProperties["templateID"];
		
		if ($template_id_sel!="" && $template_id_sel>0) {
			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
				$inc_modul_template = $rs->Field("template_box");
		}
	}
    


	WebApp::addVar("with_token",'no');
    WebApp::addVar("token",'');
  	WebApp::addVar("idStempCHP",$session->Vars["idstemp"]);
    
	if (isset($_REQUEST["apprcss"]) && $_REQUEST["apprcss"]=="changePassModule") {
		$inc_modul_template = "Ajax_".$inc_modul_template;
	} 
 
    if (isset($_REQUEST["token"]) && $_REQUEST["token"]!="" && (strpos($session->Vars["idstemp"],'CI') !== false)) {
        
      WebApp::addVar("with_token",'yes');
      WebApp::addVar("token",$_REQUEST["token"]);      
	  $inc_modul_template = "Ajax_".$inc_modul_template;      
	} 
	       
    
	WebApp::addVar("include_eUserChangePass","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/eUserChangePass/".$inc_modul_template."\"/>");

}
?>