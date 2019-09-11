<?
function eUserProfile_onRender() {
	global $session;
	

	INCLUDE_ONCE INC_PATH."user.functionality.class.php";		
	$member_name = "{{member_name_mesg}}";
	WebApp::addVar("idstempLogout","");
	WebApp::addVar("idstempChpass","");
	WebApp::addVar("idstempChdata","");
 
    $Properties= array();    
	$defaultProperties= array();
    
	$inc_modul_template = "UserProfileModuleDefault.html";
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$objIdList =  $session->Vars["idstemp"];
		$objectPropList = unserialize(base64_decode(WebApp::findNemProp($objIdList)));	
        
        $ObjUsr = new UserFullFunctionality($session->Vars["ses_userid"],"","","");		
		$ObjUsr->getUserInfo($session->Vars["ses_userid"]);
    
    
        $Properties["accountInformation"]	= "accountInformation";
       	$Properties["professionalData"]     = "professionalData"; 
        $Properties["contactDetails"]	    = "contactDetails"; 
        $Properties["authenticationData"]	= "authenticationData";
        $Properties["publicProfile"]	    = "publicProfile";
        $Properties["changePassword"]	    = "changePassword";
        $Properties["logOut"]	            = "logOut";
    
  		$objectPropList = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
        
		$template_id_sel=$objectPropList["templateID"];
		if ($template_id_sel!="" && $template_id_sel>0) {
			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
				$inc_modul_template = $rs->Field("template_box");
		}    
    
        while (list($key,$value)=each($Properties)) {     
               if(isset($objectPropList[$value]) &&  $objectPropList[$value]!='')
					$defaultProperties[$key] = $objectPropList[$value];  
                
              if(isset($objectPropList[$value."_target"]) &&  $objectPropList[$value."_target"]!='')
                	$defaultTarget[$value."_target"] = $objectPropList[$value."_target"];
                else  $defaultTarget[$value."_target"] = "";
                
                if(isset($objectPropList["link_".$value]) &&  $objectPropList["link_".$value] =='show')
                	$defaultTarget[$value."_show"] = "show";
                else  $defaultTarget[$value."_show"] = "hide";
                
	     }    
        reset($defaultProperties);
		while (list($key,$value)=each($defaultProperties)) {
			WebApp::addVar("_".$key, "$value");
		}         
         while (list($key,$value)=each($defaultTarget)) {
			WebApp::addVar("$key", "$value");
		}  
     }
  	WebApp::addVar("include_UserProfileTemplate","<Include SRC=\"".NEMODULES_PATH."eUserFunction/eUserProfile/".$inc_modul_template."\"/>");
     		
	WebApp::addVar("uniqueid",$session->Vars["uniqueid"]);
	WebApp::addVar("uiId",$session->Vars["ses_userid"]);
	WebApp::addVar("lang",$session->Vars["lang"]);
}