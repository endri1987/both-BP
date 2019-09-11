<?
function CollectUserEnquiryFeedback_eventHandler($event) 
{
	global $session,$event,$enquiryFeedbackData;
	extract($event->args);
}

function labelMessage($prop_arr){

	if(isset($prop_arr["enquirerTypeLBL"]) && $prop_arr["enquirerTypeLBL"]!="")
			$enquirerTypeLBL = $prop_arr["enquirerTypeLBL"];
	else	$enquirerTypeLBL = "{{_enquirerTypeLBL}}";

	if(isset($prop_arr["questionTypeLBL"]) && $prop_arr["questionTypeLBL"]!="")
			$questionTypeLBL = $prop_arr["questionTypeLBL"];
	else	$questionTypeLBL = "{{_questionTypeLBL}}";
	
	if(isset($prop_arr["subjectLBL"]) && $prop_arr["subjectLBL"]!="")
			$subjectLBL = $prop_arr["subjectLBL"];
	else	$subjectLBL = "{{_subjectLBL}}";
	
	if(isset($prop_arr["salutationLBL"]) && $prop_arr["salutationLBL"]!="")
			$salutationLBL = $prop_arr["salutationLBL"];
	else	$salutationLBL = "{{_salutationLBL}}";

	if(isset($prop_arr["firstnameLBL"]) && $prop_arr["firstnameLBL"]!="")
			$firstnameLBL = $prop_arr["firstnameLBL"];
	else	$firstnameLBL = "{{_firstnameLBL}}";

	if(isset($prop_arr["lastnameLBL"]) && $prop_arr["lastnameLBL"]!="")
			$lastnameLBL = $prop_arr["lastnameLBL"];
	else	$lastnameLBL = "{{_lastnameLBL}}";

	if(isset($prop_arr["emailLBL"]) && $prop_arr["emailLBL"]!="")
			$emailLBL = $prop_arr["emailLBL"];
	else	$emailLBL = "{{_emailLBL}}";

	if(isset($prop_arr["phoneLBL"]) && $prop_arr["phoneLBL"]!="")
			$phoneLBL = $prop_arr["phoneLBL"];
	else	$phoneLBL = "{{_phoneLBL}}";

	if(isset($prop_arr["institutionLBL"]) && $prop_arr["institutionLBL"]!="")
			$institutionLBL = $prop_arr["institutionLBL"];
	else	$institutionLBL = "{{_institutionLBL}}";

	if(isset($prop_arr["countryLBL"]) && $prop_arr["countryLBL"]!="")
			$countryLBL = $prop_arr["countryLBL"];
	else	$countryLBL = "{{_countryLBL}}";
	
	if(isset($prop_arr["enquiryDetailsLBL"]) && $prop_arr["enquiryDetailsLBL"]!="")
			$enquiryDetailsLBL = $prop_arr["enquiryDetailsLBL"];
	else 	$enquiryDetailsLBL = "{{_enquiryDetailsLBL}}";

	if(isset($prop_arr["submitButtonLBL"]) && $prop_arr["submitButtonLBL"]!="")
			$submitButtonLBL = $prop_arr["submitButtonLBL"];
	else 	$submitButtonLBL = "{{_submitButtonLBL}}";

	if(isset($prop_arr["resetButtonLBL"]) && $prop_arr["resetButtonLBL"]!="")
			$resetButtonLBL = $prop_arr["resetButtonLBL"];
	else 	$resetButtonLBL = "{{_resetButtonLBL}}";

	if(isset($prop_arr["captchaCodeLBL"]) && $prop_arr["captchaCodeLBL"]!="")
			$captchaCodeLBL = $prop_arr["captchaCodeLBL"];
	else 	$captchaCodeLBL = "{{_captcha_code_lbl_msg}}";
	
	WebApp::addVar("feedback_enquirer_mesg", 	$enquirerTypeLBL);
	WebApp::addVar("feedback_question_msg", 	$questionTypeLBL);
	WebApp::addVar("feedback_subject_msg", 		$subjectLBL);
	WebApp::addVar("feedback_salutation_msg", 	$salutationLBL);
	WebApp::addVar("contact_firstName_msg", 	$firstnameLBL);
	WebApp::addVar("contact_secontName_msg", 	$lastnameLBL);
	WebApp::addVar("contact_email_msg", 		$emailLBL);
	WebApp::addVar("contact_phone_msg", 		$phoneLBL);
	WebApp::addVar("contact_institution_msg", 	$institutionLBL);
	WebApp::addVar("contact_country_msg", 		$countryLBL);
	WebApp::addVar("enquiry_details_msg", 		$enquiryDetailsLBL);
	WebApp::addVar("captcha_code_lbl_msg", 		$captchaCodeLBL);

	WebApp::addVar("subject_lbl_msg", 		$subjectLBL);

	WebApp::addVar("submit_button_lbl", 		$submitButtonLBL);
	WebApp::addVar("reset_button_lbl", 			$resetButtonLBL);


}
function deleteEFCreatedButNotSent(){
	/*Delete created day before*/
    $date = getdate();
    $dateNow = $date["year"]."-".$date["mon"]."-".$date["mday"]." ".$date["hours"].":".$date["minutes"].":".$date["seconds"];
    $day_before = date( "Y-m-d H:i:s", strtotime( $dateNow . ' -1 day' ) );

    $deleteDayBeforeSql = "DELETE FROM enquiry_feedback WHERE ef_status='new' AND insertDate <='".$day_before."'";
    WebApp::execQuery($deleteDayBeforeSql);
	/*Delete created day before*/

	$deleteAttachedDocsSql = "DELETE FROM enquiry_feedback_file_attach WHERE status_flag='new' AND record_timestamp <= '".$day_before."'";
	WebApp::execQuery($deleteAttachedDocsSql);
}

function CollectUserEnquiryFeedback_onRender() 
{
	global $session, $event, $enquiryFeedbackData;

	require_once(EASY_PATH."bo_toolsset/InquiryFeedbackManagement/InquiryFeedback.class.php");
	$toolObj = new InquiryFeedback();
//$starts = WebApp::get_formatted_microtime();

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		WebApp::addVar("idstemp",$session->Vars["idstemp"]);

		//per te mos inkluduar dy here JS -------------------------------------------
          GLOBAL $ContactModule_Include;
          $include_js_contactmodule = "Y";
          IF (ISSET($ContactModule_Include) AND ($ContactModule_Include == "Y"))
             {
              $include_js_contactmodule = "N";
             }
          $ContactModule_Include = "Y";
        //per te mos inkluduar dy here JS -------------------------------------------


		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
		$templateType = "default";

		if(isset($prop_arr["EFType"]) && $prop_arr["EFType"]!=""){
			/*$EF_desc_type = $toolObj->getKwDescription("EFM", $prop_arr["EFType"]);
			$templateType = strtolower($EF_desc_type);*/
			$templateType = "EF_".$prop_arr["EFType"];
		}

		$idstemp_sel 	= $session->Vars["idstemp"];
		$idstemp_array 	= EXPLODE("-", $idstemp_sel);
        $id_nem        	= $idstemp_array[4];

		WebApp::addVar("id_nem",$id_nem);
		$templateTypeSelected = $templateType.'_template.html';

	/*	echo'<textarea>';
			print_r($prop_arr);
		echo'</textarea>';
	*/
		/*if (isset($prop_arr["templateType"]) && $prop_arr["templateType"] != "") {

			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateType"]."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF()) {
				$templateTypeSelected = $rs->Field("template_box");
			}
			//------------------------------------------------------------------------------------------------------------------------
		}
*/
		WebApp::addVar("templateTypeSelected",$templateTypeSelected);
		WebApp::addVar("CollectUserEnquiryFeedback_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}CollectUserEnquiryFeedback/".$templateTypeSelected."\"/>");


		if(count($prop_arr) > 0)
		foreach($prop_arr as $k => $v)
			if(!is_array($v))
				WebApp::addVar($k, $v);
	}



	if(isset($prop_arr["use_default_captcha"])){
		if($prop_arr["use_default_captcha"] == "n"){
			$captcha_aktiv 	= $prop_arr["captcha_aktiv"];
			$captcha_type 	= $prop_arr["captcha_type"];
			$captcha_length = $prop_arr["captcha_length"];			
		}elseif($prop_arr["use_default_captcha"] == "y"){
			require_once(EASY_PATH."bo_toolsset/InquiryFeedbackManagement/InquiryFeedback.class.php");
			$EFToolObj = new InquiryFeedback();
			$tool_id = "150";
			$currentToolSettings = $toolObj->getUnserializedSettingsData($tool_id);

			$captcha_aktiv 	= $currentToolSettings["captcha_aktiv"];
			$captcha_type 	= $currentToolSettings["captcha_type"];
			$captcha_length = $currentToolSettings["captcha_length"];			
		
		}
	}

	 //captcha -------------------------------------------------------------------
        $cap_uni     = $session->Vars["uni"];
        $cap_idstemp = $session->Vars["idstemp"];

        //gjenerojme captcha ------------------------------------------------------
        INCLUDE(ASP_FRONT_PATH."nems/UserEnquiryFeedback/gjenero_captcha.php");
        //gjenerojme captcha ------------------------------------------------------
      //captcha -------------------------------------------------------------------
      
      //captcha image -------------------------------------------------------------
        //image -------------------------------------------------------------------
          $captcha_img     = "";
          $captcha_img_id  = "";
          $captcha_img_src = "";
        //image -------------------------------------------------------------------
        
        //input -------------------------------------------------------------------
          $captcha_input      = "";
          $captcha_input_name = "";
        //input -------------------------------------------------------------------
        
        //reload ------------------------------------------------------------------
          $captcha_reload_fun = "";
        //reload ------------------------------------------------------------------
        IF ((($captcha_aktiv == "AC") OR (($captcha_aktiv == "NLO") AND ($session->Vars["ses_userid"] == "2"))) AND ($captcha_type == "i"))
           {
            $dataora         = DATE("Ymdhis");
            $captcha_img_id  = "cap_".$session->Vars["idstemp"];
            $captcha_img_src = APP_URL."enquiryFeedback_captcha.php?uni=".$session->Vars["uni"]."&idstemp=".$session->Vars["idstemp"]."&t=".$dataora;
            $captcha_img     = '<img id="'.$captcha_img_id.'" src="'.$captcha_img_src.'" width="200" border="0" />';
            
            $captcha_input_name = "capt_i";
            $captcha_input      = '<input type="text" name="'.$captcha_input_name.'" value="" valid="1,0,0,0,0,0" etiketa="{{captcha_kodi_mesg}}" maxlength="'.$captcha_length.'" />';
           
            $captcha_reload_fun = 'javascript:captcha_reload(\''.$session->Vars["uni"].'\',\''.$session->Vars["idstemp"].'\');';
           }
        
        WebApp::addVar("captcha_aktiv",      $captcha_aktiv);
        WebApp::addVar("captcha_type",       $captcha_type);
        WebApp::addVar("captcha_img",        $captcha_img);
        WebApp::addVar("captcha_img_id",     $captcha_img_id);
        WebApp::addVar("captcha_img_src",    $captcha_img_src);
        
        WebApp::addVar("captcha_input",      $captcha_input);
        WebApp::addVar("captcha_input_name", $captcha_input_name);
		WebApp::addVar("captcha_length", $captcha_length);
        WebApp::addVar("captcha_reload_fun", $captcha_reload_fun);
      //captcha image -------------------------------------------------------------


	$userDatas = array();
	$disableLabel="";
	$user_id="";
	$UserSalutation = "";
	$UserFirstName = "";
	$UserSecondName = "";
	$UserEmail = "";
	$phone = "";
	$institution = "";
	$country = "";
	$enquiry_details = "";
	if(isset($session->Vars["ses_userid"]) && $session->Vars["ses_userid"]!="2"){

		$user_id = $session->Vars["ses_userid"];
        $ObjUsr = new UserFullFunctionality($user_id,"","","");	

        $userData = $ObjUsr->getUserInfo($user_id); 

		$UserSalutation 	= $userDatas[$user_id]["UserSalutation"];
		$usrname 			= $userDatas[$user_id]["UserName"];
		$UserFirstName 		= $userDatas[$user_id]["UserFirstName"];
		$UserSecondName 	= $userDatas[$user_id]["UserSecondName"];
		$UserEmail 			= $userDatas[$user_id]["UserEmail"];
		$phone 				= $userDatas[$user_id]["UserPhone"];
		//$institution 		= $userDatas[$user_id]["UserInstitution"];
		//$country 			= $userDatas[$user_id]["UserCountry"];

		//$disableLabel="state-disabled";

	}

	WebApp::addVar("systemUserId",$user_id);

	WebApp::addVar("UserSalutation",$UserSalutation);
	WebApp::addVar("UserName",$usrname);
	WebApp::addVar("UserFirstName",$UserFirstName);
	WebApp::addVar("UserSecondName",$UserSecondName);
	WebApp::addVar("UserEmail",$UserEmail);

	WebApp::addVar("phone",$phone);
	WebApp::addVar("institution",$institution);
	WebApp::addVar("country",$country);
	WebApp::addVar("enquiry_details",$enquiry_details);
	
	$salutation = $toolObj->getSalutationGrid();
	WebApp::addVar("salutationGrid", $salutation);

	$EFType="";
	$EF_desc_type="";
	if(isset($prop_arr["EFType"]) && $prop_arr["EFType"]!=""){
		$EFType = $prop_arr["EFType"];
		$EF_desc_type = $toolObj->getKwDescription("EFM", $EFType);
	}
	WebApp::addVar("EF_desc_type", $EF_desc_type);
	WebApp::addVar("typeEF", $EFType);

	$topicsType = $toolObj->getEnquiryAndFeedbackTypeFilter("EFQ");
	$topics=array();
	if(isset($prop_arr["topicType"]) && $prop_arr["topicType"]!=""){
		if(isset($topicsType["data"]) && count($topicsType["data"])>0){

			foreach ($topicsType["data"] as $key => $value) {
				if(in_array($topicsType["data"][$key]["id"], $prop_arr["topicType"]))
				$topics[$topicsType["data"][$key]["id"]] = $topicsType["data"][$key]["label"];
			}

			if(count($topics)==1){

				$elemKey = key($topics);

	    		WebApp::addVar("singleQuestion", "yes");
	    		WebApp::addVar("EnquiryQuestion", $elemKey);

			}elseif(count($topics)>1){

				WebApp::addVar("singleQuestion", "no");
	    		WebApp::addVar("EnquiryQuestion", "");

			}
			$EFQuestionsType=array();
        	$EFQuestionsTypeGrid = array("data"=>array(),"AllRecs"=>"0"); $ind = 0;
        	while(list($qId,$qVal)=each($topics)){
        		$EFQuestionsType["data"][$ind]["id"] = "$qId";
        		$EFQuestionsType["data"][$ind]["label"] = "$qVal";
        		$EFQuestionsType["data"][$ind++]["sel"] = "";
        	}

        	$EFQuestionsTypeGrid["data"]      = $EFQuestionsType["data"];
        	$EFQuestionsTypeGrid["AllRecs"]   = count($EFQuestionsType["data"]);
    		WebApp::addVar("enquireQuestionGrid", $EFQuestionsTypeGrid);	
		

		}

	}else{
		WebApp::addVar("enquireQuestionGrid", $topicsType);
	}


	$enquirerType = $toolObj->getEnquiryAndFeedbackTypeFilter("EFT");
    WebApp::addVar("enquirerTypeGrid", $enquirerType);
	$enquirerType="";
	$EnquirerTypeSel="no";
	if(isset($prop_arr["enquirerType"]) && $prop_arr["enquirerType"] !=""){
		$enquirerType = $prop_arr["enquirerType"];			
		$EnquirerTypeSel= "yes";			
	}
	WebApp::addVar("EnquirerType", $enquirerType);			
	WebApp::addVar("EnquirerTypeSel", $EnquirerTypeSel);			

	if(isset($prop_arr["defaultEnquirerTypeShow"]) && $prop_arr["defaultEnquirerTypeShow"] == "show"){
		$showEnquirerType = $prop_arr["defaultEnquirerTypeShow"];
	}elseif(isset($prop_arr["defaultEnquirerTypeShow"]) && $prop_arr["defaultEnquirerTypeShow"] == "hide"){
		$showEnquirerType = $prop_arr["defaultEnquirerTypeShow"];
	}
	WebApp::addVar("showEnquirerType", $showEnquirerType);			

   	/*call function to Delete created day before but not sent*/
   		deleteEFCreatedButNotSent();
	/*Delete created day before*/
    
	$sendFeedbackProces = "NEW";

	if ($event->name == "enquiryFeedback"){
		if(isset($session->Vars["sendFeedbackProces"]) && $session->Vars["sendFeedbackProces"]!=""){
	    	$sendFeedbackProces  = $session->Vars["sendFeedbackProces"];		
		}		
	}

    WebApp::addVar("sendFeedbackProces",$sendFeedbackProces);

 
    if($sendFeedbackProces == "NEW"){
		// Create new Enquiry_feedback in db 
	    $maxEnquiryId="SELECT max(id) as maxEnquiryId FROM enquiry_feedback";
	    $rsEnquiryMaxId = WebApp::execQuery($maxEnquiryId);
	    if (!$rsEnquiryMaxId->EOF() && $rsEnquiryMaxId->Field("maxEnquiryId") != "undefined") {
	        $maxId = $rsEnquiryMaxId->Field("maxEnquiryId");
	        $maxId++;
	    }else{
	        $maxId=1;
	    }    
		WebApp::addVar("EF_id", $maxId);
	    $addEnquiryFeedbackSql="INSERT INTO enquiry_feedback (id, id_zeroNivel, language, type, insertDate) VALUES ('".$maxId."','".$session->Vars["level_0"]."','".$session->Vars["lang"]."','".$EFType."',Now())";
	    WebApp::execQuery($addEnquiryFeedbackSql);
		/*----------------------*/
    }elseif($sendFeedbackProces == "RESEND"){
    	if(isset($enquiryFeedbackData) && count($enquiryFeedbackData)>0){

			foreach($enquiryFeedbackData as $k => $v)
				if(!is_array($v))
					WebApp::addVar($k, $v);
    	}

    }

  //  $countryList = $toolObj->getCountryList();
  //  WebApp::addVar("countryListGrid", $countryList);


    if(isset($prop_arr["use_default_attached_format"]) && $prop_arr["use_default_attached_format"] !=""){
    	if($prop_arr["use_default_attached_format"] == "y"){
    		
    	}
    }

    if(isset($prop_arr["success_registration_message"]) && $prop_arr["success_registration_message"]!=""){
		WebApp::addVar("success_send_message", $prop_arr["success_registration_message"]);
    }
	else{
		WebApp::addVar("success_send_message", "{{_success_registration_message}}");
	}	  

	if(isset($prop_arr["error_registration_message"]) && $prop_arr["error_registration_message"]!=""){
		WebApp::addVar("error_send_message", $prop_arr["error_registration_message"]);
    }
	else{
		WebApp::addVar("error_send_message", "{{_error_registration_message}}");
	}

	$error_captcha_match="{{_error_captcha_match}}";
	if(isset($prop_arr["error_captcha_match"]) && $prop_arr["error_captcha_match"] !=""){
		$error_captcha_match = $prop_arr["error_captcha_match"];
	}			
	WebApp::addVar("error_captcha_match", $error_captcha_match);

    labelMessage($prop_arr);

      //per te mos inkluduar dy here JS -------------------------------------------------------------------------
          $mesg_js = "";
          IF ($include_js_contactmodule == "Y")
             {
              $captcha_html_js = '';
              IF ((($captcha_aktiv == "AC") OR ($captcha_aktiv == "NLO")) AND ($captcha_type == "h")){
                  $captcha_html_js = 'contactmodule_capt_h["'.$session->Vars["idstemp"].'"] = "'.$captcha_txt.'";';
                 }
              	$mesg_js  = '<script type="text/javascript" language="JavaScript1.2"> 
                             var APP_URL               = "'.APP_URL.'";
                             var contactmodule_capt_h  = new Array();
                             '.$captcha_html_js.'
                             
                             var alert_isnull_mesg     = "'.$alert_isnull_mesg.'";  
                             var alert_isalpha_mesg    = "'.$alert_isalpha_mesg.'";
                      
                             var alert_isdate1_mesg    = "'.$alert_isdate1_mesg.'";
                             var alert_isdate2_mesg    = "'.$alert_isdate2_mesg.'";
                      
                             var alert_isdate3_mesg    = "'.$alert_isdate3_mesg.'";
                             var alert_isemail_mesg    = "'.$alert_isemail_mesg.'";
                             var alert_isinteger_mesg  = "'.$alert_isinteger_mesg.'";
                             var alert_isnumber1_mesg  = "'.$alert_isnumber1_mesg.'";
                         
                             var contact_but_send_mesg = "'.$contact_but_send_mesg.'";
                             var contact_id_nem        = "'.$id_nem.'";
                           </script>
                          ';
             }
          ELSE
             {
              IF ((($captcha_aktiv == "AC") OR ($captcha_aktiv == "NLO")) AND ($captcha_type == "h")){

                  $mesg_js = '<script type="text/javascript" language="JavaScript1.2"> 
                                contactmodule_capt_h["'.$session->Vars["idstemp"].'"] = "'.$captcha_txt.'";
                              </script>
                             ';
                 }
             }
        //per te mos inkluduar dy here JS -------------------------------------------------------------------------
        WebApp::addVar("mesg_js", $mesg_js);
      //per mesazhet ne javascript ------------------------------------------------
}