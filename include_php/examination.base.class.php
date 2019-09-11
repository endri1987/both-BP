<?php

global $sl_file_nedded_data;
require_once(INCLUDE_AJAX_PATH . "/CiManagerFe.class.php");

class examinationBase
{

    var $EvaluationTestContainer = "EvaluationTestContainer.html";
    var $templateDefaultQuestion = "EvaluationTestQuestion.html";
    var $templateDefaultQuestionResults = "EvaluationTestQuestionAnswer.html";

    var $mainEntry = "no";
    var $tagsToBeReplaced = array();
    var $error_no_question = "0";

    /*****************************************************
     *** CONSTRUCTOR OF THE CLASS ****************
     ******************************************************/
    function examinationBase()
    {
        global $session;
        $sessLANG = $session->Vars["lang"];
        if (isset($sessLANG) && $sessLANG != "") {
            if (eregi("Lng", $sessLANG)) {
                $lngIDCode = eregi_replace("Lng", "", $sessLANG) * 1;
                if (!defined("LNG" . $lngIDCode)) {
                    $lngIDCode = 1;
                } else {
                    $session->Vars["lang"] = 'Lng' . $lngIDCode;
                }
            }
        }
        $this->lang = $session->Vars["lang"];
        $this->lngId = eregi_replace("Lng", "", $this->lang);
        $this->thisMode = $session->Vars["thisMode"];
        $this->uniqueid = $session->Vars["uniqueid"];
        if (isset($session->Vars["tip"]) && $session->Vars["tip"] > 0)
            $this->tip = $session->Vars["tip"];
        if (isset($session->Vars["ses_userid"]) && $session->Vars["ses_userid"] > 0)
            $this->userSystemID = $session->Vars["ses_userid"];
        $this->initUserProfile();
        if ($session->Vars["thisMode"]=="_new")
        		$this->thisModeCode = 0;
        else	$this->thisModeCode = 1;          
    }
    function returnNemProp($idstemp = "")
    {
        global $session, $event;
        if ($idstemp == "") $this->idstemp = $session->Vars["idstemp"];
        else                        $this->idstemp = $idstemp;
        $objects = unserialize(base64_decode(WebApp::findNemProp($this->idstemp)));
        $this->NEM_PROP = $objects; //kjo eshte kapur qe te gjitha propertite qe mund te shtohen te kapen aty ku duhen vetme nese duhen
    }
    function initCiReference($cid = "")
    {
        global $session, $sessUserObj;
        if ($cid == "") 			$this->cidFlow = $session->Vars["contentId"];
        else                        $this->cidFlow = $cid;
        $this->getCiReadWriteRights($this->cidFlow);

        $this->ci_type_configuration = $this->appRelatedState["doctype_ID"][$this->cidFlow];
        $this->getStructuredInformationEcc();
        $this->getCiNeededVar($this->cidFlow);
        WebApp::addVar("cidFlowItm", $this->cidFlow);
        
        
        if ($session->Vars["simpleEditAuthoring"] == "t" && $session->Vars["simpleModePreview"] == "yes" 
        			&& ($session->Vars["typeOfUser"] == "BO" || $session->Vars["typeOfUser"] == "AC")) { 
            
            if (isset($this->appRelatedState["CiRights"][$this->cidFlow]["read_write"])
                && $this->appRelatedState["CiRights"][$this->cidFlow]["read_write"] == "W"
            ) {
                $this->PlayerOfQuestionsEditMode = "yes";
            }
        } else {
            $this->PlayerOfQuestionsEditMode = "no";
        }
        
        
        //$this->appRelatedState = $sessUserObj->
            
	/*echo "<textarea>-sessUserObj-".$session->Vars["typeOfUser"];
	print_r($sessUserObj);
	echo "</textarea>";	    */    
        
    }
    function getStructuredInformationEcc()
    {
        $hierarchy_level = $this->appRelatedState["hierarchy_level"][$this->cidFlow];
        if ( //kap metadatat e lecture
            $this->ci_type_configuration == "EL"
            || $this->ci_type_configuration == "CQ"
            || $this->ci_type_configuration == "ES"
            || $this->ci_type_configuration == "RA"
            || $this->ci_type_configuration == "RQ"
            || $this->ci_type_configuration == "RQ"
            || $this->ci_type_configuration == "EQ"
        ) {
            //jemi ne nivelin e sub lecture elements
            //kap hierarkine per te marre informacion - Bottom-Up
            $nivel_condition = array();
            //kap hierarkine
            if ($hierarchy_level == 4) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][2] . "' ";
                $nivel_condition[3] = " content.id_thirdNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][3] . "' ";
            } elseif ($hierarchy_level == 3) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][1] . "' ";
                $nivel_condition[2] = " content.id_secondNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][2] . "' ";

            } elseif ($hierarchy_level == 2) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][0] . "' ";
                $nivel_condition[1] = " content.id_firstNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][1] . "' ";

            } elseif ($hierarchy_level == 1) {
                $nivel_condition[0] = " content.id_zeroNivel = '" . $this->appRelatedState["coord"][$this->cidFlow][0] . "' ";
            }

            if (count($nivel_condition) > 0) {
                $kushtSql = implode(" AND ", $nivel_condition);
                $sql_con = "SELECT content_id, ci_type, group_concat(`read_write`) as rights,
										coalesce(doctype_description,'document') as doctype_name,ci_type,
										content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel,
										titleLng1 as title
										
										
								  FROM content
                      					 JOIN document_types ON document_types.doctype_name = content.ci_type
								  JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
															AND content.id_firstNivel  = profil_rights.id_firstNivel
															AND content.id_secondNivel = profil_rights.id_secondNivel
															AND content.id_thirdNivel  = profil_rights.id_thirdNivel
															AND content.id_fourthNivel = profil_rights.id_fourthNivel
															AND profil_rights.profil_id in (" . $this->tip . ")
														)                    
								WHERE " . $kushtSql . "
									  AND orderContent   = '0'
							 GROUP BY content_id";

                $rs_con = WebApp::execQuery($sql_con);
                while (!$rs_con->EOF() AND mysql_errno() == 0) {

                    $ci_type = $rs_con->Field("ci_type");
                    $relid = $rs_con->Field("content_id");
                    $title = $rs_con->Field("title");


                    $this->appRelatedState["LECTURE_RELATED"][$ci_type] = $relid;

                    $rights = explode(",", $rs_con->Field("rights"));
                    if (is_array($rights) && in_array("W", $rights))
                        $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["read_write"] = "W";
                    else    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["read_write"] = "R";

                    $this->getCiNeededVar($relid);

                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["title"] = $title;
                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["actualNodeDescription"] = $this->mixedNeededData["ci"][$relid]["DC"]["actualNodeDescription"];

                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["doctype_name"] = $rs_con->Field("doctype_name");

                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["coord"][0] = $rs_con->Field("id_zeroNivel");
                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["coord"][1] = $rs_con->Field("id_firstNivel");
                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["coord"][2] = $rs_con->Field("id_secondNivel");
                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["coord"][3] = $rs_con->Field("id_thirdNivel");
                    $this->appRelatedState["LECTURE_RELATED_INFO"][$relid]["coord"][4] = $rs_con->Field("id_fourthNivel");

                    $rs_con->MoveNext();
                }
            }
        }
    }
    function getCiReadWriteRights($ids = "")
    {
        $dataToReturn = "";
        $sql_con = "SELECT content_id, ci_type, group_concat(`read_write`) as rights,
							coalesce(doctype_description,'') as doctype_description,		 
							content.id_zeroNivel, content.id_firstNivel,content.id_secondNivel,content.id_thirdNivel,content.id_fourthNivel

                      FROM content
                       JOIN document_types ON document_types.doctype_name = content.ci_type
                      
                      JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
												AND content.id_firstNivel  = profil_rights.id_firstNivel
												AND content.id_secondNivel = profil_rights.id_secondNivel
												AND content.id_thirdNivel  = profil_rights.id_thirdNivel
												AND content.id_fourthNivel = profil_rights.id_fourthNivel
												AND profil_rights.profil_id in (" . $this->tip . ")
											)                    
                    WHERE content.content_id   = '" . $ids . "' 
                 GROUP BY content_id";

        $rs_con = WebApp::execQuery($sql_con);
        IF (!$rs_con->EOF() AND mysql_errno() == 0) {
            //$dataToReturn["id"] 	   		= $rs_con->Field("content_id");

            $id = $rs_con->Field("content_id");

            $this->appRelatedState["doctype_name"][$id] = $rs_con->Field("doctype_name");
            $this->appRelatedState["doctype_description"][$id] = $rs_con->Field("doctype_description");

            $this->appRelatedState["doctype_ID"][$id] = $rs_con->Field("ci_type");

            $this->appRelatedState["coord"][$id][0] = $rs_con->Field("id_zeroNivel");
            $this->appRelatedState["coord"][$id][1] = $rs_con->Field("id_firstNivel");
            $this->appRelatedState["coord"][$id][2] = $rs_con->Field("id_secondNivel");
            $this->appRelatedState["coord"][$id][3] = $rs_con->Field("id_thirdNivel");
            $this->appRelatedState["coord"][$id][4] = $rs_con->Field("id_fourthNivel");

            if ($this->appRelatedState["coord"][$id][4] > 0)
                $this->appRelatedState["hierarchy_level"][$id] = 4;
            elseif ($this->appRelatedState["coord"][$id][3] > 0)
                $this->appRelatedState["hierarchy_level"][$id] = 3;
            elseif ($this->appRelatedState["coord"][$id][2] > 0)
                $this->appRelatedState["hierarchy_level"][$id] = 2;
            elseif ($this->appRelatedState["coord"][$id][1] > 0)
                $this->appRelatedState["hierarchy_level"][$id] = 1;
            else    $this->appRelatedState["hierarchy_level"][$id] = 0;

            $dataToReturn["ci_type"] = $rs_con->Field("ci_type");

            $rights = explode(",", $rs_con->Field("rights"));
            if (is_array($rights) && in_array("W", $rights))
                $dataToReturn["read_write"] = "W";
            else    $dataToReturn["read_write"] = "R";
        }
        $this->appRelatedState["CiRights"][$id] = $dataToReturn;
    }

    function testPassedQuestions($evaluation_test_id)
    {
        $sql = "SELECT count(1) as cnt FROM z_EccE_user_examination_question WHERE  test_id = '" . $evaluation_test_id . "'";
        $rs = WebApp::execQuery($sql);
        if (!$rs->EOF()) $cnt = $rs->Field("cnt");
        else                $cnt = 0;
        return $cnt;
    }

    function removeUnfinishedEvaluation()
    {
        ////////////////echo "<hr>".$this->assesment_id."--removeUnfinishedSession--";
        /*$get_unfinished_session_main = "
				SELECT test_id 
				  FROM z_EccE_user_examination
				 WHERE test_state = 'init' || test_state = 'running'
				   AND user_id				= '".$this->userSystemID."'
				   AND examination_id 		= ".$this->cidFlow."";
			//AND filled_sessions		!= '0'
			$rs_get_unfinished_session_main = WebApp::execQuery($get_unfinished_session_main);
			
			while  (!$rs_get_unfinished_session_main->Eof()) {
				
				$del_test_id= $rs_get_unfinished_session_main->Field("test_id");
				$delete_from_kurs 			= "DELETE FROM z_EccE_user_examination 					WHERE test_id=".$del_test_id;
				WebApp::execQuery($delete_from_kurs);
				$delete_from_kurs_session 	= "DELETE FROM z_EccE_user_examination_question 		WHERE test_id=".$del_test_id;
				WebApp::execQuery($delete_from_kurs_session);
				
				$rs_get_unfinished_session_main->MoveNext();
			//	//////////////echo "fshihen gjerat";
			}	*/
    }

    function resetAllUserDataForExamination($player_test_id)
    {
        //kontrollo nese useri ka plotesuar survey


			

			
		
			


		if (isset($this->appRelatedState["LECTURE_RELATED"]["EL"]) && $this->appRelatedState["LECTURE_RELATED"]["EL"] > 0) {					
			$referenceId = $this->appRelatedState["LECTURE_RELATED"]["EL"];
			
			$workingCiSurveyC = new CiManagerFe($referenceId, $session->Vars["lang"]);
			$confSurvey = $workingCiSurveyC->getSurveyConfigurationInParentNodes($this->thisModeCode,"EL",$referenceId);
			
			if ($confSurvey["type_of_survey"]=="internal" && $confSurvey["target_survey_ci"]=="") {
				
				if (isset($this->appRelatedState["LECTURE_RELATED"]["US"]) && $this->appRelatedState["LECTURE_RELATED"]["US"] > 0) {	
					$confSurvey["target_survey_ci"]	= $this->appRelatedState["LECTURE_RELATED"]["US"];
				}
			}
		}




        $get_unfinished_session_main = "
				SELECT test_id, 
					   coalesce(automaticSurvey,'no') as automaticSurvey, 
					   coalesce(automaticSurveyId,'0') as automaticSurveyId, 
					   coalesce(automaticSurveyOpened,'no') as automaticSurveyOpened
				  FROM z_EccE_user_examination
				 WHERE user_id				= '" . $this->userSystemID . "'
				   AND examination_id 		= " . $player_test_id . "";

        $rs_get_unfinished_session_main = WebApp::execQuery($get_unfinished_session_main);
        while (!$rs_get_unfinished_session_main->Eof()) {

            $automaticSurvey 		= $rs_get_unfinished_session_main->Field("automaticSurvey");
            $automaticSurveyId 		= $rs_get_unfinished_session_main->Field("automaticSurveyId");
            $automaticSurveyOpened 	= $rs_get_unfinished_session_main->Field("automaticSurveyOpened");
            
            if ($automaticSurvey=="yes" && $automaticSurveyOpened=="yes" &&  $automaticSurveyId>0) {
				$getSurveyRelated = "
						SELECT test_id as surveyIdToRemove
						  FROM z_survey_user_exam
						 WHERE user_id				= '".$this->userSystemID."'
						   AND related_to_ci 		= '".$automaticSurveyId."'";	
				$rs_getSurveyRelated = WebApp::execQuery($getSurveyRelated);
				while (!$rs_getSurveyRelated->Eof()) {

					$surveyIdToRemove	= $rs_getSurveyRelated->Field("surveyIdToRemove");
					$deleteS = "DELETE FROM z_survey_user_exam WHERE test_id='".$surveyIdToRemove."'";
					WebApp::execQuery($deleteS);
					$deleteS = "DELETE FROM z_survey_user_question WHERE test_id='".$surveyIdToRemove."'";
					WebApp::execQuery($deleteS);

					$rs_getSurveyRelated->MoveNext();
				}
            } 
            
            if (isset($confSurvey["target_survey_ci"]) && $confSurvey["target_survey_ci"]!="") {
            	$automaticSurveyId = $confSurvey["target_survey_ci"];
				$getSurveyRelated = "
						SELECT test_id as surveyIdToRemove
						  FROM z_survey_user_exam
						 WHERE user_id				= '".$this->userSystemID."'
						   AND examination_id 		= '".$automaticSurveyId."'";	
				$rs_getSurveyRelated = WebApp::execQuery($getSurveyRelated);
				while (!$rs_getSurveyRelated->Eof()) {

					$surveyIdToRemove	= $rs_getSurveyRelated->Field("surveyIdToRemove");
					$deleteS = "DELETE FROM z_survey_user_exam WHERE test_id='".$surveyIdToRemove."'";
					WebApp::execQuery($deleteS);
					$deleteS = "DELETE FROM z_survey_user_question WHERE test_id='".$surveyIdToRemove."'";
					WebApp::execQuery($deleteS);

					$rs_getSurveyRelated->MoveNext();
				}            
            }
            
          
            
            $del_test_id 			= $rs_get_unfinished_session_main->Field("test_id");
            $delete_from_kurs = "DELETE FROM z_EccE_user_examination 					WHERE test_id=" . $del_test_id;
            WebApp::execQuery($delete_from_kurs);
            $delete_from_kurs_session = "DELETE FROM z_EccE_user_examination_question 		WHERE test_id=" . $del_test_id;
            WebApp::execQuery($delete_from_kurs_session);
            $delete_from_kurs_session = "DELETE FROM z_EccE_user_examination_certificate 		WHERE test_id=" . $del_test_id;
            WebApp::execQuery($delete_from_kurs_session);
            $rs_get_unfinished_session_main->MoveNext();
        }
    }
    function resetQuestionForExamination($player_test_id, $question_id, $examination_id)
    {
        $get_unfinished_session_main = "
				SELECT test_id 
				  FROM z_EccE_user_examination
				 WHERE user_id				= '" . $this->userSystemID . "'
				   AND examination_id 		= '" . $player_test_id . "'
				   AND test_id = '" . $examination_id . "'";

        $rs_get_unfinished_session_main = WebApp::execQuery($get_unfinished_session_main);
        if (!$rs_get_unfinished_session_main->Eof()) {

            $reset_test_id = $rs_get_unfinished_session_main->Field("test_id");
            $sql_update = "
					UPDATE z_EccE_user_examination_question
					   SET container_user_input = '',
						   user_point 			= '0',
						   question_state 		= 'new',
						   filled_state 		= 'new',
						   question_state 		= 'new',
						   end_time 			= '" . date("H:i:s") . "'

					 WHERE test_id 		= '" . $reset_test_id . "'
					   AND question_id	= '" . $question_id . "'";
            WebApp::execQuery($sql_update);

            $getState = "SELECT count(1) as nr_filled
							   FROM z_EccE_user_examination_question
							  WHERE test_id = '" . $reset_test_id . "'
								AND question_state not in ('new','init')";

            $rsGetState = WebApp::execQuery($getState);
            $filled_sessions = $rsGetState->Field("nr_filled");

            $sql_update = "
					UPDATE z_EccE_user_examination
					   SET filled_sessions 	= '" . $filled_sessions . "',
						   test_state		= 'running'	
					 WHERE test_id 			= '" . $reset_test_id . "'";
            WebApp::execQuery($sql_update);
        }
    }

    function getTestHistoryResults()
    {
        $GridSessionProp["data"] = array();

        $indQ = 0;
        //gjenden totalet per plotesimet ne lidhje me assesment
        $select_totals_status = "
						SELECT  test_id,
			
								date_format(date_of_test,'%d.%m.%Y') as date_of_test,
								date_format(begin_time,'%H:%i:%s') as begin_time, 			

								date_format(date_of_test_end,'%d.%m.%Y') as date_of_test_end,
								date_format(end_time,'%H:%i:%s') as end_time, 			

								total_user_time,

								if (date_of_test!=date_of_test_end, 'no','yes') as same_date,

								'bg-color-green' as actual,

								if (test_state='Evaluated',1,0) as finished,
								if (test_state='running' ,1,0) as runinng,
								if (test_state='readyToEvaluate' ,1,0) as readyToEvaluate,
								if (test_state='Evaluated' AND results_state = 'passed',1,0) as passed,
								if (test_state='Evaluated' AND results_state = 'not_passed',1,0) as not_passed,

								if (test_state='Evaluated' AND results_state = 'passed','green','red') as fa_color,
								if (test_state='Evaluated' AND results_state = 'passed','fa-graduation-cap','fa-user-times') as fa_icon,
								if (test_state='Evaluated' AND results_state = 'passed','yes','no') as is_passed,

								total_points,	
								total_user_points,	
								user_points_perqindje,
								per_points_to_pass			
						 
						 FROM z_EccE_user_examination
							
				 WHERE user_id				= '" . $this->userSystemID . "'
				   AND examination_id 		= " . $this->cidFlow . "
				   AND test_state in ('readyToEvaluate','Evaluated')
				   ORDER BY test_id desc";

        //`test_state` enum('new','init','running','readyToEvaluate','Evaluated') 
        $rs__totals_status = WebApp::execQuery($select_totals_status);

        while (!$rs__totals_status->EOF()) {

            $GridSessionProp["data"][$indQ]["oidH"] = $rs__totals_status->Field("test_id");
            $this->getFullTestStructureToViewResults($GridSessionProp["data"][$indQ]["oidH"]);

            $indQ++;
            $rs__totals_status->MoveNext();
        }

        $GridSessionProp["AllRecs"] = count($GridSessionProp["data"]);
        WebApp::addVar("TestHistoryResultsToGrid", $GridSessionProp);

    }

    function getFullTestStructureToViewResults()
    {
        $GridSessionProp["data"] = array();

        $GridSessionProp["totals"]["started"] = 0;
        $GridSessionProp["totals"]["nr_test_done"] = 0;
        $GridSessionProp["totals"]["is_running"] = 0;
        $GridSessionProp["totals"]["is_evaluated"] = 0;
        $GridSessionProp["totals"]["nr_certificate"] = 0;
        $GridSessionProp["totals"]["not_passed"] = 0;


        //	echo $this->actual_question_id.":actual_question_id-getFullTestStructureToViewResults";

        $indQ = 0;

        //gjenden totalet per plotesimet ne lidhje me assesment
        $select_totals_status = "
						SELECT  test_id,
			
			
							date_format(date_of_test,'%d.%m.%Y') as date_of_test,
							date_format(begin_time,'%H:%i:%s') as begin_time, 			

							date_format(date_of_test_end,'%d.%m.%Y') as date_of_test_end,
							date_format(end_time,'%H:%i:%s') as end_time, 			

							total_user_time,

							if (date_of_test!=date_of_test_end, 'no','yes') as same_date,

							'bg-color-green' as actual,

							if (test_state='Evaluated',1,0) as finished,
							if (test_state='running' ,1,0) as runinng,
							if (test_state='readyToEvaluate' ,1,0) as readyToEvaluate,
							if (test_state='Evaluated' AND results_state = 'passed',1,0) as passed,
							if (test_state='Evaluated' AND results_state = 'not_passed',1,0) as not_passed,

							if (test_state='Evaluated' AND results_state = 'passed','green','red') as fa_color,
							if (test_state='Evaluated' AND results_state = 'passed','fa-graduation-cap','fa-user-times') as fa_icon,
							if (test_state='Evaluated' AND results_state = 'passed','yes','no') as is_passed,

							coalesce(time_spent,0) as time_spent,
							coalesce(time_spent_server,0) as time_spent_server,
							coalesce(timer_response,0) as timer_response,
							coalesce(time_allowed,1) as time_allowed,
							
							coalesce(automaticSurvey,'no') as automaticSurvey,
							coalesce(automaticSurveyOpened,'no') as automaticSurveyOpened,

							total_points,	
							total_user_points,	
							user_points_perqindje,
							per_points_to_pass			
						 
			FROM z_EccE_user_examination
						
			WHERE test_id = '" . $this->evaluation_test_id . "'";



 



        $rs__totals_status = WebApp::execQuery($select_totals_status);
        if (!$rs__totals_status->EOF()) {

            
            
            $this->EvaluationState["surveyRelated"]["automaticSurvey"] 		 = $rs__totals_status->Field("automaticSurvey");
            $this->EvaluationState["surveyRelated"]["automaticSurveyOpened"] = $rs__totals_status->Field("automaticSurveyOpened");
            
            
            
            
            $GridSessionProp["totals"]["started"] += 1;
            $GridSessionProp["totals"]["nr_test_done"] += $rs__totals_status->Field("finished");
            $GridSessionProp["totals"]["is_running"] += $rs__totals_status->Field("runinng");
            $GridSessionProp["totals"]["is_evaluated"] += $rs__totals_status->Field("readyToEvaluate");
            $GridSessionProp["totals"]["nr_certificate"] += $rs__totals_status->Field("passed");
            $GridSessionProp["totals"]["not_passed"] += $rs__totals_status->Field("not_passed");

            $timer_remained = $rs__totals_status->Field("timer_response");

            $time_allowed = $rs__totals_status->Field("time_allowed");
            $GridSessionProp["data"][$indQ]["total_time_allowed"] = round($time_allowed / 60, 0);
            $time_allowed_formated = $this->secondsToTime($time_allowed);
            $GridSessionProp["data"][$indQ]["total_time_allowed_formated"] = $time_allowed_formated;


            //time_spent //time_spent_server	 //timer_response

            $time_spent = $rs__totals_status->Field("time_spent");
            $time_spent_server = $rs__totals_status->Field("time_spent_server");

            $time_spended_by_the_user = $time_spent_server;
            $GridSessionProp["data"][$indQ]["time_spended_by_the_user"] = round($time_spended_by_the_user / 60, 0);

            $time_spended_by_the_user_formated = $this->secondsToTime($time_spended_by_the_user);
            $GridSessionProp["data"][$indQ]["time_spended_by_the_user_formated"] = $time_spended_by_the_user_formated;

            $GridSessionProp["data"][$indQ]["currOID"] = $this->evaluation_test_id;

            $GridSessionProp["data"][$indQ]["fullContentResultSession"] = $this->getPlaceholdersToReplace($this->EvaluationState["configuration"]["fullContentResult"]);

            $this->tagsToBeReplaced["total_time_allowed"] = $GridSessionProp["data"][$indQ]["total_time_allowed"];
            $this->tagsToBeReplaced["total_time_allowed_formated"] = $GridSessionProp["data"][$indQ]["total_time_allowed_formated"];
            $this->tagsToBeReplaced["time_spended_by_the_user"] = $GridSessionProp["data"][$indQ]["time_spended_by_the_user"];
            $this->tagsToBeReplaced["time_spended_by_the_user_formated"] = $GridSessionProp["data"][$indQ]["time_spended_by_the_user_formated"];

            $GridSessionProp["data"][$indQ]["same_date"] = $rs__totals_status->Field("same_date");

            $GridSessionProp["data"][$indQ]["date_of_test"] = $rs__totals_status->Field("date_of_test");
            $GridSessionProp["data"][$indQ]["begin_time"] = $rs__totals_status->Field("begin_time");

            $GridSessionProp["data"][$indQ]["date_of_test_end"] = $rs__totals_status->Field("date_of_test_end");
            $GridSessionProp["data"][$indQ]["end_time"] = $rs__totals_status->Field("end_time");


            $GridSessionProp["data"][$indQ]["fa_icon"] = $rs__totals_status->Field("fa_icon");
            $GridSessionProp["data"][$indQ]["is_passed"] = $rs__totals_status->Field("is_passed");
            $GridSessionProp["data"][$indQ]["fa_color"] = $rs__totals_status->Field("fa_color");

            $GridSessionProp["data"][$indQ]["test_id"] = $rs__totals_status->Field("test_id");

            $GridSessionProp["data"][$indQ]["total_points"] = number_format($rs__totals_status->Field("total_points"), 1);
            $GridSessionProp["data"][$indQ]["total_user_points"] = number_format($rs__totals_status->Field("total_user_points"), 1);

            $GridSessionProp["data"][$indQ]["user_points_perqindje"] = number_format($rs__totals_status->Field("user_points_perqindje"), 0);
            $GridSessionProp["data"][$indQ]["per_points_to_pass"] = number_format($rs__totals_status->Field("per_points_to_pass"), 0);


            $this->tagsToBeReplaced["user_points_percentage"] = $GridSessionProp["data"][$indQ]["user_points_perqindje"];
            $this->tagsToBeReplaced["total_user_points"] = $GridSessionProp["data"][$indQ]["total_user_points"];


            $this->tagsToBeReplaced["total_points"] = $GridSessionProp["data"][$indQ]["total_points"];


            //	_tag_trafficLightCase _tag_userTime _tag_user_traffic_light_feedback_tag	

            $GridSessionProp["data"][$indQ]["enableTrafficLightFeedback"] = "no";
            if (isset($this->user_report_rules["traffic_light_feedback"]) && $this->user_report_rules["traffic_light_feedback"] == "yes") {

                $GridSessionProp["data"][$indQ]["enableTrafficLightFeedback"] = "yes";

                $redLimit = $this->user_report_rules["traffic_light_red"];
                $orangeLimit = $this->user_report_rules["traffic_light_orange"];

                if ($GridSessionProp["data"][$indQ]["user_points_perqindje"] <= $redLimit) {
                    $GridSessionProp["data"][$indQ]["trafficLightCase"] = "red";
                    $this->tagsToBeReplaced["trafficLightCase"] = "red";

                } elseif ($GridSessionProp["data"][$indQ]["user_points_perqindje"] <= $orangeLimit) {
                    $GridSessionProp["data"][$indQ]["trafficLightCase"] = "amber";
                    $this->tagsToBeReplaced["trafficLightCase"] = "amber";
                } else {
                    $GridSessionProp["data"][$indQ]["trafficLightCase"] = "green";
                    $this->tagsToBeReplaced["trafficLightCase"] = "green";

                }
                $this->tagsToBeReplaced["user_traffic_light_feedback_tag"] = $this->modulDynamicMessages["trafficLight"][$this->tagsToBeReplaced["trafficLightCase"]];
            } else {


                if ($GridSessionProp["data"][$indQ]["user_points_perqindje"] < $this->evaluation_rules["per_points_to_pass"]) {
                    $this->tagsToBeReplaced["IconTag"] = "<i class=\"fa fa-exclamation-triangle\"></i>";
                    $this->tagsToBeReplaced["examinationColorCase"] = "red";
                    $this->tagsToBeReplaced["UserResultRelatedTag"] = $this->modulDynamicMessages["result"]["Failed"];
                    $this->tagsToBeReplaced["CmeCreditsRelatedTag"] = $this->modulDynamicMessages["cmeCredits"]["Failed"];
                } else {


                    $this->tagsToBeReplaced["IconTag"] = "<i class=\"fa fa-graduation-cap\"></i>";
                    $this->tagsToBeReplaced["examinationColorCase"] = "green";
                    $this->tagsToBeReplaced["UserResultRelatedTag"] = $this->modulDynamicMessages["result"]["Passed"];
                    $this->tagsToBeReplaced["CmeCreditsRelatedTag"] = $this->modulDynamicMessages["cmeCredits"]["Passed"];

/*
    [user_report_rules] => Array
        (
            [user_certificate] => yes
            [cme_credits] => no
        )
        
        certificate_evaluation

*/
                    if ($this->user_report_rules["user_certificate"] == "yes") {

                        if ($this->user_report_rules["certificate_evaluation"] == "automatic") {

                            $this->tagsToBeReplaced["CertificateRelatedTag"] = $this->modulDynamicMessages["certificate"]["immediate"];
                            $controllCert = "SELECT COUNT(1) as  exist
												   FROM z_EccE_user_examination_certificate
												  WHERE test_id = '" . $this->evaluation_test_id . "'";
                            $rsControllCert = WebApp::execQuery($controllCert);
                            if (!$rsControllCert->EOF()) {
                                $this->tagsToBeReplaced["CertificateRelatedTag"] .=
                                    "<br><a class=\"btn btn-xs btn-default view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D" . $this->evaluation_test_id . "\">{{_tag_view_certificate}}</a>";
                            }

                        } elseif ($this->user_report_rules["certificate_evaluation"] == "deadline") {


                            //certificate_evaluation_days
                            $this->tagsToBeReplaced["CertificateRelatedTag"] = $this->modulDynamicMessages["certificate"]["later"];
                            $this->tagsToBeReplaced["certificate_days"] = $this->user_report_rules["certificate_evaluation_days"];

                            $getDifDate = "SELECT DATEDIFF(now(), date_of_test_end)  as nrDays, 
													date_of_test_end, 
													date_of_test,
													
													now() as now_date
													
													
											     FROM z_EccE_user_examination
											    WHERE test_id = '" . $this->evaluation_test_id . "'";
                            $rs__getDifDate = WebApp::execQuery($getDifDate);
                            if (!$rs__getDifDate->EOF()) {
                                $nrDays = $rs__getDifDate->Field("nrDays");
                                $date_of_test = $rs__getDifDate->Field("date_of_test");
                                $date_of_test_end = $rs__getDifDate->Field("date_of_test_end");
                                $now_date = $rs__getDifDate->Field("now_date");

                                if ($this->user_report_rules["certificate_evaluation_days"] < $nrDays) {
                                    $this->tagsToBeReplaced["CertificateRelatedTag"] .=
                                        "<br><a class=\"btn btn-xs btn-default view-pdf\" href=\"{{APP_URL}}get_certificate.php%3Ftest_id%3D" . $this->evaluation_test_id . "\">{{_tag_view_certificate}}</a>";

                                }
                            }
                        }
                    }


                }
            }


            $this->tagsToBeReplaced["date_of_test"] = $GridSessionProp["data"][$indQ]["date_of_test"];
            $this->tagsToBeReplaced["begin_time"] = $GridSessionProp["data"][$indQ]["begin_time"];
            $this->tagsToBeReplaced["end_time"] = $GridSessionProp["data"][$indQ]["end_time"];

            if (isset($this->tagsToBeReplaced) && count($this->tagsToBeReplaced) > 0) {
                reset($this->tagsToBeReplaced);
                while (list($key, $value) = each($this->tagsToBeReplaced)) {
                    $GridSessionProp["data"][$indQ]["_tag_$key"] = "$value";
                }
            }


/*echo "<textarea>GridSessionProp";
print_r($GridSessionProp);
echo "</textarea>";	*/


            $GridSessionProp["data"][$indQ]["actual_test"] = $rs__totals_status->Field("actual");

            $QuestionProp = array("data" => array(), "AllRecs" => "0");
            $indQQ = 0;
            $select_finished_session_info = "
					SELECT question_id
					  FROM z_EccE_user_examination_question
					 WHERE test_id		= '" . $this->evaluation_test_id . "'
					  ORDER BY sequence_id ASC";
            $rs_main = WebApp::execQuery($select_finished_session_info);
            while (!$rs_main->Eof()) {

                $questionid = $rs_main->Field("question_id");
                if (!isset($this->actual_question_id) || $this->actual_question_id == "")
                    $this->actual_question_id = $questionid;

                $QuestionProp["data"][$indQQ++]["q_id"] = $questionid;
                //$this->getCiNeededVar($questionid);
                //$this->getQuestionResult($questionid);			
                $rs_main->MoveNext();
            }

            $QuestionProp["AllRecs"] = count($QuestionProp["data"]);
            WebApp::addVar("AllQuestionToResultGrid", $QuestionProp);
        }
        $this->report_assesement_user_Results = $GridSessionProp;
        //echo $this->actual_question_id.":actual_question_id-getFullTestStructureToViewResults";
    }

    function format_points($point)
    {
        $point = number_format($point, 1);
        return $point;
    }

    function getQuestionResult($actual_question_id = "")
    {

        if ($actual_question_id == "") $actual_question_id = $this->actual_question_id;

        $QuestionProp = array("data" => array(), "AllRecs" => "0");
        $indQ = 0;

        $val_DC = $this->EvaluationState["question"]["structure"][$actual_question_id]["DC"];
        $val_EX = $this->EvaluationState["question"]["structure"][$actual_question_id]["EX"];
        $val_OPT = $this->EvaluationState["question"]["structure"][$actual_question_id]["OPT"];
        $val_DCA = $this->EvaluationState["question"]["structure"][$actual_question_id]["DCA"];

        $q_id = $actual_question_id;
        $q_type = $val_EX["question_type"];

        $QuestionProp["data"][$indQ]["q_type"] = $q_type;
        $QuestionProp["data"][$indQ]["q_id"] = $q_id;
        $QuestionProp["data"][$indQ]["q_name"] = $val_DCA["question_title"];
        $QuestionProp["data"][$indQ]["q_description"] = $val_DCA["question_abstract"];
        $QuestionProp["data"][$indQ]["dp_q_description"] = $val_DCA["exist_abst"];
        $QuestionProp["data"][$indQ]["dp_fullContent"] = $val_DCA["question_full_content_exist"];
        $QuestionProp["data"][$indQ]["fullContent"] = $val_DCA["question_full_content"];
       // $QuestionProp["data"][$indQ]["dp_fullContentResult"] = $val_DCA["fullContentResult_exist"];
       // $QuestionProp["data"][$indQ]["fullContentResult"] = $val_DCA["fullContentResult"];
        
        
        $QuestionProp["data"][$indQ]["fullContentQResult_exist"] = $val_DCA["question_fullContentResult_exist"];
        $QuestionProp["data"][$indQ]["fullContentQResult"] = $val_DCA["question_fullContentResult"];
        
        

        $userSelection = array();
        $optionsShownToTheUser = array();
        $GridResultQuestionToGrid = array("data" => array(), "AllRecs" => "1");

        $getProp = "SELECT 	coalesce(order_passed,'') as order_passed,sequence_id,
							coalesce(begin_time,'') as begin_time, 
							coalesce(end_time,'') as end_time,
							
							TIMEDIFF(end_time, begin_time) as userTime,
							
							coalesce(timer_response,'') as timer_response,  

							coalesce(question_state,'') as question_state,
							coalesce(results_state,'') as results_state,  

							coalesce(max_point,'') as max_point,
							coalesce(min_point,'') as min_point, 
							coalesce(user_point,'') as user_point, 
							
							coalesce(container_user_input,'') as container_user_input,
							coalesce(options_shown_to_the_user,'') as options_shown_to_the_user

					  FROM z_EccE_user_examination_question 
					  
					 WHERE test_id='" . $this->evaluation_test_id . "' AND question_id = '" . $actual_question_id . "'";
        $rs = WebApp::execQuery($getProp);
//sequence_id_order
        if (!$rs->EOF()) {

            $sequence_id = $rs->Field("sequence_id");
            WebApp::addVar("sequence_id_order", $sequence_id);

            $QuestionProp["data"][$indQ]["sequence_id"] = $sequence_id;

            $question_state = $rs->Field("question_state");

            $results_state = $rs->Field("results_state");
            $max_point = $this->format_points($rs->Field("max_point"));
            $min_point = $this->format_points($rs->Field("min_point"));
            $user_point = $this->format_points($rs->Field("user_point"));

            $userTime = $rs->Field("userTime");

            $container_user_input = $rs->Field("container_user_input");
            if ($container_user_input != "") {
                $userSelection = explode(",", $container_user_input);
            }

            $options_shown_to_the_user = $rs->Field("options_shown_to_the_user");
            if ($options_shown_to_the_user != "") {
                $optionsShownToTheUser = explode(",", $options_shown_to_the_user);
            }
            $GridResultQuestionToGrid["data"][0]["results_flag"] = $results_state;

            $GridResultQuestionToGrid["data"][0]["question_state"] = "{{_" . $question_state . "T}}";
            $GridResultQuestionToGrid["data"][0]["results_state"] = "{{_" . $results_state . "T}}";
            $GridResultQuestionToGrid["data"][0]["max_point"] = $max_point;
            $GridResultQuestionToGrid["data"][0]["min_point"] = $min_point;
            $GridResultQuestionToGrid["data"][0]["user_point"] = $user_point;

            $GridResultQuestionToGrid["data"][0]["userTime"] = $userTime;
            $QuestionProp["data"][0]["question_state"] = $question_state;
        }

        //		Points taken: _tag_user_point of _tag_max_point

        $this->tagsToBeReplaced["user_point"] = $user_point;
        $this->tagsToBeReplaced["max_point"] = $max_point;

        $QuestionProp["AllRecs"] = count($QuestionProp["data"]);
        WebApp::addVar("QuestionToGrid", $QuestionProp);
        WebApp::addVar("QuestionToGrid_$actual_question_id", $QuestionProp);

        WebApp::addVar("GridResultQuestionToGrid", $GridResultQuestionToGrid);
        WebApp::addVar("GridResultQuestionToGrid_$q_id", $GridResultQuestionToGrid);

        /*	echo "<textarea>";
			print_r($QuestionToGrid);
			echo "</textarea>";		*/

        $question_max_point = "";
        $question_min_point = "";

        if (isset($val_OPT) && count($val_OPT) > 0) {

            $OptionQuestionProp = array("data" => array(), "AllRecs" => "1");
            $sessionQAct = $val_OPT;
            $indQ = 0;
            reset($sessionQAct);
            while (list($keyi, $valo) = each($sessionQAct)) {

                $this->initMainRecord["question"][$q_id] = $q_id;

                $o_id = $valo["EQItemID"];

                //	if (in_array($o_id, $optionsShownToTheUser)) {

                $o_name = $valo["option_name"];
                $o_desc = "";
                $o_answer = $valo["option_comment"];

                $OptionQuestionProp["data"][$indQ]["valid_response"] = $valo["valid_response"];

                $points_if_selected = $valo["points_if_selected"];
                $points_if_not_selected = $valo["points_if_not_selected"];

                if ($this->evaluation_rules["evaluation_rule"] == "overrided") {
                    if ($valo["valid_response"] == "y") {
                        $points_if_selected 		= $this->evaluation_rules["correct_ifSelected"];
                        $points_if_not_selected 	= $this->evaluation_rules["correct_ifNotselected"];
                    } else {
                        $points_if_selected			= $this->evaluation_rules["incorrect_ifSelected"];
                        $points_if_not_selected		= $this->evaluation_rules["incorrect_ifNotselected"];
                    }
                }

                $is_selected = "";
                if (in_array($o_id, $userSelection)) {
                    $OptionQuestionProp["data"][$indQ]["checked"] = " checked=\"checked\"";
                    $OptionQuestionProp["data"][$indQ]["user_point"] = $this->format_points($points_if_selected);

                    $is_selected = "yes";
                } else {
                    $OptionQuestionProp["data"][$indQ]["checked"] = "";
                    $OptionQuestionProp["data"][$indQ]["user_point"] = $this->format_points($points_if_not_selected);
                    $is_selected = "no";
                }


                $OptionQuestionProp["data"][$indQ]["is_selected"] = $is_selected;
                $OptionQuestionProp["data"][$indQ]["options_true"] = "asn";
                if ($is_selected == "yes") {
                    if ($valo["valid_response"] == "y") {
                        $OptionQuestionProp["data"][$indQ]["options_true"] = "yes";
                    } else {
                        $OptionQuestionProp["data"][$indQ]["options_true"] = "no";
                    }
                }

                /*
								if ($valo["valid_response"]=="y" && $is_selected == "yes") {
									$OptionQuestionProp["data"][$indQ]["options_true"] = "yes";
								
								} elseif ($valo["valid_response"]=="y" && $is_selected == "no") {
									$OptionQuestionProp["data"][$indQ]["options_true"] = "no";
								
								} elseif ($valo["valid_response"]=="n" && $is_selected == "no") {
									$OptionQuestionProp["data"][$indQ]["options_true"] = "asn";
								} else {
									$OptionQuestionProp["data"][$indQ]["options_true"] = "asn";
								}*/

                $OptionQuestionProp["data"][$indQ]["o_id"] = $o_id;
                if ($q_type == "single" || $q_type == "true_false")
                    $OptionQuestionProp["data"][$indQ]["o_type"] = "radio";
                elseif ($q_type == "multi")
                    $OptionQuestionProp["data"][$indQ]["o_type"] = "checkbox";

                $OptionQuestionProp["data"][$indQ]["o_name"] = $o_name;
                if ($o_name != "")
                    $OptionQuestionProp["data"][$indQ]["dp_o_name"] = "yes";
                else
                    $OptionQuestionProp["data"][$indQ]["dp_o_name"] = "no";

                $OptionQuestionProp["data"][$indQ]["o_desc"] = $o_desc;

                if ($o_answer != "")
                    $OptionQuestionProp["data"][$indQ]["dp_o_answer"] = "yes";
                else
                    $OptionQuestionProp["data"][$indQ]["dp_o_answer"] = "no";
                $OptionQuestionProp["data"][$indQ]["o_answer"] = $o_answer;

                $indQ++;
                //	}
            }

            $OptionQuestionProp["AllRecs"] = count($OptionQuestionProp["data"]);
            WebApp::addVar("GridOption_" . $q_id, $OptionQuestionProp);

            /*echo "<textarea>$q_id";
		print_r($GridResultQuestionToGrid);
		print_r($OptionQuestionProp);
		echo "</textarea>";	*/

        }
        $this->getProgressBarGrid();
    }

    function doSubmitInstance($values_posted, $elFormValues)
    {
        global $session, $event;

        if ($values_posted["timer_response"] == "")
            $timer_response = 1;
        else
            $timer_response = $values_posted["timer_response"];

        if ($values_posted["time_used"] == "")
            $time_used = 1;
        else
            $time_used = $values_posted["time_used"];

        $totalTimeUsed = $timer_response + $time_used;

        $allow_back = $values_posted["all_bck"];
        $ass_session_id = $values_posted["s_id"];

        $tmpSession = array();
        $tmpSessionQuestion = array();

        $BllokInformationTemp = array();
        $informacionPerPlotesimitBllokut = array();
        //userit i jane shfaqur pyetjet e meposhtme per tju pergjigjur

        if (count($values_posted["questions"]) > 0) {
            reset($values_posted);
            while (list($questionID, $vl) = each($values_posted["questions"])) {


                $this->actual_question_id = $questionID;
                $this->getCiNeededVar($questionID);

                if (isset($this->EvaluationState["question"]["structure"][$questionID])) {
                    $tmpSessionQuestion = $this->EvaluationState["question"]["structure"][$questionID];
                }

                $BllokInformationTemp[$questionID] = $questionID;

                $q_type = $tmpSessionQuestion["EX"]["question_type"];




/*
echo "<textarea>";
print_r($tmpSessionQuestion);
echo "</textarea>";


    [EX] => Array
        (

            [if_all_selected] => 0.00
            [if_none_selected] => 0.00
     



*/

                $informacionPerPlotesimitBllokut[$questionID]["max_points"] = $tmpSessionQuestion["EX"]["max_point"];
                $informacionPerPlotesimitBllokut[$questionID]["min_points"] = $tmpSessionQuestion["EX"]["min_point"];
                
                $informacionPerPlotesimitBllokut[$questionID]["if_all_selected"] 	= $tmpSessionQuestion["EX"]["if_all_selected"];
                $informacionPerPlotesimitBllokut[$questionID]["if_none_selected"] = $tmpSessionQuestion["EX"]["if_none_selected"];
                
                
                
                
                $informacionPerPlotesimitBllokut[$questionID]["user_points"] = 0;
                $informacionPerPlotesimitBllokut[$questionID]["q_type"] = $q_type;

                $bllokOptions = $tmpSessionQuestion["OPT"];
                $informacionPerPlotesimitBllokut[$questionID]["q_type"] = $q_type;

                /*if ($q_type==3) { //free selection, totali qe duhet te marre useri nga plotesimi, 

					$informacionPerPlotesimitBllokut[$questionID]["max_points"] = $bllokOptions["o_freeSel"];
					$informacionPerPlotesimitBllokut[$questionID]["containerUserInput"] = implode(",",$elFormValues[$questionID]);
					$informacionPerPlotesimitBllokut[$questionID]["results_state"] = "";
				} else {*/

                $llogarit_user_points = "no";

                if (is_array($elFormValues[$questionID]) && count($elFormValues[$questionID]) > 0) {

                    $informacionPerPlotesimitBllokut[$questionID]["containerUserInput"] = implode(",", $elFormValues[$questionID]);
                    $trueOptions = 0;
                    $trueOptionsSelected = 0;

                    reset($bllokOptions);
                    $nrOfOptions = 0;
                    $nrOfOptionsSelected = 0;

                    while (list($kopt, $vopt) = each($bllokOptions)) {

                        $userPoints = 0;
                        $isSelectedByUser = "no";
                        $isRightOption = "no";
                        $nrOfOptions++;
                        if (isset($elFormValues[$questionID][$vopt["EQItemID"]])) {
                            $nrOfOptionsSelected++;
                            $isSelectedByUser = "yes";
                        }

                        if ($vopt["valid_response"] == "y") {
                            $trueOptions++;
                            $isRightOption = "yes";
                            if (isset($elFormValues[$questionID][$vopt["EQItemID"]])) {
                                $trueOptionsSelected++;
                            }
                        }

                       
                        if ($this->evaluation_rules["evaluation_rule"] == "overrided") {

                            if ($isRightOption == "yes") {
                                if ($isSelectedByUser == "yes") {
                                    $userPoints = $this->evaluation_rules["correct_ifSelected"];
                                } else {
                                    $userPoints = $this->evaluation_rules["correct_ifNotselected"];
                                }

                            } else {

                                if ($isSelectedByUser == "yes") {
                                    $userPoints = $this->evaluation_rules["incorrect_ifSelected"];
                                } else {
                                    $userPoints = $this->evaluation_rules["incorrect_ifNotselected"];
                                }
                            }

                        } else {

                            if (isset($elFormValues[$questionID][$vopt["EQItemID"]])) {
                                $userPoints = $vopt["points_if_selected"];
                            } else {
                                $userPoints = $vopt["points_if_not_selected"];
                            }
                        }

                        $informacionPerPlotesimitBllokut[$questionID]["user_points"] += $userPoints;
                    }


                    if ($trueOptionsSelected == 0) {
                        $informacionPerPlotesimitBllokut[$questionID]["results_state"] = "not_passed";
                    } else if ($trueOptionsSelected == $trueOptions) {
                        $informacionPerPlotesimitBllokut[$questionID]["results_state"] = "passed";
                    } elseif ($trueOptionsSelected > 0) {
                        $informacionPerPlotesimitBllokut[$questionID]["results_state"] = "parcial";
                    }

                }
/*

  `results_state` enum('passed','parcial','not_passed','allSel','noneSel','new') COLLATE utf8_unicode_ci DEFAULT 'new',
  `filled_state` enum('someSel','allSel','noneSel','new') COLLATE u
*/
                if ($nrOfOptionsSelected == 0) {
                    $informacionPerPlotesimitBllokut[$questionID]["filledState"] = "noneSel";


                    if ($this->evaluation_rules["evaluation_rule"] == "overrided")
                        	$informacionPerPlotesimitBllokut[$questionID]["user_points"] = $this->evaluation_rules["if_none_selected"];
                    else    $informacionPerPlotesimitBllokut[$questionID]["user_points"] = $informacionPerPlotesimitBllokut[$questionID]["if_none_selected"];

                    $informacionPerPlotesimitBllokut[$questionID]["results_state"] = "not_passed";

                } elseif ($nrOfOptionsSelected == $nrOfOptions && $nrOfOptionsSelected > $trueOptionsSelected) {
                    
                    $informacionPerPlotesimitBllokut[$questionID]["filledState"] = "allSel";
                    
                    if ($this->evaluation_rules["evaluation_rule"] == "overrided")
                        	$informacionPerPlotesimitBllokut[$questionID]["user_points"] = $this->evaluation_rules["if_all_selected"];
                    else    $informacionPerPlotesimitBllokut[$questionID]["user_points"] = $informacionPerPlotesimitBllokut[$questionID]["if_all_selected"];
                
                    $informacionPerPlotesimitBllokut[$questionID]["results_state"] = "not_passed";
                    
                    
                } else
                    $informacionPerPlotesimitBllokut[$questionID]["filledState"] = "someSel";



                //echo $informacionPerPlotesimitBllokut[$questionID]["user_points"]."-q_type----";

                //}

                $this->informacionPerPlotesimitBllokut = $informacionPerPlotesimitBllokut;
            }
        } else {
            $this->error_code = 9; //-useri ka bere submit formen por nuk kane ardhur id e pyetjeve te shfaqura nga submiti
            //return;					
        }

        $session_total_points = 0;
        $session_user_points = 0;

        if (count($informacionPerPlotesimitBllokut) > 0) {
            $questSessions = count($informacionPerPlotesimitBllokut);
            $timer_response = $values_posted["timer_response"] / $questSessions;
            $order_passed = $this->testPassedQuestions($this->evaluation_test_id) + 1;
            while (list($fillSees, $vfss) = each($informacionPerPlotesimitBllokut)) {
                $replaceData = "yes";

                //feedback_model sessionEnd

                //if ($this->configuration_rules["allow_back"] == "n") {

                if ($this->flow_model["feedback_model"] != "sessionEnd") {
                    //para se te futesh informacionet, kontrollo nese jane futur me pare
                    $answerIsSubmitetBefore = "
							SELECT count(1) as exist
							  FROM z_EccE_user_examination_question
							 WHERE test_id 		='" . $this->evaluation_test_id . "' 
							   AND question_id	='" . $fillSees . "'
							   AND question_state not in ('new','init')";

                    $rsAnswerIsSubmitetBefore = WebApp::execQuery($answerIsSubmitetBefore);
                    if (!$rsAnswerIsSubmitetBefore->EOF()) {
                        if ($rsAnswerIsSubmitetBefore->Field("exist") > 0)
                            $replaceData = "no";
                    }
                }

                if ($replaceData == "yes") {

                    if ($vfss["q_type"] == 3) { //free selection, totali qe duhet te marre useri nga plotesimi, 
                        $question_state = "readyToEvaluate";
                    } else {
                        $question_state = "Evaluated";
                    }

                    
					 $vfss["user_points_real"] = $vfss["user_points"];
                    
					 if ($this->evaluation_rules["allow_negative_points"] == "n" && $vfss["user_points"]<0) {
						$vfss["user_points"] = 0;
					 }
     










               
                    
                    $sql_update = "
								UPDATE z_EccE_user_examination_question
								   SET container_user_input 	= '" . $vfss["containerUserInput"] . "',
									   confirmed_user_points	= '" . $vfss["user_points_real"] . "',
									   user_point 				= '" . $vfss["user_points"] . "',
									   results_state 			= '" . $vfss["results_state"] . "',
									   filled_state 			= '" . $vfss["filledState"] . "',
									   question_state 			= '" . $question_state . "',
									   timer_response 			= '" . $values_posted["timer_response"] . "',
									   end_time 				= '" . date("H:i:s") . "',
									   nr_of_responses			= nr_of_responses+1

								 WHERE test_id 		= '" . $this->evaluation_test_id . "'
								   AND question_id	= '" . $fillSees . "'";
                    WebApp::execQuery($sql_update);

                    $getState = "SELECT count(1) as nr_filled
										   FROM z_EccE_user_examination_question
										  WHERE test_id = '" . $this->evaluation_test_id . "'
											AND question_state not in ('new','init')";

                    $rsGetState = WebApp::execQuery($getState);
                    $filled_sessions = $rsGetState->Field("nr_filled");

                    $sql_update = "
								UPDATE z_EccE_user_examination
								   SET filled_sessions 	= '" . $filled_sessions . "',
								   	   test_state		= 'running'	
								 WHERE test_id 			= '" . $this->evaluation_test_id . "'";
                    WebApp::execQuery($sql_update);

                    $sql_update = "
								UPDATE z_EccE_user_examination_question
								   SET order_passed = '" . $filled_sessions . "'
								 WHERE test_id 		= '" . $this->evaluation_test_id . "'
								   AND question_id	= '" . $fillSees . "'";
                    WebApp::execQuery($sql_update);

                    $this->lastSubmitedQuestion = $filled_sessions;
                }
            }
        }

        $this->refreshTimer($timer_response);

        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET end_time = NOW()
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);

        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET time_spent_server = TIME_TO_SEC(TIMEDIFF(end_time, begin_time))
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);


        $this->controllTestInstance("no");
    }

    //`test_state` enum('new','init','running','readyToEvaluate','Evaluated')
    function refreshTimer($timer_response)
    {
        $sql_update = "
			UPDATE z_EccE_user_examination
			   SET time_spent = '" . $timer_response . "',
				   end_time = NOW()
			 WHERE test_id 		= '" . $this->evaluation_test_id . "'
			   
			   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);

        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET time_spent_server = TIME_TO_SEC(TIMEDIFF(end_time, begin_time))
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);

    }

    function refreshTimerForced()
    {
        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET time_spent_server = TIME_TO_SEC(TIMEDIFF(end_time, begin_time))
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);

    }

    function getTimerAverage()
    {

        $this->time_spent = 0;
        $this->time_remaining = 0;
        $this->timeAllowed = 0;

        if ($this->configuration_rules["time_limit"] == "y") {
            if (isset($this->configuration_rules["time_limit_sec_total"]) && $this->configuration_rules["time_limit_sec_total"] > 0) {
                $this->time_remaining = $this->configuration_rules["time_limit_sec_total"];
                $this->timeAllowed = $this->time_remaining;
            }
        }

        $avgPerQuestionM = "";
        $avgUserPerQuestionM = "";
        $evaluateUserAvg = "";

        $avgPerQuestionS = "";
        $avgUserPerQuestionS = "";
        $avgUserPerQuestionS = "--:--";

        $averageCalc = "no";


        /*
			
			if ($this->configuration_rules["time_limit"] == "y") {
				$existRekord = "SELECT TIME_TO_SEC(TIMEDIFF(end_time, begin_time)) as userSpentTime
								  FROM z_EccE_user_examination
								 WHERE examination_id 		= '".$this->cidFlow."'
								   AND test_id 				= '".$this->evaluation_test_id."'
								   AND test_state in ('new','init','running')";

				$rsData = WebApp::execQuery($existRekord);	
				if  (!$rsData->EOF()) {	// gjej strukturen		
					$userSpentTime = $rsData->Field("userSpentTime");
					
		
					
					if ($userSpentTime>$this->configuration_rules["time_limit_sec_total"]) {
						$controlToFinishTheRunningTest = "finish";
					}
				}					
			}	
*/
        if (isset($this->evaluation_test_id) && $this->evaluation_test_id > 0) {

            $getAverage = "
					SELECT coalesce(timer_response,0) as timer_remaining, 
						   coalesce(time_allowed,1) as time_allowed, 
						   coalesce(time_spent,1) as time_spent, 
						   coalesce(total_sessions,1) as total_sessions,
						   coalesce(filled_sessions,0) as filled_sessions,
						   
						   TIMEDIFF(end_time, begin_time) as userAllowedTime,
						   
						   TIME_TO_SEC(TIMEDIFF(end_time, begin_time)) as userSpentTime,
						   
						   test_state

					  FROM z_EccE_user_examination
					 WHERE test_id = '" . $this->evaluation_test_id . "'";
            $rsAverage = WebApp::execQuery($getAverage);
            if (!$rsAverage->Eof()) {

                $timer_remaining = $rsAverage->Field("timer_remaining");
                $time_spent = $rsAverage->Field("time_spent");
                $time_spent = $rsAverage->Field("time_spent");

                $time_allowed = $rsAverage->Field("time_allowed");
                $total_sessions = $rsAverage->Field("total_sessions");
                $filled_sessions = $rsAverage->Field("filled_sessions");
                $userSpentTime = $rsAverage->Field("userSpentTime");

                $this->time_spent = $time_spent;
                $this->time_allowed = $time_allowed;
                $this->time_remaining = $this->time_allowed - $this->time_spent;

                if ($this->configuration_rules["time_limit"] == "y") {
                    $this->time_spent = $userSpentTime;
                }

                $test_state = $rsAverage->Field("test_state");

                $avgPerQuestion = $time_allowed / $total_sessions;
                $HalfAvgPerQuestion = $avgPerQuestion / 4;
                $avgPerQuestionS = $this->secondsToTime($avgPerQuestion, "no");

                $minLimit = round(($avgPerQuestion - $HalfAvgPerQuestion), 0);
                $maxLimit = round(($HalfAvgPerQuestion + $avgPerQuestion), 0);

                $avgPerQuestionM = round($avgPerQuestion / 60);

                $averageCalc = "yes";
                if ($test_state != "new" && $test_state != "init") {

                    if ($filled_sessions > 0) {
                        //$avgUserPerQuestion 	= ($timer_remaining)/($total_sessions-$filled_sessions);
                        //$avgUserPerQuestion 	= ($time_allowed-$timer_remaining)/($filled_sessions);


                        if ($filled_sessions > 0)
                            $avgUserPerQuestion = ($this->time_spent) / ($filled_sessions);
                        $avgUserPerQuestionS = $this->secondsToTime($avgUserPerQuestion, "no");
                        $avgUserPerQuestionM = round($avgUserPerQuestion / 60);

                        if ($total_sessions > 1)
                            $avgPerQuestionMin = ($time_allowed - $timer_remaining) / ($total_sessions - 1);
                        else    $avgPerQuestionMin = 0;

                        $avgPerQuestionMax = ($time_allowed - $timer_remaining) / ($total_sessions + 1);

                        $evaluateUserAvg = "";
                        if ($avgUserPerQuestion >= $minLimit && $avgUserPerQuestion <= $maxLimit) {
                            $evaluateUserAvg = "normal";
                        } elseif ($avgUserPerQuestion > $maxLimit) {
                            $evaluateUserAvg = "bad";
                        } else {
                            $evaluateUserAvg = "good";
                        }
                    }

                    /*if ($avgUserPerQuestionM>$avgPerQuestionM) 
							$evaluateUserAvg = "bad";
						elseif ($avgUserPerQuestionM<$avgPerQuestionMax) 
							$evaluateUserAvg = "good";
						else 
							$evaluateUserAvg = "normal";*/


                }
            }
        }

        if ($averageCalc == "no") {
            if ($this->collect_rules["nr_of_question_to_be_included"] > 0) {
                $avgPerQuestion = $this->configuration_rules["time_limit_sec_total"] / $this->collect_rules["nr_of_question_to_be_included"];
                $avgPerQuestionM = round($avgPerQuestion / 60);
                $avgPerQuestionS = $this->secondsToTime($avgPerQuestion, "no");
            }
        }


        WebApp::addVar("minLimit", $minLimit);
        WebApp::addVar("maxLimit", $maxLimit);
        WebApp::addVar("avgPerQuestion", $avgPerQuestion);
        WebApp::addVar("avgUserPerQuestion", $avgUserPerQuestion);

        WebApp::addVar("userAvgRateClass", $evaluateUserAvg);
        WebApp::addVar("expAvgRate", $avgPerQuestionS);
        WebApp::addVar("userAvgRate", $avgUserPerQuestionS);

        if (isset($this->evaluation_test_id) && $this->evaluation_test_id > 0) {
            //if (isset($this->time_remaining) && $this->time_remaining>0) {
            //	$timeRemaining = $this->time_remaining;
            //}
        }

        $timeRemainingFormated = $this->secondsToTime($this->time_remaining);
        $timeAllowedFormated = $this->secondsToTime($this->timeAllowed);

        WebApp::addVar("timeRemainingFormated", "" . $timeRemainingFormated);
        WebApp::addVar("timeAllowedFormated", "" . $timeAllowedFormated);

        WebApp::addVar("timeAllowed", "" . $this->timeAllowed);
        WebApp::addVar("timeRemaining", "" . $this->time_remaining);
        WebApp::addVar("timeSpent", "" . $this->time_spent);
/*


Event Object
(
    [sourcePage] => main.html
    [targetPage] => main.html
    [name] => 
    [target] => 
    [args] => Array
        (
        )

)
Session Object
(
    [Vars] => Array
        (
            [ID_S] => IP: 192.168.1.20; DATE: 2015/10/28/ 13:27:23
            [tip] => 1
            [contentType] => db
            [ses_userid] => 24
            [thisMode] => _new
            [level_0] => 3
            [level_1] => 2
            [level_2] => 5
            [level_3] => 1
            [level_4] => 0
            [level] => 0
            [nodetype] => 0
            [node_family_id] => 0
            [lang] => Lng1
            [simpleEditAuthoring] => t
            [simpleModePreview] => no
            [uni] => 20151028124246192168120846922351
            [typeOfUser] => BO
            [zRef] => 0
            [contentId] => 1719
        )

    [VALID_FUN_VALIDATEVARFUN] => Array
        (
            [f_real_escape_string] => Y
            [control_validity_of_koordinate] => Y
            [control_array] => Y
            [control_fe_mode] => Y
            [apl_languages_set] => Y
            [f_pozitive_numbers] => Y
            [f_only_numbers] => Y
            [f_only_all_natural_numbers] => Y
            [f_only_numbers_presje] => Y
            [f_only_numbers_minus] => Y
            [f_safe_unserialize] => Y
            [f_thisMode] => Y
            [f_convert_to_html_entities] => Y
            [f_lang] => Y
            [f_enum_mode] => Y
            [f_web_or_mob] => Y
            [enum_styles] => Y
            [only_dates] => Y
            [only_dates_mysql] => Y
            [only_chars] => Y
            [source_target_page] => Y
            [f_idstemp] => Y
            [enum_feed] => Y
            [f_userid] => Y
            [f_level] => Y
            [f_contentId] => Y
            [f_is_numeric] => Y
            [f_uni] => Y
            [f_ID_S] => Y
            [f_string_35] => Y
            [f_delete_sensitive_words_get] => Y
            [f_delete_sensitive_words_post] => Y
        )

)



echo "<textarea>";
print_r($_REQUEST);
print_r($this);
echo "</textarea>";

Event Object
(
    [sourcePage] => main.html
    [targetPage] => main.html
    [name] => 
    [target] => 
    [args] => Array
        (
        )

)
Session Object
(
    [Vars] => Array
        (
            [ID_S] => IP: 192.168.1.20; DATE: 2015/10/28/ 13:27:23
            [tip] => 1
            [contentType] => db
            [ses_userid] => 24
            [thisMode] => _new
            [level_0] => 3
            [level_1] => 2
            [level_2] => 5
            [level_3] => 1
            [level_4] => 0
            [level] => 0
            [nodetype] => 0
            [node_family_id] => 0
            [lang] => Lng1
            [simpleEditAuthoring] => t
            [simpleModePreview] => no
            [uni] => 20151028124246192168120846922351
            [typeOfUser] => BO
            [zRef] => 0
            [contentId] => 1719
        )

    [VALID_FUN_VALIDATEVARFUN] => Array
        (
            [f_real_escape_string] => Y
            [control_validity_of_koordinate] => Y
            [control_array] => Y
            [control_fe_mode] => Y
            [apl_languages_set] => Y
            [f_pozitive_numbers] => Y
            [f_only_numbers] => Y
            [f_only_all_natural_numbers] => Y
            [f_only_numbers_presje] => Y
            [f_only_numbers_minus] => Y
            [f_safe_unserialize] => Y
            [f_thisMode] => Y
            [f_convert_to_html_entities] => Y
            [f_lang] => Y
            [f_enum_mode] => Y
            [f_web_or_mob] => Y
            [enum_styles] => Y
            [only_dates] => Y
            [only_dates_mysql] => Y
            [only_chars] => Y
            [source_target_page] => Y
            [f_idstemp] => Y
            [enum_feed] => Y
            [f_userid] => Y
            [f_level] => Y
            [f_contentId] => Y
            [f_is_numeric] => Y
            [f_uni] => Y
            [f_ID_S] => Y
            [f_string_35] => Y
            [f_delete_sensitive_words_get] => Y
            [f_delete_sensitive_words_post] => Y
        )

)


*/

    }

    function getActualQuestionToGrid()
    {
        //$this->actual_question_id	

        $QuestionProp = array("data" => array(), "AllRecs" => "0");
        $indQ = 0;

        $val_DC = $this->EvaluationState["question"]["structure"][$this->actual_question_id]["DC"];
        $val_EX = $this->EvaluationState["question"]["structure"][$this->actual_question_id]["EX"];
        $val_OPT = $this->EvaluationState["question"]["structure"][$this->actual_question_id]["OPT"];
        $val_DCA = $this->EvaluationState["question"]["structure"][$this->actual_question_id]["DCA"];

        $q_id = $this->actual_question_id;
        $q_type = $val_EX["question_type"];

        $QuestionProp["data"][$indQ]["q_id"] = $q_id;
        $QuestionProp["data"][$indQ]["q_type"] = $q_type;

        $QuestionProp["data"][$indQ]["q_name"] = $val_DCA["question_title"];

        $QuestionProp["data"][$indQ]["q_description"] = $val_DCA["question_abstract"];
        $QuestionProp["data"][$indQ]["dp_q_description"] = $val_DCA["exist_abst"];

        $QuestionProp["data"][$indQ]["dp_fullContent"] = $val_DCA["question_full_content_exist"];
        $QuestionProp["data"][$indQ]["fullContent"] = $val_DCA["question_full_content"];

        $QuestionProp["data"][$indQ]["fullContentQResult_exist"] = $val_DCA["question_fullContentResult_exist"];
        $QuestionProp["data"][$indQ]["fullContentQResult"] = $val_DCA["question_fullContentResult"];

        $QuestionProp["data"][$indQ]["question_state"] = "init";

        $QuestionProp["AllRecs"] = count($QuestionProp["data"]);
        WebApp::addVar("QuestionToGrid", $QuestionProp);

        if (isset($val_OPT) && count($val_OPT) > 0) {

            $OptionQuestionProp = array("data" => array(), "AllRecs" => "1");
            $sessionQAct = $val_OPT;

            $indQ = 0;
            while (list($keyi, $valo) = each($sessionQAct)) {

                $this->initMainRecord["question"][$q_id] = $q_id;

                $o_id = $valo["EQItemID"];

                $opsionsShownToTheUser[$o_id] = $o_id;

                $o_name = $valo["option_name"];
                $o_desc = "";
                $o_answer = $valo["option_comment"];

                //klevi ndryshim-------------------------------------------
                $OptionQuestionProp["data"][$indQ]["o_id"] = $o_id;
                if ($q_type == "single" || $q_type == "true_false")
                    $OptionQuestionProp["data"][$indQ]["o_type"] = "radio";
                elseif ($q_type == "multi")
                    $OptionQuestionProp["data"][$indQ]["o_type"] = "checkbox";


                $OptionQuestionProp["data"][$indQ]["o_name"] = $o_name;
                if ($o_name != "") $OptionQuestionProp["data"][$indQ]["dp_o_name"] = "yes";
                else                    $OptionQuestionProp["data"][$indQ]["dp_o_name"] = "no";

                $OptionQuestionProp["data"][$indQ]["o_desc"] = $o_desc;
                if ($o_desc != "") $OptionQuestionProp["data"][$indQ]["dp_o_desc"] = "yes";
                else                    $OptionQuestionProp["data"][$indQ]["dp_o_desc"] = "no";

                $OptionQuestionProp["data"][$indQ]["checked"] = "";
                $indQ++;
            }

            $OptionQuestionProp["AllRecs"] = count($OptionQuestionProp["data"]);
            WebApp::addVar("GridOption_" . $q_id, $OptionQuestionProp);
        }


        $sql_update = "
			UPDATE z_EccE_user_examination_question
			   SET question_state 				= 'init',
				   begin_time 					= '" . date("H:i:s") . "',
				   nr_of_views_unanswered  		= nr_of_views_unanswered+1
			 WHERE test_id 		= '" . $this->evaluation_test_id . "'
			   AND question_id	= '" . $this->actual_question_id . "' 
			   AND question_state in ('new','init')";
        WebApp::execQuery($sql_update);

        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET end_time = NOW()
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);

        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET time_spent_server = TIME_TO_SEC(TIMEDIFF(end_time, begin_time))
				 WHERE test_id 		= '" . $this->evaluation_test_id . "'
				   AND test_state in ('new','init','running')";
        WebApp::execQuery($sql_update);


        //$this->getProgressBarGrid();
    }

    function getProgressBarGrid()
    {
        if (isset($this->evaluation_test_id) && $this->evaluation_test_id > 0) {

            $QuestionProp = array("data" => array(), "AllRecs" => "0");
            $indQQ = 0;

            $getNrQuest = "
					SELECT count(1) as nrQ
					  FROM z_EccE_user_examination_question
					 WHERE test_id = '" . $this->evaluation_test_id . "'";
            $rsNrQuest = WebApp::execQuery($getNrQuest);
            $totalQuestion = $rsNrQuest->Field("nrQ");

            $select_finished_session_info = "
					SELECT question_id, question_state, sequence_id, results_state
					  FROM z_EccE_user_examination_question
					 WHERE test_id = '" . $this->evaluation_test_id . "'
					  ORDER BY sequence_id DESC";

            WebApp::addVar("tot_questions_in_test", $totalQuestion);
            $rs_main = WebApp::execQuery($select_finished_session_info);

            $barWidth = 23 * ($totalQuestion + 1);
            $barWidthEval = 22 * ($totalQuestion);

            $lastQuestionId = "";

            //	width_image_m
            $width_passed_question = 100;
            while (!$rs_main->Eof()) {

                $questionid = $rs_main->Field("question_id");
                $sequence_id = $rs_main->Field("sequence_id");
                $question_state = $rs_main->Field("question_state");
                $results_state = $rs_main->Field("results_state");

                $width_passed_question = round(($sequence_id / ($totalQuestion + 1)) * 100);
                $width_passed_question_eval = round(($sequence_id / ($totalQuestion)) * 100);

                $filledOrNot = "";
                if ($question_state == "new" || $question_state == "init") {
                    $results_state = "init";
                    $filledOrNot = "false";
                } else {
                    $filledOrNot = "filled";
                }

                $tmp_actual_question_id = $questionid;

                if ($lastQuestionId == "") $lastQuestionId = $questionid;


                if (
                    ((isset($this->stateInterfaceOfQuestion) && $this->stateInterfaceOfQuestion == "ready_to_be_finalized")
                        || $this->isLastQuestion == "yes")
                    && ($totalQuestion == $sequence_id)


                ) {
                    //$results_state .= " current";
                    //$filledOrNot .= " current";				

                } else {

                    //nxt:request_actiongoNext


                    //if ($this->request_action=="nxt" && $sequence_id == $this->evaluation_test_state["total_sessions"]) {
                    if ($this->isLastQuestion == "yes" || $this->stateInterfaceOfQuestion == "ready_to_be_finalized") {

                    } elseif (isset($this->actual_question_id) && $this->actual_question_id == $questionid) {

                        $results_state .= " current";
                        $filledOrNot .= " current";
                        WebApp::addVar("sequence_id_order", $sequence_id);

                        $sql_update = "
								UPDATE z_EccE_user_examination_question
								   SET nr_of_views_tot	= nr_of_views_tot+1
								 WHERE test_id			= '" . $this->evaluation_test_id . "'
								   AND question_id		= '" . $this->actual_question_id . "'";
                        WebApp::execQuery($sql_update);
                    }
                }


                //$this->isLastQuestion


                /*echo "<textarea>";
					print_r($this->request_action.":request_action");
					print_r($this->controllTestInstance_action.":controllTestInstance_action");
					print_r($this->actual_question_id.":stateInterfaceOfQuestion");
					print_r($this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion");
					echo "</textarea>";*/

                $QuestionProp["data"][$indQQ]["questionid"] = $questionid;
                $QuestionProp["data"][$indQQ]["sequence_id"] = $sequence_id;
                $QuestionProp["data"][$indQQ]["pgbsclassColor"] = $results_state;
                $QuestionProp["data"][$indQQ]["pgbsclass"] = $filledOrNot;
                $QuestionProp["data"][$indQQ]["width_image"] = $width_passed_question;
                $QuestionProp["data"][$indQQ++]["width_image_eval"] = $width_passed_question_eval;
                $rs_main->MoveNext();
            }

            /*echo "<textarea>$lastQuestionId:lastQuestionId";
			print_r($this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion\n");
			print_r($this->actual_question_id.":actual_question_id\n");
			print_r($this->isLastQuestion.":isLastQuestion\n");			
			echo "</textarea>";	*/

            if (
                (isset($this->stateInterfaceOfQuestion) && $this->stateInterfaceOfQuestion == "ready_to_be_finalized")
                || $this->isLastQuestion == "yes"


            ) {

                $this->actual_question_id = $lastQuestionId;
                //WebApp::addVar("actual_question_id", $lastQuestionId);
                WebApp::addVar("actual_question_id", $lastQuestionId);
                WebApp::addVar("actual_sequence_id", $totalQuestion);

            }

            /*echo "<textarea>$lastQuestionId:lastQuestionId";
			print_r($this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion\n");
			print_r($this->actual_question_id.":actual_question_id\n");
			print_r($this->isLastQuestion.":isLastQuestion\n");			
			echo "</textarea>";	*/
            /*				
				if (!isset($this->actual_question_id) || $this->actual_question_id=="" || $this->isLastQuestion=="yes") { // || $this->isLastQuestion=="yes"
				
				} else {
					WebApp::addVar("actual_question_id", $tmp_actual_question_id);
					WebApp::addVar("actual_sequence_id", $this->evaluation_test_state["total_sessions"]);
				}
*/
            WebApp::addVar("barWidth", $barWidth);
            WebApp::addVar("barWidthEval", $barWidthEval);

            WebApp::addVar("actual_sequence_id", $this->actual_sequence_id);

            $QuestionProp["AllRecs"] = count($QuestionProp["data"]);
            WebApp::addVar("GridPrgBarTotEvalQuestionNew", $QuestionProp);

            $QuestionProp["AllRecs"] = count($QuestionProp["data"]);
            WebApp::addVar("GridPrgBarTotEvalQuestionResults", $QuestionProp);
        }
    }

    function getSessionFinished()
    {
        //echo $this->evaluation_test_id."evaluation_test_id<br>";
        if ($this->evaluation_test_id != "") {

            $select_finished_session_info = "
				SELECT question_id
				  FROM z_EccE_user_examination_question
				 WHERE test_id		= '" . $this->evaluation_test_id . "' AND question_state!='init'
				  ORDER BY sequence_id";
            $rs_main = WebApp::execQuery($select_finished_session_info);
            while (!$rs_main->Eof()) {
                $this->finished_session_info["questions"][$rs_main->Field("question_id")] = $rs_main->Field("question_id");
                $rs_main->MoveNext();
            }
        }
    }

    function constructDetailsOfCiGrid($contentToFindProp)
    {
        if ($contentToFindProp != "" && $contentToFindProp > 0) {

            if (isset($this->mixedNeededData["ci"][$contentToFindProp]) && count($this->mixedNeededData["ci"][$contentToFindProp]) > 0) {

            } else $this->getCiNeededVar($contentToFindProp);

            if (isset($this->mixedNeededData["ci"][$contentToFindProp]) && count($this->mixedNeededData["ci"][$contentToFindProp]) > 0) {

                $tmp = array_merge($this->mixedNeededData["ci"][$contentToFindProp]["DC"], $this->mixedNeededData["ci"][$contentToFindProp]["EX"]);
                $tmp["doctype_description"] = $this->appRelatedState["doctype_description"][$contentToFindProp];

                $tmp["cidFlowItm"] = $contentToFindProp;
                $tmp["mim_format"] = strtolower(pathinfo($this->mixedNeededData["ci"][$contentToFindProp]["EX"]["filename"], PATHINFO_EXTENSION));

                $ggg["data"][0] = $tmp;
                $ggg["AllRecs"] = count($ggg["data"]);

                WebApp::addVar("ci_in_list_grid_" . $contentToFindProp, $ggg);
                $this->ci_in_list_grid[$contentToFindProp] = $ggg;
            }
        }
    }

    function getCiNeededVar($contentToFindProp = "")
    {
        global $session;

        if ($contentToFindProp == "") {
            if (isset($this->cidFlow) && $this->cidFlow > 0)
                $contentToFindProp = $this->cidFlow;
            else        $contentToFindProp = $session->Vars["contentId"];
        }

        //if (!isset($this->mixedNeededData["ci"][$contentToFindProp])) {
        $grid_id = $contentToFindProp;

        $workingCi = new CiManagerFe($contentToFindProp, $session->Vars["lang"]);
        $prop = array();

        $workingCi->getDocProperties();

        $this->mixedNeededData["ci"][$contentToFindProp]["DC"] = $workingCi->properties_structured["DC"];

        $actualCiType = $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ci_type"];

        ////echo $actualCiType.":actualCiType ".$this->cidFlow.":cidFlow $contentToFindProp:contentToFindProp<br>";
        $this->mixedNeededData["ci"][$contentToFindProp]["DC"] = $workingCi->properties_structured["DC"];
        $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["doctype_description"] = $this->appRelatedState["doctype_description"][$contentToFindProp];
        $this->mixedNeededData["ci"][$contentToFindProp]["EX"] = $workingCi->properties_structured["EX"];
        $this->mixedNeededData["ci"][$contentToFindProp]["KW"] = $workingCi->properties_structured["KW"];

        if ($actualCiType == "EQ") {


/*
         	get_ci_sl_resources

    
*/
            
            
          //  if ($contentToFindProp == $this->cidFlow) {
            
        		$imagesInQuestion = $workingCi->get_ci_sl_resources($contentToFindProp);
        		
			/*	echo "<textarea>";
				print_r($imagesInQuestion);
				echo "</textarea>";        */  		
          //  }
            
            
            $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_title"] = $workingCi->properties_structured["DC"]["ew_title"];
            $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_abstract"] = $workingCi->properties_structured["DC"]["ew_abstract"];
            $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["exist_abst"] = $workingCi->properties_structured["DC"]["exist_abst"];









            $fullContent = $workingCi->properties_structured["DC"]["ci_content"];
            $controllfullContent = trim($fullContent);
            if ($controllfullContent != "") {
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_full_content_exist"] = "yes";
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_full_content"] = $fullContent;
            } else {
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_full_content_exist"] = "no";
            }

 //   [EX] => Array
 //       (
//
 //           [fullContentResult] => <span style="line-height: 18.5714px;">together with some content in the case of results</span>




            $fullContentResult = $workingCi->properties_structured["EX"]["fullContentResult"];
            $controllfullContentRe = trim($fullContentResult);
            if ($controllfullContentRe != "") {

                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_fullContentResult_exist"] = "yes";
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_fullContentResult"] = $fullContentResult;

            } else {
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["DCA"]["question_fullContentResult_exist"] = "no";
            }
            
          

            $this->EvaluationState["question"]["structure"][$contentToFindProp]["EX"] = $workingCi->properties_structured["EX"];
            if (isset($workingCi->properties_structured["OPT"]) && $workingCi->properties_structured["OPT"] != "") {
                $this->EvaluationState["question"]["structure"][$contentToFindProp]["OPT"] = $workingCi->properties_structured["OPT"];
            }

        } elseif (($actualCiType == "RQ" || $actualCiType == "CQ" || $actualCiType == "ES") && $contentToFindProp == $this->cidFlow) {

            $this->EvaluationState["collector_prop"]["DC"] = $workingCi->properties_structured["DC"];
            $this->EvaluationState["collector_prop"]["EX"] = $workingCi->properties_structured["EX"];
            
           // 	global $sessUserObj;
            
            if (isset($workingCi->properties_structured["EX"]["enable_survey"])
            	&& $workingCi->properties_structured["EX"]["enable_survey"]=="yes"
            	) {
            	
            	
				if (isset($this->appRelatedState["LECTURE_RELATED"]["EL"]) && $this->appRelatedState["LECTURE_RELATED"]["EL"] > 0) {					

					$referenceId = $this->appRelatedState["LECTURE_RELATED"]["EL"];
					$workingCiSurveyC = new CiManagerFe($referenceId, $session->Vars["lang"]);
					$confSurvey = $workingCiSurveyC->getSurveyConfigurationInParentNodes($this->thisModeCode,"EL",$referenceId);

					if (isset($confSurvey["type_of_survey"]) && $confSurvey["type_of_survey"]!="none" && $confSurvey["target_survey_ci"]>0) {
						 
						 $this->EvaluationState["surveyRelated"]["referenceLectureID"] 			= $this->appRelatedState["LECTURE_RELATED"]["EL"];
						 
						 $this->EvaluationState["surveyRelated"]["enabled"] = "yes";
						 $this->EvaluationState["surveyRelated"]["targetSurvey"] 			= $confSurvey["target_survey_ci"];
						 $this->EvaluationState["surveyRelated"]["survey_ci_title"] = $confSurvey["survey_ci_title"];
						 
						 
						// $this->EvaluationState["evaluation_rules"]["enabledSurvey"] = "yes";
						// $this->EvaluationState["evaluation_rules"]["target"] 			= $confSurvey["target_survey_ci"];
						// $this->EvaluationState["evaluation_rules"]["survey_ci_title"] = $confSurvey["survey_ci_title"];
					
/*

-collector_prop-Array
(
    [type_of_survey] => internal
    [target_survey_ci] => 2194
    [survey_ci_title] => User Satisfaction - Survey
    [survey_ci_identifier] => 3.3.8.10.8.2194
)

*/					
					
					
					
					
					}
				}
            }
            
/* 	echo "<textarea>-collector_prop-";
	print_r($confSurvey);
	echo "</textarea>";	    
  */            

                                            
            $this->getRQConfiguration();


            $descriptor_Full = $this->EvaluationState["configuration"]["descriptor_Full"];
            $descriptor_FullR = trim($descriptor_Full);
            if ($descriptor_FullR != "") {

                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultDescriptor_exist"] = "yes";
                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultDescriptor"] = $this->getPlaceholdersToReplace($descriptor_Full);

            } else {
                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultDescriptor_exist"] = "no";
            }

            $fullContentResult = $this->EvaluationState["configuration"]["fullContentResult"];
            $controllfullContentR = trim($fullContentResult);
            if ($controllfullContentR != "") {

                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultFullContentResult_exist"] = "yes";
                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultFullContentResult"] = $this->getPlaceholdersToReplace($fullContentResult);

            } else {
                $this->mixedNeededData["ci"][$contentToFindProp]["DC"]["ClResultFullContentResult_exist"] = "no";
            }
        }
    }
    function getMaxTestId()
    {
        $sql = "SELECT max(test_id) as max_id_level FROM z_EccE_user_examination ";
        $rs = WebApp::execQuery($sql);
        if (!$rs->EOF()) $max_id = $rs->Field("max_id_level") + 1;
        else                $max_id = 1;
        return $max_id;
    }

    function getQuestionInHistory($question_id_param, $backNext)
    {
        global $session;

        if ($backNext == "back") {
            $operator = " < ";
            $orderBY = " DESC";
        } else {
            $operator = " > ";
            $orderBY = " ASC";
        }

        //$this->evaluation_state_flag = "0"; //testi ka mbaruar duhet ti ndryshohet statusi
        $get_actual_question_to_run = "
				SELECT sequence_id
				  FROM z_EccE_user_examination_question
				 WHERE test_id	= " . $this->evaluation_test_id . "
				   AND question_id ='" . $question_id_param . "'";

        $rs = WebApp::execQuery($get_actual_question_to_run);
        if (!$rs->Eof()) {

            $sequence_id = $rs->Field("sequence_id");
            $get_actual_question_to_run = "
						SELECT question_id, sequence_id
						  FROM z_EccE_user_examination_question
						 WHERE test_id	= " . $this->evaluation_test_id . "
						   AND sequence_id " . $operator . " '" . $sequence_id . "'
						   ORDER BY sequence_id " . $orderBY . "
						   limit 0,1";
            //AND filled_sessions		!= '0'
            $rs1 = WebApp::execQuery($get_actual_question_to_run);
            if (!$rs1->Eof()) {
                $this->controlStateForQuestionId($rs1->Field("question_id"));
            } else {
                $this->controlStateForQuestionId($question_id_param);
                $this->isLastQuestion = "yes";
                //$this->error_code = 103; //nuk gjetem asnje question per tu shfaqur
            }

        } else {
            $this->error_code = 102; //nuk gjetem asnje question per tu shfaqur
        }
    }

    function getQuestionBackInHistoryOrderPassed($question_id_param)
    {
        global $session;

        $this->evaluation_state_flag = "0"; //testi ka mbaruar duhet ti ndryshohet statusi
        $get_actual_question_to_run = "
				SELECT sequence_id
				  FROM z_EccE_user_examination_question
				 WHERE test_id	= " . $this->evaluation_test_id . "
				   AND question_id ='" . $question_id_param . "'";

        $rs = WebApp::execQuery($get_actual_question_to_run);
        if (!$rs->Eof()) {

            $order_passed = $rs->Field("order_passed");
            $get_actual_question_to_run = "
						SELECT question_id, order_passed
						  FROM z_EccE_user_examination_question
						 WHERE question_state not in ('new','init')
						   AND test_id	= " . $this->evaluation_test_id . "
						   AND sequence_id < '" . $order_passed . "'
						   ORDER BY sequence_id DESC";
            //AND filled_sessions		!= '0'
            $rs1 = WebApp::execQuery($get_actual_question_to_run);
            if (!$rs1->Eof()) {
                $this->controlStateForQuestionId($rs1->Field("question_id"));
            } else {
                $this->error_code = 103; //nuk gjetem asnje question per tu shfaqur
            }

        } else {
            $this->error_code = 102; //nuk gjetem asnje question per tu shfaqur
        }
    }

    function controlStateForQuestionId($question_id_param = "")
    {
        // `question_state` enum('new','init','readyToEvaluate','Evaluated') 
        //ketu do gjejme pyetjen qe duhet te shfaqet, qe mund te jete question id, qe duhet te shfaqet per tu bere submit nga useri
        //e merr prioritetin pyetja e fundit e shfaqur pra qe ka flagun init, dhe pastaj behet orderi sipas sekuences ose random ne varesi te konfigurimit
        $question_id = "";
        if ($question_id_param != "" && $question_id_param > 0) {
            $question_id = $question_id_param;
        } elseif (isset($this->actual_question_id) && $this->actual_question_id > 0) {
            $question_id = $this->actual_question_id;
        }

        IF ($question_id == "") {

            $condition = "";
            if ($this->stateInterfaceOfQuestion == "ready_to_be_evaluated") {

                $orderBy = " sequence_id DESC";
            } else if ($this->evaluation_state_flag > 0) {
                $orderBy = "sequence_id ASC";
            } else {

                if (isset($this->lastSubmitedQuestion))
                    $condition = "  sequence_id > '" . $this->lastSubmitedQuestion . "' AND";

                $orderBy = " priori DESC, sequence_id ";
            }

            $get_actual_question_to_run = "
					SELECT question_id, if (question_state in ('new','init'), 1, 0)  as priori, sequence_id,
						if (question_state in ('new','init'), 'init', question_state)  as question_state
					  FROM z_EccE_user_examination_question
					 WHERE " . $condition . " test_id	= " . $this->evaluation_test_id . "
					   ORDER BY " . $orderBy . " ";


            //AND filled_sessions		!= '0'
            $rs = WebApp::execQuery($get_actual_question_to_run);
            IF (!$rs->Eof()) {
                $this->actual_sequence_id = $rs->Field("sequence_id");
                $this->actual_question_id = $rs->Field("question_id");
                $this->actual_question_state = $rs->Field("question_state");
            } ELSE
                $this->error_code = 101; //nuk gjetem asnje question per tu shfaqur	
        } ELSE {

            $get_actual_question_to_run = "
				SELECT question_id, sequence_id,
						if (question_state in ('readyToEvaluate','Evaluated'),'finished','init') as question_state
				  FROM z_EccE_user_examination_question
				 WHERE test_id	= " . $this->evaluation_test_id . "
				   AND question_id = '" . $question_id . "'";

            $rs = WebApp::execQuery($get_actual_question_to_run);
            IF (!$rs->Eof()) {
                $this->actual_question_id = $rs->Field("question_id");
                $this->actual_question_state = $rs->Field("question_state");
                $this->actual_sequence_id = $rs->Field("sequence_id");

            } ELSE
                $this->error_code = 110; //nuk gjetem asnje question per tu shfaqur
            /*	echo "<textarea>controlStateForQuestionId";
			print_r($this->actual_question_id.":actual_question_id\n");
			print_r($this->actual_question_state.":actual_question_state\n");
			print_r($this->player_flag.":player_flag\n");
			echo "</textarea>";		*/
        }

        IF ($this->actual_question_id > 0) {

            $this->getCiNeededVar($this->actual_question_id);
            IF ($this->actual_question_state == "init" && $this->player_flag == 1)
                $this->getActualQuestionToGrid();
            ELSE    $this->getQuestionResult();


        } ELSE {
            $this->error_code = 111; //nuk gjetem asnje question per tu shfaqur
        }
    }

    function controllTestInstance($goToNextQuestion = "yes", $question_id = "")
    {
        global $session;

        $this->evaluation_state_flag = "0";
        $this->controllTestInstance_action = $goToNextQuestion;

        $getState = "SELECT total_sessions, filled_sessions, test_state, results_state
					   FROM z_EccE_user_examination
					  WHERE test_id = '" . $this->evaluation_test_id . "'";

        $rsGetState = WebApp::execQuery($getState);
        if (!$rsGetState->EOF()) {

            $total_sessions = $rsGetState->Field("total_sessions");
            $filled_sessions = $rsGetState->Field("filled_sessions");

            $test_state = $rsGetState->Field("test_state");
            $results_state = $rsGetState->Field("results_state");

            $this->evaluation_test_state["total_sessions"] = $total_sessions;
            $this->evaluation_test_state["filled_sessions"] = $filled_sessions;
            $this->evaluation_test_state["test_state"] = $test_state;
            $this->evaluation_test_state["results_state"] = $results_state;

            $this->tagsToBeReplaced["filled_sessions"] = $filled_sessions;
            $this->tagsToBeReplaced["total_sessions"] = $total_sessions;

            if ($this->evaluation_test_state["test_state"] != "readyToEvaluate" && $this->evaluation_test_state["test_state"] != "Evaluated") {

                if ($goToNextQuestion == "finalizeR") { // || $this->flow_model["feedback_model"] == "sessionEnd"
                    $this->stateInterfaceOfQuestion = "ready_to_be_finalized"; //READY TO BE Finalized
                } elseif ($goToNextQuestion == "finalize") {
                    $this->getSessionFinishedResult();

                } elseif (($this->actual_sequence_id == $total_sessions)) {
                    //($total_sessions==$filled_sessions) ||

                    if ($this->actual_sequence_id == $total_sessions) {
                        //$this->actual_question_id ="";
                        $this->stateInterfaceOfQuestion = "ready_to_be_finalized"; //READY TO BE Finalized
                    } elseif ($goToNextQuestion == "finalize" || $goToNextQuestion == "finalize") { // || $this->flow_model["feedback_model"] == "sessionEnd"
                        $this->getSessionFinishedResult();
                    } else {
                        //kontrollo me kujdes cfare bente ky para se ta heqesh
                        /*if ($this->PlayerOfQuestionsEditMode=="yes" || $this->configuration_rules["allow_back"]=="y") {
							//$this->evaluation_test_state = 10; //READY TO BE EVALUATE
							$this->stateInterfaceOfQuestion = "ready_to_be_evaluated"; //READY TO BE Finalized
							$this->controlStateForQuestionId("lastQuestion");
						} else {
							//$this->getSessionFinishedResult();
							//$this->getEvaluationState();
						}*/
                    }
                }
            }
        }

        /*echo "<textarea>controllTestInstance-3-";
			print_r($this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion\n");
			print_r($this->actual_sequence_id.":total_sessions\n");
			print_r($this->actual_sequence_id.":actual_sequence_id\n");
			print_r($this->isLastQuestion.":isLastQuestion\n");			
			echo "</textarea>";	*/


        if ($this->evaluation_test_state["test_state"] == "init") {
            $this->player_flag = 1;
        } elseif ($this->evaluation_test_state["test_state"] == "running") {
            $this->player_flag = 1;
        } elseif ($this->evaluation_test_state["test_state"] == "readyToEvaluate") {
            $this->evaluation_state_flag = 4; //testi ka mbaruar duhet te vleresohet nga kontrolluesi		
        } else {
            if ($this->evaluation_test_state["results_state"] == "passed")
                $this->evaluation_state_flag = 5; //testi ka mbaruar me sukses			
            else    $this->evaluation_state_flag = 6; //testi i userit ka deshtuar
        }

        if ($this->evaluation_test_id > 0 && $this->evaluation_test_id != "") {

            if ($goToNextQuestion == "goTo")
                $this->controlStateForQuestionId($question_id);
            elseif ($goToNextQuestion == "goBack")
                $this->getQuestionInHistory($question_id, "back");
            elseif ($goToNextQuestion == "goNext") {
                $this->getQuestionInHistory($question_id, "next");


                /*echo "<textarea>$question_id-";
			print_r($this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion\n");
			print_r($this->actual_sequence_id.":total_sessions\n");
			print_r($this->actual_sequence_id.":actual_sequence_id\n");
			print_r($this->isLastQuestion.":isLastQuestion\n");			
			echo "</textarea>";	*/


                if ($this->isLastQuestion == "yes") {
                    //$this->actual_question_id ="";
                    $this->stateInterfaceOfQuestion = "ready_to_be_finalized"; //READY TO BE Finalized
                }


            } elseif ($this->player_flag == 1)
                $this->controlStateForQuestionId($question_id);
            else    $this->controlStateForQuestionId();

            /*if ($goToNextQuestion=="rfrAfterReview") {
					//$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"];	
			} else {

				if ($this->evaluation_state_flag>0)	{
					//$this->evaluation_test_id = ""; 
					if ($goToNextQuestion=="no") {
						$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"];	
						$this->getQuestionResult();
					} else {			
						$this->getSessionFinishedResult(); 
					}
				} else {
					//$this->controllUserCanRedoTest(); //implementoje me vone
					if ($goToNextQuestion=="yes") {
						$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"]+1;	
						$this->getQuestionToPass(); 
					} elseif ($goToNextQuestion=="go_back_in_history") {
						//$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"]+1;	
						//$this->getQuestionToPass(); 
						$this->getQuestionBackInHistory(); 
					} elseif ($goToNextQuestion=="go_back_in_history_question") {
						//$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"]+1;	
						//$this->getQuestionToPass(); 
						$this->getQuestionBackInHistory(); 
					} else {
						//$this->getSessionFinished(); 
						$this->EvaluationState["evaluation"]["navigation"]["next_question"] = $this->EvaluationState["evaluation"]["navigation"]["passed_question"];	
						$this->getQuestionResult();
					}
				}
			}*/
        }

    }

    function controllUserTestData()
    {
        $select_cme_info = "
			 SELECT count(if(results_state ='passed' and test_state ='Evaluated',1,null)) as nr_certificate,
					count(if(results_state ='not_passed' and test_state ='Evaluated',1,null)) as nr_test_failed,
					count(if(test_state ='running',1,null)) as nr_test_running,
					count(1) as nr_test_done
			  FROM z_EccE_user_examination
				WHERE examination_id = '" . $this->cidFlow . "'
				  AND user_id = '" . $this->userSystemID . "'";

        $get_select_cme_info = WebApp::execQuery($select_cme_info);
        //nese gjejme rekorde per kete user dhe kete assesment, lexojme se sa here useri e ka kaluar testin
        // qe jepet nga nr_certificate - kjo inkrementohet me nje sa here qe useri e kalon kufirin mininale per ta quajtur testin e kaluar me sukses
        //nr_test_done- percakton se sa here useri e ka kaluar testin gjithsej, 
        //dhe nese testi eshte kaluar me shume se sa limit qe eshte percaktuar nr_allowed_try
        //nese nr_allowed_try=0, ska asnje kufizim
        //useri e kalon testin deri sa te marre cerfikaten

        if (!$get_select_cme_info->EOF()) {
            $this->user_test_certificate_exist = $get_select_cme_info->Field("nr_certificate");
            $this->user_nr_test_failed = $get_select_cme_info->Field("nr_test_failed");
            $this->user_nr_test_running = $get_select_cme_info->Field("nr_test_running");
            $this->user_nr_test_done = $get_select_cme_info->Field("nr_test_done");
        } else {
            $this->user_test_certificate_exist = 0;
            $this->user_nr_test_failed = 0;
            $this->user_nr_test_running = 0;
            $this->user_nr_test_done = 0;
        }

        $existRekordDone = "SELECT test_id FROM z_EccE_user_examination
						 WHERE examination_id 		= '" . $this->cidFlow . "'
						   AND user_id 				= '" . $this->userSystemID . "'
						   AND test_state not in ('new','init','running')
						   ORDER BY test_id DESC LIMIT 0,1";

        $rsDataDone = WebApp::execQuery($existRekordDone);
        if (!$rsDataDone->EOF()) {
            $this->player_last_evaluation_id = $rsDataDone->Field("test_id");
        }

        $existRekordDone = "SELECT test_id FROM z_EccE_user_examination
						 WHERE examination_id 		= '" . $this->cidFlow . "'
						   AND user_id 				= '" . $this->userSystemID . "'
						   AND test_state not in ('new','init','running')
						   ORDER BY user_points_perqindje DESC LIMIT 0,1";

        $rsDataDone = WebApp::execQuery($existRekordDone);
        if (!$rsDataDone->EOF()) {
            $this->player_best_evaluation_id = $rsDataDone->Field("test_id");
        }


        if ($this->user_test_certificate_exist > "0") {
            //USERI E KA MARRE CERTIFIKATEN, LEJO RIBERJEN E KURSIT
            $this->player_flag = 10;    //SuccesTestUserCanRedo


            if ($this->ci_type_configuration == "CQ") {

                if ($this->configuration_rules["nr_allowed_try"] != '' && $this->configuration_rules["nr_allowed_try"] > '0') {


                    if ($this->user_nr_test_done >= $this->configuration_rules["nr_allowed_try"]) {

                        $this->player_flag = 21;    //failedTestUserCanNotRedoNrOfAllowedTry

                    } else {

                        $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = "";
                        $this->tagsToBeReplaced["redoTestAfterInterval"] = "";
                        $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = $this->configuration_rules["nr_allowed_try"] - $this->user_nr_test_done;


                        //	echo $this->configuration_rules["nr_allowed_try_days"].":nr_allowed_try_days<br>";

                        if (isset($this->configuration_rules["nr_allowed_try_days"]) && $this->configuration_rules["nr_allowed_try_days"] > 0) {
                            $this->tagsToBeReplaced["timeRestedFromLastDoneTest"] = $this->configuration_rules["nr_allowed_try_days"] - $nrDaysPassed;

                            $getDifDate = "SELECT DATEDIFF(now(), date_of_test_end)  as nrDays
											     FROM z_EccE_user_examination
											    WHERE examination_id 		= '" . $this->cidFlow . "'
						   						  AND user_id 				= '" . $this->userSystemID . "'
						   					 ORDER BY test_id DESC
						   						LIMIT 0,1";

                            $rs__getDifDate = WebApp::execQuery($getDifDate);
                            if (!$rs__getDifDate->EOF()) {
                                $nrDaysPassed = $rs__getDifDate->Field("nrDays");
                                if ($nrDaysPassed >= $this->configuration_rules["nr_allowed_try_days"]) {

                                    $this->internvalBetweenEvalueations = "allow";
                                    $this->tagsToBeReplaced["redoTestAfterInterval"] = "";

                                } else {
                                    $this->tagsToBeReplaced["timeRestedFromLastDoneTest"] = $this->configuration_rules["nr_allowed_try_days"] - $nrDaysPassed;
                                    $this->internvalBetweenEvalueations = "dont_allow";
                                    //$this->player_flag = 22;
                                }
                            }

                        } else {
                            $this->internvalBetweenEvalueations = "allow";
                        }
                    }
                } else {
                    $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = "";
                    $this->tagsToBeReplaced["redoTestAfterInterval"] = "";
                    //retry
                    $this->ulimitedTimesToBeRepeat = "yes";
                    $this->internvalBetweenEvalueations = "allow";
                }


            } else {

                if ($this->configuration_rules["allow_redo_cert"] == 'n') {
                    //NUK LEJOHET RIBERJA E KURSIT NESE USERI E KA MARRE NJEHERE CERTIFIKATEN
                    $this->player_flag = 11;    //SuccesTestUserCanNotRedoAfterSecrtificate
                    ////////////////////echo "<br>NUK LEJOHET RIBERJA E KURSIT NESE USERI E KA MARRE NJEHERE CERTIFIKATEN<br>";
                    if ($this->ci_type_configuration == "ES") {
                        $update_z_EccE_user_progress = "
												UPDATE z_EccE_user_progress
												   SET progress_state	= 'finished',
													   results_state	= 'passed',
													   date_end			= NOW()
												 WHERE item_id 	= '" . $this->appRelatedState["LECTURE_RELATED"]["EL"] . " '
												   AND user_id 		= '" . $this->userSystemID . "'
												   AND progress_state!='finished'";
                        WebApp::execQuery($update_z_EccE_user_progress);
                    }

                } else {


                    if ($this->configuration_rules["nr_allowed_try"] != '' && $this->configuration_rules["nr_allowed_try"] > '0') {
                        //NESE USERI lejohet to redo but have reached nr_allowed_try, disabel
                        if ($this->user_nr_test_done >= $this->configuration_rules["nr_allowed_try"]) {
                            $this->player_flag = 12;    //SuccesTestUserCanNotRedoNrOfAllowedTry

                            if ($this->ci_type_configuration == "ES") {
                                $update_z_EccE_user_progress = "
													UPDATE z_EccE_user_progress
													   SET progress_state	= 'finished',
													       results_state	= 'passed',
													       date_end			= NOW()
													 WHERE item_id 	= '" . $this->appRelatedState["LECTURE_RELATED"]["EL"] . " '
													   AND user_id 		= '" . $this->userSystemID . "'
													   AND progress_state!='finished'";
                                WebApp::execQuery($update_z_EccE_user_progress);
                            }

                        }
                    }

                }
            }


        } else { //

            if ($this->user_nr_test_done == 0) {
                //USERI SE KA BERE ASNJEHERE TESTIN 
                $this->player_flag = 0;    //initEntryTest
                /*if (isset($objEccELrn->calledByAjax) && $objEccELrn->calledByAjax == "true") {
				} else {
					$this->player_flag = 0;	//user
				}*/
            } else {


                $this->player_flag = 20;    //failedTestUserCanRedoLinkToTest


                if ($this->configuration_rules["nr_allowed_try"] != '' && $this->configuration_rules["nr_allowed_try"] > '0') {


                    if ($this->user_nr_test_done >= $this->configuration_rules["nr_allowed_try"]) {

                        $this->player_flag = 21;    //failedTestUserCanNotRedoNrOfAllowedTry


                        if ($this->ci_type_configuration == "ES") {
                            $update_z_EccE_user_progress = "
												UPDATE z_EccE_user_progress
												   SET progress_state	= 'finished',
													   results_state	= 'not_passed',
													   date_end			= NOW()
												 WHERE item_id 	= '" . $this->appRelatedState["LECTURE_RELATED"]["EL"] . " '
												   AND user_id 		= '" . $this->userSystemID . "'
												   AND progress_state!='finished'";
                            WebApp::execQuery($update_z_EccE_user_progress);
                        }

                    } else {

                        $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = "";
                        $this->tagsToBeReplaced["redoTestAfterInterval"] = "";
                        $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = $this->configuration_rules["nr_allowed_try"] - $this->user_nr_test_done;


                        //	echo $this->configuration_rules["nr_allowed_try_days"].":nr_allowed_try_days<br>";

                        if (isset($this->configuration_rules["nr_allowed_try_days"]) && $this->configuration_rules["nr_allowed_try_days"] > 0) {
                            $this->tagsToBeReplaced["timeRestedFromLastDoneTest"] = $this->configuration_rules["nr_allowed_try_days"] - $nrDaysPassed;

                            $getDifDate = "SELECT DATEDIFF(now(), date_of_test_end)  as nrDays
											     FROM z_EccE_user_examination
											    WHERE examination_id 		= '" . $this->cidFlow . "'
						   						  AND user_id 				= '" . $this->userSystemID . "'
						   					 ORDER BY test_id DESC
						   						LIMIT 0,1";

                            $rs__getDifDate = WebApp::execQuery($getDifDate);
                            if (!$rs__getDifDate->EOF()) {
                                $nrDaysPassed = $rs__getDifDate->Field("nrDays");
                                if ($nrDaysPassed >= $this->configuration_rules["nr_allowed_try_days"]) {

                                    $this->internvalBetweenEvalueations = "allow";
                                    $this->tagsToBeReplaced["redoTestAfterInterval"] = "";

                                } else {
                                    $this->tagsToBeReplaced["timeRestedFromLastDoneTest"] = $this->configuration_rules["nr_allowed_try_days"] - $nrDaysPassed;
                                    $this->internvalBetweenEvalueations = "dont_allow";
                                    //$this->player_flag = 22;
                                }
                            }

                        } else {
                            $this->internvalBetweenEvalueations = "allow";
                        }
                    }
                } else {
                    $this->tagsToBeReplaced["remaining_nr_of_allowed_tries"] = "";
                    $this->tagsToBeReplaced["redoTestAfterInterval"] = "";
                    //retry
                    $this->ulimitedTimesToBeRepeat = "yes";
                    $this->internvalBetweenEvalueations = "allow";
                }
            }
        }


    }

    /*
You are allowed to retry test [[remaining_nr_of_allowed_tries]]
[[redoTestAfterInterval]]
*/

    function getSessionFinishedResult()
    {
        //riregullo piket
        $getTotals = "
			SELECT sum(user_point) as usPoint, sum(max_point) 	as mxPoint,
				   sum(if (user_point>0,user_point,0)    ) 		as usPozPoint
			  FROM z_EccE_user_examination_question
			 WHERE test_id = '" . $this->evaluation_test_id . "'";
        $rsTotals = WebApp::execQuery($getTotals);

        $total_user_points = $rsTotals->Field("usPoint");
        $max_assesment_points = $rsTotals->Field("mxPoint");
        $min_assesment_points = $rsTotals->Field("minPoint");

        $total_user_points = $rsTotals->Field("usPoint");
        // [evaluation_rules] => [allow_negative_points] => n

        if ($total_user_points < 0) $total_user_points = 0;
        $total_user_only_pozitive_points = $rsTotals->Field("usPozPoint");

        $max_assesment_points_FROM = $rsTotals->Field("mxPoint");

        $max_assesment_points = $max_assesment_points;
        $user_points_perqindje = number_format(($total_user_points / $max_assesment_points * 100), 0);


        ////-------------------Assessment Rules [Percentage to pass test ]  -----------------------------------------------------
        $per_points_to_pass = 60;
        if (isset($this->EvaluationState["configuration"]['per_points_to_pass']) && $this->EvaluationState["configuration"]['per_points_to_pass'] != "") {
            $per_points_to_pass = $this->EvaluationState["configuration"]['per_points_to_pass'];
        }

        if ($user_points_perqindje >= $per_points_to_pass && $per_points_to_pass != "" && $per_points_to_pass > 0) {

            $results_state = "passed";
            //exec("php ".APP_PATH."include_php/save_test_certificate.php ".$this->evaluation_test_id." ".$this->evaluation_test_id." ".$this->session_user." >/dev/null &");	

            exec("php " . APP_PATH . "include_php/save_test_certificate.php " . $this->evaluation_test_id . " ".APP_URL." >/dev/null &");
            
            
            


            //$test_id = $this->evaluation_test_id;
            //require_once(APP_PATH."include_php/save_test_certificate.php");

        } else {
            $results_state = "not_passed";
        }


        //PER ENIN, KETU DUHET TE THIRRET AUTOMATIKISHT savimi i videos
        if ($this->tip != "2") {
            $filedir = RC_ASSESSMENT_FOLDER_PATH . $this->cidFlow . "/" . $this->userSystemID . "/" . $this->evaluation_test_id . "/";
            if (SUDOCMD == 0) {
                $ex_command = CACHEMOD_PATH . "cacheActions \"createvideo " . $filedir . "\"	 ";    // >/dev/null & echo \$!
            } else if (SUDOCMD == 1) {
                $ex_command = "sudo -u " . CACHEUSER . " " . CACHEMOD_PATH . "cacheActions \"createvideo " . $filedir . "\"	 ";    // >/dev/null & echo \$!
            }
            exec($ex_command);
            $sql_update = "
				UPDATE z_EccE_user_examination
				   SET autentification_folder 			= '" . $filedir . "',
					   autentification_flag 			= 'initialized'
				 WHERE test_id 							= '" . $this->evaluation_test_id . "'";
            WebApp::execQuery($sql_update);
        }
        //NESE TESTI ESHTE AUTOMATIK - useri mund ti shikoje direkt rezultatet
        $test_state = 'Evaluated';
        $sql_update = "
				UPDATE z_EccE_user_examination
				   SET confirmed_total_points 			= '" . $max_assesment_points . "',
					   confirmed_user_points 			= '" . $total_user_points . "',
					   total_user_points 				= '" . $total_user_points . "',
					   confirmed_user_pozitive_points 	= '" . $total_user_only_pozitive_points . "',
					   user_points_perqindje			= '" . $user_points_perqindje . "',
					   per_points_to_pass				= '" . $per_points_to_pass . "',

					   total_points						= '" . $max_assesment_points . "',
					   results_state					= '" . $results_state . "',
					   test_state 						= '" . $test_state . "',
					   
					   date_of_test_end 				= NOW(),
					   end_time 						= '" . date("H:i:s") . "'
					   
				 WHERE test_id 							= '" . $this->evaluation_test_id . "'";
        WebApp::execQuery($sql_update);

        $this->evaluation_test_state["test_state"] = $test_state;
        $this->evaluation_test_state["results_state"] = $results_state;
        $this->getEvaluationState();
    }

    function controlActualTestIdState()
    {


        //TIMEDIFF(end_time, begin_time) as userAllowedTime,
        //$userAllowedTime

        $controlToFinishTheRunningTest = "no";
        if ($this->configuration_rules["allow_restart"] == "y") {
            //$this->time_used 		= $rsData->Field("timer_response");
            //$this->time_remaining 	= $rsData->Field("timer_response");

            if ($this->configuration_rules["time_limit"] == "y") {
                $existRekord = "SELECT TIME_TO_SEC(TIMEDIFF(end_time, begin_time)) as userSpentTime
								  FROM z_EccE_user_examination
								 WHERE examination_id 		= '" . $this->cidFlow . "'
								   AND test_id 				= '" . $this->evaluation_test_id . "'
								   AND test_state in ('new','init','running')";

                $rsData = WebApp::execQuery($existRekord);
                if (!$rsData->EOF()) {    // gjej strukturen		
                    $userSpentTime = $rsData->Field("userSpentTime");


                    /*	echo "<textarea>";
					echo "-controlToFinishTheRunningTest-";
					echo "-$userSpentTime:userSpentTime-";
					echo $this->configuration_rules["time_limit_sec_total"]."-time_limit_sec_total-";
					echo "</textarea>";*/

                    if ($userSpentTime > $this->configuration_rules["time_limit_sec_total"]) {
                        $controlToFinishTheRunningTest = "finish";
                    }
                }
            }

        } else {

            $controlToFinishTheRunningTest = "finish";
        }
        return $controlToFinishTheRunningTest;
    }

    function getEvaluationState()
    {
        //ketu do kontrollohet ne varesi te konfigurimit nese kemi te drejte te bejme restart apo jo
        //eshte kapur rasti qe ndryshon sessioni

        $this->player_flag = 0;
        $this->controllUserTestData();
        $controlToFinishTheRunningTest = "";

        $existRekord = "SELECT test_id, timer_response, uni 
						  FROM z_EccE_user_examination
						 WHERE examination_id 		= '" . $this->cidFlow . "'
						   AND user_id 				= '" . $this->userSystemID . "'
						   AND test_state in ('new','init','running')
						   AND uni 					= '" . $this->uniqueid . "'";

        $rsData = WebApp::execQuery($existRekord);
        if (!$rsData->EOF()) {    // gjej strukturen		
            if ($this->mainEntry == "yes") {
                //jemi ne te njejtin session
                $this->evaluation_test_id = $rsData->Field("test_id");
                $controlToFinishTheRunningTest = $this->controlActualTestIdState();
            }

        } else {
            //JEMI NE TJETER SESSION

            //allow_restart
            //$this->configuration_rules["allow_restart"] =  "y";	
            //If you interrupt the test all the answers you have submitted earlier will be saved and you will be allowed to resume it back where you left. 

            //$this->configuration_rules["allow_restart"] =  "n";	
            //If you interrupt the test you will not be allowed to resume it back and only the answers you have submitted earlier will be evaluated. 

            $existRekord = "SELECT test_id, timer_response, uni 
							  FROM z_EccE_user_examination
							 WHERE examination_id 		= '" . $this->cidFlow . "'
							   AND user_id 				= '" . $this->userSystemID . "'
							   AND test_state in ('new','init','running')";

            $rsData = WebApp::execQuery($existRekord);
            if (!$rsData->EOF()) {    // gjej strukturen		
                $this->evaluation_test_id = $rsData->Field("test_id");
                $controlToFinishTheRunningTest = $this->controlActualTestIdState();
            }

        }
        if ($controlToFinishTheRunningTest == "finish") {
            $this->getSessionFinishedResult();

        } elseif ($this->evaluation_test_id > 0) {
            $this->player_flag = 1;
        } else {

            /*if ($this->user_nr_test_done) { 
				$this->getTestHistoryResults();
			}*/
        }
    }

    function continueEvaluation()
    {
    }



    //					$questionToBeExluded = array();
    //		$questionToBeExluded[0] = 0;
    //			$allQuestionToBeExluded  = implode(",",$questionToBeExluded);


    function initEvaluation()
    {
        global $session;

        //$this->removeUnfinishedEvaluation();

        $this->evaluation_test_id = $this->getMaxTestId();
        //type_of_cme, kaf=2 default, nese do implentohet edhe learning loop kjo do kete vleren 1
        $this->actualDate = date("Y-m-d");
        $this->actualTime = date("H:i:s");

        $total_sessions = count($this->EvaluationState["set_of_question_to_be_included"]["allowed"]);
        $configuration_test_prop = $this->constructGuideHtml();
        //ketu ruaj si objekt configurimin e playerit per cdo test te ri, shiko cfare duhet te lexosh qe ketu qe testi te vazhdoje me konfigurimin e nisur

        $testIdConfigurationRules = array();
        $testIdConfigurationRules["collect_rules"] = $this->collect_rules;
        $testIdConfigurationRules["configuration_rules"] = $this->configuration_rules;
        $testIdConfigurationRules["evaluation_rules"] = $this->evaluation_rules;
        $testIdConfigurationRules["user_report_rules"] = $this->user_report_rules;
        $testIdConfigurationRules["tagsToBeReplaced"] = $this->tagsToBeReplaced;
        $testIdConfigurationRules["modulDynamicMessages"] = $this->modulDynamicMessages;

        $testIdConfigurationRules["lecture_info_data"]["id"] = $this->appRelatedState["LECTURE_RELATED"]["EL"];
        $testIdConfigurationRules["lecture_info_data"]["dt"] = $this->appRelatedState["LECTURE_RELATED_INFO"][$this->appRelatedState["LECTURE_RELATED"]["EL"]];

        //	$testIdConfigurationRulesToSave = print_r($testIdConfigurationRules, true);

        $prop = base64_encode(serialize($testIdConfigurationRules));
		
		$automaticSurvey 	= "no";
		$automaticSurveyId 	= 0;
		if (isset($this->EvaluationState["surveyRelated"]["enabled"]) && $this->EvaluationState["surveyRelated"]["enabled"]=="yes") {
			$automaticSurvey = "yes";
			$automaticSurveyId 	= $this->EvaluationState["surveyRelated"]["referenceLectureID"];
		}

        $sql_insert_assesment_main_first_record = "
			INSERT INTO z_EccE_user_examination
			(test_id, examination_id, user_id,total_sessions, filled_sessions,test_state,
				date_of_test, begin_time,
				date_of_test_end, end_time, configuration_test_prop,testIdConfigurationRules,
				
				time_allowed,timer_response,
				uni,					automaticSurvey,	automaticSurveyId,	automaticSurveyOpened)
			VALUES
			('" . $this->evaluation_test_id . "', '" . $this->cidFlow . "', '" . $this->userSystemID . "','" . $total_sessions . "',0,'init',
				NOW(), '" . date("H:i:s") . "',
				NOW(), '" . date("H:i:s") . "', '" . $configuration_test_prop . "', '" . $prop . "', 
				
				'" . $this->configuration_rules["time_limit_sec_total"] . "', '" . $this->configuration_rules["time_limit_sec_total"] . "',
				'".$this->uniqueid."',	'".$automaticSurvey."','".$automaticSurveyId."',	'no')";

        WebApp::execQuery($sql_insert_assesment_main_first_record);
        if (isset($this->EvaluationState["set_of_question_to_be_included"]["allowed"]) && count($this->EvaluationState["set_of_question_to_be_included"]["allowed"]) > 0) {

            $allQuestionToBeIncluded = implode(",", $this->EvaluationState["set_of_question_to_be_included"]["allowed"]);
            $questionToBeExluded = $this->getAnsweredQuestions($allQuestionToBeIncluded);
            
            
            
            if ($this->PlayerOfQuestionsEditMode == "yes")
                $orderQuestion = " sequence_id";
            else    $orderQuestion = " RAND()";
            
            
            
			if ($this->collect_rules["collection_order"] == "sequential")
				$orderQuestion = " sequence_id";
			else    $orderQuestion = " RAND()";           
            
            
            
            

            $sql_con = "SELECT content_id as question_id, if (content_id in (" . $questionToBeExluded . "),0,1) as traitOrder
								  FROM eq_data 
							WHERE content_id in (" . $allQuestionToBeIncluded . ") AND lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
						 ORDER BY traitOrder ASC, " . $orderQuestion . "";

            $sequence_id = 1;
            $rs = WebApp::execQuery($sql_con);
            
            
/*echo "<textarea>";	
print_r($this->collect_rules);
print_r($questionToBeExluded);
print_r($rs);
echo "</textarea>";	    */        
            
            
            
            
            while (!$rs->EOF()) {
                $question_id = $rs->Field("question_id");
                $traitOrder = $rs->Field("traitOrder");
                if ($traitOrder == 0)
                    $createNewCombination = "yes";
                else    $createNewCombination = "no";

                $sql_insert_assesment_main_first_record = "
							INSERT INTO z_EccE_user_examination_question
									(test_id, 							user_id,						question_id, 		sequence_id,		question_state, 	begin_time, 			end_time, 			 createNewCombination, uni)
							VALUES
									('" . $this->evaluation_test_id . "', '" . $this->userSystemID . "', '" . $question_id . "', '" . $sequence_id . "',	'new', '" . date("H:i:s") . "', '" . date("H:i:s") . "', '" . $createNewCombination . "', '" . $this->uniqueid . "')";
                WebApp::execQuery($sql_insert_assesment_main_first_record);
                $this->calculateQuestionMaxMinPoints($question_id, $createNewCombination);
                $sequence_id++;
                $rs->MoveNext();
            }
        }
    }


    function getAnsweredQuestions($allQuestionToBeIncluded)
    {
        global $session;

        $questionToBeExluded = array();
        $questionToBeExluded[0] = 0;
        $getUsedQuestion = "
			SELECT question_id
			  FROM z_EccE_user_examination_question
			 WHERE user_id = " . $this->userSystemID . "
			   AND question_id in (" . $allQuestionToBeIncluded . ") ";

        $rs = WebApp::execQuery($getUsedQuestion);
        while (!$rs->EOF()) {
            $question_id = $rs->Field("question_id");
            $questionToBeExluded[$question_id] = $question_id;
            $rs->MoveNext();
        }


        return implode(",", $questionToBeExluded);

    }

    function calculateQuestionMaxMinPoints($questionID, $createNewCombination)
    {
        $this->getCiNeededVar($questionID);


        $val_DC = $this->EvaluationState["question"]["structure"][$questionID]["DC"];
        $val_EX = $this->EvaluationState["question"]["structure"][$questionID]["EX"];
        $val_OPT = $this->EvaluationState["question"]["structure"][$questionID]["OPT"];
        $val_DCA = $this->EvaluationState["question"]["structure"][$questionID]["DCA"];

        $q_id = $questionID;
        $q_type = $val_EX["question_type"];
        $opsionsShownToTheUser = array();
        
        $max_point_calculated = $val_EX["max_point"];
        $min_point_calculated = $val_EX["min_point"];
        
        
		$max_point_calculated = 0;
		$min_point_calculated = 0;        

        if ($this->evaluation_rules["evaluation_rule"] == "overrided") {
            $max_point_calculated = 0;
            $min_point_calculated = 0;
        }


        if ($createNewCombination == "yes") {

        }

        $optionsFalseToUserAfterEval = array();

/*echo "<textarea>$questionID";
print_r($this->evaluation_rules);
print_r($val_OPT);
echo "</textarea>";*/


        if (isset($val_OPT) && count($val_OPT) > 0) {

            $sessionQAct = $val_OPT;
            while (list($keyi, $valo) = each($sessionQAct)) {

                $o_id = $valo["EQItemID"];
                $opsionsShownToTheUser[$o_id] = $o_id;

                ////-------------------Evaluation points[repository configuration]-----------------------------------------------	
                if ($this->evaluation_rules["evaluation_rule"] == "overrided") {
                    if ($valo['valid_response'] == "y") {//correct
                        $valo["points_if_selected"] = $this->evaluation_rules["correct_ifSelected"];
                        $valo["points_if_not_selected"] = $this->evaluation_rules["correct_ifNotselected"];

                    } else {//not correct
                        $valo["points_if_selected"] = $this->evaluation_rules["incorrect_ifSelected"];
                        $valo["points_if_not_selected"] = $this->evaluation_rules["incorrect_ifNotselected"];
                    }
                }

                if ($q_type == "single" || $q_type == "true_false") {
                    if ($valo['valid_response'] == "y")
                        $max_point_calculated = $valo["points_if_selected"];
                    else {
                        if ($valo["points_if_selected"] < $min_point_calculated)
                            $min_point_calculated = $valo["points_if_selected"];
                    }
                } else {
                    if ($valo['valid_response'] == "y") {
                        $max_point_calculated += $valo["points_if_selected"];
                        
                        $min_point_calculated += $valo["points_if_not_selected"];
                    } else {
                        $max_point_calculated += $valo["points_if_not_selected"];
                        
                        $min_point_calculated += $valo["points_if_selected"];
                    }
                }
                if ($valo['valid_response'] == "y") {
                    $optionsTrueToUserAfterEval[$o_id] = $o_id;
                } else {
                    $optionsFalseToUserAfterEval[$o_id] = $o_id;
                }

            }
        }
        $opsionsShown = implode(",", $opsionsShownToTheUser);
        $sql_update = "
			UPDATE z_EccE_user_examination_question
			   SET max_point			 		= '" . $max_point_calculated . "',
				   min_point 					= '" . $min_point_calculated . "',
				   options_shown_to_the_user 	= '" . $opsionsShown . "'
			 WHERE test_id 		= '" . $this->evaluation_test_id . "'
			   AND question_id	= '" . $questionID . "'";

/*echo "<textarea>";
print_r($sql_update);
echo "</textarea>";*/

        WebApp::execQuery($sql_update);
    }


    function getRunningQuestionConfiguration()
    {
        /*echo "-collect_rules-<textarea>";
			print_r($this->collect_rules);
			echo "</textarea>";	
			echo "-question in repository-<textarea>";
			print_r($this->EvaluationState["question"]);
			echo "</textarea>";	*/


/*



		if (isset($this->collect_rules["nr_of_question"]) 
			&& $this->collect_rules["nr_of_question"] =="custom"
			&& isset($this->collect_rules["nr_of_question_custom"]) 
			&& count($this->collect_rules["nr_of_question_custom"]) >0
			) {
			$this->collect_rules["nr_of_question_configured"] = $this->collect_rules["nr_of_question_custom"];
		} elseif (isset($this->collect_rules["nr_of_question"]) && $this->collect_rules["nr_of_question"] =="all") {
			$this->collect_rules["nr_of_question_configured"] = count($questionAvaible);
		} elseif (isset($this->collect_rules["nr_of_question"]) && count($this->collect_rules["nr_of_question"])>0) {
			$this->collect_rules["nr_of_question_configured"] = $this->collect_rules["nr_of_question"];
		} else {
			$this->collect_rules["nr_of_question_configured"] = count($questionAvaible);
		}

		if ($this->PlayerOfQuestionsEditMode == "yes") {        
                $this->collect_rules["original_nr_of_question_configured"] = $this->collect_rules["nr_of_question_configured"];

		}

*/







        if (isset($this->EvaluationState["question"]["sequence"]) && count($this->EvaluationState["question"]["sequence"])) {

           /* if ($this->PlayerOfQuestionsEditMode == "yes") {
                //read all

				$this->EvaluationState["question_to_be_included"] = $this->EvaluationState["question"]["sequence"];
				while (list($question_id, $val) = each($this->EvaluationState["question"]["allowed"])) {
                    $this->EvaluationState["set_of_question_to_be_included"]["allowed"][$question_id] = $question_id;
                    $this->EvaluationState["set_of_question_to_be_included"]["createNewCombination"][$question_id] = "no";
				}


            } else {*/

                //rikrijo bashkesine e pyetjeve to be included
                $questionAvaible = array();

                $questionToBeExluded = array();
                $questionToBeExluded[0] = 0;

                //$this->collect_rules["questionType"] = "single";


                //COLLECT TYPE OF QUESTION 
                if ($this->collect_rules["collection_type"] == "quiz") {

                    if (isset($this->EvaluationState["question"]["available_for"]["quiz"]) && count($this->EvaluationState["question"]["available_for"]["quiz"]) > 0)
                        $questionAvaible = $this->EvaluationState["question"]["available_for"]["quiz"];

                    if (isset($this->EvaluationState["question"]["available_for"]["both"]) && count($this->EvaluationState["question"]["available_for"]["both"]) > 0)
                        $questionAvaible = array_merge($questionAvaible, $this->EvaluationState["question"]["available_for"]["both"]);

                } elseif ($this->collect_rules["collection_type"] == "examination") {

                    if (isset($this->EvaluationState["question"]["available_for"]["examination"]) && count($this->EvaluationState["question"]["available_for"]["examination"]) > 0)
                        $questionAvaible = $this->EvaluationState["question"]["available_for"]["examination"];

                    if (isset($this->EvaluationState["question"]["available_for"]["both"]) && count($this->EvaluationState["question"]["available_for"]["both"]) > 0)
                        $questionAvaible = array_merge($questionAvaible, $this->EvaluationState["question"]["available_for"]["both"]);

                } else {
                    $questionAvaible = $this->EvaluationState["question"]["allowed"];
                }








				$avaiableQuestionNr = count($questionAvaible);
				
				

				
                //NUMBER OF QUESTION TO BE INCLUDED
				if (isset($this->collect_rules["nr_of_question"]) && $this->collect_rules["nr_of_question"] =="custom"
					&& isset($this->collect_rules["nr_of_question_custom"]) 
					&& $this->collect_rules["nr_of_question_custom"] >0
					&& $this->collect_rules["nr_of_question_custom"] <=$avaiableQuestionNr
					) {
					$this->collect_rules["nr_of_question_configured"] = $this->collect_rules["nr_of_question_custom"];
				
				} elseif (isset($this->collect_rules["nr_of_question"]) && $this->collect_rules["nr_of_question"] =="all") {
					$this->collect_rules["nr_of_question_configured"] = count($questionAvaible);
				
				} elseif (isset($this->collect_rules["nr_of_question"]) 
					&& $this->collect_rules["nr_of_question"]>0
					&& $this->collect_rules["nr_of_question"] <= $avaiableQuestionNr
					) {
					$this->collect_rules["nr_of_question_configured"] = $this->collect_rules["nr_of_question"];
				
				} else {
					$this->collect_rules["nr_of_question_configured"] = count($questionAvaible);
				
				}
				
			
							
				
				
				$this->collect_rules["original_nr_of_question_configured"] = $this->collect_rules["nr_of_question_configured"];
				$this->EvaluationState["set_of_question_to_be_included"] = array();

					if ($this->PlayerOfQuestionsEditMode == "yes") {
						//read all

						$this->EvaluationState["question_to_be_included"] = $this->EvaluationState["question"]["sequence"];
						while (list($question_id, $val) = each($this->EvaluationState["question"]["allowed"])) {
							$this->EvaluationState["set_of_question_to_be_included"]["allowed"][$question_id] = $question_id;
							$this->EvaluationState["set_of_question_to_be_included"]["createNewCombination"][$question_id] = "no";
						}
						
								$allQuestionToBeIncluded = implode(",", $questionAvaible);
								$allQuestionToBeExluded = implode(",", $questionToBeExluded);						
						
						
						
								//if ($this->collect_rules["collection_order"] == "sequential")
									$orderQuestion = " sequence_id";
							//	else    $orderQuestion = " RAND()";


								$sql_con = "SELECT content_id as question_id, 
													   sequence_id,
													   if (content_id in (" . $allQuestionToBeExluded . "),'yes','no') as createNewCombination
												  FROM eq_data 

											WHERE content_id in (" . $allQuestionToBeIncluded . ") AND lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
										 ORDER BY " . $orderQuestion . "";

								$rs = WebApp::execQuery($sql_con);

     
								while (!$rs->EOF()) {
									$question_id = $rs->Field("question_id");
									$createNewCombination = $rs->Field("createNewCombination");


									$this->EvaluationState["set_of_question_to_be_included"]["allowed"][$question_id] = $question_id;
									$this->EvaluationState["set_of_question_to_be_included"]["createNewCombination"][$question_id] = $createNewCombination;

									$rs->MoveNext();
								}						
						
						
						
						
						
						

					} else {


							//COLLECTION_ORDER - 'sequential','random','manually'
							if ($this->collect_rules["collection_order"] == "manually") {
								/*	$joinTb.= " JOIN rq_manually_selected_eq 
											 ON  rq_manually_selected_eq.question_id=  eq_data.content_id
											 AND  rq_manually_selected_eq.content_id=  '".$this->cidFlow."'
											 AND rq_manually_selected_eq.lng_id = '".$this->lngId."'";
										$orderBy = "ORDER BY q_order ASC";*/
							} else {

								$allQuestionToBeIncluded = implode(",", $questionAvaible);
								$allQuestionToBeExluded = implode(",", $questionToBeExluded);

								if ($this->collect_rules["collection_order"] == "sequential")
									$orderQuestion = " sequence_id";
								else    $orderQuestion = " RAND()";


								$sql_con = "SELECT content_id as question_id, 
													   sequence_id,
													   if (content_id in (" . $allQuestionToBeExluded . "),'yes','no') as createNewCombination
												  FROM eq_data 

											WHERE content_id in (" . $allQuestionToBeIncluded . ") AND lng_id = '".$this->lngId."' AND statusInfo = '".$this->thisModeCode."'
										 ORDER BY " . $orderQuestion . "
										 LIMIT 0, " . $this->collect_rules["nr_of_question_configured"] . "";

								$rs = WebApp::execQuery($sql_con);

     
								while (!$rs->EOF()) {
									$question_id = $rs->Field("question_id");
									$createNewCombination = $rs->Field("createNewCombination");


									$this->EvaluationState["set_of_question_to_be_included"]["allowed"][$question_id] = $question_id;
									$this->EvaluationState["set_of_question_to_be_included"]["createNewCombination"][$question_id] = $createNewCombination;

									$rs->MoveNext();
								}
							}
					}
                
                
                
                
                
                
                
			
               
                
                
                
                
            //}

            $this->collect_rules["nr_of_question_to_be_included"] = count($this->EvaluationState["set_of_question_to_be_included"]["allowed"]);
        } else {
        }
        
  
        
/*echo "<textarea>";	
print_r($rs);
echo "</textarea>";	 




            [question] => Array
                (
                    [available_for] => Array
                        (
                            [both] => Array
                                (
                                    [768] => 768
                                )

                        )






*/    
        
        
        
        
        
    }

    function getQuestionsAllowedInList()
    {
		global $session;
		
        $RQID = $this->appRelatedState["LECTURE_RELATED"]["RQ"];
        $coord = $this->appRelatedState["LECTURE_RELATED_INFO"][$RQID]["coord"];

        $orderBy = "ORDER BY orderContent ASC";
        $fieldToSelect = " ";
        $joinTypeCondition = "";
		if ($this->thisModeCode ==0) {
			$conditionToSql = "
						  AND content.state" . $this->lang . " not in (0,5,7)
					";
		} else {
			$conditionToSql = "
						  AND content.state" . $this->lang . " not in (0,5,7)
						  AND content.published" . $this->lang . " = 'Y'
					";		
		}
        $sql_con = "SELECT distinct content.content_id, available_for,coalesce(eq_data.sequence_id,orderContent) as seq		
						
						  FROM content
						  JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
													AND content.id_firstNivel  = profil_rights.id_firstNivel
													AND content.id_secondNivel = profil_rights.id_secondNivel
													AND content.id_thirdNivel  = profil_rights.id_thirdNivel
													AND content.id_fourthNivel = profil_rights.id_fourthNivel
													AND profil_rights.profil_id in (" . $this->tip . ")
												) 
						  
						 JOIN eq_data 
							 ON  eq_data.content_id= content.content_id 
							AND eq_data.repository_id = '" . $RQID . "'  
							AND eq_data.lng_id = '".$this->lngId."'
							AND eq_data.statusInfo = '".$this->thisModeCode."'


						WHERE content.id_zeroNivel 		= '" . $coord[0] . "'
						  AND content.id_firstNivel 	= '" . $coord[1] . "'
						  AND content.id_secondNivel	= '" . $coord[2] . "'
						  AND content.id_thirdNivel 	= '" . $coord[3] . "'
						  AND content.id_fourthNivel 	= '" . $coord[4] . "'
						  AND orderContent!=0
						  AND content.ci_type= 'EQ'
						  ".$conditionToSql."
					 GROUP BY content.content_id";
	/*echo "<textarea>-objEccELrn-".$this->thisModeCode;
	print_r($sql_con);
	echo "</textarea>";	*/


        $itemGrid = array("data" => array(), "AllRecs" => "0");
        $ind = 0;
        $ddd = array();

        $rs = WebApp::execQuery($sql_con);
		$this->EvaluationState["question"]["InTotal"] = 0;
		$this->EvaluationState["question"]["both"] = 0;
		$this->EvaluationState["question"]["quiz"] = 0;
		$this->EvaluationState["question"]["examination"] = 0;
        while (!$rs->EOF()) {

            $content_id = $rs->Field("content_id");
            $ci_type = $rs->Field("ci_type");
            $seqNr = $rs->Field("seq");
            $available_for = $rs->Field("available_for");

            $sortedGuide[$ind]["seqNr"] = $seqNr;

            if ($available_for == "quiz") {
                $this->EvaluationState["question"]["available_for"]["quiz"][$content_id] = $content_id;
                $this->EvaluationState["question"]["quiz"] += 1;
			}
            if ($available_for == "examination") {
                $this->EvaluationState["question"]["available_for"]["examination"][$content_id] = $content_id;
                $this->EvaluationState["question"]["examination"] += 1;
			}
            if ($available_for == "both") {
                $this->EvaluationState["question"]["available_for"]["both"][$content_id] = $content_id;
                $this->EvaluationState["question"]["both"] += 1;
			}
            $this->EvaluationState["question"]["allowed"][$content_id] = $content_id;
            $this->EvaluationState["question"]["sequence"][$seqNr] = $content_id;
            $this->EvaluationState["question"]["InTotal"] += 1;

            //$this->getCiNeededVar($content_id);

            $rs->MoveNext();
        }
    }

    function constructGuideHtml()
    {
		global $session;
		$session->Vars["parseBox"] = "true";
		$session->Vars["callBox"] = "y";

        $tmp["data"] = array();
        $i = 0;
        if (isset($this->guideToHtmlToSave) && $this->guideToHtmlToSave>0) {
        while (list($indexG, $valueG) = each($this->guideToHtmlToSave)) {
            $tmp["data"][$i++]["guideLab"] = $valueG; //WebApp::replaceVars($valueG);
        }}

        $tmp["AllRecs"] = count($tmp["data"]);
        WebApp::addVar("AutomaticallyGuideToGrid", $tmp);
        $f_name_container = NEMODULES_PATH . "EccElearning/AutomaticallyGuideToGrid.html";
        
		WebApp::collectHtmlPage();
		WebApp::constructHtmlPage($f_name_container);
		$guideHtml = WebApp::getHtmlPage();       
        return $guideHtml;
    }
    function initPlayerConfiguration()
    {

        $tplY = new WebBox("buildNavX");
        $message_file = NEMODULES_PATH . "EccElearning/learningEvaluation_English.mesg";
        $tplY->parse_msg_file($message_file);
        extract($GLOBALS["tplVars"]->Vars[0]);

        $guide_collector = array();
        $itemGrid = array("data" => array(), "AllRecs" => "0");


        /*		
		_tag_total_points
		_tag_filled_sessions
		_tag_total_sessions
		_tag_nr_cme_credits
		_tag_total_nr_of_question
		_tag_fixeddate
		_tag_fixedtime
		_tag_startdate
		_tag_finishdate
		_tag_percentage_to_pass
		_tag_nr_allowed_try
		_tag_nr_allowed_try_days
		_tag_time_limit_min_total
		_tag_time_limit_sec
		_tag_nr_cme_credits
		_tag_days_after_assesment
		*/


        $this->tagsToBeReplaced["total_points"] = "";
        $this->tagsToBeReplaced["filled_sessions"] = "";
        $this->tagsToBeReplaced["total_sessions"] = "";

        $this->tagsToBeReplaced["nr_cme_credits"] = "";
        $this->tagsToBeReplaced["total_nr_of_question"] = "";
        $this->tagsToBeReplaced["fixeddate"] = "";
        $this->tagsToBeReplaced["fixedtime"] = "";
        $this->tagsToBeReplaced["startdate"] = "";
        $this->tagsToBeReplaced["finishdate"] = "";
        $this->tagsToBeReplaced["percentage_to_pass"] = "";
        $this->tagsToBeReplaced["percentage_to_pass_mesg"] = "{{_tag_percentage_to_pass}}";


        $this->tagsToBeReplaced["nr_allowed_try"] = "";
        $this->tagsToBeReplaced["nr_allowed_try_days"] = "";
        $this->tagsToBeReplaced["time_limit_min_total"] = "";
        $this->tagsToBeReplaced["time_limit_sec"] = "";
        $this->tagsToBeReplaced["nr_cme_credits"] = "";
        $this->tagsToBeReplaced["days_after_assesment"] = "";

        $this->tagsToBeReplaced["user_points_perqindje"] = "";
        $this->tagsToBeReplaced["total_user_points"] = "";
        $this->tagsToBeReplaced["total_points"] = "";

        $this->tagsToBeReplaced["user_point"] = "";
        $this->tagsToBeReplaced["max_point"] = "";

        $this->tagsToBeReplaced["filled_sessions"] = "";
        $this->tagsToBeReplaced["total_sessions"] = "";

        $this->tagsToBeReplaced["IconTag"] = "";
        $this->tagsToBeReplaced["examinationColorCase"] = "";
        $this->tagsToBeReplaced["CertificateRelatedTag"] = "";
        $this->tagsToBeReplaced["CmeCreditsRelatedTag"] = "";
        $this->tagsToBeReplaced["trafficLightCase"] = "";
        $this->tagsToBeReplaced["enableTrafficLightFeedback"] = "";

        $this->tagsToBeReplaced["view_certificate"] = "{{_view_certificate}}";

        $this->tagsToBeReplaced["user_selected_correct"] = "";
        $this->tagsToBeReplaced["user_selected_in_correct"] = "";
        $this->tagsToBeReplaced["user_if_none_selected"] = "";

        $ind = 20;


        $this->guide_collector_to_user = array();
        $this->collect_rules["nr_of_question_to_be_included"] = 0;

        if (isset($this->EvaluationState["configuration"])) {

            $tmp = $this->EvaluationState["configuration"];

            $this->collect_rules["collection_type"] = $tmp["collection_type"];
            $this->collect_rules["nr_of_question"] = $tmp["nr_of_question"];
            $this->collect_rules["nr_of_question_custom"] = $tmp["nr_of_question_custom"];
            $this->collect_rules["collection_order"] = $tmp["collection_order"];
            $this->collect_rules["nr_options_per_question"] = $tmp["nr_options_per_question"];
            $this->collect_rules["nr_options_costum"] = $tmp["nr_options_costum"];
            $this->collect_rules["avoid_same_question"] = $tmp["avoid_same_question"];

            //Nr. of allowed attempts
            $this->configuration_rules["nr_allowed_try"] = "unlimited";
            if (isset($tmp["nr_allowed_try"]) && $tmp["nr_allowed_try"] > 0) {
                $this->configuration_rules["nr_allowed_try"] = $tmp["nr_allowed_try"];
                $this->tagsToBeReplaced["nr_allowed_try"] = $tmp["nr_allowed_try"];

                if ($this->tagsToBeReplaced["nr_allowed_try"] == 1) {
                    $this->tagsToBeReplaced["nr_allowed_try"] = 1;
                    $this->configuration_rules["nr_allowed_try"] = $tmp["nr_allowed_try"];
                    $sortedGuide[7] = WebApp::getVar("_ases_repetition_allowed_data_one_time");
                } elseif ($this->tagsToBeReplaced["nr_allowed_try"] == 2) {
                    $this->tagsToBeReplaced["nr_allowed_try"] = 1;
                   // $this->configuration_rules["nr_allowed_try"] = $tmp["nr_allowed_try"];
                    $sortedGuide[7] = WebApp::getVar("_ases_repetition_allowed_data_one_retake");
                } else {
                    $this->tagsToBeReplaced["nr_allowed_try"] = $tmp["nr_allowed_try"]-1;
                    $sortedGuide[7] = WebApp::getVar("_ases_repetition_allowed_data");
                }
               
               
               //   $this->tagsToBeReplaced["nr_allowed_try"] = $tmp["nr_allowed_try"]-1;

            } else {
                $this->tagsToBeReplaced["nr_allowed_try"] = 1;
                $this->configuration_rules["nr_allowed_try"] = $tmp["nr_allowed_try"];
                $sortedGuide[7] = WebApp::getVar("_ases_repetition_allowed_data_one_time");
            }


            //Nr of day(s) after the last attempt
            if (isset($tmp["nr_allowed_try_days"]) && $tmp["nr_allowed_try_days"] > 0) {
                $this->configuration_rules["nr_allowed_try_days"] = $tmp["nr_allowed_try_days"];
                $this->tagsToBeReplaced["nr_allowed_try_days"] = $tmp["nr_allowed_try_days"];
                $sortedGuide[8] = WebApp::getVar("_repeatedTimeInterval");
            }


            //Assessment timing
            $this->configuration_rules["assesment_timing"] = "any_time";
            if ($tmp["assesment_timing"] == "any_time") {
                $this->configuration_rules["assesment_timing"] = "any_time";

            } elseif ($tmp["time_limit"] == "fixed") {

                $this->configuration_rules["fixeddate"] = $tmp["fixeddate"];
                $this->configuration_rules["fixedtime"] = $tmp["fixedtime"];

                $sortedGuide[$ind++] = WebApp::getVar("_assesment_timing_at_a_fixed_date");

            } elseif ($tmp["time_limit"] == "interval") {

                $this->configuration_rules["startdate"] = $tmp["startdate"];
                $this->configuration_rules["finishdate"] = $tmp["finishdate"];

                $sortedGuide[$ind++] = WebApp::getVar("_assesment_timing_interval_data");
            }


            if (isset($tmp["allow_negative_points"]) && $tmp["allow_negative_points"] != "")
                $this->evaluation_rules["allow_negative_points"] = $tmp["allow_negative_points"];

            if (isset($tmp["evaluation_rule"]) && $tmp["evaluation_rule"] == "defined") {

                $this->evaluation_rules["evaluation_rule"] = "overrided";
                if (isset($tmp["correct_ifSelected"]) && $tmp["correct_ifSelected"] != "") {
                    $this->evaluation_rules["correct_ifSelected"] = $tmp["correct_ifSelected"];
                    $this->tagsToBeReplaced["user_selected_correct"] = $this->format_points($tmp["correct_ifSelected"]);
                }

                if (isset($tmp["correct_ifNotselected"]) && $tmp["correct_ifNotselected"] != "")
                    $this->evaluation_rules["correct_ifNotselected"] = $tmp["correct_ifNotselected"];

                if (isset($tmp["incorrect_ifSelected"]) && $tmp["incorrect_ifSelected"] != "") {
                    $this->evaluation_rules["incorrect_ifSelected"] = $tmp["incorrect_ifSelected"];
                    $this->tagsToBeReplaced["user_selected_incorrect"] = $this->format_points($tmp["incorrect_ifSelected"]);

                }

                if (isset($tmp["incorrect_ifNotselected"]) && $tmp["incorrect_ifNotselected"] != "")
                    $this->evaluation_rules["incorrect_ifNotselected"] = $tmp["incorrect_ifNotselected"];
                if (isset($tmp["max_point"]) && $tmp["max_point"] != "")
                    $this->evaluation_rules["if_all_selected"] = $tmp["max_point"];

                if (isset($tmp["min_point"]) && $tmp["min_point"] != "") {
                    $this->evaluation_rules["if_none_selected"] = $tmp["min_point"];
                    $this->tagsToBeReplaced["user_if_none_selected"] = $this->format_points($tmp["min_point"]);
                }

                $sortedGuide[3] = WebApp::getVar("_evaluation_points_to_user");

            } else {
                $this->evaluation_rules["evaluation_rule"] = "fromQuestion";
            }


            //Percentage to pass test
            if (isset($tmp["per_points_to_pass"]) && $tmp["per_points_to_pass"] > 0) {
                $this->evaluation_rules["per_points_to_pass"] = $tmp["per_points_to_pass"];
                $this->evaluation_rules["percentpoint"] = $tmp["per_points_to_pass"];
                $this->tagsToBeReplaced["percentage_to_pass"] = $tmp["per_points_to_pass"];
                $sortedGuide[4] = WebApp::getVar("_percentage_to_pass");
            }


            //CME Credits


            $this->user_report_rules["user_certificate"] = "no";

            if (isset($tmp["user_certificate"]) && $tmp["user_certificate"] == "yes") {

                $this->user_report_rules["user_certificate"] = "yes";
                $this->user_report_rules["cme_credits"] = "no";
                if (isset($tmp["cme_credits"]) && $tmp["cme_credits"] == "yes") {

                    $this->user_report_rules["cme_credits"] = $tmp["cme_credits"];
                    $this->user_report_rules["nr_cme_credits"] = $tmp["nr_cme_credits"];
                    $this->tagsToBeReplaced["nr_cme_credits"] = $tmp["nr_cme_credits"];


                    if (isset($tmp["evaluation_time"]) && $tmp["evaluation_time"] != "")
                        $this->user_report_rules["evaluation_time"] = $tmp["evaluation_time"]; //`evaluation_time` enum('automatic','later','deadline')	
                    if ($this->user_report_rules["evaluation_time"] == "deadline") {
                        $this->user_report_rules["answer_evaluation_deadline"] = $tmp["answer_evaluation_deadline"];
                    }

                    if (isset($tmp["user_report"]) && $tmp["user_report"] != "")
                        $this->user_report_rules["user_report"] = $tmp["user_report"];

                    /*if (isset($tmp["after_face_identification"]) && $tmp["after_face_identification"]!="")
						$this->user_report_rules["after_face_identification"] =  $tmp["after_face_identification"];*/

                    $this->user_report_rules["certificate_evaluation"] = "";

                  
                  //kjo ishte te else poshte
                  $this->user_report_rules["certificate_evaluation"] = "automatic";
                  $sortedGuide[$ind++] = WebApp::getVar("_receive_certificate_yes_cmeCredits_immediatly");
                  
                  
                  if (isset($tmp["certificate_evaluation"]) && $tmp["certificate_evaluation"] != "") {
                        $this->user_report_rules["certificate_evaluation"] = $tmp["certificate_evaluation"];

                        if ($this->user_report_rules["certificate_evaluation"] == "deadline"
                            && $tmp["certificate_evaluation_days"] > 0
                        ) {
                            $this->user_report_rules["certificate_evaluation_days"] = $tmp["certificate_evaluation_days"];

                            $sortedGuide[$ind++] = WebApp::getVar("_receive_certificate_yes_cmeCredits_later");
                            $this->tagsToBeReplaced["certificate_evaluation_days"] = $tmp["certificate_evaluation_days"];

                       }/*  else {
                            $this->user_report_rules["certificate_evaluation"] = "automatic";
                            $sortedGuide[$ind++] = WebApp::getVar("_receive_certificate_yes_cmeCredits_immediatly");
                        }*/
                    }
                } else {
                    $sortedGuide[$ind++] = WebApp::getVar("_receive_certificate_yes");
                }
            }

            //nr_of_question_to_be_included			
            if (isset($tmp["traffic_light_feedback"]) && $tmp["traffic_light_feedback"] == "yes") {

                $this->user_report_rules["traffic_light_feedback"] = $tmp["traffic_light_feedback"];
                if (isset($tmp["traffic_light_red"]) && $tmp["traffic_light_red"] > 0)
                    $this->tagsToBeReplaced["percentage_to_pass"] = $tmp["traffic_light_red"];
                else
                    $this->tagsToBeReplaced["percentage_to_pass"] = 40;

                $this->user_report_rules["traffic_light_red"] = $tmp["traffic_light_red"];
                if (isset($tmp["traffic_light_orange"]) && $tmp["traffic_light_orange"] > 0)
                    $this->user_report_rules["traffic_light_orange"] = $tmp["traffic_light_orange"];
                else    $this->user_report_rules["traffic_light_orange"] = 60;

                $this->tagsToBeReplaced["percent_red"] = $this->user_report_rules["traffic_light_red"];
                $this->tagsToBeReplaced["percent_orange"] = $this->user_report_rules["traffic_light_orange"];

                $sortedGuide[4] = WebApp::getVar("_lightFeedback_percentage_report");
            }

            //`feedback_model` enum('client','each','sessionEnd'
            if (isset($tmp["feedback_model"]) && $tmp["feedback_model"] != "")
                $this->flow_model["feedback_model"] = $tmp["feedback_model"];


            //Allow reset
            $this->configuration_rules["allow_reset"] = "no";
            if ($tmp["allow_reset"] == "all_users") {
                $this->configuration_rules["allow_reset"] = "yes";
            } elseif ($tmp["allow_reset"] == "yes" && $this->appRelatedState["CiRights"][$this->cidFlow]["read_write"] == "W") {
                $this->configuration_rules["allow_reset"] = "yes";
            }

            if ($this->PlayerOfQuestionsEditMode == "yes") {
                $this->configuration_rules["allow_reset"] = "yes";
            }

            $this->getQuestionsAllowedInList();
            $this->getRunningQuestionConfiguration();
            /*if (isset($tmp["nr_of_question_custom"]) && $tmp["nr_of_question_custom"]>0) {
				$this->configuration_rules["total_nr_of_question"] =  $tmp["nr_of_question_custom"];
			} elseif (isset($tmp["nr_of_question"]) && $tmp["nr_of_question"]=="all") { 
				//read from actual state and configuration
				$this->configuration_rules["total_nr_of_question"] =  count($this->EvaluationState["set_of_question_to_be_included"]["allowed"]);
			} else {
				$this->configuration_rules["total_nr_of_question"] =  $tmp["nr_of_question"];	
			}*/
            $this->configuration_rules["total_nr_of_question"] = "" . $this->collect_rules["nr_of_question_to_be_included"];
           
            if (isset($this->collect_rules["original_nr_of_question_configured"]) 
            	&& $this->collect_rules["original_nr_of_question_configured"]!=$this->collect_rules["total_nr_of_question"])
            	$this->tagsToBeReplaced["total_nr_of_question"] = "" . $this->collect_rules["original_nr_of_question_configured"];
            else
            	$this->tagsToBeReplaced["total_nr_of_question"] = "" . $this->configuration_rules["total_nr_of_question"];

/*	echo "<textarea>-objEccELrn-";
	print_r($this->collect_rules);
	print_r($this->tagsToBeReplaced);
	echo "</textarea>";	*/

            $sortedGuide[1] = WebApp::getVar("_totalNumberOfQuestion");

            //time_limit
            $this->configuration_rules["time_limit"] = "n";
            $this->configuration_rules["timed_on"] = "off";
            if ($tmp["time_limit"] == "yes") {
                $this->configuration_rules["time_limit"] = "y";        //limit percaktohet per pyetje duke e lexuar limitimin nga pyetja
                //$sortedGuide[$ind++] = WebApp::getVar("_koha_e_lejuar_e_testit_from_question");
                $sortedGuide[$ind++] = WebApp::getVar("_koha_e_lejuar_e_testit");

            } elseif ($tmp["time_limit"] == "custom") {

                $this->configuration_rules["time_limit"] = "y";    //limit percaktohet per pyetje duke e lexuar limitin costum te percaktuar tek playeri vet

                if (isset($tmp["time_limit_sec"]) && $tmp["time_limit_sec"] > 0) {

                    $this->configuration_rules["time_limit_sec"] = $tmp["time_limit_sec"];
                    $this->configuration_rules["time_limit_sec_total"] = $tmp["time_limit_sec"];

                    $this->configuration_rules["time_limit_min_total"] = round($this->configuration_rules["time_limit_sec_total"] / 60, 0);
                    $this->tagsToBeReplaced["time_limit_min_total_formated"] = $this->secondsToTime($this->configuration_rules["time_limit_sec_total"]);

                    $this->configuration_rules["timed_on"] = "on";

                    $sortedGuide[2] = WebApp::getVar("_koha_e_lejuar_e_testit_total");
                    $this->tagsToBeReplaced["time_limit_min_total"] = $this->configuration_rules["time_limit_min_total"];
                } else {
                    $this->configuration_rules["time_limit"] = "n";
                }
            }


            //_allow_restart_yes_with_time_limit
            //allow_restart


            $varTo = "";
            if (isset($tmp["allow_restart"]) && $tmp["allow_restart"] == 1) {

                $this->configuration_rules["allow_restart"] = "y";
                if ($this->configuration_rules["time_limit"] == "y") {
                    $sortedGuide[6] = WebApp::getVar("_allow_restart_yes_with_time_limit");
                    $varTo = "_allow_restart_yes_with_time_limit";
                } else {
                    $sortedGuide[6] = WebApp::getVar("_allow_restart_yes");
                    $varTo = "_allow_restart_yes_with_time_limit";
                }

            } else {
                $this->configuration_rules["allow_restart"] = "n";
                /*_allow_restart_no:
				If you interrupt the test you will not be allowed to resume it back and only the answers you have submitted earlier will be evaluated. */
                $sortedGuide[6] = WebApp::getVar("_allow_restart_no");
                $varTo = "_allow_restart_no";
            }
            //	$sortedGuide[6] .= $tmp["allow_restart"].":allow_restart".$this->configuration_rules["allow_restart"]."<br>";
            //	$sortedGuide[6] .= ":time_limit".$this->configuration_rules["time_limit"]."<br>$varTo<br>";

            //allow_back
            if (isset($tmp["allow_back"]) && $tmp["allow_back"] == 1) {
                $this->configuration_rules["allow_back"] = "y";
                if ($tmp["feedback_model"] == "sessionEnd") {
                    $sortedGuide[5] = WebApp::getVar("_allow_back_reset_yes");
                } else {
                    $sortedGuide[5] = WebApp::getVar("_allow_back_yes");
                }
            } else {
                $this->configuration_rules["allow_back"] = "n";
                $sortedGuide[5] = WebApp::getVar("_allow_back_no");
            }

            //allow_redo_cert
            if (isset($tmp["allow_redo_cert"]) && $tmp["allow_redo_cert"] == 1) {
                $this->configuration_rules["allow_redo_cert"] = "y";
                //$sortedGuide[8] = WebApp::getVar("_allow_redo_cert_yes"); 
            } else {
                $this->configuration_rules["allow_redo_cert"] = "n";
                //$sortedGuide[8] = WebApp::getVar("_allow_redo_cert_no"); 
            }

            //User identification
            $this->configuration_rules["capture_identification"] = "no";
            if ($tmp["capture_identification"] == "yes") {
                $this->configuration_rules["capture_identification"] = "yes";


                if (isset($tmp["capture_identification_force"]) && $tmp["capture_identification_force"] != "") {
                    $this->configuration_rules["capture_identification_force"] = $tmp["capture_identification_force"];
                    $sortedGuide[$ind++] = WebApp::getVar("_capture_identification_forced_yes");
                } else {
                    $this->configuration_rules["capture_identification_force"] = "no";
                    $sortedGuide[$ind++] = WebApp::getVar("_capture_identification_yes");
                }

            }

            //Answers evaluation and Model of feedback
            $this->configuration_rules["evaluation_time"] = "automatic";
            if ($tmp["evaluation_time"] == "deadline") {
                $this->configuration_rules["evaluation_time"] = "deadline";

                //Nr of day(s) after the last attempt
                if (isset($tmp["_days_after_assesment"]) && $tmp["_days_after_assesment"] > 0) {
                    $this->configuration_rules["days_after_assesment"] = $tmp["_days_after_assesment"];
                    $sortedGuide[$ind++] = WebApp::getVar("_days_after_assesmentResult");
                }
            } elseif ($tmp["evaluation_time"] == "later") {
                $this->configuration_rules["evaluation_time"] = "later";
                $sortedGuide[$ind++] = WebApp::getVar("evaluation_timeLater");

            } elseif ($tmp["evaluation_time"] == "automatic") {
                $this->configuration_rules["evaluation_time"] = "automatic";


            } else {
            }
        }


        if (isset($sortedGuide) && $sortedGuide > 0) {
            ksort($sortedGuide);
            $this->guideToHtmlToSave = $sortedGuide;

            $itemGrid["data"] = array();
            $ind = 0;
            while (list($key, $value) = each($sortedGuide)) {
                $itemGrid["data"][$ind]["guideLabAutor"] = $value;
                $itemGrid["data"][$ind++]["guideLabAutor"] = trim($this->getPlaceholdersToReplace($value));
            }

            $itemGrid["AllRecs"] = count($sortedGuide);
            WebApp::addVar("GuideToGrid", $itemGrid);
        }

    
     global $sessUserObj;
     
     
     /*echo "<textarea>";
     print_r($this->PlayerOfQuestionsEditMode.":PlayerOfQuestionsEditMode<br>");
     print_r($sessUserObj->typeOfUser.":typeOfUser");
     echo "</textarea>";*/
    
    //  		WebApp::addVar("rqAuto_reviewMode", "{{_reviewMode}}");
    //  	WebApp::addVar("rqAuto_reviewEditMode", "{{_reviewEditMode}}");

	//$modulDynamicMessages[rqAuto]["reviewMode"]
	//$modulDynamicMessages[rqAuto]["reviewEditMode"]

        //$this->modulDynamicMessages["trafficLight"][$this->tagsToBeReplaced["trafficLightCase"]];
        global $session;
	//	$this->tagsToBeReplaced["nr_of_question_to_be_included"] = $this->configuration_rules["nr_of_question_to_be_included"];

        $this->getEvaluationState();
        $this->getMessagesModulTags();
        
        
      /*  if ($session->Vars["uni"]=='20151117202725192168120245547238') {
        echo "<textarea>";
        print_r($this->modulDynamicMessages);
        echo "</textarea>";
        }*/
        
		WebApp::addVar("_tag_nr_of_question_to_be_included", "".$this->collect_rules["nr_of_question_to_be_included"]);
        
 
        if ($this->PlayerOfQuestionsEditMode == "yes") {
            $this->configuration_rules["nr_of_question_to_be_included"] = "" . $this->collect_rules["nr_of_question_to_be_included"];
            
            if ($sessUserObj->typeOfUser == "AC") {
				
				
				if ($this->collect_rules["nr_of_question_to_be_included"] == 0) {
					$this->error_no_question = 1;

					$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_testAvaiableSoon}}</span>";
					$tmpD["AllRecs"] = count($tmpD["data"]);
					WebApp::addVar("GuideToGridEditMode", $tmpD);
				
				
				}  else {
					
					if (isset($this->modulDynamicMessages["reviewMode"]["label"])  && $this->modulDynamicMessages["reviewMode"]["label"]!="") {
					
						$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">".$this->modulDynamicMessages["reviewMode"]["label"]."</span>";
						$tmpD["AllRecs"] = count($tmpD["data"]);
						WebApp::addVar("GuideToGridEditMode", $tmpD);
					
					} else {					
					
						$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_reviewMode}}</span>";
						$tmpD["AllRecs"] = count($tmpD["data"]);
						WebApp::addVar("GuideToGridEditMode", $tmpD);
					}
				} 
				
            } else {
				
				if ($this->collect_rules["nr_of_question_to_be_included"] == 0) {
					$this->error_no_question = 1;
					$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_PleaseConfigureQuestions}}</span>";
					$tmpD["AllRecs"] = count($tmpD["data"]);
					WebApp::addVar("GuideToGridEditMode", $tmpD);
				
				} else {
					
					if (isset($this->modulDynamicMessages["reviewEditMode"]["label"]) && $this->modulDynamicMessages["reviewEditMode"]["label"]!="") {
					
						$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">".$this->modulDynamicMessages["reviewEditMode"]["label"]."</span>";
						$tmpD["AllRecs"] = count($tmpD["data"]);
						WebApp::addVar("GuideToGridEditMode", $tmpD);
					} else {					
					
						$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_reviewModeEdit}}</span>";
						$tmpD["AllRecs"] = count($tmpD["data"]);
						WebApp::addVar("GuideToGridEditMode", $tmpD);
					}
					
				}
            }
        } else {

            if ($this->collect_rules["nr_of_question_to_be_included"] == 0) {
                $this->error_no_question = 1;

                $tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_testAvaiableSoon}}</span>";
                $tmpD["AllRecs"] = count($tmpD["data"]);
                WebApp::addVar("GuideToGridEditMode", $tmpD);
           
           
           } else {
					
					if (isset($session->Vars["EccEmode"])  && $session->Vars["EccEmode"]=="Review") {
											
						if (isset($this->modulDynamicMessages["reviewMode"]["label"])  && $this->modulDynamicMessages["reviewMode"]["label"]!="") {

							$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">".$this->modulDynamicMessages["reviewMode"]["label"]."</span>";
							$tmpD["AllRecs"] = count($tmpD["data"]);
							WebApp::addVar("GuideToGridEditMode", $tmpD);

						} else {					

							$tmpD["data"][0]["guideLabInfo"] = "<span style=\"color:red\">{{_reviewMode}}</span>";
							$tmpD["AllRecs"] = count($tmpD["data"]);
							WebApp::addVar("GuideToGridEditMode", $tmpD);
						}
						
						
					}            
            
            }
        }
//session.AddVar('EccEmode','Review');



    }

    function getMessagesModulTags()
    {
        $modulDynamicMessages = array();
        $getDescriptors = "SELECT coalesce(group_id, '') as group_id, coalesce(key_message, '') as key_message
								  , coalesce(value_message, '') as value_message
							 FROM rq_modul_messages 
							WHERE content_id = '" . $this->cidFlow . "' AND lng_id = '".$this->lngId."'";
        $rs = WebApp::execQuery($getDescriptors);
        while (!$rs->EOF()) {

            $group_id = $rs->Field("group_id");
            $key_message = $rs->Field("key_message");
            $value_message = $rs->Field("value_message");
            if ($key_message=="")	$key_message = "label";
            $modulDynamicMessages[$group_id][$key_message] = trim($this->getPlaceholdersToReplace($value_message));

            $rs->MoveNext();
        }

        /*
        $buttonMessages["finalizeWarning"] = "{{_finalize_warning}}";

        WebApp::addVar("rqAuto_finalize_descr", "{{_descriptor_finalizeContent}}");    //rqAuto_descr_finalize
        WebApp::addVar("rqAuto_finalize_fc", 	"{{_finalize_FullContent}}");                    //rqAuto_fc_finalize

        WebApp::addVar("rqAuto_trafficLight_red", "{{_redHeadline}}");
        WebApp::addVar("rqAuto_trafficLight_orange", "{{_orangeHeadline}}");
        WebApp::addVar("rqAuto_trafficLight_green", "{{_greenHeadline}}");

        WebApp::addVar("rqAuto_resultHeadline_Passed", "{{_testPassedHeadline}}");
        WebApp::addVar("rqAuto_resultHeadline_Failed", "{{_testFailedHeadline}}");

        WebApp::addVar("rqAuto_resultfc_result", "{{_test_report_to_user}}");

        WebApp::addVar("rqAuto_certificateHeadline_immediate", "{{_immediateCertificationHeadline}}");
        WebApp::addVar("rqAuto_certificateHeadline_FaceIden", "{{_afterFaceIdentificationCertificationHeadline}}");
        WebApp::addVar("rqAuto_certificateHeadline_cmeCredits", "{{_cmeCreditsHeadline}}");


*/

        $buttonMessages = array();

        $buttonMessages["totalTime"] = "{{_total_time}}";
        $buttonMessages["remainingTime"] = "{{_Remaining_time}}";
        $buttonMessages["averageTime"] = "{{_average_time_header}}";
        $buttonMessages["expectedAverage"] = "{{_expected_avg}}";
        $buttonMessages["userAverage"] = "{{_users_avg}}";


        $buttonMessages["beginTest"] = "{{_begin_test}}";
        $buttonMessages["continueTest"] = "{{_continue_test}}";
        $buttonMessages["retryTest"] = "{{_retry_test}}";
        $buttonMessages["GoBack"] = "{{_Go_back}}";
        $buttonMessages["GoNext"] = "{{_Go_next}}";
        $buttonMessages["submitAnswer"] = "{{_submitAnser}}";
        $buttonMessages["resetAnswer"] = "{{_reset_answer}}";
        $buttonMessages["finalize"] = "{{_finalize}}";
        $buttonMessages["finalizeWarning"] = "{{_finalize_warning_test}}";

        $buttonMessages["optionPoints"] = "{{_optionPoints}}";
        $buttonMessages["questionPoints"] = "{{_questionPoints}}";

        if ($this->ci_type_configuration == "CQ") {

            $buttonMessages["beginTest"] = "{{_begin_quiz}}";
            $buttonMessages["continueTest"] = "{{_continue_quiz}}";
            $buttonMessages["retryTest"] = "{{_retry_quiz}}";
            $buttonMessages["finalizeWarning"] = "{{_finalize_warning_quiz}}";
        }


        //$buttonMessages["finalize_headline"] 	= WebApp::getVar("_descriptor_finalizeContent");
        $buttonMessages["finalize_text"] = WebApp::getVar("_finalize_FullContent");


        $buttonMessages["trafficLight_red"] = "{{_redHeadline}}";
        $buttonMessages["trafficLight_orange"] = "{{_orangeHeadline}}";
        $buttonMessages["trafficLight_green"] = "{{_greenHeadline}}";


        $buttonMessages["result_Passed"] = "{{_testPassedHeadline}}";
        $buttonMessages["result_Failed"] = "{{_testFailedHeadline}}";

        $buttonMessages["cmeCredits_Passed"] = "{{_cmeCreditsReceived}}";
        $buttonMessages["result_Failed"] = "{{_cmeCreditsLost}}";

        $buttonMessages["certificate_immediate"] = "{{_immediateCertificationHeadline}}";
        $buttonMessages["certificate_later"] = "{{_laterCertificationHeadline}}";


        $buttonMessages["Authentication"] = "{{_Authentication}}";
        $buttonMessages["AuthenticationHelp"] = "{{_Authentication_help}}";
        $buttonMessages["activateCamera"] = "{{_activate_camera}}";
        $buttonMessages["activateCameraForced"] = "{{_activate_camera_forced}}";
        $buttonMessages["EnableCamera"] = "{{_Enable_Camera}}";

        $buttonMessages["firstWarning"] = "{{_first_warning}}";
        $buttonMessages["secondWarning"] = "{{_second_warning}}";
        $buttonMessages["lastWarning"] = "{{_last_warning}}";


        if (isset($modulDynamicMessages["btn"])) {

            $btn = $modulDynamicMessages["btn"];
            if (isset($btn["beginTest"]) && $btn["beginTest"] != "") $buttonMessages["beginTest"] = $btn["beginTest"];
            if (isset($btn["continueTest"]) && $btn["continueTest"] != "") $buttonMessages["continueTest"] = $btn["continueTest"];
            if (isset($btn["retryTest"]) && $btn["retryTest"] != "") $buttonMessages["retryTest"] = $btn["retryTest"];
            if (isset($btn["GoBack"]) && $btn["GoBack"] != "") $buttonMessages["GoBack"] = $btn["GoBack"];
            if (isset($btn["GoNext"]) && $btn["GoNext"] != "") $buttonMessages["GoNext"] = $btn["GoNext"];
            if (isset($btn["submitAnswer"]) && $btn["submitAnswer"] != "") $buttonMessages["submitAnswer"] = $btn["submitAnswer"];
            if (isset($btn["resetAnswer"]) && $btn["resetAnswer"] != "") $buttonMessages["resetAnswer"] = $btn["resetAnswer"];


            if (isset($btn["finalize"]) && $btn["finalize"] != "") $buttonMessages["finalize"] = $btn["finalize"];

            if (isset($modulDynamicMessages["points"]["bllok"]) && $modulDynamicMessages["points"]["bllok"] != "") $buttonMessages["questionPoints"] = $modulDynamicMessages["points"]["bllok"];
            if (isset($modulDynamicMessages["points"]["option"]) && $modulDynamicMessages["points"]["option"] != "") $buttonMessages["optionPoints"] = $modulDynamicMessages["points"]["option"];


            //if (isset($modulDynamicMessages["finalize"]["descr"]) && $modulDynamicMessages["finalize"]["descr"]!="") 		$buttonMessages["finalize_headline"] = $modulDynamicMessages["finalize"]["descr"];
            if (isset($modulDynamicMessages["finalize"]["fc"]) && $modulDynamicMessages["finalize"]["fc"] != "") $buttonMessages["finalize_text"] = $modulDynamicMessages["finalize"]["fc"];

            if (isset($modulDynamicMessages["finalize"]["warning"]) && $modulDynamicMessages["finalize"]["warning"] != "")
                $buttonMessages["finalizeWarning"] = $modulDynamicMessages["finalize"]["warning"];

            if (isset($modulDynamicMessages["timer"]["totalTime"]) && $modulDynamicMessages["timer"]["totalTime"] != "")
                $buttonMessages["totalTime"] = $modulDynamicMessages["timer"]["totalTime"];

            if (isset($modulDynamicMessages["timer"]["remainingTime"]) && $modulDynamicMessages["timer"]["remainingTime"] != "")
                $buttonMessages["remainingTime"] = $modulDynamicMessages["timer"]["remainingTime"];

            if (isset($modulDynamicMessages["timer"]["averageTime"]) && $modulDynamicMessages["timer"]["averageTime"] != "")
                $buttonMessages["averageTime"] = $modulDynamicMessages["timer"]["averageTime"];

            if (isset($modulDynamicMessages["timer"]["expectedAverage"]) && $modulDynamicMessages["timer"]["expectedAverage"] != "")
                $buttonMessages["expectedAverage"] = $modulDynamicMessages["timer"]["expectedAverage"];

            if (isset($modulDynamicMessages["timer"]["userAverage"]) && $modulDynamicMessages["timer"]["userAverage"] != "")
                $buttonMessages["userAverage"] = $modulDynamicMessages["timer"]["userAverage"];


            if (isset($modulDynamicMessages["authentication"]["Authentication"]) && $modulDynamicMessages["authentication"]["Authentication"] != "")
                $buttonMessages["Authentication"] = $modulDynamicMessages["authentication"]["Authentication"];
            if (isset($modulDynamicMessages["authentication"]["AuthenticationHelp"]) && $modulDynamicMessages["authentication"]["AuthenticationHelp"] != "")
                $buttonMessages["AuthenticationHelp"] = $modulDynamicMessages["authentication"]["AuthenticationHelp"];
            if (isset($modulDynamicMessages["authentication"]["activateCamera"]) && $modulDynamicMessages["authentication"]["activateCamera"] != "")
                $buttonMessages["activateCamera"] = $modulDynamicMessages["authentication"]["activateCamera"];
            if (isset($modulDynamicMessages["authentication"]["activateCameraForced"]) && $modulDynamicMessages["authentication"]["activateCameraForced"] != "")
                $buttonMessages["activateCameraForced"] = $modulDynamicMessages["authentication"]["activateCameraForced"];
            if (isset($modulDynamicMessages["authentication"]["EnableCamera"]) && $modulDynamicMessages["authentication"]["EnableCamera"] != "")
                $buttonMessages["EnableCamera"] = $modulDynamicMessages["authentication"]["EnableCamera"];
            if (isset($modulDynamicMessages["authentication"]["firstWarning"]) && $modulDynamicMessages["authentication"]["firstWarning"] != "")
                $buttonMessages["firstWarning"] = $modulDynamicMessages["authentication"]["firstWarning"];
            if (isset($modulDynamicMessages["authentication"]["secondWarning"]) && $modulDynamicMessages["authentication"]["secondWarning"] != "")
                $buttonMessages["secondWarning"] = $modulDynamicMessages["authentication"]["secondWarning"];


            if (isset($modulDynamicMessages["authentication"]["AuthenticationHelpDescriptor"]) && $modulDynamicMessages["authentication"]["AuthenticationHelpDescriptor"] != "")
                $buttonMessages["AuthenticationHelpDescriptor"] = $modulDynamicMessages["authentication"]["AuthenticationHelpDescriptor"];


            if (isset($modulDynamicMessages["retry"]["AuthenticationHelp"]) && $modulDynamicMessages["retry"]["AuthenticationHelp"] != "")
                $buttonMessages["AuthenticationHelp"] = $modulDynamicMessages["retry"]["AuthenticationHelp"];

            if (isset($modulDynamicMessages["retry"]["fc"]) && $modulDynamicMessages["retry"]["fc"] != "")
                $buttonMessages["retryFullContent"] = $modulDynamicMessages["retry"]["fc"];
            else    $buttonMessages["retryFullContent"] = "";


        }

        $this->modulDynamicMessages = $modulDynamicMessages;
        if (isset($this->modulDynamicMessages["btn"]["redoTestAfterInterval"])
            && $this->modulDynamicMessages["btn"]["redoTestAfterInterval"] != ""
            && $this->internvalBetweenEvalueations == "dont_allow"
        ) {
            $this->tagsToBeReplaced["redoTestAfterInterval"] = $this->modulDynamicMessages["btn"]["redoTestAfterInterval"];
        }

        if (isset($this->ulimitedTimesToBeRepeat) && $this->ulimitedTimesToBeRepeat == "yes") {
            $buttonMessages["retryFullContent"] = "";
            $this->tagsToBeReplaced["retryFullContent"] = "";
        }


        while (list($key, $value) = each($buttonMessages)) {

            $valueP = $this->getPlaceholdersToReplace($value);
            WebApp::addVar("lbl_$key", "$valueP");

            //echo "lbl_$key:$valueP<br>";
        }
    }

    function getPlaceholdersToReplace($htmlToParse)
    {

        $content_html = $contentTemplateTI;
        $contentTemplateContainerTI = $contentTemplateTI;
        $regex_var_templ = "#\[\[([^\]]*)\]\]#is";        //([\]]*)

        //echo "<textarea>";
        //print_r($htmlToParse);

        if (preg_match_all($regex_var_templ, $htmlToParse, $matches_var_templ)) {

            $i = 0;
            while ($i < count($matches_var_templ[1])) {
                $tagToBeReplaced = "#\[\[" . $matches_var_templ[1][$i] . "\]\]#";
                $tagToReplace = "{{_tag_" . $matches_var_templ[1][$i] . "}}";

                $htmlToParse = preg_replace($tagToBeReplaced, $tagToReplace, $htmlToParse);
                $i++;
            }
        }
        //print_r($htmlToParse);timevalue		_tag_timevalue
        //echo "</textarea>";		
        return $htmlToParse;
    }

    function AddNeddedVar()
    {
        if (isset($this->configuration_rules) && count($this->configuration_rules) > 0) {
            while (list($key, $value) = each($this->configuration_rules)) {
                WebApp::addVar("$key", "$value");
            }
        }

        if (isset($this->evaluation_rules) && count($this->evaluation_rules) > 0) {
            while (list($key, $value) = each($this->evaluation_rules)) {
                WebApp::addVar("$key", "$value");
            }
        }

        WebApp::addVar("allow_back_btn", $this->configuration_rules["allow_back"]);
        WebApp::addVar("internvalBetweenEvalueations", $this->internvalBetweenEvalueations);

        //WebApp::addVar("internvalBetweenEvalueations", 			"allow");	
        //WebApp::addVar("cameraToCaptureImage", 					"no");	


        WebApp::addVar("allow_reset", $this->configuration_rules["allow_reset"]);
        WebApp::addVar("timed_on", $this->configuration_rules["timed_on"]);
        WebApp::addVar("time_limit_sec", $this->configuration_rules["time_limit_sec"]);

        WebApp::addVar("user_nr_test_done", $this->user_nr_test_done);
        WebApp::addVar("user_test_certificate_exist", $this->user_test_certificate_exist);

        WebApp::addVar("cidFlow", $this->cidFlow);
        WebApp::addVar("player_flag", "" . $this->player_flag);

        WebApp::addVar("evaluation_state_flag", "" . $this->evaluation_state_flag);
        WebApp::addVar("evaluation_test_id", "" . $this->evaluation_test_id);
        WebApp::addVar("evaluation_test_state", "" . $this->evaluation_test_state);

        WebApp::addVar("PlayerOfQuestionsEditMode", "" . $this->PlayerOfQuestionsEditMode);
        WebApp::addVar("flow_model", "" . $this->flow_model["feedback_model"]);

        if (isset($this->EvaluationState["collector_prop"]["DC"]["descriptor_RQGuide"])) {
            $descriptor_Guide = $this->EvaluationState["collector_prop"]["DC"]["descriptor_RQGuide"];
        } else {
            if ($this->ci_type_configuration == "CQ") $descriptor_Guide = "{{_quiz_info}}";
            else                                        $descriptor_Guide = "{{_test_info}}";

        }

        WebApp::addVar("descriptor_Guide", $this->getPlaceholdersToReplace($descriptor_Guide));
        WebApp::addVar("descriptor_Guide_title", trim(strip_tags($descriptor_Guide)));

        if (isset($this->EvaluationState["collector_prop"]["DC"]["ci_content_to_display"])) {
            $fullContent = $this->EvaluationState["collector_prop"]["DC"]["ci_content_to_display"];
            $controllfullContent = trim(strip_tags($fullContent));
            if ($controllfullContent == "") {
                WebApp::addVar("content_Guide_exist", "no");


            } else {
                WebApp::addVar("content_Guide_exist", "yes");
                WebApp::addVar("content_Guide", $this->getPlaceholdersToReplace($fullContent));
            }

        }


        if (isset($this->tagsToBeReplaced) && count($this->tagsToBeReplaced) > 0) {
            reset($this->tagsToBeReplaced);
            while (list($key, $value) = each($this->tagsToBeReplaced)) {
                WebApp::addVar("_tag_$key", "$value");
            }
        }
        if (isset($this->evaluation_test_state) && count($this->evaluation_test_state) > 0) {
            while (list($key, $value) = each($this->evaluation_test_state)) {
                WebApp::addVar("state_$key", "$value");
            }
        }

        if (isset($this->actual_sequence_id)) WebApp::addVar("actual_sequence_id", "" . $this->actual_sequence_id);
        if (isset($this->actual_question_id)) WebApp::addVar("actual_question_id", "" . $this->actual_question_id);
        if (isset($this->question_state)) WebApp::addVar("question_state", "" . $this->question_state);

        if (isset($this->actual_sequence_id)) WebApp::addVar("actual_sequence_id", "" . $this->actual_sequence_id);


        $this->getTimerAverage();

        WebApp::addVar("mainEntry", $this->mainEntry);


    }

    function secondsToTime($seconds, $fullFormat = "yes")
    {
        // extract hours
        $obj = array(
            "h" => 0,
            "m" => 0,
            "s" => 0,
        );

        $hours = floor($seconds / (60 * 60));
        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);

        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);

        // return the final array
        $obj = array(
            "h" => (int)$hours,
            "m" => (int)$minutes,
            "s" => (int)$seconds,
        );

        if (strlen($obj["h"]) == 0) $obj["h"] = "00";
        if (strlen($obj["m"]) == 0) $obj["m"] = "00";
        if (strlen($obj["s"]) == 0) $obj["s"] = "00";

        if (strlen($obj["h"]) == 1) $obj["h"] = "0" . $obj["h"];
        if (strlen($obj["m"]) == 1) $obj["m"] = "0" . $obj["m"];
        if (strlen($obj["s"]) == 1) $obj["s"] = "0" . $obj["s"];

        if ($fullFormat == "yes") {

            //if ($obj["h"]!="00")			
            return $obj["h"] . ":" . $obj["m"] . ":" . $obj["s"];
            //else	return $obj["m"].":".$obj["s"];
        } else {
            return $obj["m"] . ":" . $obj["s"];
        }

    }


    function getRQConfiguration()
    {


        $getProp = "SELECT  content_id,
							
							coalesce(sequence_id,'') as sequence_id,
							coalesce(includeQuestionType,'') as includeQuestionType,
							
							coalesce(nr_of_question,'') as nr_of_question,
							coalesce(nr_of_question_custom,'') as nr_of_question_custom, 
							
							coalesce(collection_type,'') as collection_type,
							coalesce(nr_options_type,'') as nr_options_type,

							coalesce(nr_options,'') as nr_options,
							coalesce(assesment_timing,'') as assesment_timing,
							
							IF (fixedDateTime IS NOT NULL AND fixedDateTime !='' AND fixedDateTime!='0000-00-00',DATE_FORMAT(fixedDateTime,'%m/%d/%Y')  ,'') 
								as fixeddate,					
							IF (fixedDateTime IS NOT NULL AND fixedDateTime !='' AND fixedDateTime!='0000-00-00',DATE_FORMAT(fixedDateTime,'%H:%i')  ,'') 
								as fixedtime,
							
							IF (startdate IS NOT NULL AND startdate !='' AND startdate!='0000-00-00',DATE_FORMAT(startdate,'%m/%d/%Y')  ,'') 
								as startdate,
							IF (finishdate IS NOT NULL AND finishdate !='' AND finishdate!='0000-00-00',DATE_FORMAT(finishdate,'%m/%d/%Y')  ,'') 
								as finishdate,
							
							coalesce(allow_back,'') as allow_back,
							coalesce(allow_restart,'') as allow_restart,
							coalesce(allow_redo_cert,'') as allow_redo_cert,

							coalesce(nr_allowed_try,'') as nr_allowed_try,
							coalesce(per_points_to_pass,'') as per_points_to_pass, 
							coalesce(time_limit,'') as time_limit,
							coalesce(time_limit_minutes,'') as time_limit_minutes,  

							coalesce(allow_reset,'') as allow_reset,
							coalesce(collect_user_points,'') as collect_user_points,  

							coalesce(avoid_same_question,'') as avoid_same_question,
							coalesce(evaluation_rule,'') as evaluation_rule,  

							coalesce(correct_selected,'') as correct_selected,
							coalesce(correct_Nselected,'') as correct_Nselected,  
							coalesce(incorrect_selected,'') as incorrect_selected,
							coalesce(incorrect_Nselected,'') as incorrect_Nselected,  
							
							coalesce(max_point,'') as max_point,
							coalesce(min_point,'') as min_point,  
							coalesce(negative_points,'') as negative_points,
							coalesce(evaluation_time,'') as evaluation_time,  
							coalesce(evaluation_type,'') as evaluation_type, 
							coalesce(feedback_model,'') as feedback_model,
							coalesce(fullContentResult,'') as fullContentResult, 
							coalesce(question_template,'') as question_template,
							coalesce(questionTid,'') as questionTid,
							coalesce(result_template,'') as result_template,
							coalesce(resultTid,'') as resultTid,
							coalesce(user_report,'') as user_report,
							coalesce(user_certificate,'') as user_certificate,
							
							coalesce(cme_credits,'') as cme_credits,
							coalesce(nr_cme_credits,'') as nr_cme_credits,
							coalesce(nr_allowed_try_days,'') as nr_allowed_try_days,
							
							coalesce(traffic_light_feedback,'') as traffic_light_feedback,
							coalesce(traffic_light_red,'') as traffic_light_red,
							coalesce(traffic_light_orange,'') as traffic_light_orange,
							coalesce(traffic_light_green,'') as traffic_light_green,
							
							coalesce(capture_identification,'') as capture_identification,
							
							coalesce(after_face_identification,'') as after_face_identification,
							coalesce(capture_identification_force,'') as capture_identification_force,
							
							coalesce(certificate_template,'') as certificate_template,
							coalesce(certificateTid,'') as certificateTid,

							coalesce(certificate_evaluation,'') as certificate_evaluation,  
							coalesce(certificate_evaluation_days,'') as certificate_evaluation_days

					  FROM rq_data 
					  
					 WHERE content_id='" . $this->cidFlow . "' AND lng_id = '".$this->lngId."'  AND statusInfo='".$this->thisModeCode."'";
        $rs = WebApp::execQuery($getProp);

        if (!$rs->EOF()) {

            $this->EvaluationState["configuration"]["collection_type"] = $rs->Field("includeQuestionType");    //Quiz, Examination, Quiz & Examination	 							('quiz','examination','both')	
            $this->EvaluationState["configuration"]["nr_of_question"] = $rs->Field("nr_of_question");            //Tot. nr. of questions [5, 6, 7, 8, 9, 10, All , Costum] 			('5','6','7','8','9','10','all')
            $this->EvaluationState["configuration"]["nr_of_question_custom"] = $rs->Field("nr_of_question_custom");    //Tot. nr. of questions --Costum

            $this->EvaluationState["configuration"]["collection_order"] = $rs->Field("collection_type");        //Collection type [sequential, random, manually	]					 ('sequential','random','manually')
            $this->EvaluationState["configuration"]["nr_options_per_question"] = $rs->Field("nr_options_type");        //Nr. of opt. per question [take all, use question conf., Costum] 	 ('all','q_conf','costum')
            $this->EvaluationState["configuration"]["nr_options_costum"] = $rs->Field("nr_options");                //Nr. of opt. per question--Costum
            $this->EvaluationState["configuration"]["assesment_timing"] = $rs->Field("assesment_timing");        //Assessment timing [Any time,at a fixed date,date interval]		('any_time','fixed','interval')

            $this->EvaluationState["configuration"]["fixeddate"] = $rs->Field("fixeddate");                //Assessment timing--at a fixed date 
            $this->EvaluationState["configuration"]["fixedtime"] = $rs->Field("fixedtime");                //Assessment timing--at a fixed date 
            $this->EvaluationState["configuration"]["startdate"] = $rs->Field("startdate");                //Assessment timing--date interval
            $this->EvaluationState["configuration"]["finishdate"] = $rs->Field("finishdate");                //Assessment timing--date interval

            $this->EvaluationState["configuration"]["allow_back"] = $rs->Field("allow_back");                //Assessment Rules--Allow back [1,0]
            $this->EvaluationState["configuration"]["allow_restart"] = $rs->Field("allow_restart");            //Assessment Rules--Allow resume [1,0]	
            $this->EvaluationState["configuration"]["allow_redo_cert"] = $rs->Field("allow_redo_cert");        //Assessment Rules--Allow repetition after success [1,0]	
            $this->EvaluationState["configuration"]["nr_allowed_try"] = $rs->Field("nr_allowed_try");            //Assessment Rules--Number of allowed attempts
            $this->EvaluationState["configuration"]["nr_allowed_try_days"] = $rs->Field("nr_allowed_try_days");    //Assessment Rules--Number of allowed attempts--Number of allowed attempts after tot days

            $this->EvaluationState["configuration"]["per_points_to_pass"] = $rs->Field("per_points_to_pass");        //Assessment Rules--Percentage to pass test	

            $this->EvaluationState["configuration"]["time_limit"] = $rs->Field("time_limit");                //Is limited in time? [no, yes (use question conf.), yes (Costum)] 		('yes','no','custom') 
            $this->EvaluationState["configuration"]["time_limit_minutes"] = $rs->Field("time_limit_minutes");        //Is limited in time? --minutes
            $this->EvaluationState["configuration"]["time_limit_sec"] = $rs->Field("time_limit_minutes");        //Is limited in time? --seconds 

            $this->EvaluationState["configuration"]["allow_reset"] = $rs->Field("allow_reset");            //Allow reset [no, yes (all users), yes (write privileged users)]		('no','all_users','right_users') 
            $this->EvaluationState["configuration"]["collect_user_points"] = $rs->Field("collect_user_points");    //Collect user points [yes, no]											('yes','no')
            $this->EvaluationState["configuration"]["avoid_same_question"] = $rs->Field("avoid_same_question");    //Avoid the same question [yes, no]										('yes','no')

            $this->EvaluationState["configuration"]["evaluation_rule"] = $rs->Field("evaluation_rule");        //Evaluation points 													[default,defined ]

            $this->EvaluationState["configuration"]["correct_ifSelected"] = $rs->Field("correct_selected");        //Evaluation points --Costum Correct 	(selected)
            $this->EvaluationState["configuration"]["correct_ifNotselected"] = $rs->Field("correct_Nselected");        //Evaluation points --Costum Correct 	(not selected)
            $this->EvaluationState["configuration"]["incorrect_ifSelected"] = $rs->Field("incorrect_selected");        //Evaluation points --Costum Incorrect 	(selected)
            $this->EvaluationState["configuration"]["incorrect_ifNotselected"] = $rs->Field("incorrect_Nselected");    //Evaluation points --Costum Incorrect  (not selected)

            $this->EvaluationState["configuration"]["max_point"] = $rs->Field("max_point");                //Evaluation points -- Maximal points
            $this->EvaluationState["configuration"]["min_point"] = $rs->Field("min_point");                //Evaluation points -- Minimal points
            $this->EvaluationState["configuration"]["allow_negative_points"] = $rs->Field("negative_points");        //Evaluation points -- Allow negative points per questions[y,n]			('y','n')


            $this->EvaluationState["configuration"]["user_report"] = $rs->Field("user_report");            //Create user Report -- [yes,no]										('yes','no')
            $this->EvaluationState["configuration"]["user_certificate"] = $rs->Field("user_certificate");        //Create user Certificate --  [yes,no]									('yes','no')

            $this->EvaluationState["configuration"]["answer_evaluation"] = $rs->Field("user_report");            //Answers evaluation -- Immediate (automatically),Later (manually), Later (with deadline)	('automatic','later','deadline')			
            $this->EvaluationState["configuration"]["answer_evaluation_deadline"] = $rs->Field("evaluation_type");        //Answers evaluation -- Later (with deadline)	

            $this->EvaluationState["configuration"]["feedback_model"] = $rs->Field("feedback_model");            //Model of feedback -- [Within the question, After each question, In the end of the session]

            $fullContentResult = $rs->Field("fullContentResult");        //Model of feedback -- [Within the question, After each question, In the end of the session]
            $this->EvaluationState["configuration"]["fullContentResult"] = WebApp::parseContentDBtoHTML($fullContentResult);


            $this->EvaluationState["configuration"]["question_template"] = $rs->Field("question_template");        //Question template -- [use question template, costum template]
            $this->EvaluationState["configuration"]["result_template"] = $rs->Field("result_template");        //Result Template 	-- [use question template, costum template]

            $this->EvaluationState["configuration"]["questionTid"] = $rs->Field("questionTid");
            $this->EvaluationState["configuration"]["resultTid"] = $rs->Field("resultTid");


            $this->EvaluationState["configuration"]["cme_credits"] = $rs->Field("cme_credits");                    //CME Credits [yes,no]	
            $this->EvaluationState["configuration"]["nr_cme_credits"] = $rs->Field("nr_cme_credits");                    //CME Credits - No. of credits

            $this->mixedNeededData["ci"][$this->cidFlow]["DC"]["fullContentResult"] = $this->EvaluationState["configuration"]["fullContentResult"];

            $this->EvaluationState["configuration"]["capture_identification"] = $rs->Field("capture_identification");            //User identification [yes,no]

            $this->EvaluationState["configuration"]["traffic_light_feedback"] = $rs->Field("traffic_light_feedback");            //traffic light feedback [yes,no]				
            $this->EvaluationState["configuration"]["traffic_light_red"] = $rs->Field("traffic_light_red");                //traffic light feedback --	% red		
            $this->EvaluationState["configuration"]["traffic_light_orange"] = $rs->Field("traffic_light_orange");            //traffic light feedback --	% Amber
            $this->EvaluationState["configuration"]["traffic_light_green"] = $rs->Field("traffic_light_green");            //traffic light feedback --	% green


            $this->EvaluationState["configuration"]["after_face_identification"] = $rs->Field("after_face_identification");        //CME Credits 
            $this->EvaluationState["configuration"]["capture_identification_force"] = $rs->Field("capture_identification_force");    //User identification


            $this->EvaluationState["configuration"]["certificate_template"] = $rs->Field("certificate_template");            //Certificate Template [use question template,custom template]
            $this->EvaluationState["configuration"]["certificateTid"] = $rs->Field("certificateTid");                    //Certificate Template --select

            $this->EvaluationState["configuration"]["certificate_evaluation"] = $rs->Field("certificate_evaluation");            //CME Credits--[Immediate ,Later (with deadline)] 
            $this->EvaluationState["configuration"]["certificate_evaluation_days"] = $rs->Field("certificate_evaluation_days");    //CME Credits--Days after ass.


        }
    }

    function initUserProfile()
    {

        if ($this->userSystemID != 2) {

            $getCred = "SELECT 					   
							   coalesce(usr_diploma, '') as usr_diploma,
							   coalesce(FirstName, '') as FirstName,
							   coalesce(SecondName, '') as SecondName,						   
							   coalesce(usr_diploma, '') as usr_diploma	   

						FROM users
					  WHERE UserId = '" . $this->userSystemID . "'";
            $rs_con = WebApp::execQuery($getCred);
            IF (!$rs_con->EOF() AND mysql_errno() == 0) {

                $dataToReturn[$this->userSystemID]["usr_title"] = $rs_con->Field("usr_title");
                $dataToReturn[$this->userSystemID]["FirstName"] = $rs_con->Field("FirstName");
                $dataToReturn[$this->userSystemID]["SecondName"] = $rs_con->Field("SecondName");
                $dataToReturn[$this->userSystemID]["usr_diploma"] = $rs_con->Field("usr_diploma");
                $systemUserGeneralInfo["data"][0] = $dataToReturn[$this->userSystemID];
                $systemUserGeneralInfo["AllRecs"] = count($systemUserGeneralInfo["data"]);
                WebApp::addVar("systemUserGeneralInfoGrid", $systemUserGeneralInfo);
            }
        }
        //initUserProfile
    }


    function getMainCiOfNode()
    {
        global $session;
        $sql_con = "SELECT content_id, ci_type, group_concat(`read_write`) as rights
                      FROM content
                      JOIN profil_rights ON (       content.id_zeroNivel   = profil_rights.id_zeroNivel
												AND content.id_firstNivel  = profil_rights.id_firstNivel
												AND content.id_secondNivel = profil_rights.id_secondNivel
												AND content.id_thirdNivel  = profil_rights.id_thirdNivel
												AND content.id_fourthNivel = profil_rights.id_fourthNivel
												AND profil_rights.profil_id in (" . $this->tip . ")
											)                    
                    WHERE content.id_zeroNivel   = '" . $session->Vars["level_0"] . "' AND
                          content.id_firstNivel  = '" . $session->Vars["level_1"] . "' AND
                          content.id_secondNivel = '" . $session->Vars["level_2"] . "' AND
                          content.id_thirdNivel  = '" . $session->Vars["level_3"] . "' AND
                          content.id_fourthNivel = '" . $session->Vars["level_4"] . "' AND
                          orderContent   = '0'
                 GROUP BY content_id";

        $rs_con = WebApp::execQuery($sql_con);
        IF (!$rs_con->EOF() AND mysql_errno() == 0) {
            $id = $rs_con->Field("content_id");
            $this->appRelatedState["MainNodeCiID"] = $id;
            $this->appRelatedState["MainNodeCi"][$id]["ci_type"] = $rs_con->Field("ci_type");
            $rights = explode(",", $rs_con->Field("rights"));
            if (is_array($rights) && in_array("W", $rights))
                $this->appRelatedState["MainNodeCi"][$id]["read_write"] = "W";
            else    $this->appRelatedState["MainNodeCi"][$id]["read_write"] = "R";
        }
    }

}

?>