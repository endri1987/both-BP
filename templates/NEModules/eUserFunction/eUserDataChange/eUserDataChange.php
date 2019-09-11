<?
function eUserDataChange_onRender() {
	global $session,$event;
    
    	INCLUDE_ONCE INC_PATH."user.functionality.class.php";
	
        extract($event->args);
        $Lng = $session->Vars["lang"];
        
        $dataProfileDefaultHtml =array ();
        
        if (isset($session->Vars["ses_userid"])){
                $ses_userid = $session->Vars["ses_userid"];
                $userSystemID=$session->Vars["ses_userid"];
                }
            else{
                $ses_userid = "2";
                $userSystemID= "2";
        
        }
            
         WebApp::addVar("ln", $session->Vars["lang"]);
         WebApp::addVar("uniqueid", $session->Vars["uniqueid"]);
         WebApp::addVar("userId", $session->Vars["ses_userid"]);
        

      if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$objIdList =  $session->Vars["idstemp"];
		$objectPropList = unserialize(base64_decode(WebApp::findNemProp($objIdList)));
        
        
       /*  echo("<textarea>");
        print_r($objectPropList);
        echo("</textarea>");*/

		$dataProfileDefaultHtml["2"] = "account_information.html";
        $dataProfileDefaultHtml["5"] = "biometrical_data.html";        
		$dataProfileDefaultHtml["4"] = "contact_details.html";
        $dataProfileDefaultHtml["3"] = "proffesional_data.html";
        $dataProfileDefaultHtml["6"] = "public_profile.html";		
		$dataProfileDefaultHtml["7"] = "change_password.html";


		if (isset($objectPropList["profile_step"]) && isset($dataProfileDefaultHtml[$objectPropList["profile_step"]]))  {

                      $html_template_to_include = '<Include SRC="{{NEMODULES_PATH}}eUserFunction/eUserDataChange/temp/'.$dataProfileDefaultHtml[$objectPropList["profile_step"]].'" />';  
                      
                     //echo $objectPropList["profile_step"]."-";
                     ///echo $dataProfileDefaultHtml[$objectPropList["profile_step"]]."-";
                     //echo('{{NEMODULES_PATH}}eUserFunction/eUserDataChange/temp/'.$dataProfileDefaultHtml[$objectPropList["profile_step"]].'');
					 
					 WebApp::addVar("htmlTemplateInclude", $html_template_to_include); 
		}
              
            $PropList["username"] 				= "username";
            $PropList["email"] 				    = "email";          
            $PropList["firstname"] 				= "firstname";
            $PropList["lastname"] 				= "lastname";
            
            $PropList["date_of_birth"] 		    = "date_of_birth";
            $PropList["address"] 				= "address";
            $PropList["city"] 				    = "city";
            $PropList["zip"] 				    = "zip";
            $PropList["country"] 			    = "country";
            $PropList["phone_number"] 			= "phone_number";  
            
            $PropList["academic_title"] 		= "academic_title";
            $PropList["affiliation"] 		    = "affiliation";
            $PropList["hospital_name"] 			= "hospital_name";
            $PropList["hospital_name_inst"] 	= "hospital_name_inst";
            $PropList["position"] 			    = "position";    
            $PropList["use_camera_picture"]     = "use_camera_picture";
            $PropList["user_public_nickname"]   = "user_public_nickname";
            $PropList["user_public_location"]   = "user_public_location";
            $PropList["user_occupation"]        = "user_occupation";
            $PropList["user_about"]             = "user_about";
            $PropList["user_interes"]           = "user_interes";
            
            $PropList["snap_photo_pr"]          = "snap_photo_pr";
            $PropList["new_photo_pr"]              = "new_photo_pr";
            
            $PropList["yes"]                    = "yes";
            $PropList["no"]                     = "no";
            
            $PropList["user_photo"]             = "user_photo";
            $PropList["snap_photo"]             = "snap_photo";
            $PropList["uplod_photo"]            = "uplod_photo";
                                   
            $PropList["new_photo"]            = "new_photo";   


            
                  
         //snap_photo
          //Take new Photo            
                      
        while (list($key,$value)=each($PropList)) {           
      //  echo($objectPropList[$value]);
            if (isset($objectPropList[$value]) && $objectPropList[$value]!='')  {          
               WebApp::addVar("_".$value."_mesg", $objectPropList[$value]);
            }			
		} 
     
        $ObjUsr = new UserFullFunctionality($session->Vars["ses_userid"],"","","");		
        $ObjUsr->getUserFullInfo($session->Vars["ses_userid"]);   
      }
    WebApp::addVar("idstemp","");
    if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
        WebApp::addVar("idstempNl",$session->Vars["idstemp"]);
    }
		


    




  	
 }
 ?>