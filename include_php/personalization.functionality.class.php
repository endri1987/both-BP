<?php
require_once(INCLUDE_PATH.'intServices/collector.Data.List.class.php');
class personalization extends collectorDataListClass
{

	var $lang				= "";
	var $lngId;
	var $thisMode			= "";
	var $uniqueid			= "";
	var $tip				= "";
	var $ses_userid			= "";
	var $templateFileName	= "personalization_default.html";
	var $nem_prop			= "";
	
	
	var $CMID 				= "";
	var $CMType 			= "";
	var $CFact 				= "";
	var $templateIdForum	= 0;
	var $CMconfiguration 	= array();
	var $authorOfCommArr 	= array();
	var $propertiesExtended	= array();
	var	$topic_total_postNr = 0;
	var	$forum_total_postNr = 0;
	var	$select_sql_param	= "";


	function personalization($idstemp = "",$ciID="",$mode = "display")
	{
		global $session, $mob_web;
		
		global $session,$event;

		$this->langId		= str_replace("Lng","",$session->Vars["lang"]);
		$this->lang			= $session->Vars["lang"];
		$this->thisMode		= $session->Vars["thisMode"];
	 	$this->uniqueid		= $session->Vars["uniqueid"];
	 	$this->ses_userid	= $session->Vars["ses_userid"];
	 	$this->tip			= $session->Vars["tip"];
	 	$this->mode			= $mode;
	 	$this->ciID			= $ciID;
		
		
		$sessLANG = $session->Vars["lang"];
		if (isset($sessLANG) && $sessLANG!="") {
		
			if (preg_match("/Lng/i",$sessLANG)) {
				$lngIDCode = str_replace("Lng","",$sessLANG)*1;
				if (!defined("LNG".$lngIDCode)) {
					$lngIDCode = 1;
				} else {
					$session->Vars["lang"] = 'Lng'.$lngIDCode;					
				}
			} 
		} else {
			$session->Vars["lang"] = 'Lng1';	
		}
		$this->lang			= $session->Vars["lang"];
		$this->lngId 		= str_replace ("Lng","",$this->lang);
		$this->thisMode		= $session->Vars["thisMode"];
		
		if ($session->Vars["thisMode"]=="_new")
        		$this->thisModeCode = 0;
        else	$this->thisModeCode = 1;
		
	 	$this->uniqueid		= $session->Vars["uniqueid"];

		if (isset($session->Vars["tip"]))
			$this->tip = $session->Vars["tip"];
		else
			$this->tip = "2";

		if (isset($session->Vars["ses_userid"]))
			$this->ses_userid = $session->Vars["ses_userid"];
		else
			$this->ses_userid = "2";

		
		
		if ($idstemp=="") {
			$this->idstemp = $session->Vars["idstemp"];
		} elseif ($idstemp=="empty") {	//CI-57-96-1-79
			$this->idstemp = "";
		} else {
			$this->idstemp = $idstemp;
			$this->initNemConfiguration();
		}

		
		$this->k =  $session->Vars["contentId"];
		$this->kc =  $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"];
		$this->koord_level_node_param =  $session->Vars["level_0"]."/".$session->Vars["level_1"]."/".$session->Vars["level_2"]."/".$session->Vars["level_3"]."/".$session->Vars["level_4"];
	}
	
	function initNemConfiguration($idstemp=""){
		global $session;
		
		if ($idstemp=="")	$this->idstemp = $session->Vars["idstemp"];
		else				$this->idstemp = $idstemp;
		
		
		if ($this->idstemp!="") {
			$objNemArr = explode("-",$this->idstemp);
			$this->type_doc =$objNemArr[0];
			$this->nemID =$objNemArr[2];
			$this->objNem =$objNemArr[4];
			$this->docId =$objNemArr[3];

			$objects = unserialize(base64_decode(WebApp::findNemProp($this->idstemp)));
		
			
			if (is_array($objects)) {
				$this->templateType = $objects["templateID"];
				//behet override templati per mobile, nese eshte bere set
				IF ($mob_web == "mob" && isset($objects["templateType_mobile"]) && $objects["templateType_mobile"]!="") {
					$this->templateType = $objects["emplateType_mobile"];
				}			
			  //selektohet template -------------------------------------------------------------------------------------------------
				$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateType."'";
				$rs = WebApp::execQuery($sql_select);
				IF (!$rs->EOF() AND mysql_errno() == 0)
				   {$this->templateFileName = $rs->Field("template_box");} 
			  //---------------------------------------------------------------------------------------------------------------------		
				if($this->templateType=='0'){
					$this->templateFileName="template_default.html";
				} else $this->templateFileName=$this->templateFileName;
			}
			$this->nem_prop = $objects;
	
		}		
	}	
	function validateInstanceComments() {
		
			$user_roles = explode(",",$this->tip);
			$user_roless = arr;
			$cnt_user_roles = count($user_roles);
			
			$get_show_comments_rating = "
				SELECT IF(show_comments_rating IS NULL,'', show_comments_rating) as show_comments_rating
				  FROM ci_extended_properties
						 WHERE content_id 	= '".$this->ciID."'						   
						   AND lng_id		= '".$this->langId."'
						   AND statusInfo	in (0)";			
			

			$rs= WebApp::execQuery($get_show_comments_rating);
						
			if(!$rs->EOF() && $rs->Field("show_comments_rating")==1) {
				$this->rights[CI]="yes";	
				if (in_array("1",$user_roles)) {
					$this->rights[FULL_WRITES]="yes";
					$this->publish_comments="yes";
					$this->publish_rating="yes";				
				}
			}	
			
			if ($this->rights[CI]=="yes") {
				
				$prop_arr 		= unserialize(base64_decode(WebApp::findNemProp($this->idstemp)));
				$this->nemProp = $prop_arr;

				if ($prop_arr["public"]=="yes") {//kontrollo te drejten read dhe write
					$this->rights[READ] = "yes";
				} else {
					
					$view_roles_nem = explode(",",$prop_arr["view_p"]);
					$cnt_view_roles_nem = count($view_roles_nem);
					$tst_viewx = array_merge($user_roles,$view_roles_nem);
					$tst_view = array_count_values($tst_viewx);
					if (count($tst_view)<($cnt_user_roles+$cnt_view_roles_nem)) //kemi view
						$this->rights[READ] = "yes";

				}
				//kontrollo te drejten write
				$contribute_nem = explode(",",$prop_arr["contribute_p"]);
				$cnt_contribute_nem = count($contribute_nem);
				
				$tst_viewy = array_merge($user_roles,$contribute_nem);
				$tst_view_c = array_count_values($tst_viewy);
				if (count($tst_view_c)<($cnt_user_roles+$cnt_contribute_nem)) {//kemi view
					$this->rights[WRITE] 	= "yes";
					$this->rights[READ] 	= "yes";
				}				
			}
			if (isset($prop_arr["rec_page"]) && $prop_arr["rec_page"]!="all")
				$this->recPages = $prop_arr["rec_page"];
			else
				$this->recPages = "all";

			if (isset($prop_arr["publish_comments"]) && $prop_arr["publish_comments"]=="yes")
				$this->publish_comments = "yes";
			else
				$this->publish_comments = "no";

	}	
	function getUserUsefull($idCi, $lngId) {

			$tmp = array();	
			$reportUsefull = "
				SELECT  content_id, useful, not_useful, (useful + not_useful) as sm
				  FROM ci_useful_report
						 WHERE content_id 	in (".$idCi.")						   
						   AND lng_id		= '".$lngId."'";	

			$rs_reportUsefull = WebApp::execQuery($reportUsefull);
			while (!$rs_reportUsefull->EOF()) {
				$content_id 	= $rs_reportUsefull->Field("content_id");			
				$useful 	 	= $rs_reportUsefull->Field("useful");			
				$not_useful  	= $rs_reportUsefull->Field("not_useful");			
				$useful_total 	= $rs_reportUsefull->Field("sm");				
				
				$tmp[$content_id]["useful"] =$useful;
				$tmp[$content_id]["useful_total"] =$useful_total;
				
				$rs_reportUsefull->MoveNext();
			}
			return $tmp;
	}
	function areCisInUserFavorites($ciids, $lngId="", $uid="")
	{
		$dataToBeReturned = array();
		$get_user_state = "
			SELECT count(ci_user_favorites.content_id) as exits, content.content_id
			  FROM content
		 LEFT JOIN ci_user_favorites on ci_user_favorites.content_id = content.content_id  AND UserId = '".$uid."' AND lng_id = '".$lngId."'
		     WHERE content.content_id 	in (".$ciids.")						   
					  
		   GROUP BY content.content_id";	

		$rs_get_user_state = WebApp::execQuery($get_user_state);	
		while (!$rs_get_user_state->EOF()) {
		
			$content_id = $rs_get_user_state->Field("content_id");
			$exits 		= $rs_get_user_state->Field("exits");
			$dataToBeReturned[$content_id]["is_favorites"] = $exits;	
			$rs_get_user_state->MoveNext();			
		}
		return $dataToBeReturned;
	}
	function display_CI_Favorite_button($ci_id, $lang="", $uid="")
	{
			
		$this->configurationProp = array();
		//kontrollo nese per kete ci eshte zgjedhur qe te shfaqet favorites button
		//kontrollo nese user e ka zgjedhur kete ci me pare
		//nese jo shfaq  vendos te my favorites
		//nese po shfaq hiq nga myfavorites
		$fav_in = "none";
		$fav_out = "none";
		$show_what = "none";
		$show_user_rating = "none";
		$show_useful = "none";
		
		if ($lang=="")	$lang = $this->lang;
		if ($uid=="")	$uid = $this->ses_userid;
		
		$lngId = eregi_replace("Lng","",$lang);
		
		if(!defined('CI_Favorite') || CI_Favorite=="NO") {
		
		
		} else {
			
			$show_useful = "yes";
			
			$reportUsefull = "
				SELECT  useful, not_useful, (useful + not_useful) as sm
				  FROM ci_useful_report
						 WHERE content_id 	= '".$ci_id."'						   
						   AND lng_id		= '".$lngId."'";	

			$rs_reportUsefull = WebApp::execQuery($reportUsefull);
			

			
			$useful 	 	= $rs_reportUsefull->Field("useful");			
			$not_useful  	= $rs_reportUsefull->Field("not_useful");			
			$useful_total 	= $rs_reportUsefull->Field("sm");			
			
			
			$this->configurationProp["nr_useful"] = $useful;
			$this->configurationProp["nr_not_useful"] = $not_useful;
			$this->configurationProp["nr_useful_total"] = $useful_total;			
			
			
			$lngId = preg_replace("#Lng#",'', $lang);
			$get_other_prop = "
				SELECT  IF(show_favorite IS NULL,'', show_favorite) as show_favorite,
						IF(show_rating IS NULL,'', show_favorite) as show_rating
				  FROM ci_extended_properties
						 WHERE content_id 	= '".$ci_id."'						   
						   AND lng_id		= '".$lngId."'
						   AND statusInfo	= '0'";	

			$rs_other_prop = WebApp::execQuery($get_other_prop);
			
			$show_favorite = $rs_other_prop->Field("show_favorite");
			if ($show_favorite == '1') {
				
				$get_user_state = "
					SELECT count(1) as exits
					  FROM ci_user_favorites
							 WHERE content_id 	= '".$ci_id."'						   
							   AND UserId		= '".$uid."'
							   AND lng_id		= '".$lngId."'";	

				$rs_get_user_state = WebApp::execQuery($get_user_state);	
				if ($rs_get_user_state->Field("exits")>=1) {
					$show_what = "remove_favorite";	
					$fav_out = "block";	
				} else	{
					$show_what = "make_favorite";	
					$fav_in = "block";	
				}
			} 
			$user_rating_value = 0;
			$show_rating = $rs_other_prop->Field("show_rating");
			
			
			if ($show_favorite == '1') {
				$show_user_rating = "yes";	
				$get_user_state_rate = "
					SELECT rating_value
					  FROM ci_user_most_popular
							 WHERE content_id 	= '".$ci_id."'						   
							   AND UserId		= '".$uid."'
							   AND lng_id		= '".$lngId."'";	

				$rs_rate = WebApp::execQuery($get_user_state_rate);	
				
				if (!$rs_rate->EOF()) {

					$user_rating_value = $rs_rate->Field("rating_value");
				} 			
			}
		}
		
		$this->configurationProp["fav_in"] = $fav_in;
		$this->configurationProp["fav_out"] = $fav_out;
		$this->configurationProp["show_what"] = $show_what;
		$this->configurationProp["show_user_rating"] = $show_user_rating;
		$this->configurationProp["user_rating_value"] = $user_rating_value;
		$this->configurationProp["show_useful"] = $show_useful;
		
		WebApp::addVar("fav_in","".$fav_in."");
		WebApp::addVar("fav_out","".$fav_out."");
		WebApp::addVar("show_what","".$show_what."");
		
		WebApp::addVar("show_user_rating","".$show_user_rating."");
		WebApp::addVar("user_rating_value","".$user_rating_value."");
		
		WebApp::addVar("show_useful","".$show_useful."");
	}		
	function adm_user_usefull()
	{
		  global $session;
		  
		  $insert="REPLACE INTO ci_useful_user (content_id,UserId,uniqueid,lng_id,vote) 
		  VALUES ('".$_REQUEST["id_art"]."','".$session->Vars["ses_userid"]."','".$_REQUEST["uni"]."','".$session->Vars["LngID"]."','".$_REQUEST["itemType"]."')";
		  WebApp::execQuery($insert);	
		  
			$getPopularData = "SELECT count(1) as nr_filled, vote FROM ci_useful_user 
							    WHERE content_id = '".$_REQUEST["id_art"]."' AND lng_id = '".$session->Vars["LngID"]."'
							 GROUP BY vote";
	

			$rs_getPopularData = WebApp::execQuery($getPopularData);
			
			$nr_useful = 0;
			$nr_not_usefull = 0;
			
			while (!$rs_getPopularData->EOF()) {
				
				$nr_filled = $rs_getPopularData->Field("nr_filled");
				$vote = $rs_getPopularData->Field("vote");
				
				if ($vote=='usefull') {
					$nr_useful = $nr_filled;
				}
				if ($vote=='not_usefull') {
					$nr_not_usefull = $nr_filled;
				}
				
				$rs_getPopularData->MoveNext();
			}
	
		  $insert="INSERT INTO ci_useful_report (content_id,lng_id,useful,not_useful) 
		                 VALUES ('".$_REQUEST["id_art"]."','".$session->Vars["LngID"]."','".$nr_useful."','".$nr_not_usefull."')";
		  WebApp::execQuery($insert);	
		  $updateUseful = "UPDATE ci_useful_report 
		  							   SET useful 		= '".$nr_useful."',
		  							   	   not_useful 	= '".$nr_not_usefull."'
		  							   	
		  							 WHERE content_id = '".$_REQUEST["id_art"]."' AND lng_id = '".$session->Vars["LngID"]."'";	
		  WebApp::execQuery($updateUseful);	
	}
	
	function saveViewCi()
	{
	
	}
	
	function adm_user_rate($itemId,$uid,$lnid)
	{
		
		  $insert="REPLACE INTO ci_user_most_popular (content_id,UserId,lng_id,rating_value) 
		  VALUES ('".$itemId."','".$uid."','".$lnid."','".$_REQUEST["rating"]."')";
		  WebApp::execQuery($insert);	
		  
			$getPopularData = "SELECT count(1) as nr_filled, sum(rating_value) as rating_sum FROM ci_user_most_popular 
							    WHERE content_id = '".$itemId."' AND lng_id = '".$lnid."'
							 GROUP BY content_id, lng_id";
	

			$rs_getPopularData = WebApp::execQuery($getPopularData);
			
			$nr_filled = 0;
			$rating_sum = 0;
			$rating_factor = 0;
			
			if (!$rs_getPopularData->EOF()) {
				$nr_filled = $rs_getPopularData->Field("nr_filled");
				$rating_sum = $rs_getPopularData->Field("rating_sum");
				
				if ($rs_getPopularData->Field("rating_sum")>0) {
					$rating_factor = round($rating_sum/$nr_filled,2);
				}
			}
			
			/*echo $nr_filled."-";
			echo $rating_sum."-";
			echo $rating_factor."-";*/
	
	
		  $insert="REPLACE INTO ci_most_popular (content_id,lng_id,rating_value,rating_sum,rating_nr) 
		                 VALUES ('".$_REQUEST["fci"]."','".$_REQUEST["fln"]."','".$rating_factor."','".$rating_sum."','".$nr_filled."')";
		  WebApp::execQuery($insert);		
	}	
	function adm_CI_Favorite_button($itemId,$uid,$lnid)
	{
		if ($_REQUEST["ftp"] == 'in') {
			$insert="REPLACE INTO ci_user_favorites (content_id,UserId,lng_id) VALUES ('".$itemId."','".$uid."','".$lnid."')";
			WebApp::execQuery($insert);			
		} elseif ($_REQUEST["ftp"] == 'out') {
			$insert="DELETE FROM ci_user_favorites WHERE content_id = '".$itemId."' AND UserId = '".$uid."' AND lng_id = '".$lnid."'";
			WebApp::execQuery($insert);					
		}
	}	
	function cntr_CI_Favorite_button($itemId,$uid,$lnid)
	{
		global $session;
		if ($_REQUEST["ftp"] == 'in') {
			$insert="REPLACE INTO ci_user_favorites (content_id,UserId,lng_id) VALUES ('".$itemId."','".$uid."','".$lnid."')";
			WebApp::execQuery($insert);			
		} elseif ($_REQUEST["ftp"] == 'out') {
			$insert="DELETE FROM ci_user_favorites WHERE content_id = '".$itemId."' AND UserId = '".$uid."' AND lng_id = '".$lnid."'";
			WebApp::execQuery($insert);					
		}
	}
	function cntr_user_usefull($itemId,$uid,$lnid) 
	{
		
		  global $session;
		  
		  $insert="REPLACE INTO ci_useful_user (content_id,UserId,uniqueid,lng_id,vote) 
		  VALUES ('".$itemId."','".$session->Vars["ses_userid"]."','".$_REQUEST["uni"]."','".$session->Vars["LngID"]."','".$_REQUEST["itemType"]."')";
		  WebApp::execQuery($insert);	
		  
			$getPopularData = "SELECT count(1) as nr_filled, vote FROM ci_useful_user 
							    WHERE content_id = '".$itemId."' AND lng_id = '".$session->Vars["LngID"]."'
							 GROUP BY vote";
	

			$rs_getPopularData = WebApp::execQuery($getPopularData);
			
			$nr_useful = 0;
			$nr_not_usefull = 0;
			
			while (!$rs_getPopularData->EOF()) {
				
				$nr_filled = $rs_getPopularData->Field("nr_filled");
				$vote = $rs_getPopularData->Field("vote");
				
				if ($vote=='usefull') {
					$nr_useful = $nr_filled;
				}
				if ($vote=='not_usefull') {
					$nr_not_usefull = $nr_filled;
				}
				
				$rs_getPopularData->MoveNext();
			}
	
		  $insert="REPLACE INTO ci_useful_report (content_id,lng_id,useful,not_useful) 
		                 VALUES ('".$itemId."','".$session->Vars["LngID"]."','".$nr_useful."','".$nr_not_usefull."')";
		  WebApp::execQuery($insert);		
	}	

	function addNewCm($dataRequest) 
	{
		global $session;

		$this->inputParams 	= $dataRequest;
		$this->errorrCodeN	=1; //ok
		
		if ($this->errorCodeCM==1) {
		
			if($this->inputParams['comment']!="" && $this->inputParams['comment']!="<p><br></p>" ){//don't add as a new item document if the comment is empty
				
				require_once(INCLUDE_AJAX_PATH."/CiManager.class.php");

				$crd = implode(",",$this->CMNodeID);
				$workingCi = new CiManager("",$this->lang,$crd,$this->userId);
				$workingCi->createNewCi($crd,"thisIsTheDocumentThatIsCreatedFromNavigation");

				//$workingCi->idci - id e ci se re

				$this->templateCMAjax			= "";
				
				$paramsArray=array();
				$paramsArray["ciTemplId"] 		= 0;
				$paramsArray["schedule"] 		= "N";
				$paramsArray["searchable"] 		= "Y";
				$paramsArray["imageGallFlag"] 	= "no";
				$paramsArray["mediaGallFlag"] 	= "no";

				$paramsArray["title"] 			= $this->inputParams['title'];

				$PEI =array();
				$PEI["content_id"]  			= $workingCi->idci;
				$PEI["lng_id"] 					= $workingCi->lngId;	
				$PEI["content"] 				= $this->inputParams['comment'];
			
				//topic, Mpost, post, replay
				$paramsArray["act"] 			= $this->inputParams['act'];
	
				if ($paramsArray["act"]=="topic") { //add new theme
				
					$PEI["mainCi"]				= 0;						//	is "forum_kategori_id" field 	--content table
					$PEI["parentId"]			= $this->CMID;				//	is "parent_content_id" field 	--content table
					$PEI["replayId"] 			= 0;
					$PEI["ci_mi_id"] 			= 3;						//	is "ci_mi_id" field 			--content table
					$paramsArray["doc_type"] 	= "CT";
					$paramsArray["ciTemplId"] 	= $this->templateIdCT;	//	must be the template id of the parent
						
					if($this->templateFileName!="template_default.html")
						$this->templateCMAjax	= $this->templateFileName;
					else
						$this->templateCMAjax	= "cm_list_detail_forum.html";
					
					//add new CT [topics item] item document  (newCiId ,forumID,languageID)
					$this->addnewCTDoc($PEI["content_id"],$this->CMID,$PEI["lng_id"]);
									
				} elseif ($paramsArray["act"]=="Mpost" || $paramsArray["act"]=="post" || $paramsArray["act"]=="replay") { //add main post or post or replay

					$PEI["mainCi"]				= $this->CMmainDocID;	//	is "forum_kategori_id" field 	--content table
					$PEI["parentId"]			= $this->CMID;			//	is "parent_content_id" field 	--content table
					$PEI["ci_mi_id"] 			= 4;					//	is "ci_mi_id" field 			--content table
					$paramsArray["doc_type"] 	= "CP";
					
					if (isset($this->inputParams['parSubId']) && $this->inputParams['parSubId']>0) {
						$PEI["replayId"] = $this->inputParams['parSubId'];
					}
					
					$this->templateCMAjax		= "cm_list_detail_forum_tema.html";
					//add new CP [post item] item document  (newCiId ,forumID,languageID)
					$PEI["topic_id"]			= $this->CMID;
					$PEI["act"]					= $paramsArray["act"];
					if($paramsArray["act"]=="Mpost"){
						$PEI["parID"]			= "0";	
						$PEI["repId"]			= "0";	

						$this->addnewCPDoc($PEI,$this->CMID);					
					}elseif($paramsArray["act"]=="post"){
						$PEI["parID"]			= $this->inputParams['parSubId'];	
						$PEI["repId"]			= "0";	
						$this->templateCMAjax	= "cm_list_main_post_detail.html";
						//get number post for this main post-----------------------------------------------------------------------------------------------------------				

							$rsAllReplayNr	= $this->getCountPost($this->inputParams['parSubId']);

							if (!$rsAllReplayNr->EOF()) {						
								$replay_nrR	= $rsAllReplayNr->Field("post_tot");			
							}else
								$replay_nrR	= 0;		
							WebApp::addVar("MainCMReplayNr", "".$replay_nrR."");

						//get number post for this main post-----------------------------------------------------------------------------------------------------------

						$this->addnewCPDoc($PEI,$this->inputParams['itemSubId']);
					}
					
				} 

				$workingCi->saveGeneralInformation($paramsArray,$called_by_front="yes");

				$PEI["user_id"] = $this->userId;

				//$workingCi->saveCMprop($PEI,$called_by_front="yes");
				$update_content_general="
					UPDATE content
					   SET
							ci_type							= '".ValidateVarFun::f_real_escape_string($paramsArray["doc_type"])."',
							ci_mi_id						= '".ValidateVarFun::f_real_escape_string($PEI["ci_mi_id"])."',
							parent_content_id				= '".ValidateVarFun::f_real_escape_string($PEI["parentId"])."',
							forum_kategori_id				= '".ValidateVarFun::f_real_escape_string($PEI["mainCi"])."'
							
					 WHERE content_id = ".$PEI["content_id"]."";

				WebApp::execQuery($update_content_general);	
				//save content
				if (isset($this->inputParams['comment'])) {

					$tst = $this->inputParams['comment'];
					$tst = WebApp::parseContentHTMLtoDB($tst);

					$update_content_C = "UPDATE content
												SET contentLng" . $PEI["lng_id"] . "_new = '" . ValidateVarFun::f_real_escape_string($tst) . "'
											  WHERE content_id = " . $PEI["content_id"] . "";
					WebApp::execQuery($update_content_C);
				}

				//if($this->returnNemProp['is_approve_required']=="no"){
						$workingCi->approveCI();
				//}		
				$this->errorrCodeN	=1; //ok
			}else{
				//$this->errorrCodeN	=0;	// a problem has occurred, the comment was empty
				if($this->inputParams['act']=="Mpost"){
						$this->templateCMAjax	= "cm_list_detail_forum_tema.html";
						
				}elseif($this->inputParams['act']=="post"){	
						$this->templateCMAjax	= "cm_list_main_post_detail.html";
				}
			}
		}else{
			$this->errorrCodeN	=0;	// a problem has occurred
	
		}
		//return $errorrCode."###".$this->CMType;		
	}
	
	function getCMconfiguration($lectureID="") 
	{
		global $global_cache_dynamic,$cacheDyn, $mob_web;
		$properties = array();
		
		$this->errorCodeCM = 1; //sukses
		
		$properties["ci_mi_id"]		 				= "";	 //kjo do percaktoje llojin e cm
		$properties["parent_content_id"]		 	= "";	 //kjo do percaktoje llojin e cm
		$properties["forum_kategori_id"]		 	= "";	 //kjo do percaktoje llojin e cm
		
			$getProp = "SELECT ci_mi_id, 	
						   coalesce(parent_content_id,0) as parent_content_id,
						   coalesce(forum_kategori_id,0) as forum_kategori_id,
						   coalesce(author,'') as authorCom,
						   coalesce(content.templateId,'') as templateId,
						   
						   coalesce(ci_type) as ci_type,

							IF (creation_date IS NOT NULL AND creation_date !='' ,DATE_FORMAT(creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p.%w')  ,'') 
								as source_creation_time_array,								   


							if (DATE_FORMAT(creation_date,'%u.%Y')=DATE_FORMAT(now(),'%u.%Y'),DATEDIFF(now(),creation_date),
								if (DATE_FORMAT(creation_date,'%m.%Y')=DATE_FORMAT(now(),'%m.%Y'),'sameMonth'  , 
								
								if (DATE_FORMAT(creation_date,'%c.%Y')=DATE_FORMAT(now(),'%c.%Y'),'sameYear','dateGrid'
								)))     

								as diffDated,							   
						   
						   
							id_zeroNivel, id_firstNivel, id_secondNivel, id_thirdNivel, id_fourthNivel,
							
							title".$this->lang." as title, 

						IF((description".$this->lang.$this->thisMode." IS NULL  OR description".$this->lang.$this->thisMode." = ''),'', description".$this->lang.$this->thisMode.")
							AS description,
						
						coalesce(content".$this->lang.$this->thisMode.",'') as ci_content							
						   
					  FROM content

					 WHERE content.content_id=".$this->CMID."";		

		$rs = WebApp::execQuery($getProp);
	
		if (!$rs->EOF()) {

					//$this->CMDocType 				= $rs->Field("ci_type");
					
					$this->CMType 					= $rs->Field("ci_mi_id");
					$this->templateIdForum 			= $rs->Field("templateId");
					
					$this->CMmainDocID 				= $rs->Field("forum_kategori_id");	
					$this->CMparentDocID 			= $rs->Field("parent_content_id");	
					
					$this->MainDocType				= $rs->Field("ci_type");

					$this->CMtitle					= $rs->Field("title");	
					$this->CMdescription			= $rs->Field("description");	
			
			//href tag-------------------------------------------------------------------------------------------------------------------------------------
					IF ($global_cache_dynamic == "Y") {
						
						$this->CMmainDocIDhrefTo = $cacheDyn->get_CiTitleToUrl($this->CMmainDocID, $this->lngId, "", "",$this->koord_level_node_param);
						$this->CMparentDocIDhrefTo = $cacheDyn->get_CiTitleToUrl($this->CMparentDocID, $this->lngId, "", "",$this->koord_level_node_param);
					
					} else {
						
						$this->CMmainDocIDhrefTo = "javascript:GoTo('thisPage?event=none.srm(k=".$this->CMmainDocID.";kc=".$this->kc.")')";
						$this->CMparentDocIDhrefTo = "javascript:GoTo('thisPage?event=none.srm(k=".$this->CMparentDocID.";kc=".$this->kc.")')";
					}
			//href tag-------------------------------------------------------------------------------------------------------------------------------------
			
			//author DATA----------------------------------------------------------------------------------------------------------------------------------
					$authorCom								= $rs->Field("authorCom");
					$ciDoublinCoreProp["authorOfComm"] 		= $authorCom;					
					
					if ($authorCom!=""){
						$this->authorOfCommArr[$authorCom] = $authorCom;
						$this->getAuthorComm();
					}	
					$this->CMUSERID = $authorCom;
	
			//author DATA----------------------------------------------------------------------------------------------------------------------------------
			
			//get number post for this main post-----------------------------------------------------------------------------------------------------------
				
					/*$replay_nrM						= $this->getCountPost($this->CMID);
					WebApp::addVar("MainCMReplayNr", "".$replay_nrM."");*/
					
			//get number post for this main post-----------------------------------------------------------------------------------------------------------
			//get other properties-------------------------------------------------------------------------------------------------------------------------
					if($rs->Field("ci_type")=="CP"){
						$getCPProp = "SELECT coalesce(post_status,'') as post_status,
												coalesce(report_reason,'') as report_reason,
												coalesce(report_comment,'') as report_comment,
												coalesce(report_author,'') as report_author												   
									FROM cp_data
									WHERE content_id='".$this->CMID."'
									AND lng_id='".$this->lngId."'
									AND statusInfo='".$this->thisModeCode."'
									";		
						$rsCPProp = WebApp::execQuery($getCPProp);
						if (!$rsCPProp->EOF()) {
								$post_statusM		= $rsCPProp->Field("post_status");
								$report_reasonM		= $rsCPProp->Field("report_reason");
								if($report_reasonM	!="none_sel"){							
									$report_reasonM		= "{{_".$report_reasonM."_reason}}";
								}else
									$report_reasonM		= "{{_other_reason}}";							
								
								$report_commentM	= $rsCPProp->Field("report_comment");
								$report_authorM		= $rsCPProp->Field("report_author");

								WebApp::addVar("post_statusM", 	"".$post_statusM."");
								WebApp::addVar("report_reasonM", "".$report_reasonM."");
								WebApp::addVar("report_commentM","".$report_commentM."");
								WebApp::addVar("report_authorM", "".$report_authorM."");							
						}else{
								WebApp::addVar("post_statusM", 	 "");	
								WebApp::addVar("report_reasonM", "");
								WebApp::addVar("report_commentM","");
								WebApp::addVar("report_authorM", "");								
						}
					}elseif($rs->Field("ci_type")=="CT"){
						//gjeje lecture ID qe te shohim a kemi te drejta mbi kete nyje, nese useri eshte moderator apo jo
						//get parent id------------------------------------------------------------------
								//first get lecture coordinate
								$arrayNode	= $this->getParentNode($this->CMID);
								//second get content id of the lecture 
								if(count($arrayNode)>0){
									$lectureIDM	= $this->getParentNodeCID($arrayNode);
								}
								//get parent id------------------------------------------------------------------
								if($lectureIDM>0) {
									require_once(INC_PATH . 'oot.session.base.class.php');
									$sessUserObj = new eLearningUserPlatform();
									$sessUserObj->initCiReference($lectureIDM);
									WebApp::addVar("lectureIDM", $lectureIDM);	
/*echo "sessUserObj------<textarea>";	
print_r($sessUserObj);
echo "</textarea>";*/
									
								}			
						//get parent id------------------------------------------------------------------
					
					
					
					
					
					}elseif($rs->Field("ci_type")=="CF"){
					
					
					}
					
					if($this->CFact == "Mpost" || $this->CFact == "MpostList"){//get all post of a main post
						WebApp::addVar("userWrite", "n");	
						
						if($lectureID>0) {
								require_once(INC_PATH . 'oot.session.base.class.php');
								$sessUserObj = new eLearningUserPlatform();
								$sessUserObj->initCiReference($lectureID);	
								
								if(isset($sessUserObj->appRelSt["MainNodeCi"]["read_write"]) && $sessUserObj->appRelSt["MainNodeCi"]["read_write"]=="W" ){
									//the user has the W right, he can remove a report or turn it back to his previously state
									WebApp::addVar("userWrite", "y");	
								}		
						}else{
								WebApp::addVar("userWrite", "n");
						}
						
					}
					
			//get other properties-------------------------------------------------------------------------------------------------------------------------
			
			//content----------------------------------------------------------------------------------------------------------------------
					$ci_content			= $rs->Field("ci_content");	
					$this->CMContent 	= CiManagerFe::checkDocHtml($ci_content,$this->CMDocType);
										
			//content----------------------------------------------------------------------------------------------------------------------
			
			//creation date format-----------------------------------------------------------------------------------------------------------
					$diffDated 					= $rs->Field("diffDated");
					$source_creation_time_array = $rs->Field("source_creation_time_array");
					
					$this->dataLifeStyleFormat("cmMainDate_".$this->CMID,$diffDated, $source_creation_time_array, $this->CMID);	
			//creation date format-----------------------------------------------------------------------------------------------------------

			//nivel -------------------------------------------------------------------------------------------------------------------------
					$this->CMNodeID["l0"] 	= $rs->Field("id_zeroNivel");	
					$this->CMNodeID["l1"] 	= $rs->Field("id_firstNivel");	
					$this->CMNodeID["l2"] 	= $rs->Field("id_secondNivel");	
					$this->CMNodeID["l3"] 	= $rs->Field("id_thirdNivel");	
					$this->CMNodeID["l4"] 	= $rs->Field("id_fourthNivel");	
			//nivel -------------------------------------------------------------------------------------------------------------------------
			
					$tmp["data"] = array();
					
					$tmp["data"][0]["authorCom"]	 = $authorCom;
					$tmp["data"][0]["CMtitle"] 		 = $this->CMtitle;
					$tmp["data"][0]["CMdescription"] = $this->CMdescription;
					$tmp["data"][0]["cmid"] 		 = $this->CMID;
			
			
			$tmp["AllRecs"] =count($tmp["data"]);
			WebApp::addVar("MainCMdata",$tmp);		

		} else
			$this->errorCode = 2;  //gabim, ci qe e kerkoi konfigurimin nuk eshte e tipit te duhur
	}	
	
	function getCMDataList() 
	{


			if ($this->MainDocType=="CC") {
				
				$this->ci_mi_id = 2;	
				$this->listTemplate = 'cm_list_all_forums.html';
				//function ---[get forum list]
				$this->CFact	= "ListForum";
				$this->getForumList();

				//get post number for all topic in the forum (count only the child post not the main )----------					
					WebApp::addVar("forum_total_postNr", "".$this->forum_total_postNr."");
				//get post number for all topic in the forum (count only the child post not the main )----------
			
			} elseif ($this->MainDocType=="CF") {
				
				$this->ci_mi_id = 3;	
				
				if($this->templateFileName!="template_default.html")
					$this->listTemplate	= $this->templateFileName;
				else
					$this->listTemplate = 'cm_list_detail_forum.html';


				//function ---[get forum details and themes list]
				$this->CFact	= "ListTopics";
				$this->getForumDetails();

				//get post number for this topic (count only the child post not the main )----------
					WebApp::addVar("topic_total_postNr", "".$this->topic_total_postNr."");
				//get post number for this topic (count only the child post not the main )----------				
				

			} elseif ($this->MainDocType=="CT") {//shko mblidh te gjitha postet per kete teme te caktuar
				
				$this->ci_mi_id = 4;
				$this->listTemplate = 'cm_list_detail_forum_tema.html';
				//function ---[get themes detail and post list]
				$this->CFact	= "MpostList";

				if($this->thisMode=="") {//everytime that show the main post list incremt with 1 dhe viewd list,if the content is draft won't increment
						
					CiManagerFe::insertCiStatistics($this->CMID, $this->lngId,"viewed_in_list"); 		
				}
				$this->getForumPost0ListDetails();
	
			} elseif ($this->MainDocType=="CP") {		
				//function ---[get post detail]
				$this->getForumTemaPostDetails();
	
			}/*  elseif ($this->CMType==6) {	
				
				if(isset($this->parentReplayID) && $this->parentReplayID!="")
					$this->getForumTemaPostReplay($this->parentReplayID);
			
			}*/	
 			
	}
	
	function getForumList() 
	{
		global $session,$event;
		
		
		$this->recPages = 100;
		$this->filterToSqlArrayState = $this->filterToSqlArray;

		//echo $session->Vars["level_0"]."--".$session->Vars["level_1"]."--".$session->Vars["level_2"]."--".$session->Vars["level_3"]."--".$session->Vars["level_4"];
		//percaktojm koordinatat e kerkimit, programi mbledh te gjithe forumet e modulit dhe leksioneve,moduli mbledh te gjitha forumet e leksioneve etj
		$this->joinTablesArray['CF']	= " LEFT JOIN cf_data on cf_data.content_id = c.content_id  AND cf_data.lng_id = '".$this->langId."'";	
		$this->filterToSqlArray[] 		= $this->getFilterCoord("",2);	//search for CT item only in the parent node	
		$this->CreateDefaultFilterToSqlPers();
		$this->CMDocType	= "CF";
				
		$this->ConstructDataList();
	}
	function getForumDetails() 
	{
		global $session,$event;
/*		
echo "nem_prop---<textarea>";
print_r($this->nem_prop);
echo "</textarea>";		*/
		
	foreach ($this->nem_prop as $key => $value) {
		
		if (substr($key, -6) == '_label') {
			WebApp::addVar($key."_lb", "$value");
		}

		if (substr($key, 0,5) == 'show_') {
			WebApp::addVar($key, "$value");
		}
	}

	if($this->nem_prop['total_post_label']!="")
		WebApp::addVar("k",$this->nem_prop['total_post_label']);	
	else
		WebApp::addVar("k",$this->nem_prop['total_post_label']);
		
		
		//get forum type [CF] attribute
		$this->documentExtraAttribute('CF');
		
		if(isset($this->nem_prop['nr_of_items']) && $this->nem_prop['show_navigation']=="yes")
			$this->recPages = $this->nem_prop['nr_of_items'];
		else
			$this->recPages = 100;


		
		$this->NrPage = "1";
		if ((isset($event->args["rpp"]) && $event->args["rpp"]!="")) {	
			$this->NrPage = $event->args["rpp"];
		} elseif (isset($_GET["rpp"]) && $_GET["rpp"]!="") {
			$this->NrPage = $_GET["rpp"];
		}				
		
		$this->filterToSqlArrayState = $this->filterToSqlArray;
		/*$this->filterToSqlArray[] = " ci_mi_id = '".$this->ci_mi_id."'";
		$this->filterToSqlArray[] = " forum_kategori_id = '".$this->CMID."'";
		$this->filterToSqlArray[] = " parent_content_id = '".$this->CMID."'";*/
		$this->joinTablesArray['CT']	= " LEFT JOIN ct_data on ct_data.content_id = c.content_id  AND ct_data.lng_id = '".$this->langId."'";	
		$this->filterToSqlArray[] 		= $this->getFilterCoord("",3);	//search for CT item only in the parent node	
		$this->CreateDefaultFilterToSqlPers();
		$this->CMDocType	= "CT";

		$this->ConstructDataList();
			
		if (isset($this->propertiesExtended[$this->CMID]) && count($this->propertiesExtended[$this->CMID]) > 0) {
            reset($this->propertiesExtended[$this->CMID]);
            while (list($key, $value) = each($this->propertiesExtended[$this->CMID])) {
                WebApp::addVar("$key", "$value");
            }
        }
	}
	function getForumPost0ListDetails() 
	{
		$this->filterToSqlArrayState 	= $this->filterToSqlArray;					
		
		$this->select_sql_param			=	"coalesce(cp_data.post_status,'') as post_status,coalesce(cp_data.report_reason,'') as report_reason,coalesce(cp_data.report_comment,'') as report_comment,coalesce(cp_data.report_author,'') as report_author,";
		$this->filterToSqlArray[] 		= " cp_data.topic_id = '".$this->CMID."'";
		$this->filterToSqlArray[] 		= " cp_data.parentId = '0'";
		$this->joinTablesArray['CP']	= " LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->langId."'";		 
		$this->CreateDefaultFilterToSqlPers();
		$this->CMDocType				= "CP";
		
		$this->ConstructDataList();
	
	}	
	
	function getForumPostDetail() 
	{

		$this->filterToSqlArrayState 	= $this->filterToSqlArray;				

		$this->select_sql_param			=	"coalesce(cp_data.post_status,'') as post_status,coalesce(cp_data.report_reason,'') as report_reason,coalesce(cp_data.report_comment,'') as report_comment,coalesce(cp_data.report_author,'') as report_author,";
		$this->filterToSqlArray[] 		= " cp_data.parentId = '".$this->CMID."'";
		$this->joinTablesArray['CP']	= " LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->langId."'";
		 
		$this->CreateDefaultFilterToSqlPers();
		$this->CMDocType				= "CP";
		$this->ConstructDataList();

	}
	
	function getForumTemaPostReplay($parentID="") 
	{
		reset($this->filterToSqlArray);
		$this->tmpInfoArray=array();

		if($parentID>0){
			$this->filterToSqlArray			= array();
			$this->filterToSqlArrayState 	= $this->filterToSqlArray;
			$this->filterToSqlArray[] 		= " cp_data.parentId = '".$parentID."'";
			$this->joinTablesArray['CP']	= " LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->langId."'";
			
			$this->CreateDefaultFilterToSqlPers();
			$this->CMDocType	= "CP";
			$this->ConstructDataList();
	
			$indexz = 0;
				$gridDataSrcTempReplay = array(
					"data" 			=>  array(),
					"AllRecs" 		=> "0"
				);

				if(count($this->tmpInfoArray["list"]["CI_DATA"])>0){
					While (list($keyX,$valueX)=each($this->tmpInfoArray["list"]["CI_DATA"])) {
						
						$gridDataSrcTempReplay["data"][$indexz++] = $valueX;					
					}
					if(count($gridDataSrcTempReplay["data"])>0){
						$gridDataSrcTempReplay["AllRecs"] =count($gridDataSrcTempReplay["data"]);
					}
				}
				WebApp::addVar("SubgridCmmData_".$parentID,$gridDataSrcTempReplay);	
	
		}
	}
	
	function getForumTemaPostDetails() 
	{
		
	}	

	function getCommentsList() 
	{
		global $session,$event;
		
		
		$this->recPages = 10;
		$this->filterToSqlArrayState = $this->filterToSqlArray;
		
		$this->listTemplate = 'cm_list_all_comments.html';
	
		/*$this->filterToSqlArray[] = " ci_mi_id = '".$this->ci_mi_id."'";
		$this->filterToSqlArray[] = " forum_kategori_id = '".$this->CMID."'";	*/	
		$this->CreateDefaultFilterToSqlPers();
		$this->ConstructDataList();
	}
	
	function documentExtraAttribute($ci_type="") {
		global $session;
		
		require_once(INCLUDE_AJAX_PATH . 'CiManagerFe.class.php');		
		$workingCiFe= new CiManagerFe($this->CMID);
			
		if($ci_type=="CF"){//get forum attribute
 
		   $properties = $workingCiFe->getCFProperties($ci_type,"1");
   
		   //show or not the button to add new theme
			$this->propertiesExtended[$this->CMID]['showBtn']	= "n";
			  if ($properties['new_theme']=="all_users"){//show the button to all users
					$this->propertiesExtended[$this->CMID]['showBtn']	= "y";
			   }elseif($properties['new_theme']=="all_users_rights"){//show the button to all users
					$this->propertiesExtended[$this->CMID]['showBtn']	= "n";
					//get user profile rights uppon that document (forum document)
					$this->getUserProfile($workingCiFe->lang,$workingCiFe->thisMode);
					
			   }else{//hide
					$this->propertiesExtended[$this->CMID]['showBtn']	= "n";
			   }
			   
			//nr_theme the max number of theme per page 
			if($properties['nr_theme']>0)
				$this->propertiesExtended[$this->CMID]['nr_theme']	= $properties['nr_theme'];
			else
				$this->propertiesExtended[$this->CMID]['nr_theme']	= "5";
				
			//notify_email ???
			//$properties['notify_email']
/*
[content_id] => 
[sequence_id] => 
[new_theme] => all_users
[nr_theme] => 5
[notify_email] => 
*/		   
		   
/*echo "propertiesExtended---<textarea>"	;	
print_r($this->propertiesExtended);
echo "</textarea>"	;	*/
		
		}

	}
	function getUserProfile ($lang="",$thisMode="") {
		global $session;
		
		//meren profilet e userit --------------------------------------------------------------
			
			$bashkesia_user_profil_id = "";
			if (isset($session->Vars["ses_userid"]) and ($session->Vars["ses_userid"] != ""))
				   {$ses_userid = $session->Vars["ses_userid"];}
			else
				   {$ses_userid = 2;}
						 
			 
			if ($ses_userid != "2")
			{
				 //kur nuk eshte user webi --------------------------------------------------
					 $getUserProfil = "SELECT profil_id
											 FROM user_profile
											WHERE UserId = '".$ses_userid."'";									 
					 $rsUserProfil	= WebApp::execQuery($getUserProfil);

					 while (!$rsUserProfil->EOF()) 
						   {
							$profil_id = $rsUserProfil->Field("profil_id");
							$bashkesia_user_profil_id .= ",".$profil_id;
							$rsUserProfil->MoveNext();
						   }
					 if ($bashkesia_user_profil_id != "")
						{
						 $bashkesia_user_profil_id = substr($bashkesia_user_profil_id, 1);
						}
			}
		//--------------------------------------------------------------------------------------
		
		//meren atributet ------------------------------------------------------------------------
			if($this->CMID>0)
				$f_content_id = $this->CMID;
			else
				$f_content_id = $session->Vars["contentId"];

			 $f_mi_prop     = "";
			 $f_title       = "";
			 $f_description = "";


			$getForum = "SELECT mi_prop".$lang.$thisMode." as f_mi_prop,
							   IF(title".$lang." IS NULL, '', title".$lang.")  as f_title,
							   IF(description".$lang.$thisMode." IS NULL, '', description".$lang.$thisMode.")  as f_description,
							   id_zeroNivel   as f_id_zeroNivel,
							   id_firstNivel  as f_id_firstNivel,
							   id_secondNivel as f_id_secondNivel,
							   id_thirdNivel  as f_id_thirdNivel,
							   id_fourthNivel as f_id_fourthNivel
							  FROM content 
							 WHERE content_id = '".$f_content_id."'";											
			$rsForum	= WebApp::execQuery($getForum);
		
			if (!$rsForum->EOF())
			{
				 $f_mi_prop        = $rsForum->Field("f_mi_prop");
				 $f_title          = $rsForum->Field("f_title");
				 $f_description    = $rsForum->Field("f_description");
				 $f_id_zeroNivel   = $rsForum->Field("f_id_zeroNivel");
				 $f_id_firstNivel  = $rsForum->Field("f_id_firstNivel");
				 $f_id_secondNivel = $rsForum->Field("f_id_secondNivel");
				 $f_id_thirdNivel  = $rsForum->Field("f_id_thirdNivel");
				 $f_id_fourthNivel = $rsForum->Field("f_id_fourthNivel");
			}
       //----------------------------------------------------------------------------------------		
		
		//shikohet nese useri ka te drejte te shkruaje ne nyjen ku ndodhet forumi ---------------
		    if ($bashkesia_user_profil_id != "")
			{	
			   $getUserRights = "SELECT count(*) as user_rights_write
											 FROM profil_rights
											WHERE profil_id IN (".$bashkesia_user_profil_id.") AND
													id_zeroNivel   = '".$f_id_zeroNivel."'  AND
													id_firstNivel  = '".$f_id_firstNivel."'  AND
													id_secondNivel = '".$f_id_secondNivel."' AND
													id_thirdNivel  = '".$f_id_thirdNivel."'  AND
													id_fourthNivel = '".$f_id_fourthNivel."' AND
													`read_write`     = 'W'";											
				$rsUserRights	= WebApp::execQuery($getUserRights);

			   if (!$rsUserRights->EOF() AND mysql_errno() == 0)
				  {
				   $user_rights_write = $rsUserRights->Field("user_rights_write");
				   if ($user_rights_write > 0)
					  {$this->propertiesExtended[$this->CMID]['showBtn']	= "y";}
				  }
			}
        //--------------------------------------------------------------------------------------

	}
	
	function ConstructDataList () {
		global $session;

		if(count($this->filterToSqlArray)>0)
			$this->filterToSql = "AND ".implode("\n AND ", $this->filterToSqlArray);
			$this->joinTables = " ".$this->joinTablesArray[$this->CMDocType];

		$getCnt = "
			SELECT count(distinct (c.content_id)) as cntid
			  FROM content	AS c
		    
															
				  ".$this->joinTables."
			
			
			WHERE c.content_id in 
			(					SELECT c.content_id
				 				 FROM				profil_rights	AS p
				 							JOIN	nivel_4			AS n4	ON (    p.id_zeroNivel   = n4.id_zeroNivel
				 																AND p.id_firstNivel  = n4.id_firstNivel
				 																AND p.id_secondNivel = n4.id_secondNivel
				 																AND p.id_thirdNivel  = n4.id_thirdNivel
				 																AND p.id_fourthNivel = n4.id_fourthNivel
				 																)
				 							JOIN	content			AS c	ON (    n4.id_zeroNivel  = c.id_zeroNivel
				 																AND n4.id_firstNivel  = c.id_firstNivel
				 																AND n4.id_secondNivel = c.id_secondNivel
				 																AND n4.id_thirdNivel  = c.id_thirdNivel
				 																AND n4.id_fourthNivel = c.id_fourthNivel
				 																)
				 
								WHERE p.profil_id in ('".$this->tip."')
								  AND n4.description".$this->lang."".$this->thisMode." IS NOT NULL 
								  AND n4.description".$this->lang."".$this->thisMode." !=''
								  AND ci_type in ('".$this->CMDocType."')
								  ".$this->joinConditions."		
								  
								  
					 		UNION DISTINCT
				 				
				 				SELECT c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							
				 							
				 							JOIN	content			AS c	ON (    p.content_id  = c.content_id)


				 							JOIN	nivel_4			AS n4	ON (    c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 																)
				 
								WHERE p.profil_id in (".$this->tip.")
								  AND n4.description".$this->lang."".$this->thisMode." IS NOT NULL 
								  AND n4.description".$this->lang."".$this->thisMode." !=''
								  AND ci_type in ('".$this->CMDocType."')
								  ".$this->joinConditions."		
			)			

				  ".$this->filterToSql."
				  ".$this->filterToSqlSubjectNode." ";
				  
		$this->sqlCount	=  $getCnt;	
		$rsGetCnt = WebApp::execQuery($getCnt);

		$this->CountItems = $rsGetCnt->Field("cntid");
		
		//---count how many main post we have--------------------------------------------------------------------------------------
		WebApp::addVar("mainPostTot","empty");
		if($this->CFact	== "MpostList"){
			if($this->CountItems>0)	
				WebApp::addVar("mainPostTot",$this->CountItems);
			else
				WebApp::addVar("mainPostTot","0");
		}
		//---count how many main post we have--------------------------------------------------------------------------------------
		
		
		if ($this->CountItems==-1) {
			WebApp::addVar("AllRecs","error");
		} elseif ($this->CountItems==0) {
			WebApp::addVar("AllRecs","empty");
		} else {
			WebApp::addVar("AllRecs","full");
			/*if (($this->recPages=="" && $this->CountItems>251) || ($this->recPages>250 && $this->CountItems>251)) {
				if(defined('recPagesMaxList') && recPagesMaxList>0) {
					$this->recPages = recPagesMaxList;
				} else {
					$this->recPages = 250;
				}
			}*/
			$this->CreateLimitToSql();
			$this->listDataArticles();
		}

		WebApp::addVar("CountItems",$this->CountItems);
		WebApp::addVar("TotPage",$this->TotPage);
		WebApp::addVar("NrPage",$this->NrPage);
		WebApp::addVar("FromRecs",$this->FromRecs);
		WebApp::addVar("ToRecs",$this->ToRecs);
		WebApp::addVar("previewsPage",$this->previewsPage);
		WebApp::addVar("recPage",$this->recPages);
		WebApp::addVar("nextPage",$this->nextPage);
			
		if (isset($this->error_code_description[$this->error_code]))
			WebApp::addVar("error_code_to_html", $this->error_code_description[$this->error_code]);
		else
			WebApp::addVar("error_code_to_html", "SomeErrorOccurred");
			
		WebApp::addVar("gridDataSrc", $this->gridDataSrc);

		WebApp::addVar("rp",$this->recPages);
		WebApp::addVar("rpp",$this->NrPage);
	
		//PER AUDIT TRAIL dhe per MSV qe kalon si variabel
		//$msvAuditTrail["sts"] = 			$this->nemID;			//	sts			nem id
		//$msvAuditTrail["gp"] = 				$this->NrPage;		//	gp			search_faqja_korrente
		//$msvAuditTrail["Cnt"] = 			$this->CountItems;	//	CountItems	search_nr_rekordeve
		if ($this->termSearch!="") {

			$msvAuditTrail["ft"] = 		$this->termSearch;	//	termSearch	search_nr_rekordeve
			//$MSparamsA["termSearch"]=$this->termSearch;
			$MSparams = base64_encode(serialize($msvAuditTrail));
			WebApp::addVar("msv","$MSparams");		
	
		} else {
			WebApp::addVar("msv","");		
		}
		$recPages	= $this->recPages;
		$NrPage		= $this->NrPage;
		$CountItems	= $this->CountItems;
		$TotPage	= $this->TotPage;
		$FromRecs	= $this->FromRecs;
		$ToRecs		= $this->ToRecs;
	}
	
	function CreateDefaultFilterToSqlPers ()
	{
		
		$dataMoment = $this->CURDATE; 
		$dataMoment = date("Y-m-d");
		$dataMomentInfo = explode("-",$dataMoment);

		if (in_array("EI",$this->ciTypesArray)) {
			
			$this->joinTablesEI = " LEFT JOIN ei_data ON (c.content_id = ei_data.content_id)";
			
			if (count($this->ciTypesArray)==1) {
				//formo filtrat vetem per EI
			
					if (isset($this->time_period) && $this->time_period=="1") {//_current, last_year

						$currentFromStart = date("Y")."-01-01";
						$currentToEnd = date("Y")."-12-31";
						
						$this->filterToSqlArray["dataFilter"] ="ei_data.from_dateEvent <='".$currentToEnd."' and ei_data.from_dateEvent>='".$currentFromStart."'";			

					} elseif (isset($this->time_period) && $this->time_period=="2") {//_archive

						$last_year = date("Y")-1;
						$currentToEnd = $last_year."-12-31";
						$this->filterToSqlArray["dataFilter"] ="ei_data.from_dateEvent <='".$currentToEnd."'";				

					} elseif (isset($this->time_period) && $this->time_period=="3") {//this week

						//$this->filterToSqlArray["dataFilter"] =" ('".$dataMoment."' <= c.scheduling_to OR '0000-00-00' = c.scheduling_to) ";

						$currentFromStart = date("Y")."-".date("m")."-01";
						$currentToEnd = date("Y-m-d");

						$currentFromStart = date("Y")."-".date("m")."-".date("d");

						$this->filterToSqlArray["dataFilter"] ="
									  DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d') 
								  AND DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -7 DAY), '%Y-%m-%d')";

					} elseif (isset($this->time_period) && $this->time_period=="4") {//this month

						
						// [CURDATE] => 2013-11-01
						
						
						$currentFromStart = date("Y")."-".date("m")."-".date('t');  //this month  duke u nisur nga dt 2013-02-28 gjejme fillimi ne saj 2013-02-01
						
						$this->filterToSqlArray["dataFilter"] ="
									  DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d') 
								  AND DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -1 MONTH), '%Y-%m-%d')";
					
					
					
					
					
					} elseif ($this->schedulingFromStart!="" && $this->schedulingFromEnd) {

							$schedulingFromStart = $this->returnMysqlDate($this->schedulingFromStart);
							$schedulingFromEnd = $this->returnMysqlDate($this->schedulingFromEnd);

							$this->filterToSqlArray["dataFilter"] ="
										  DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  >= DATE_FORMAT('".$schedulingFromStart."', '%Y-%m-%d') 
									  AND DATE_FORMAT(ei_data.to_dateEvent, '%Y-%m-%d') <= DATE_FORMAT('".$schedulingFromEnd."', '%Y-%m-%d') ";

					} else if ($this->thisMode=='') {
							$this->filterToSqlArray["dataFilter"] ="
							 DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  >= ".$dataMoment." ";
					} 
			
			
			
			} else {
				//FORMO FILTRAT PER EI, DHE TIPET E TJERA
			

		$dataMoment = $this->CURDATE; $dataMoment = date("Y-m-d");
		$dataMomentInfo = explode("-",$dataMoment);


							if (isset($this->time_period) && $this->time_period=="1") {//_current, last_year

								$currentFromStart = $dataMomentInfo[0]."-01-01";
								$currentToEnd = $dataMomentInfo[0]."-12-31";

								$this->filterToSqlArray["dataFilter"] ="
								   if (ci_type='EI',
											 ei_data.from_dateEvent <='".$currentToEnd."' and ei_data.from_dateEvent>='".$currentFromStart."',			
										(c.scheduling='Y' 
											AND 
												(
													(c.scheduling_from<='".$currentToEnd."' AND c.scheduling_from>='".$currentFromStart."') 
													AND 
													(c.scheduling_to<='".$currentToEnd."' OR c.scheduling_to='0000-00-00')
												)
										) 
												OR 
										(c.scheduling='N' AND c.scheduling_from<='".$currentToEnd."' AND c.scheduling_from>='".$currentFromStart."')
								) ";			

							} elseif (isset($this->time_period) && $this->time_period=="2") {//_archive

								$last_year = $dataMomentInfo[0]-1;
								$currentToEnd = $last_year."-12-31";

								$this->filterToSqlArray["dataFilter"] ="
								   if (ci_type='EI',
											 ei_data.from_dateEvent <='".$currentToEnd."',			
									(c.scheduling='Y' 
										AND 
											(
												(c.scheduling_from<='".$currentToEnd."') 
												AND 
												(c.scheduling_to<='".$currentToEnd."' OR c.scheduling_to='0000-00-00')
											)
									) 
											OR 
									(c.scheduling='N' AND c.scheduling_from<='".$currentToEnd."')
								) ";				

							} elseif (isset($this->time_period) && $this->time_period=="3") {//this week

								$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-01";
								$currentToEnd = $dataMoment;

								$currentFromStart = date("Y")."-".date("m")."-".date("d");
								$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-".$dataMomentInfo[2];

								$this->filterToSqlArray["dataFilter"] ="

								   if (ci_type='EI',
											  DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d') 
										  AND DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -7 DAY), '%Y-%m-%d'),			

									 DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d')
														AND DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -7 DAY), '%Y-%m-%d')
												) ";


							} elseif (isset($this->time_period) && $this->time_period=="4") {//this month

								$currentFromStart = date("Y")."-".date("m")."-".date('t');  //this month  duke u nisur nga dt 2013-02-28 gjejme fillimi ne saj 2013-02-01
								$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-".date('t');
								
								$this->filterToSqlArray["dataFilter"] ="
								   if (ci_type='EI',
											  DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d')  <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d') 
										  AND DATE_FORMAT(ei_data.from_dateEvent, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -1 MONTH), '%Y-%m-%d'),			
									 DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d')
														AND DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -1 MONTH), '%Y-%m-%d')
												) ";
							
							
							
							
							
					} elseif ($this->schedulingFromStart!="" && $this->schedulingFromEnd) {

							$schedulingFromStart = $this->returnMysqlDate($this->schedulingFromStart);
							$schedulingFromEnd = $this->returnMysqlDate($this->schedulingFromEnd);

							$this->filterToSqlArray["dataFilter"] ="
							(
								(c.scheduling='Y' 
									AND 
										(
											(c.scheduling_from<='".$schedulingFromEnd."' AND c.scheduling_from>='".$schedulingFromStart."') 
											AND 
											(c.scheduling_to<='".$schedulingFromEnd."' OR c.scheduling_to='0000-00-00')
										)
								) 
										OR 
								(c.scheduling='N' AND c.scheduling_from<='".$schedulingFromEnd."' AND c.scheduling_from>='".$schedulingFromStart."')
							) ";
					
					} elseif ($this->thisMode=='') {
									$this->filterToSqlArray["dataFilter"] ="
									(
										(c.scheduling='Y' 
											AND 
												(
													(c.scheduling_from<='".$dataMoment."') 
													AND 
													('".$dataMoment."'<=c.scheduling_to OR c.scheduling_to='0000-00-00')
												)
										) 
												OR 
										(c.scheduling='N')
									) ";
					}							
							
			}
		
		} else {
			//formo filtrat per tipet e tjera te ndryshme nga EI
		
			$dataMoment = $this->CURDATE; 
			$dataMoment = date("Y-m-d");
			$dataMomentInfo = explode("-",$dataMoment);		
	
				if (isset($this->time_period) && $this->time_period=="1") {//_current, last_year

						//$this->filterToSqlArray["dataFilter"] =" ('".$dataMoment."' <= c.scheduling_to OR '0000-00-00' = c.scheduling_to) ";

						$currentFromStart = date("Y")."-01-01";
						$currentToEnd = date("Y")."-12-31";

						$currentFromStart = $dataMomentInfo[0]."-01-01";
						$currentToEnd = $dataMomentInfo[0]."-12-31";						

						$this->filterToSqlArray["dataFilter"] ="
								(c.scheduling='Y' 
									AND 
										(
											(c.scheduling_from<='".$currentToEnd."' AND c.scheduling_from>='".$currentFromStart."') 
											AND 
											(c.scheduling_to<='".$currentToEnd."' OR c.scheduling_to='0000-00-00')
										)
								) 
										OR 
								(c.scheduling='N' AND c.scheduling_from<='".$currentToEnd."' AND c.scheduling_from>='".$currentFromStart."')";			

					} elseif (isset($this->time_period) && $this->time_period=="2") {//_archive

						$last_year = date("Y")-1;
						$currentToEnd = $last_year."-12-31";
						
						$last_year = $dataMomentInfo[0]-1;
						$currentToEnd = $last_year."-12-31";						
						

						$this->filterToSqlArray["dataFilter"] ="
							(c.scheduling='Y' 
								AND 
									(
										(c.scheduling_from<='".$currentToEnd."') 
										AND 
										(c.scheduling_to<='".$currentToEnd."' OR c.scheduling_to='0000-00-00')
									)
							) 
									OR 
							(c.scheduling='N' AND c.scheduling_from<='".$currentToEnd."') ";				

					} elseif (isset($this->time_period) && $this->time_period=="3") {//this week

						/*$currentFromStart = date("Y")."-".date("m")."-01";
						$currentToEnd = date("Y-m-d");
						$currentFromStart = date("Y")."-".date("m")."-".date("d");*/
						
						$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-01";
						$currentToEnd = $dataMoment;
						$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-".$dataMomentInfo[2];						
						
						$this->filterToSqlArray["dataFilter"] ="
							 DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d')
						 AND DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -7 DAY), '%Y-%m-%d')";

					
					} elseif (isset($this->time_period) && $this->time_period=="4") {//this month

						$currentFromStart = date("Y")."-".date("m")."-".date('t');  //this month  duke u nisur nga dt 2013-02-28 gjejme fillimi ne saj 2013-02-01
						$currentFromStart = $dataMomentInfo[0]."-".$dataMomentInfo[1]."-".date('t');
						$this->filterToSqlArray["dataFilter"] ="
							 DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') <= DATE_FORMAT('".$currentFromStart."', '%Y-%m-%d')
												AND DATE_FORMAT(c.scheduling_from, '%Y-%m-%d') >= DATE_FORMAT(DATE_ADD('".$currentFromStart."', INTERVAL -1 MONTH), '%Y-%m-%d')";

					
					} elseif ($this->schedulingFromStart!="" && $this->schedulingFromEnd) {

							$schedulingFromStart = $this->returnMysqlDate($this->schedulingFromStart);
							$schedulingFromEnd = $this->returnMysqlDate($this->schedulingFromEnd);

							$this->filterToSqlArray["dataFilter"] ="
							(
								(c.scheduling='Y' 
									AND 
										(
											(c.scheduling_from<='".$schedulingFromEnd."' AND c.scheduling_from>='".$schedulingFromStart."') 
											AND 
											(c.scheduling_to<='".$schedulingFromEnd."' OR c.scheduling_to='0000-00-00')
										)
								) 
										OR 
								(c.scheduling='N' AND c.scheduling_from<='".$schedulingFromEnd."' AND c.scheduling_from>='".$schedulingFromStart."')
							) ";
					} else  if ($this->thisMode=='') {
							$this->filterToSqlArray["dataFilter"] ="
							(
								(c.scheduling='Y' 
									AND 
										(
											(c.scheduling_from<='".$dataMoment."') 
											AND 
											('".$dataMoment."'<=c.scheduling_to OR c.scheduling_to='0000-00-00')
										)
								) 
										OR 
								(c.scheduling='N')
							) ";
					}
		}


		if (isset($this->filterToSqlArray["dataFilter"])) $this->filterToSqlArray["dataFilter"] = "(".$this->filterToSqlArray["dataFilter"].")";


		if ($this->thisMode=='') {
			$this->filterToSqlArray[] =" c.state".$this->lang." not in (0,5,7)";
			$this->filterToSqlArray[] =" c.published".$this->lang." = 'Y'";
			
			/*$this->filterToSqlArray[] =" n.active".$this->lang." != '1' ";
			$this->filterToSqlArray[] =" n.state".$this->lang." != 7 ";*/
		} else {
			$this->filterToSqlArray[] =" c.state".$this->lang." not in (7)";
		}
		
			
		if ($this->get_items_by_CI_rights == "yes") {

			$this->joinTablesArray['rights_privileges'] = " JOIN profil_rights_ci AS p ON (c.content_id  = p.content_id) ";

		} else {

			$this->joinTablesArray['rights_privileges'] = "
					JOIN	profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
									AND p.id_firstNivel  = c.id_firstNivel
									AND p.id_secondNivel = c.id_secondNivel
									AND p.id_thirdNivel  = c.id_thirdNivel
									AND p.id_fourthNivel = c.id_fourthNivel
						) ";
		}

																		
		if ($this->get_favorite_items=="yes") {
		
			$this->joinTablesArray['favorite_items'] = "
														 JOIN ci_user_favorites AS fav 
														   ON (	   c.content_id  = fav.content_id  
														   	   AND fav.lng_id  = '".$this->lngId."'
														       AND fav.UserId = ".$this->ses_userid."
														      ) ";
		}
		
		
		if ($this->get_eshopMyItems=="yes") {
		
			$this->joinTablesArray['eshopMyItems'] = "
														 JOIN z_my_cme_learning AS myEshop 
														   ON (	   c.content_id  = myEshop.content_id  AND myEshop.UserId  = '".$this->ses_userid."'
														   	  
														      ) ";
		}
				
		
		

		
			
		if ($this->get_popular_items=="yes") {
		
			$this->joinTablesArray['popular_items'] = " 
														 JOIN ci_most_popular AS pop 
														   ON (	    c.content_id  = pop.content_id 
														   	    AND pop.lng_id  = '".$this->lngId."'
														        AND pop.rating_value >0
														        
														       ) ";
		}


		//trajto filtrat e ardhur me forme

		if ($this->intervalBegin!="" && $this->intervalEnd!="") {

				$schedulingFromStart = $this->returnMysqlDate($this->intervalBegin);
				$schedulingFromEnd = $this->returnMysqlDate($this->intervalEnd);

				$this->filterToSqlArray["dataSearchFilter"] =" c.scheduling_from<='".$schedulingFromEnd."' AND c.scheduling_from>='".$schedulingFromStart."'";
		
		} elseif ($this->intervalBegin!="") {

				$schedulingFromStart = $this->returnMysqlDate($this->intervalBegin);
				$this->filterToSqlArray["dataSearchFilter"] =" c.scheduling_from>='".$schedulingFromStart."'";
		}elseif ($this->intervalEnd!="") {

				$schedulingFromEnd = $this->returnMysqlDate($this->intervalEnd);
				$this->filterToSqlArray["dataSearchFilter"] =" c.scheduling_from<='".$schedulingFromEnd."'";
		}
		
		
		if ($this->termSearch!="" && $this->termSearch!="") {
		
				$this->filterToSqlArray["contenxtSearch"] ="
				(
						 match (ctxt.context".$this->lang.$this->thisMode.") 		against ('".ValidateVarFun::f_real_escape_string($this->termSearch)."' IN BOOLEAN MODE)
						OR  match (c.title".$this->lang.")	against ('".ValidateVarFun::f_real_escape_string($this->termSearch)."' IN BOOLEAN MODE)
						OR  match (c.description".$this->lang.$this->thisMode.")	against ('".ValidateVarFun::f_real_escape_string($this->termSearch)."' IN BOOLEAN MODE)
				
				
				)";
				
				
		
			$this->joinTablesArray['contenxtSearch'] = "
														 JOIN content_text AS ctxt 
														   ON (	   c.content_id  = ctxt.content_id  
														 
														      ) ";				
				
				
				
				
		
		}
		


/*


						0 as weightUsedToSortKW,
						".$this->orderNrArrayWeight["ctxt.context".$this->ln_search.$this->md_search]." as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.context".$this->ln_search.$this->md_search]."
						)


		$this->lang			= $session->Vars["lang"];
		
		
		$this->lngId 		= str_replace ("Lng","",$this->lang);
		
		
		
		
		$this->thisMode		= $session->Vars["thisMode"];












	intervalEnd		
	termSearch		
	intervalBegin		
echo $this->objNem."<textarea>";
print_r($event->args);
echo "</textarea>";			
			
*/

	}
	
	function listDataArticles () 
	{
		global $session,$node_nedded_data,$sl_file_nedded_data,$templateDefaultForZone,$KeywordsRelatedItemInfo,$global_cache_dynamic,$cacheDyn, $mob_web;
		
		if ($this->sort_by=="0") {
			$sortBy = " DESC ";
		} else {
			$sortBy = " ASC ";
		}

		$this->listPropOrder =array();
		$ciIdsInList =array();

		$this->orderBySql = "  c.creation_date ".$sortBy;	//creation_date
		$this->orderBySql = $this->orderBySql.", content_id  ".$sortBy."";
		
		$this->orderBySql = " ORDER BY ".$this->orderBySql;

			  
			
		IF ($global_cache_dynamic == "Y") {
		} elseif (defined("Caching_Metatags") AND (Caching_Metatags == "Y")) {
		
				$application_is_cached = "no";
				if ($session->Vars["parseBox"]=="true") {
					$cache = new Caching();
					$cache->domain	= APP_URL;
					$cache->lang	= $session->Vars["lang"];
					$cache->readFromConfig();	

					if ($session->Vars["parseBox"]=="true" && $cache->error_code == "0") {
						$application_is_cached = "yes";
					}
				}		
		
		}

		$gridDataSrc = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);	
		$gridDataSrcParams = array();		
		
		$gridDataSrcRelatedKeyowrds = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);

		$displayField			= $this->display;
		$makeLink				= $this->display_link;
		$display_link_label		= $this->display_link_label;	
		
		//$authorOfCommArr = array();

		$orderCiAtribute 			= 1;
	

		$groupByCategory = array();
        


		$getdata = "
			SELECT distinct (c.content_id) as content_id,
			
						concat_ws('_', c.id_zeroNivel, c.id_firstNivel, c.id_secondNivel, c.id_thirdNivel,c.id_fourthNivel) as crd,
						
						c.id_zeroNivel, c.id_firstNivel, c.id_secondNivel, c.id_thirdNivel,c.id_fourthNivel,
						n.orderMenu as nodeOrder,
						c.templateId  as templateId,

						title".$this->lang." as title, 
						filename".$this->lang." AS filename,
						ci_type,
						
						COALESCE(c.with_https,      'n') as with_https,
						
						".$this->select_sql_param."
						
						c.author as authorOfComm,
				
							IF (creation_date IS NOT NULL AND creation_date !='' ,DATE_FORMAT(creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p.%w')  ,'') 
								as source_creation_time_array,								   

							if (DATE_FORMAT(creation_date,'%u.%Y')=DATE_FORMAT(now(),'%u.%Y'),DATEDIFF(now(),creation_date),
								if (DATE_FORMAT(creation_date,'%m.%Y')=DATE_FORMAT(now(),'%m.%Y'),'sameMonth'  , 
								if (DATE_FORMAT(creation_date,'%c.%Y')=DATE_FORMAT(now(),'%c.%Y'),'sameYear','dateGrid'
								)))     
								as diffDated,							   
					
						IF((source_author".$this->lang." IS NULL    OR source_author".$this->lang." = ''),'', source_author".$this->lang.")  
							AS source_author,

						IF((c.description".$this->lang.$this->thisMode." IS NULL  OR c.description".$this->lang.$this->thisMode." = ''),'', c.description".$this->lang.$this->thisMode.")
							AS description,

						IF((doc_source".$this->lang." IS NULL 	OR doc_source".$this->lang." = ''),'', doc_source".$this->lang.")	
							AS source,

						IF((c.imageSm_id >0),c.imageSm_id,'') as imageSm_id,
						coalesce(c.imageSm_id_name, '') as imageSm_id_name,
						
						IF((c.imageSm_id_mob >0),c.imageSm_id_mob,'') as imageSm_id_mob,
						coalesce(c.imageSm_id_mob_name, '') as imageSm_id_mob_name,
						
						IF((c.imageBg_id >0),c.imageBg_id,'') as imageBg_id,
						coalesce(c.imageBg_id_name, '') as imageBg_id_name,
						
						
						IF((c.imageBg_id_mob >0),c.imageBg_id_mob,'') as imageBg_id_mob,
						coalesce(c.imageBg_id_mob_name, '') as imageBg_id_mob_name,
			
						
						coalesce(content".$this->lang.$this->thisMode.",'') as ci_content,
						
						coalesce(ci_report_statistics.viewed,0) as nr_viewd	
			
			 FROM content	AS c
						JOIN	nivel_4			AS n	ON (    c.id_zeroNivel   = n.id_zeroNivel
															AND c.id_firstNivel  = n.id_firstNivel
															AND c.id_secondNivel = n.id_secondNivel
															AND c.id_thirdNivel  = n.id_thirdNivel
															AND c.id_fourthNivel = n.id_fourthNivel
															)
						JOIN	profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
										AND p.id_firstNivel  = c.id_firstNivel
										AND p.id_secondNivel = c.id_secondNivel
										AND p.id_thirdNivel  = c.id_thirdNivel
										AND p.id_fourthNivel = c.id_fourthNivel
							)	
	LEFT JOIN ci_report_statistics ON ci_report_statistics.content_id = c.content_id AND ci_report_statistics.lng_id = '".$this->lngId."' 

											
				  ".$this->joinTables."
			WHERE p.profil_id in (".$this->tip.")
			  AND n.description".$this->lang."".$this->thisMode." IS NOT NULL 
			  AND n.description".$this->lang."".$this->thisMode." !=''
			  AND ci_type in ('".$this->CMDocType."')
				".$this->joinConditions."		

				  ".$this->filterToSql."
				  ".$this->filterToSqlSubjectNode." 
				  ".$this->orderBySql.$this->limitToSql;
			

		$this->getdata = $getdata;	
		

					
		$depthData = array();
	
		$tmpInfoArray = array();
		global $tmpInfoArray_sl;
		$tmpInfoArray_sl = array();

		$tmpInfoArray["list"] = array();
		if ($this->thisMode =="_new") $this->thisModeCode = "0";
		else						  $this->thisModeCode = "1";

		$i							= 0;
		$indexArray					= 0;
		$this->forum_total_postNr	= 0;
		
		$actual_koordinate = $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"]."";
		
		$rsGetdataLL = WebApp::execQuery($getdata);
		
/*echo "rsGetdataLL----<textarea>";
print_r($rsGetdataLL);
echo "</textarea>";*/



		while (!$rsGetdataLL->EOF()) {

			$ciDoublinCoreProp = array();
						
			$item_ci_id 	= $rsGetdataLL->Field("content_id");
			$item_ci_type 	= $rsGetdataLL->Field("ci_type");
			
			//$replay_id 		= $rsGetdataLL->Field("replayId");
			$item_ci_id 	= $rsGetdataLL->Field("content_id");
		
			$groupByCategory[$rsGetdataLL->Field("crd")]["koord"][$item_ci_id] = $item_ci_id;
 
			$ciIdsInList[$item_ci_id] = $item_ci_id;

			if ($this->CFact	== "MpostList") {	//numero postimet per cdo main post
							
				//get all forum post total number----------------------------------------------------------------------------------
					$rsPostAllNr	= $this->getCountPost($item_ci_id);

					if (!$rsPostAllNr->EOF()) {						
						$replay_nr	= $rsPostAllNr->Field("post_tot");			
					}else
						$replay_nr	= 0;		
					$ciDoublinCoreProp["replay_nr"] = $replay_nr;
				//get all forum post total number----------------------------------------------------------------------------------
			}

			//$ciDoublinCoreProp["replay_id"] 		= $replay_id;
			$ciDoublinCoreProp["CID"] 				= $item_ci_id;
			$ciDoublinCoreProp["cm_id"] 			= $item_ci_id;
			$ciDoublinCoreProp["ci_type"] 			= $rsGetdataLL->Field("ci_type");
			$ciDoublinCoreProp["zone_id"] 			= $rsGetdataLL->Field("id_zeroNivel");
			
			//$ciDoublinCoreProp["web_nick_name"] 	= $rsGetdataLL->Field("web_nick_name");
			//$ciDoublinCoreProp["web_user_id"] 		= $rsGetdataLL->Field("web_user_id");
			$ciDoublinCoreProp["web_nick_name"] 	= "";
			$ciDoublinCoreProp["web_user_id"] 		= "";
					 
			//TITLE-----------------------------------------------------------------------------------------------------------------------
					$titleToDisplay 						= $rsGetdataLL->Field("title");
					$titleToAlt 							= str_replace("\"","&quot;",$titleToDisplay);
					$titleToAlt								= str_replace("\r\n", " ", $titleToAlt);

					$ciDoublinCoreProp["ew_title"] 			= $titleToDisplay;	
					$ciDoublinCoreProp["titleToAlt"] 		= $titleToAlt;
					$ciDoublinCoreProp["titleToDisplay"] 	= $titleToDisplay;	
			//TITLE-----------------------------------------------------------------------------------------------------------------------
			
			//author DATA-----------------------------------------------------------------------------------------------------------------
					$authorOfComm							= $rsGetdataLL->Field("authorOfComm");
					$ciDoublinCoreProp["authorOfComm"] 		= $authorOfComm;
					
					$this->authorOfCommArr[$authorOfComm] 	= $authorOfComm;
					$ciDoublinCoreProp["authorOfComm"] 		= $authorOfComm;	
			//author DATA-----------------------------------------------------------------------------------------------------------------
			
							
			//EW_ABSTRACT------------------------------------------------------------------------------------------------------------------
					$ciDoublinCoreProp["abstractToDisplay"] 		= $rsGetdataLL->Field("description");
					if (isset($this->display_details["abstract"]) && $ciDoublinCoreProp["abstractToDisplay"]!="")  {
						$ciDoublinCoreProp["dp_abst"] = "yes";
						//description
						$description 		= $ciDoublinCoreProp["abstractToDisplay"];
						if ($this->split_description>0 && strlen($description) > $this->split_description) {
							$description = substr($description,0, $this->split_description)."...";
						} 		
						$ciDoublinCoreProp["abstractToDisplay"] = $description;				
					} else $ciDoublinCoreProp["dp_abst"] = "no";
			//EW_ABSTRACT------------------------------------------------------------------------------------------------------------------
			
			
			//EW_ABSTRACT------------------------------------------------------------------------------------------------------------------
					if (isset($this->display_details["content"]))  {
						$ciDoublinCoreProp["dp_cont"] = "yes";			
					} else $ciDoublinCoreProp["dp_cont"] = "no";
			//EW_ABSTRACT------------------------------------------------------------------------------------------------------------------
			
			//content----------------------------------------------------------------------------------------------------------------------
					$ciDoublinCoreProp["ci_content"] 				= $rsGetdataLL->Field("ci_content");	
					$ciDoublinCoreProp["ci_content_to_display"] 	= CiManagerFe::checkDocHtml($ciDoublinCoreProp["ci_content"],$ciDoublinCoreProp["ci_type"]);
			//content----------------------------------------------------------------------------------------------------------------------
			
			
			//creation date format-----------------------------------------------------------------------------------------------------------
				//data e dokumentit doublin core
				//source_creation_date source_creation_time_12 source_creation_time
					$diffDated 					= $rsGetdataLL->Field("diffDated");
					$source_creation_time_array = $rsGetdataLL->Field("source_creation_time_array");
					
					$this->dataLifeStyleFormat("gridDocumentFullDate_".$item_ci_id,$diffDated, $source_creation_time_array, $item_ci_id);	
			//creation date format-----------------------------------------------------------------------------------------------------------

			//SOURCE-------------------------------------------------------------------------------------------------------------------------
					$ciDoublinCoreProp["ew_source"]	= $rsGetdataLL->Field("source");
					$ciDoublinCoreProp["dp_source"] = "no";
					$ciDoublinCoreProp["sourceToDisplay"] = "";
					if (isset($this->display_details["source"]) && $ciDoublinCoreProp["ew_source"]!="") {
						$ciDoublinCoreProp["dp_source"] = "yes";
						$source = $ciDoublinCoreProp["ew_source"];
						if ($source!="") {
								$ciDoublinCoreProp["sourceToDisplay"] = $source;	
						} 				
					}
			//SOURCE-------------------------------------------------------------------------------------------------------------------------
			
			//SOURCE AUTHOR------------------------------------------------------------------------------------------------------------------
					$ciDoublinCoreProp["ew_source_author"]  = $rsGetdataLL->Field("source_author");
					$ciDoublinCoreProp["dp_author"] = "no";
					$ciDoublinCoreProp["AuthorToDisplay"] = "";
					if (isset($this->display_details["source_author"])  && $ciDoublinCoreProp["ew_source_author"]!="") {
						$ciDoublinCoreProp["dp_author"] = "yes";
						$source_author 		= $ciDoublinCoreProp["ew_source_author"];
						if ($source_author!="") {
								$ciDoublinCoreProp["AuthorToDisplay"] = $source_author;	
						} 				
					}			
			//SOURCE AUTHOR------------------------------------------------------------------------------------------------------------------
			
			//Big and Small images-----------------------------------------------------------------------------------------------------------
					$ciDoublinCoreProp["dp_image"] 				= "no";
					$ciDoublinCoreProp["dp_Bigimage"] 			= "no";
					$ciDoublinCoreProp["linkToimage"] 			= "no";
					$ciDoublinCoreProp["srcImageToDisplay"] 		= "";
		
					IF ($mob_web == "mob") {
						$imageSm_id = $rsGetdataLL->Field("imageSm_id_mob");
						$imageBg_id = $rsGetdataLL->Field("imageBg_id_mob");	
						$imageSm_id_name = $rsGetdataLL->Field("imageSm_id_mob_name");
						$imageBg_id_name = $rsGetdataLL->Field("imageBg_id_mob_name");				
					} ELSE {
						$imageSm_id = $rsGetdataLL->Field("imageSm_id");
						$imageBg_id = $rsGetdataLL->Field("imageBg_id");
						$imageSm_id_name = $rsGetdataLL->Field("imageSm_id_name");
						$imageBg_id_name = $rsGetdataLL->Field("imageBg_id_name");
					}

					$ciDoublinCoreProp["imageSm_id"] = "$imageSm_id";
					$ciDoublinCoreProp["imageBg_id"] = "$imageBg_id";	

					if (isset($this->display_details["image"]) && $imageSm_id!="" && $imageSm_id>"0") {
						$ciDoublinCoreProp["dp_image"] 			= "yes";
						//$sl_file_nedded_data_sl["id"][$imageSm_id] = $imageSm_id;
						
						$ciDoublinCoreProp["srcImageToDisplay"] = APP_URL."show_image.php?file_id=".$imageSm_id;
						IF ($global_cache_dynamic == "Y") {
							
							$ciDoublinCoreProp["srcImageToDisplay"] = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
						} elseif ($application_is_cached=="yes") {
							
						}	
						if (isset($this->display_link_in["image"])) 
							$ciDoublinCoreProp["linkToimage"] 			= "yes";
					}
			
					if (isset($this->display_details["image"]) && $imageBg_id!="" && $imageBg_id>"0") {
						$ciDoublinCoreProp["dp_Bigimage"] 			 = "yes";
						//$sl_file_nedded_data_sl["id"][$imageSm_id] 	 = $imageBg_id;
						
						$ciDoublinCoreProp["srcBigImageToDisplay"] 	 =  APP_URL."show_image.php?file_id=".$imageBg_id;
						IF ($global_cache_dynamic == "Y") {
							$ciDoublinCoreProp["srcBigImageToDisplay"] = $cacheDyn->get_SlDocTitleToUrl($imageBg_id, $imageBg_id_name);
						} elseif ($application_is_cached=="yes") {
							
						}					
					}
				//Big and Small images-----------------------------------------------------------------------------------------------------------
				//thirr funksionin qe gjeneron pathin e cachuar per filen

				
			//********************************************************************************************************************/
			//koordinata-----------------------------------------------------------------------------------------------------------
					$koord_level_node_param   = $session->Vars["level_0"]."/".$session->Vars["level_1"]."-".$session->Vars["level_2"]."/".$session->Vars["level_3"]."/".$session->Vars["level_4"];
					$actual_koordinate = $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"]."";

					//nga GoTo po e kthejm ne crd
					$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$item_ci_id.";)');"; //kc=$actual_koordinate
					//$hrefTo = "?crd=".$item_ci_id;
					 
					IF ($global_cache_dynamic == "Y") {

						$titleCI 			= $rsGetdataLL->Field("title");
						$filenameCI 		= $rsGetdataLL->Field("filename");
						$with_https 		= $rsGetdataLL->Field("with_https");

						$hrefTo = $cacheDyn->get_CiTitleToUrl($item_ci_id, $this->lngId, $titleCI, $filenameCI,$koord_level_node_param,$with_https);

					} 
			
					$hrefToOther			= $hrefTo;
					$hrefToDocTargetOther	= $hrefToDocTarget;		
					
					$ciDoublinCoreProp["hrefToDoc"] 		= $hrefTo;
					$ciDoublinCoreProp["hrefToDocTarget"] 	= $hrefToDocTarget;
					
					$ciDoublinCoreProp["hrefToDocOther"] 		= $hrefTo;
					$ciDoublinCoreProp["hrefToDocTargetOther"] = $hrefToDocTarget;

					$ciDoublinCoreProp["alias"] = "$indexArray";
			//koordinata-----------------------------------------------------------------------------------------------------------
			//*********************************************************************************************************************/
			//get last post-----------------------------------------------------------------------------------------------------------
					if ($this->CFact=="ListTopics") {	//detjaet e forumit --
			
								if(count($this->filterToSqlArrayState)>0)
								$this->filterToSqlDet = " AND ".implode("\n AND ", $this->filterToSqlArrayState);	
								
								//get viewed number-------------------------------------------------------------------------------------------------							
									if($this->nem_prop['show_views']=="yes"){
									
											$ciDoublinCoreProp["viewed_tot_post"]	= "0";

											$getViewdNr = "SELECT viewed,viewed_in_list
																FROM ci_report_statistics
																WHERE content_id='".$item_ci_id."'
																AND lng_id='".$this->lngId."'
																";													
											$rsViewdNr = WebApp::execQuery($getViewdNr);								
											if (!$rsViewdNr->EOF()) {
												$ciDoublinCoreProp["viewed_tot_post"] 	= $rsViewdNr->Field("viewed_in_list");								
											}
									}else{
										$ciDoublinCoreProp["viewed_tot_post"] 	= "";	
									}

								//get viewed number-------------------------------------------------------------------------------------------------

								//get discussions number for this topic (count all)-------------------------------------------
									if($this->nem_prop['show_discussions']=="yes"){
											$rsgetRelData = $this->getNrTopicPost($item_ci_id);

											$ciDoublinCoreProp["post_nr"]	= 0;
											
											if (!$rsgetRelData->EOF()) {

												$parent_content_id = $rsgetRelData->Field("parent_content_id");
												$ciDoublinCoreProp["post_nr"] 					= $rsgetRelData->Field("post_nr");
												$ciDoublinCoreProp["existForumDetails"] 		= "yes";
												$ciDoublinCoreProp["existpost"] 				= "yes";

												$this->topic_total_postNr	= $this->topic_total_postNr + (($ciDoublinCoreProp["post_nr"])*1);

														
											}
									}else{
										$ciDoublinCoreProp["post_nr"] 					= "0";
										$ciDoublinCoreProp["existpost"] 				= "no";
									}
								
								//get last post date for this topic (the last one who posted in this topic, get date and username)-------------------------------------------
									$tmpLastPost= array();
									
									if($this->nem_prop['show_last_message']=="yes"){
											$rsgetLatestPost = $this->getLatestTopicPost($item_ci_id);
											
											if (!$rsgetLatestPost->EOF()) {

												$postId			= $rsgetLatestPost->Field("content_id");
												$posttitle		= $rsgetLatestPost->Field("title");
												
												if (!isset($tmpLastPost["data"][$item_ci_id])) {

													$tmpLastPost["data"][$item_ci_id] = $item_ci_id;
													$tmpLastPost["hier"][$item_ci_id][$postId] = $postId;

													$ciDoublinCoreProp["LastPost_headline"] 		= $rsgetLatestPost->Field("title");
													$ciDoublinCoreProp["authorID"] 					= $rsgetLatestPost->Field("author");
													$authorID 										= $rsgetLatestPost->Field("author");
													$this->authorOfCommArr[$authorID]				= $authorID;
													$this->getAuthorComm();
													
													$ciDoublinCoreProp["LastPost_id"] 				= $postId;
													$ciDoublinCoreProp["existForumDetails"] 		= "yes";
													
													$diffDated 					= $rsgetLatestPost->Field("diffDated");
													$source_creation_time_array = $rsgetLatestPost->Field("source_creation_time_array");
													$this->dataLifeStyleFormat("gridPostDate_$postId",$diffDated, $source_creation_time_array, $postId);
												}
												//$rsgetLatestPost->MoveNext();
											}
									}else{
											$ciDoublinCoreProp["LastPost_headline"] 		= "";
											$ciDoublinCoreProp["authorID"] 					= "";
											$ciDoublinCoreProp["LastPost_id"] 				= "";
											$ciDoublinCoreProp["existForumDetails"] 		= "no";
									}
								//get last post date for this topic (the last one who posted in this topic, get date and username)-------------------------------------------
								
								//get CT lecture node document name------------------------------------------------------------------------------

									$ParentData 	= $this->getParentNodeData($this->getParentNode($item_ci_id));
									if($ParentData['title']!="")
										$ciDoublinCoreProp["nodeDocLectureTitle"] 		= $ParentData['title'];	
									else
										$ciDoublinCoreProp["nodeDocLectureTitle"] 		= "";	
								//get CT lecture node document name------------------------------------------------------------------------------
								
					}
					
					if ($this->CFact=="ListForum") {	//lista e forumeve mblesh CF
						
						//get coordinate for the first CF item and go and take all CT under that CF item
						$filterToSqlCoord		= array();
						$filterToSqlCoordStr	= array();
						$ciDoublinCoreProp['nr_of_post_for_forumL']	= "0";
						
						
						$filterToSqlCoord[$item_ci_id] 		= $this->getFilterCoord($item_ci_id,3);	//search for CT item only in the parent node


						if(count($filterToSqlCoord[$item_ci_id])>0)
							$filterToSqlCoordStr[$item_ci_id] = " AND ".$filterToSqlCoord[$item_ci_id];
						else
							$filterToSqlCoordStr[$item_ci_id] = " ";	

						if(count($this->filterToSqlArrayState)>0)
							$this->filterToSqlDet = " AND ".implode("\n AND ", $this->filterToSqlArrayState);

								$getDistCTId ="SELECT distinct (c.content_id) as content_id
					
												FROM content	AS c
												JOIN	nivel_4			AS n	ON (    c.id_zeroNivel   = n.id_zeroNivel
																								AND c.id_firstNivel  = n.id_firstNivel
																								AND c.id_secondNivel = n.id_secondNivel
																								AND c.id_thirdNivel  = n.id_thirdNivel
																								AND c.id_fourthNivel = n.id_fourthNivel
																								)
													   
												JOIN	profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
															AND p.id_firstNivel  = c.id_firstNivel
															AND p.id_secondNivel = c.id_secondNivel
															AND p.id_thirdNivel  = c.id_thirdNivel
															AND p.id_fourthNivel = c.id_fourthNivel
												)
												
												".$this->joinTables."

												WHERE p.profil_id in (".$this->tip.")
												AND n.description".$this->lang."".$this->thisMode." IS NOT NULL 
												AND n.description".$this->lang."".$this->thisMode." !=''
												AND ci_type in ('CT')
											
												".$this->joinConditions."		
												".$this->filterToSqlDet."
												".$filterToSqlCoordStr[$item_ci_id]."
												
												";	
																	 
								$rsDistCTId = WebApp::execQuery($getDistCTId); 
/*echo "rsDistCTId----<textarea>";
print_r($rsDistCTId);
echo "</textarea>";	*/			
								$topic_array_data	= array();								
																
								$last_post_date													= 0;
								$last_post_date_id												= 0;
								
								$topic_total_postNrL											= 0;
								$topic_array_data[$content_id]["topic_total_postNrL"]			= 0;
								$viewed_tot_postL												= 0;
								$topic_array_data[$content_id]["viewed_tot_postL"]				= 0;
								$topic_array_data[$content_id]["LastPost_headlineL"]			= "";
								$topic_array_data[$content_id]["LastPost_idL"]					= 0;
								$topic_array_data[$content_id]["existForumDetailsL"]			= "no";
								$topic_array_data[$content_id]["diffDatedL"]					= "";
								$topic_array_data[$content_id]["source_creation_time_compare"]	= "";
								
								while (!$rsDistCTId->EOF()) {//get all CT item
									
									$content_id		= $rsDistCTId->Field("content_id");
									
										//get post number for this topic (count only the child post not the main )-------------------------------------------
											$rsgetRelDataL = $this->getNrTopicPost($content_id);
																				
											if (!$rsgetRelDataL->EOF()) {
									
												$topic_total_postNrL	= $topic_total_postNrL + (($rsgetRelDataL->Field("post_nr"))*1);
												$topic_array_data[$content_id]["topic_total_postNrL"]	= $topic_total_postNrL;												
											}
										//get post number for this topic (count only the child post not the main )-------------------------------------------
										
										//get viewed number-------------------------------------------------------------------------------------------------							

												$getViewdNrL = "SELECT viewed,viewed_in_list
																	FROM ci_report_statistics
																	WHERE content_id='".$content_id."'
																	AND lng_id='".$this->lngId."'
																	";													
												$rsViewdNrL = WebApp::execQuery($getViewdNrL);	
												
												if (!$rsViewdNrL->EOF()) {
													$viewed_tot_postL									= $viewed_tot_postL + (($rsViewdNrL->Field("viewed_in_list"))*1);	
													$topic_array_data[$content_id]["viewed_tot_postL"]	= $viewed_tot_postL;													
												}

										//get viewed number-------------------------------------------------------------------------------------------------

										//get last post date for this topic (the last one who posted in this topic, get date and username)-------------------------------------------


											$diffDated_Cmp													= 0;
											
											$rsgetLatestPostL = $this->getLatestTopicPost($content_id);
											
											while (!$rsgetLatestPostL->EOF()) {

													$topic_array_data[$content_id]["LastPost_headlineL"] 			= $rsgetLatestPostL->Field("title");
													$topic_array_data[$content_id]["LastPost_idL"]					= $rsgetLatestPostL->Field("content_id");
													$topic_array_data[$content_id]["authorIDL"] 					= $rsgetLatestPostL->Field("author");
													$authorIDL 														= $topic_array_data[$content_id]["authorIDL"];
													$this->authorOfCommArr[$authorIDL]								= $authorIDL;
													
													$this->getAuthorComm();
													
													$topic_array_data[$content_id]["existForumDetailsL"] 			= "yes";
																
													$topic_array_data[$content_id]["diffDatedL"]					= $rsgetLatestPostL->Field("diffDated");
													$topic_array_data[$content_id]["source_creation_time_arrayL"]	= $rsgetLatestPostL->Field("source_creation_time_array");
													
													$topic_array_data[$content_id]["source_creation_time_compare"]	= $rsgetLatestPostL->Field("source_creation_time_compare");
													$topic_array_data[$content_id]["strtotime_compare"]				= strtotime($topic_array_data[$content_id]["source_creation_time_compare"]);
													
													//compare who topic has the latest post date, the one that has the minimum diffDated is the latest
												$rsgetLatestPostL->MoveNext();
											}
										
										if($last_post_date<$topic_array_data[$content_id]["strtotime_compare"]){
											$last_post_date			= $topic_array_data[$content_id]["strtotime_compare"];
											$last_post_date_id		= $content_id;
										}else{
											$last_post_date	= $last_post_date;
										}
										
										//get last post date for this topic (the last one who posted in this topic, get date and username)-------------------------------------------

										
									$rsDistCTId->MoveNext();
								}

								$ciDoublinCoreProp['nr_of_post_for_forumL']		= "".$topic_array_data[$content_id]["topic_total_postNrL"]."";							
								$ciDoublinCoreProp["viewed_tot_postL"]			= "".$topic_array_data[$content_id]["viewed_tot_postL"]."";
								$ciDoublinCoreProp["LastPost_headlineL"]		= $topic_array_data[$last_post_date_id]["LastPost_headlineL"];
								$ciDoublinCoreProp["authorIDL"]					= $topic_array_data[$last_post_date_id]["authorIDL"];
								$ciDoublinCoreProp["LastPost_idL"]				= $last_post_date_id;
								$ciDoublinCoreProp["existForumDetailsL"]		= $topic_array_data[$content_id]["existForumDetailsL"];
								$this->dataLifeStyleFormat("gridPostDateL_$last_post_date_id",$topic_array_data[$last_post_date_id]["diffDatedL"], $topic_array_data[$last_post_date_id]["source_creation_time_arrayL"], $last_post_date_id);
					}

			//get last post-----------------------------------------------------------------------------------------------------------	
			if($this->CFact	== "Mpost" || $this->CFact	== "MpostList"){
				$ciDoublinCoreProp["post_status"]			= $rsGetdataLL->Field("post_status");
				$report_reasonP								= $rsGetdataLL->Field("report_reason");				
				if($report_reasonP	!="none_sel"){							
					$ciDoublinCoreProp["report_reason"]	= "{{_".$report_reasonP."_reason}}";
				}else
					$ciDoublinCoreProp["report_reason"]	= "{{_other_reason}}";	
				
				$ciDoublinCoreProp["report_comment"]		= $rsGetdataLL->Field("report_comment");
				$ciDoublinCoreProp["report_author"]			= $rsGetdataLL->Field("report_author");
			}else{
				$ciDoublinCoreProp["post_status"]			= "";
				$ciDoublinCoreProp["report_reason"]			= "";
				$ciDoublinCoreProp["report_comment"]		= "";
				$ciDoublinCoreProp["report_author"]			= "";
		
			}
		
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id] = $ciDoublinCoreProp;

			if ($this->CFact=="ListForum") {	//lista e forumeve mblesh CF
				//calculate the total number of post in all the forums of this program
				
				if(count($tmpInfoArray["list"]["CI_DATA"])>0){
					
					while (list($key,$val)=each($tmpInfoArray["list"]["CI_DATA"])) {
						$this->forum_total_postNr	= $this->forum_total_postNr+($val["nr_of_post_for_forumL"]*1);
					}			
				}								
			}

		
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id]["exist_useful"] 				= "no";
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id]["post_viewed_nr"] 			= "0";
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id]["LastPost_headline"] 			= "";
			
			
			$ciDoublinCoreProp["post_viewed_nr"] = $rsGetdataLL->Field("nr_viewd");
			
			
			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id]["post_viewed_nr"] 			= $ciDoublinCoreProp["post_viewed_nr"] ;
			
			
			//$tmpInfoArray["treeInformation"][$replay_id][$item_ci_id] = $item_ci_id;
			$tmpInfoArray["grid_data"][$indexArray] = $item_ci_id;
			$tmpInfoArray["groupedType"][$item_ci_type][$item_ci_id] = $item_ci_id;
			
	
			$indexArray++;
			$rsGetdataLL->MoveNext();			
		}
			
		$this->tmpInfoArray 		= $tmpInfoArray;

        
/*echo "rsGetdataLL---<textarea>";
print_r($rsGetdataLL);
echo "</textarea>";      
echo "tmpInfoArray---<textarea>";
print_r($tmpInfoArray);
echo "</textarea>";*/


		//nese kemi qe duhet te shfaqim extended prop ose content, duhet te gjejme propertite extended per cdo type dokumenti
			//get the user data---------------------------------------------------------------------------------------------- 
				//$this->getAuthorComm();
			//get the user data---------------------------------------------------------------------------------------------- 

			
		$gridDataSrcGrouped = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);	       
       
       
       $indexG = 0;
       
       if ($this->CFact=="ListForum") {
       	//grouped by category
		IF (IS_ARRAY($groupByCategory)) {
			while (list($indexArray,$ciExtendedID)=each($groupByCategory)) {
				
				
				//$groupByCategory[$rsGetdataLL->Field("crd")]["label"] = $rsGetdataLL->Field("nodelabel");
				//$gridDataSrcGrouped["data"][$indexG]["cat"] = $ciExtendedID["label"];
				
					$l = explode("_",$indexArray);

					$DescriptionARr = array();
					//$RObjP = new tNode();
					$descrArray = tNode::getParentIds($l[0].",".$l[1].",".$l[2].",".$l[3].",".$l[4]);
					
					$koord_level_node 	 	 = implode(",",$l);
					
					$link_title = "";
					for ($k=0;$k<count($descrArray);$k++) {
						$sel_node = new tNode($descrArray[$k]);
						if ($k==0) { //worgroup description
						} else {
							$DescriptionARr[] = $sel_node->descriptions[$session->Vars["lang"]];
						}
					}
					if (count($DescriptionARr)>0) {

						$full_link_title = implode($this->concat_levels_with,$DescriptionARr);
						$gridDataSrcGrouped["data"][$indexG]["cat"] = $full_link_title;
						
						$gridDataSrcGrouped["data"][$indexG]["hrefToNodeTarget"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$this->CMID.";catId=".$koord_level_node.")');";
						
						IF ($global_cache_dynamic == "Y") {
							$gridDataSrcGrouped["data"][$indexG]["hrefToNodeTarget"] = $cacheDyn->get_CiTitleToUrl($this->CMID,$this->lngId,"",$koord_level_node);
							$gridDataSrcGrouped["data"][$indexG]["hrefToNodeTarget"] .= "?catId=".$koord_level_node;
							//ECHO $node_nedded_data[$koord_level_node_key]["hrefToNodeTarget"]."-$koord_level_node<br>";
						}						
					}
				
				$gridDataSrcGrouped["data"][$indexG++]["grouped"] = $indexArray;
				
				
				$tmp = array("data" =>  array(), "AllRecs" => "0");				
				$indexGt = 0;
				while (list($kk,$vv)=each($ciExtendedID["koord"])) {
				
					$tmp["data"][$indexGt++] = $tmpInfoArray["list"]["CI_DATA"][$kk];
				}
				$tmp["AllRecs"] =count($tmp["data"]);
				WebApp::addVar("gridGrouped_".$indexArray,$tmp);		
			}
			$gridDataSrcGrouped["AllRecs"] =count($gridDataSrcGrouped["data"]);
			WebApp::addVar("gridGrouped",$gridDataSrcGrouped);				
		     
       }}
  
       
       /*		echo "<textarea>";
     		print_r($groupByCategory);
      		print_r($userfulGrid);
      	 	print_r($tmpInfoArray);
       		echo "</textarea>";*/
  
		IF (IS_ARRAY($this->tmpInfoArray["grid_data"])) {
			while (list($indexArray,$ciExtendedID)=each($this->tmpInfoArray["grid_data"])) {
				if (isset($this->display_details["content"])) {
					$tmpInfoArray["list"]["CI_DATA"][$ciExtendedID]["dsp_full_content"] = "yes";
				}
				$gridDataSrcParams[$indexArray] = $tmpInfoArray["list"]["CI_DATA"][$ciExtendedID];
			}
		}	
		
		$gridDataSrcTemp = array(
				"data" 			=>  array(),
				"AllRecs" 		=> "0"
		);
		$ind = 0;
		if (isset($tmpInfoArray["list"]["CI_DATA"]) && count($tmpInfoArray["list"]["CI_DATA"])>0) {
			While (list($keyX,$valueX)=each($tmpInfoArray["list"]["CI_DATA"])) {							
				$gridDataSrcTemp["data"][$ind++] = $valueX;
			}
		}
		
		$gridDataSrcTemp["AllRecs"] =count($gridDataSrcTemp["data"]);
		WebApp::addVar("gridCmmDataAll",$gridDataSrcTemp);					
		
		if (count($gridDataSrcParams)>0) { 
			$gridDataSrc["data"] = $gridDataSrcParams;
			$gridDataSrc["AllRecs"] = count($gridDataSrcParams);

			$gridDataSrc["dataNAV"]["CntI"] 	=	$this->CountItems;
			$gridDataSrc["dataNAV"]["fromI"]	=	$this->FromRecs;
			$gridDataSrc["dataNAV"]["toI"] 		=	$this->ToRecs;
			$gridDataSrc["dataNAV"]["pg"] 		=	$this->NrPage;
			$gridDataSrc["dataNAV"]["pgCnt"]	=	$this->TotPage;
			$gridDataSrc["dataNAV"]["pgRec"]	=	$this->recPages;
		}
		$this->gridDataSrc = $gridDataSrc;
		//$session->Vars["contentId"] = $MainId;
	
		
	}

	function addnewCTDoc($newCiId,$forumCiId,$lngId) {
		 
		$maxId = "SELECT max(sequence_id) as max FROM ct_data
						 WHERE  lng_id = '" . $lngId . "'AND forum_id = '" . $forumCiId . "'  AND statusInfo = 0";
		$rsmax = WebApp::execQuery($maxId);

		if (!$rsmax->EOF()) $seq_id = $rsmax->Field("max");
		else                            $seq_id = 0;

		$seq_id = $seq_id + 1;
		
		$insertExtCT = "INSERT INTO ct_data (forum_id,content_id, lng_id, statusInfo,sequence_id,new_post,nr_post)
							 VALUES ('" . $forumCiId . "','" . $newCiId . "','" . $lngId . "',0,'" . $seq_id . "','true','5')";
				WebApp::execQuery($insertExtCT);
		
		$this->approveCTprop($newCiId,$lngId);
	
	}
	
	function approveCTprop($CiId = '', $lngID = '')
    {

        $del = "DELETE FROM ct_data WHERE content_id = '" . $CiId . "' AND lng_id = '" . $lngID . "'  AND statusInfo='1'";
        WebApp::execQuery($del);
        
		$approveItem = "
			REPLACE INTO ct_data 	(forum_id,content_id,lng_id,statusInfo,sequence_id,new_post,nr_post)
							SELECT
									 forum_id,content_id,lng_id,1,sequence_id,new_post,nr_post
							  FROM ct_data
							 WHERE content_id = '" . $CiId . "' AND lng_id = '" . $lngID . "'  AND statusInfo='0'";
        WebApp::execQuery($approveItem);
    }
	
	function addnewCPDoc($param,$topicCiId) {
/*
$PEI["content_id"],$this->CMID,
$PEI["lng_id"]
$PEI["topic_id"]			
$PEI["parID"]				
$PEI["repId"]
$PEI["act"]
*/		 	

		$maxIdCP = "SELECT max(sequence_id) as max FROM cp_data
						 WHERE  lng_id = '".$param['lng_id']."'AND topic_id = '".$param['topic_id']."'  AND statusInfo = 0  AND parentId ='".$param['parID']."'";
		$rsmaxIdCP = WebApp::execQuery($maxIdCP);

		if (!$rsmaxIdCP->EOF()) $seq_idCP = $rsmaxIdCP->Field("max");
		else                            $seq_idCP = 0;

		$seq_idCP = $seq_idCP + 1;
		
		$insertExtCP = "INSERT INTO cp_data (topic_id,content_id, lng_id, statusInfo,sequence_id,parentId,replayId, new_post,nr_post,post_status)
							 VALUES ('".$topicCiId."','".$param["content_id"]."','".$param['lng_id']."',0,'".$seq_idCP."','".$param['parID']."','".$param['repId']."','true','5','normal')";
				 $rs=WebApp::execQuery($insertExtCP);
			
		$this->approveCPprop($param["content_id"] ,$param['lng_id']);
	
	}
	
	function approveCPprop($CiId = '', $lngID = '')
    {

        $delCP = "DELETE FROM cp_data WHERE content_id = '" . $CiId . "' AND lng_id = '" . $lngID . "'  AND statusInfo='1'";
        WebApp::execQuery($delCP);
        
		$approveItemCP = "
			REPLACE INTO cp_data 	(topic_id,content_id,lng_id,statusInfo,sequence_id,parentId,replayId,new_post,nr_post,post_status)
							SELECT
									 topic_id,content_id,lng_id,1,sequence_id,parentId,replayId,new_post,nr_post,post_status
							  FROM cp_data
							 WHERE content_id = '".$CiId."' AND lng_id = '".$lngID."'  AND statusInfo='0'";
       WebApp::execQuery($approveItemCP);

    }
		
	function getCountPost($item_ci_id) {
	
			//percaktojm koordinatat e kerkimit, programi mbledh te gjithe forumet e modulit dhe leksioneve,moduli mbledh te gjitha forumet e leksioneve etj
			
			$filterToSqlArrayPub	= array();
			if ($this->thisMode=='') {
				$filterToSqlArrayPub[] =" c.state".$this->lang." not in (0,5,7)";
				$filterToSqlArrayPub[] =" c.published".$this->lang." = 'Y'";
			} else {
				$filterToSqlArrayPub[] =" c.state".$this->lang." not in (7)";
			}
			if(count($filterToSqlArrayPub)>0)
				$filterToSqlStrPub = " AND ".implode("\n AND ", $filterToSqlArrayPub);
			
				$getPostAllNr = "SELECT COUNT(cp_data.content_id) AS post_tot

											   FROM content  AS c
												  
												 LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
												  
												  JOIN  nivel_4      AS n  ON (    c.id_zeroNivel   = n.id_zeroNivel
																	AND c.id_firstNivel  = n.id_firstNivel
																	AND c.id_secondNivel = n.id_secondNivel
																	AND c.id_thirdNivel  = n.id_thirdNivel
																	AND c.id_fourthNivel = n.id_fourthNivel
																	)
												  JOIN  profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
														  AND p.id_firstNivel  = c.id_firstNivel
														  AND p.id_secondNivel = c.id_secondNivel
														  AND p.id_thirdNivel  = c.id_thirdNivel
														  AND p.id_fourthNivel = c.id_fourthNivel
													)

												WHERE p.profil_id in (".$this->tip.")
												AND n.description".$this->lang."".$this->thisMode." IS NOT NULL
												AND n.description".$this->lang."".$this->thisMode." !=''
												AND ci_type in ('CP')
												AND cp_data.parentId='".$item_ci_id."'
												".$filterToSqlStrPub."
												";								
			
			$rsPostAllNr = WebApp::execQuery($getPostAllNr); 
/*echo "rsPostAllNr---<textarea>";	
print_r($rsPostAllNr);
echo "</textarea>";	*/
			
			return $rsPostAllNr;
	}
	
	function getParentNode($cid="")
	{
		global $session;
		
		$getCnt = "SELECT c.id_zeroNivel as niv0, c.id_firstNivel as niv1,c.id_secondNivel as niv2, c.id_thirdNivel as niv3,c.id_fourthNivel as niv4 
					from content as c
					where c.content_id='".$cid."'";
		$rsCnt = WebApp::execQuery($getCnt);		
		$NodeArray	= array();
		if (!$rsCnt->EOF()){

				$NodeArray['0']	= $rsCnt->Field("niv0");
				$NodeArray['1']	= $rsCnt->Field("niv1");
				$NodeArray['2']	= $rsCnt->Field("niv2");
				$NodeArray['3']	= $rsCnt->Field("niv3");
				$NodeArray['4']	= $rsCnt->Field("niv4");				
		}

		return $NodeArray;
		
	}
	
	function getParentNodeCID($nodeArray="")
	{
		global $session;

		$getCID = "SELECT content_id  
					from content
					where id_zeroNivel	='".ValidateVarFun::f_real_escape_string($nodeArray['0'])."'
					AND id_firstNivel	='".ValidateVarFun::f_real_escape_string($nodeArray['1'])."'
					AND id_secondNivel	='".ValidateVarFun::f_real_escape_string($nodeArray['2'])."'
					AND id_thirdNivel	='".ValidateVarFun::f_real_escape_string($nodeArray['3'])."'
					AND id_fourthNivel	='0'	                                           
					AND ci_type			='EL'					
					";
		$rsCID = WebApp::execQuery($getCID);
/*if($session->Vars["uni"]=="20150915094810192168121606044256"){
echo "rsCID-----<textarea>";
print_r($rsCID);
echo "</textarea>";
}*/			
		if (!$rsCID->EOF()){
			
			$CId	= $rsCID->Field("content_id");					
		}
		return $CId;		
	}
	
	function getParentNodeData($nodeArray="")
	{
		global $session;
		
		$nodeNivelArray	= array();
		$ParentData		= array();
		
		$i	= 0; //5 livels [0,1,2,3,4]
		if(count($nodeArray)>0){
			
			if($nodeArray['0']>0)
				$i++;
			
			if($nodeArray['1']>0)
				$i++;
				
			if($nodeArray['2']>0)
				$i++;
				
			if($nodeArray['3']>0)
				$i++;
				
			if($nodeArray['4']>0)
				$i++;
			
			$table_label=array("id_zeroNivel","id_firstNivel","id_secondNivel","id_thirdNivel","id_fourthNivel");
			
			if($i>0){
				for($j=0;$j<=$i-1;$j++){
					if($j==$i-1){
						$nodeNivelArray[$j]	=	" ".$table_label[$j]."		='0'";
					}else{
						$nodeNivelArray[$j]	=	" ".$table_label[$j]."		='".ValidateVarFun::f_real_escape_string($nodeArray[$j])."'";
					}
				}
			}
			
			if(count($nodeNivelArray)>0)
				$nodeNivelString = " AND ".implode("\n AND ", $nodeNivelArray);		

			$getData = "SELECT content_id ,
								title".$this->lang." as title 
					FROM content
					WHERE 1=1
					".$nodeNivelString."	                                           					
					AND ci_type			='EL'					
					";
			$rsData = WebApp::execQuery($getData);
			if (!$rsData->EOF()) {
			
				$ParentData['title'] 		= $rsData->Field("title");
				
				$rsData->MoveNext();
			}			
		}
		return $ParentData;		
	}

	function getFilterCoord($cid="",$level="") {
		
		global $session;
		
		if($cid!=""){
			$crdItemsArr	= $this->getParentNode($cid);
			
			if($level=="2"){
				$crdItems[0]	= $crdItemsArr[0];
				$crdItems[1]	= $crdItemsArr[1];
			}elseif($level=="3"){
				$crdItems[0]	= $crdItemsArr[0];
				$crdItems[1]	= $crdItemsArr[1];
				$crdItems[2]	= $crdItemsArr[2];
			}else{
				$crdItems[0]	= $crdItemsArr[0];
				$crdItems[1]	= $crdItemsArr[1];
				$crdItems[2]	= $crdItemsArr[2];
				$crdItems[3]	= $crdItemsArr[3];
				$crdItems[4]	= $crdItemsArr[4];
			}
			
		}else{		
			
			if($level=="2"){
				$crdItems[0]	= $session->Vars["level_0"];
				$crdItems[1]	= $session->Vars["level_1"];
			}elseif($level=="3"){
				$crdItems[0]	= $session->Vars["level_0"];
				$crdItems[1]	= $session->Vars["level_1"];
				$crdItems[2]	= $session->Vars["level_2"];
			}else{
				$crdItems[0]	= $session->Vars["level_0"];
				$crdItems[1]	= $session->Vars["level_1"];
				$crdItems[2]	= $session->Vars["level_2"];
				$crdItems[3]	= $session->Vars["level_3"];
				$crdItems[4]	= $session->Vars["level_4"];
			}
		}

			if ($crdItems[4]>0) {
											
					$filterToSqlArray[] = "
						(
								c.id_zeroNivel=".$crdItems[0]."
							AND c.id_firstNivel=".$crdItems[1]."
							AND c.id_secondNivel=".$crdItems[2]."
							AND c.id_thirdNivel=".$crdItems[3]."
							AND c.id_fourthNivel=".$crdItems[4]."
						)  ";							
			} elseif ($crdItems[3]>0) {

					$filterToSqlArray[] = "
						(
								c.id_zeroNivel=".$crdItems[0]."
							AND c.id_firstNivel=".$crdItems[1]."
							AND c.id_secondNivel=".$crdItems[2]."
							AND c.id_thirdNivel=".$crdItems[3]."

						)  ";							
			} elseif ($crdItems[2]>0) {

					$filterToSqlArray[] = "
						(
								c.id_zeroNivel=".$crdItems[0]."
							AND c.id_firstNivel=".$crdItems[1]."
							AND c.id_secondNivel=".$crdItems[2]."

						)  ";							
			} elseif ($crdItems[1]>0) {

					$filterToSqlArray[] = "
						(
								c.id_zeroNivel=".$crdItems[0]."
							AND c.id_firstNivel=".$crdItems[1]."

						)  ";							
			} else {

					$filterToSqlArray[] = "
						(c.id_zeroNivel=".$crdItems[0].")  ";	
			}
		
		if (count($filterToSqlArray) > 0)
			$sqlCondition =  "
							(
								".implode(" OR ",$filterToSqlArray)."
							)
			";
		
		return $sqlCondition;
			
	}
	function getAuthorCommStatistics($ids) {
	
		$this->authorStatistics = array();
		//per momentin gjenden postet dhe temat qe ka krijuar nje user i caktuar ne te gjithe zonen e author
		//get user tema total number---------------------------------------------------------------------------------------------
		$getTemaUserNr = "SELECT COUNT(ct_data.content_id) AS teme_nr, c.author as UserId
						  FROM content  AS c
							LEFT JOIN ct_data on ct_data.content_id = c.content_id  AND ct_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'							  
							JOIN  nivel_4      AS n  ON (    c.id_zeroNivel   = n.id_zeroNivel
												AND c.id_firstNivel  = n.id_firstNivel
												AND c.id_secondNivel = n.id_secondNivel
												AND c.id_thirdNivel  = n.id_thirdNivel
												AND c.id_fourthNivel = n.id_fourthNivel
												)
							JOIN  profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
									  AND p.id_firstNivel  = c.id_firstNivel
									  AND p.id_secondNivel = c.id_secondNivel
									  AND p.id_thirdNivel  = c.id_thirdNivel
									  AND p.id_fourthNivel = c.id_fourthNivel
								)

							WHERE p.profil_id in (".$this->tip.")
							AND n.description".$this->lang."".$this->thisMode." IS NOT NULL
							AND n.description".$this->lang."".$this->thisMode." !=''
							AND ci_type in ('CT')
							AND c.author in (".$ids.")
							GROUP BY UserId
							";								
			
		$rsTemaUserNr = WebApp::execQuery($getTemaUserNr);
		while (!$rsTemaUserNr->EOF()) {
			
			$UserId 		= $rsTemaUserNr->Field("UserId");
			$teme_nr 		= $rsTemaUserNr->Field("teme_nr");
			if($UserId>0){
				$this->authorStatistics[$UserId]["teme_nr"] = $teme_nr;
			}else
				$this->authorStatistics[$UserId]["teme_nr"] = 0;
			
			$rsTemaUserNr->MoveNext();
		
		}
		//get user tema total number---------------------------------------------------------------------------------------------
		
		
		//get user post total number---------------------------------------------------------------------------------------------
		$getPostUserNr = "SELECT COUNT(cp_data.content_id) AS post_nr, c.author as UserId
						   FROM content  AS c							  
						    LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
							  
							JOIN  nivel_4      AS n  ON (    c.id_zeroNivel   = n.id_zeroNivel
												AND c.id_firstNivel  = n.id_firstNivel
												AND c.id_secondNivel = n.id_secondNivel
												AND c.id_thirdNivel  = n.id_thirdNivel
												AND c.id_fourthNivel = n.id_fourthNivel
												)
							JOIN  profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
									  AND p.id_firstNivel  = c.id_firstNivel
									  AND p.id_secondNivel = c.id_secondNivel
									  AND p.id_thirdNivel  = c.id_thirdNivel
									  AND p.id_fourthNivel = c.id_fourthNivel
								)

							WHERE p.profil_id in (".$this->tip.")
							AND n.description".$this->lang."".$this->thisMode." IS NOT NULL
							AND n.description".$this->lang."".$this->thisMode." !=''
							AND ci_type in ('CP')
							AND c.author in (".$ids.")
							GROUP BY UserId
							";								
			
		$rsPostUserNr = WebApp::execQuery($getPostUserNr);
/*echo "rsPostUserNr----<textarea>";
print_r($rsPostUserNr);
echo "</textarea>";	*/
		while (!$rsPostUserNr->EOF()) {
			
			$UserId 		= $rsPostUserNr->Field("UserId");
			$post_nr 		= $rsPostUserNr->Field("post_nr");
			
			if($UserId>0){
				$this->authorStatistics[$UserId]["post_nr"] = $post_nr;	
			}else
				$this->authorStatistics[$UserId]["post_nr"] = $post_nr;	
				
			$rsPostUserNr->MoveNext();
		
		}
		//get user post total number---------------------------------------------------------------------------------------------	
	
/*echo "authorStatistics----<textarea>";
print_r($this->authorStatistics);
echo "</textarea>";	*/
	}

	function getAuthorComm() {
	
			
		$ids = implode(",",$this->authorOfCommArr);
		$this->getAuthorCommStatistics($ids);
		
		//UserId	FirstName	SecondName	usr_short	usr_title	usr_diploma	usr_email	usr_gender	Status_user
		//1	admin	admin	admin			info@arkit.info	Female	y
		
		
			$getUserInfor = "SELECT COALESCE(users.UserId,'') 	as UserId,
								COALESCE(FirstName,'') 		as FirstName,
								COALESCE(SecondName,'') 	as SecondName,
								COALESCE(usr_short,'') 		as usr_short,
								COALESCE(usr_title,'') 		as usr_title,
								COALESCE(usr_diploma,'') 	as usr_diploma,
								COALESCE(usr_email,'') 		as usr_email,
								COALESCE(usr_gender,'') 	as usr_gender,
								COALESCE(Status_user,'') 	as Status_user,
								
								 coalesce(date_format(usr_entry,'%M %d.%Y'),'') as joined
								

							FROM users
							LEFT JOIN users_extended on users.UserId = users_extended.UserId
							WHERE users.UserId in (".$ids.")";
								 
		$rsgetLatestPost = WebApp::execQuery($getUserInfor);
/*echo "------authorStatistics----<textarea>";
print_r($this->authorStatistics);
echo "</textarea>";*/
		while (!$rsgetLatestPost->EOF()) {

			$tmpSourceToGrid["data"] = array();

			$userId = $rsgetLatestPost->Field("UserId");
			
		//	avatars
/*echo $userId."------authorStatistics----<textarea>";
print_r($this->authorStatistics);
echo "</textarea>";*/
			
			
			if (isset($this->authorStatistics[$userId])) {
				$this->authorStatistics[$userId]["contributing_nr"] = $this->authorStatistics[$userId]["teme_nr"]+$this->authorStatistics[$userId]["post_nr"];
				$tmpSourceToGrid["data"][0] = $this->authorStatistics[$userId];
			} else {

				$tmpSourceToGrid["data"][0]["teme_nr"]			= "0";
				$tmpSourceToGrid["data"][0]["post_nr"]			= "0";
				$tmpSourceToGrid["data"][0]["contributing_nr"]	= "0";
			}
			$tmpSourceToGrid["data"][0]["UserId"]			= $userId;
			$tmpSourceToGrid["data"][0]["FirstName"]		= $rsgetLatestPost->Field("FirstName");
			$tmpSourceToGrid["data"][0]["SecondName"]		= $rsgetLatestPost->Field("SecondName");
			$tmpSourceToGrid["data"][0]["usr_short"]		= $rsgetLatestPost->Field("usr_short");
			$tmpSourceToGrid["data"][0]["usr_title"]		= $rsgetLatestPost->Field("usr_title");
			$tmpSourceToGrid["data"][0]["usr_diploma"]		= $rsgetLatestPost->Field("usr_diploma");
			$tmpSourceToGrid["data"][0]["usr_email"]		= $rsgetLatestPost->Field("usr_email");
			$tmpSourceToGrid["data"][0]["usr_gender"]		= $rsgetLatestPost->Field("usr_gender");
			$tmpSourceToGrid["data"][0]["Status_user"]		= $rsgetLatestPost->Field("Status_user");
			$tmpSourceToGrid["data"][0]["joined"]			= $rsgetLatestPost->Field("joined");
			

			$this->authorOfCommData[$userId] = $tmpSourceToGrid["data"][0];
			
			//get user photo
			$this->getUserPhoto($userId);
								
			$tmpSourceToGrid["AllRecs"] = 1;	
			WebApp::addVar("MemberData_".$userId,$tmpSourceToGrid);	
			$rsgetLatestPost->MoveNext();
		}							
	}
	
	function getUserLoged() {

		global $session;
		
		$userID	= $session->Vars["ses_userid"];
	
				$getUserInfor = "SELECT coalesce(users.UserId,'') 					as UserId,
									coalesce(users.FirstName,'') 					as FirstName,
									coalesce(users.SecondName,'') 					as SecondName,
									coalesce(users.UserName,'') 					as userName,
									coalesce(users.usr_email,'') 					as usr_email,
									coalesce(date_format(usr_entry,'%M %d.%Y'),'') 	as joined
									
							FROM users
							LEFT JOIN users_extended on users.UserId = users_extended.UserId
							WHERE users.UserId ='".$userID."'";
								 
		$rsUserInfor = WebApp::execQuery($getUserInfor); 	
		
		$tmpSourceToGrid["data"] = array();
		$i=0;
		while (!$rsUserInfor->EOF()) {
			$userId = $rsUserInfor->Field("UserId");

			$tmpSourceToGrid["data"][$i]["UserId"]			= $userId;
			$tmpSourceToGrid["data"][$i]["FirstName"]		= $rsUserInfor->Field("FirstName");
			$tmpSourceToGrid["data"][$i]["SecondName"]		= $rsUserInfor->Field("SecondName");
			$tmpSourceToGrid["data"][$i]["userName"]		= $rsUserInfor->Field("userName");		
			$tmpSourceToGrid["data"][$i]["usr_email"]		= $rsUserInfor->Field("usr_email");

			//get user photo
			$this->getUserPhoto($userId);
			
			$i++;
			$rsUserInfor->MoveNext();	
		}

		$tmpSourceToGrid["AllRecs"] = count($tmpSourceToGrid["data"]);	
		WebApp::addVar("UserGrid",$tmpSourceToGrid);
	
	}
	function getUserPhoto($userId="") {
		if($userId!=""){
			
			/*$geUsers = "SELECT ad.cme_author_name, ad.cme_author_surname,ad.cme_id_author,

									COALESCE(ad.cme_author_title,'') as cme_author_title,
									COALESCE(ad.cme_author_inst,'') as cme_author_inst,
									COALESCE(ad.cme_author_country,'') as cme_author_country,
									COALESCE(ad.cme_author_mail,'') as cme_author_mail,
                                    COALESCE(ad.add_info,'')        as add_info,
                                 
									ad.author_lecture as author_lecture,
									ad.author_editor as author_editor,
									COALESCE(sl.file_id,'') as photoId

							  FROM authors_data as ad
						 LEFT JOIN sl as sl
								ON ad.photo_id=sl.file_id
							 WHERE ad.cme_id_author=" . $id_auth . " ";

            $rs = WebApp::execQuery($geUsers);
			
			if (!$rs->EOF()) {			
				$photoId= $rs->Field("photoId"); 
				CiManagerFe::get_SL_CACHE_INDEX($photoId,'','');
			}
			*/
			
		}
	
	}
	function getNrTopicPost($item_ci_id) {
		global $session;
		
		
		if ($this->thisMode=='') {
				$filterToSqlArrayPub[] =" c.state".$this->lang." not in (0,5,7)";
				$filterToSqlArrayPub[] =" c.published".$this->lang." = 'Y'";
			} else {
				$filterToSqlArrayPub[] =" c.state".$this->lang." not in (7)";
			}
			if(count($filterToSqlArrayPub)>0)
				$filterToSqlStrPub = " AND ".implode("\n AND ", $filterToSqlArrayPub);
		
		
		$getRelData = "SELECT COUNT(cp_data.content_id) AS post_nr
				
					   FROM content  AS c
						  
						 LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
						  
						  JOIN  nivel_4      AS n  ON (    c.id_zeroNivel   = n.id_zeroNivel
											AND c.id_firstNivel  = n.id_firstNivel
											AND c.id_secondNivel = n.id_secondNivel
											AND c.id_thirdNivel  = n.id_thirdNivel
											AND c.id_fourthNivel = n.id_fourthNivel
											)
						  JOIN  profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
								  AND p.id_firstNivel  = c.id_firstNivel
								  AND p.id_secondNivel = c.id_secondNivel
								  AND p.id_thirdNivel  = c.id_thirdNivel
								  AND p.id_fourthNivel = c.id_fourthNivel
							)
							
							".$this->joinTables."

						WHERE p.profil_id in (".$this->tip.")
						AND n.description".$this->lang."".$this->thisMode." IS NOT NULL
						AND n.description".$this->lang."".$this->thisMode." !=''
						AND ci_type in ('CP')
						
							".$this->joinConditions."		
							".$this->filterToSqlDet."

						AND cp_data.topic_id in (".$item_ci_id.")
						".$filterToSqlStrPub."
						
						";								
			
		$rsgetRelData = WebApp::execQuery($getRelData); 
		return $rsgetRelData;
	}
	
	function getLatestTopicPost($item_ci_id) {
		global $session;
		
		$getLatestPost = "SELECT  c.content_id, title".$this->lang." as title,cp_data.topic_id as parent_id,c.author as author,
											
							IF (c.creation_date IS NOT NULL AND c.creation_date !='' ,DATE_FORMAT(c.creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p.%w')  ,'') 
								as source_creation_time_array,
								
							IF (c.creation_date IS NOT NULL AND c.creation_date !='' ,DATE_FORMAT(c.creation_date,'%d.%m.%Y %H:%i:%s')  ,'') 
								as source_creation_time_compare,
								
							if (DATE_FORMAT(c.creation_date,'%u.%Y')=DATE_FORMAT(now(),'%u.%Y'),DATEDIFF(now(),c.creation_date),
								if (DATE_FORMAT(c.creation_date,'%m.%Y')=DATE_FORMAT(now(),'%m.%Y'),'sameMonth'  , 								
								if (DATE_FORMAT(c.creation_date,'%c.%Y')=DATE_FORMAT(now(),'%c.%Y'),'sameYear','dateGrid'								
								)))     							 
								as diffDated

						FROM content	AS c
				
						LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
						
						JOIN	nivel_4			AS n	ON (    c.id_zeroNivel   = n.id_zeroNivel
															AND c.id_firstNivel  = n.id_firstNivel
															AND c.id_secondNivel = n.id_secondNivel
															AND c.id_thirdNivel  = n.id_thirdNivel
															AND c.id_fourthNivel = n.id_fourthNivel
															)
						JOIN	profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
										AND p.id_firstNivel  = c.id_firstNivel
										AND p.id_secondNivel = c.id_secondNivel
										AND p.id_thirdNivel  = c.id_thirdNivel
										AND p.id_fourthNivel = c.id_fourthNivel
							)	
						".$this->joinTables."
		  
						WHERE p.profil_id in (".$this->tip.")
						  AND n.description".$this->lang."".$this->thisMode." IS NOT NULL 
						  AND n.description".$this->lang."".$this->thisMode." !=''
						  AND c.ci_type in ('CP')
	  
							".$this->joinConditions."		
							".$this->filterToSqlDet."
		
						AND cp_data.topic_id in (".$item_ci_id.")
						AND DATE_FORMAT(c.creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p.%w')=(
												SELECT MAX(DATE_FORMAT(c.creation_date,'%d.%m.%Y.%H.%h.%i.%s.%p.%w'))
												FROM content  AS c
												LEFT JOIN cp_data on cp_data.content_id = c.content_id  AND cp_data.lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'

												JOIN  nivel_4      AS n  ON (    c.id_zeroNivel   = n.id_zeroNivel
																	AND c.id_firstNivel  = n.id_firstNivel
																	AND c.id_secondNivel = n.id_secondNivel
																	AND c.id_thirdNivel  = n.id_thirdNivel
																	AND c.id_fourthNivel = n.id_fourthNivel
																	)
												JOIN  profil_rights AS p ON (    p.id_zeroNivel   = c.id_zeroNivel
														  AND p.id_firstNivel  = c.id_firstNivel
														  AND p.id_secondNivel = c.id_secondNivel
														  AND p.id_thirdNivel  = c.id_thirdNivel
														  AND p.id_fourthNivel = c.id_fourthNivel
													)
												  ".$this->joinTables."

												WHERE p.profil_id in (".$this->tip.")
												  AND n.description".$this->lang."".$this->thisMode." IS NOT NULL 
												  AND n.description".$this->lang."".$this->thisMode." !=''
												  AND c.ci_type in ('CP')
												  
													".$this->joinConditions."		
													".$this->filterToSqlDet."
												  
												  
												  AND cp_data.topic_id in (".$item_ci_id."))

						GROUP BY cp_data.topic_id";									

		$rsgetLatestPost = WebApp::execQuery($getLatestPost);
		return $rsgetLatestPost;
	}
	
	
	function reportPost($params) {
		global $session;

		if(isset($params["reportThisID"]) && $params["reportThisID"]!=""){
			if($params["act"]=="report"){
				
				if($params["report_reason"]!="")
					$report_reason	= $params["report_reason"];
				else
					$report_reason	= "";
				
				if($params["comment"]!="")
					$comment	= $params["comment"];
				else
					$comment	= "";
				
				$update_cp = "UPDATE cp_data
								SET post_status 		='reported',
									report_reason		='".ValidateVarFun::f_real_escape_string($report_reason). "',
									report_comment		='".ValidateVarFun::f_real_escape_string($comment). "',
									report_author		='".ValidateVarFun::f_real_escape_string($this->ses_userid). "'
								WHERE content_id 		= '".$params["reportThisID"]."'";
				$rs_update	= WebApp::execQuery($update_cp);			
			}
		}
	}
	
	function removePost($params) {
		global $session;

		if(isset($params["reportThisID"]) && $params["reportThisID"]!=""){
			if($params["act"]=="remove"){
				
				$updateR_cp = "UPDATE cp_data
								SET post_status 		='removed',
									removed_author		='".$this->ses_userid."'
								WHERE content_id 		= '".$params["reportThisID"]."'";
				$rs_updateR_cp	= WebApp::execQuery($updateR_cp);			
			}
		}
	}
	
	function removePostReport($params) {
		global $session;

		if(isset($params["reportThisID"]) && $params["reportThisID"]!=""){

				$updateN_cp = "UPDATE cp_data
								SET post_status 		='normal'
								WHERE content_id 		= '".$params["reportThisID"]."'";
				$rs_updateN_cp	= WebApp::execQuery($updateN_cp);			
			
		}
	}
	

	function dataLifeStyleFormat($nameGrid,$diffDate, $source_creation_time_array, $ciID="") {


				$tempDateGrid = explode(".",$source_creation_time_array);
				
				if ($diffDate=="dateGrid") {
					//data eshte ne vitet paraardhes
					$tmpSourceToGrid["data"][0]["dateIn"] = "previewsYear";	
				
				} elseif ($diffDate=="sameMonth") { //ne muajin aktual
					$tmpSourceToGrid["data"][0]["dateIn"] = "sameMonth";	
				
				} elseif ($diffDate=="sameYear") { //ne vitin aktual
					$tmpSourceToGrid["data"][0]["dateIn"] = "sameYear";	
				
				} else {		//ne javen aktuale
					
					
					if ($diffDate==0) {
						$tmpSourceToGrid["data"][0]["dateIn"] = "today";	
						$tmpSourceToGrid["data"][0]["daysBefore"] = $diffDate;						
					} elseif ($diffDate==1) {
						$tmpSourceToGrid["data"][0]["dateIn"] = "yestarday";	
						$tmpSourceToGrid["data"][0]["daysBefore"] = $diffDate;						
					} else {
					
						$tmpSourceToGrid["data"][0]["dateIn"] = "sameWeek";	
						$tmpSourceToGrid["data"][0]["daysBefore"] = $diffDate;	
					}
				
				}
			
	
				$tmpSourceToGrid["data"][0]["year"]			 	= "".$tempDateGrid[2];	
				$tmpSourceToGrid["data"][0]["month"] 			= "".$tempDateGrid[1];	
				$tmpSourceToGrid["data"][0]["monthLabel"]	 	= "{{_month_".$tempDateGrid[1]."}}";


				$tmpSourceToGrid["data"][0]["monthLabelShort"] 	= "{{_month_short_".$tempDateGrid[1]."}}";
				$tmpSourceToGrid["data"][0]["date"] 			= "".$tempDateGrid[0];	
				
				$tmpSourceToGrid["data"][0]["hour24"] = "".$tempDateGrid[3];	
				$tmpSourceToGrid["data"][0]["hour12"] = "".$tempDateGrid[4];	
				$tmpSourceToGrid["data"][0]["min"] = "".$tempDateGrid[5];	
				$tmpSourceToGrid["data"][0]["sec"] = "".$tempDateGrid[6];	
				$tmpSourceToGrid["data"][0]["pm_am"] = "".$tempDateGrid[7];	
				$tmpSourceToGrid["data"][0]["day_label"] = "{{_day_".$tempDateGrid[8]."}}";
				$tmpSourceToGrid["data"][0]["day_label_short"] = "{{_day_short_".$tempDateGrid[8]."}}";
				
				$tmpSourceToGrid["AllRecs"] = 1;	
		
				if ($ciID!="")			$this->dateInGrid[$ciID] = $tmpSourceToGrid["data"][0];
				else					$this->dateInGrid[$nameGrid] = $tmpSourceToGrid["data"][0];
				//%d.%m.%Y.%H.%h.%i.%P
				WebApp::addVar($nameGrid,$tmpSourceToGrid);	
			
	}
	
	function  sendEmailOnStausChange($params) //funkioni qe con email
    {
/*
$paramsEmail['CID']					= $params['CID'];
$paramsEmail['actoin']				= $params['actoin'];//confirm or notify start stop notify
$paramsEmail['ci_type']				= $this->appRelatedState["CiRights"][$params['CID']]["ci_type"];
$paramsEmail['input_collection']	= $input_collection;
$paramsEmail['proccesing_raw']		= $proccesing_raw;
$paramsEmail['review_structured']	= $review_structured;
*/
        ////perfshirja e mesazheve ne funksionin e emailt
        if ($this->lngId == "1") {//english
            $lang = "English";
        } else {//shqip
            $lang = "Shqip";
        }

        $tplY = new WebBox("buildNavY");
        $message_file = NEMODULES_PATH . "EditCMEMetadata/EditCMEMetadata_" . $lang . ".mesg";

        $tplY->parse_msg_file($message_file);
        extract($GLOBALS["tplVars"]->Vars[0]);

        ////mesazhe-------------------------------------------------------------
        $_Title = $_headline;
        $_ID = $_doc_id;
        $_action = $_action;
        ///----------------------------------------------------------------------

        if (isset($this->MainGridData['ew_title']) && $this->MainGridData['ew_title'] != "") {
            $ew_title = $this->MainGridData['ew_title'];
        } else {
            $ew_title = "";
        }

        if (isset($params['actoin']) && $params['actoin'] == "start") {//confirm
            $action = $_change_status_Confirm;
        } elseif (isset($params['actoin']) && $params['actoin'] == "stop") {//notify
            $action = $_change_status_Notify;
        } else {
        }

        $sendEmail = "N";
        $save_as_option = 1;
        $from_label = STATUS_EMAIL_FROM_LABEL;
        $from = STATUS_EMAIL_FROM;
        $to = STATUS_EMAIL_FROM_TO;
        $subject = STATUS_EMAIL_FROM_LABEL . " - " . $_change_status_subject;
        $message = $_change_status_message;

        //---------------------------------------------email action -----------------------------------------------------------------------------------------
        if ($save_as_option == 1) {
            $MailConfirmation = new htmlMimeMail();
            $from_x = '';
            if ($from_label != '')
                $from_x = "\"" . $from_label . "\" <" . $from . ">";
            else
                $from_x = $from;

            $MailConfirmation->setFrom($from_x);
            $MailConfirmation->setSubject(eregi_replace("'", "", $subject));

            ////shtoje tipin e kongresit tek subjekti i email ne menyre qe administratori ta ket me te lehte te kuptuartit e natyres se tipit te konresit
            $CID = $params['CID'];
            $doc_title = $ew_title;
            $ci_type = $params['ci_type'];

            $message .= "<br/><br/><b>" . $_action . " </b>:" . $action;
            $message .= "<br/><br/><b>" . $_ID . " </b>:" . $CID;
            $message .= "<br/><br/><b>" . $_Title . " </b>:" . $ew_title . "[" . $ci_type . "] <br><br>";
            $message = $this->parseExelGride($message);

            $MailConfirmation->setHtml($message, $text, './');
            $MailConfirmation->setBcc(STATUS_EMAIL_FROM_BCC);
            $result = $MailConfirmation->send(array($to));
            if (!$result) $succes_send = 1;


            /////////////pjesa e dergimit te email-it per feedback/////////////////
        } else {

        }
        //--------------------------------------------------------------------------------------------------------------------------------------------------------
    }
	
}
?>