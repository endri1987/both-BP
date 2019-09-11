<?php

		$auth = 'false';
		$user_rights     = 0;
		$user_login_id   = 2;
		$stateAplication = "init";
		$stepAplication  = "0";

		if (isset($_REQUEST["uni"]) && $_REQUEST["uni"]!=""  && isset($_REQUEST["tool"]) &&  $_REQUEST["tool"]!="") {
			
			$stepAplication = "3 KETU ESHTE VALIDIMI QE BEHET NGA TOOLSET";
			$stateAplication = getSessionFromTransition($_REQUEST["uni"]);
			$user_rights = '1';
			
			if (count($_REQUEST)>3 && isset($_REQUEST["tool"])) {
				$tools_id_validation = $_REQUEST["tool"];
			}
			
		} else {
			exit;
		}

		//echo "#ECHO##ketu thirret nga indeksi i toolseve";

		if (isset($tools_id_validation) && $tools_id_validation>0) {
			$tip_case="";

			$query1 = "SELECT count(1) as user_rights FROM users, profil_tools, user_profile
					 WHERE  users.UserId = user_profile.UserId AND user_profile.profil_id=profil_tools.profil_id AND
						   users.userid = ".$session->Vars["ses_userid"]." AND profil_tools.tools_id in (".$tools_id_validation.") ".$tip_case."";
			$rsquery1 = WebApp::execQuery($query1);
			$_rights  = $rsquery1->Field("user_rights");
			if ($_rights==0)
				$user_rights =0;
			else {
				$user_rights =1;
				$session->Vars["thisMode"] = "_new";
			}
		}
				
		

	if($user_rights) {
		$i = '1';
		$auth = 'true';
	} else {
		$a = '1';
		$auth = 'false';
	}
	if ($auth == 'false') {
		exit;
	}
	





function collectUProfils($tip,$prof_arr) {

	$stprofil = "SELECT profil_id  FROM profil where profil_parentId=".$tip." and profil_parentId!=profil_id ORDER BY profil_id";
	$rs = WebApp::execQuery($stprofil);
	while (!$rs->EOF())
	{
		$profil_id=$rs->Field("profil_id");
		if (!in_array($profil_id,$prof_arr))
			$prof_arr[]=$profil_id;
		$prof_arr=collectUProfils($profil_id,$prof_arr);
		$rs->MoveNext();
	}
	return $prof_arr;
}




function getSessionFromTransition($uni)
{
	global $session;
	$sql="SELECT ses_userid,lng,sessionVars from transition WHERE ID_S='".$uni."' and tool_id in (0,-1) order by insertdate desc limit 0,1";
	$rs = WebApp::execQuery($sql);
	
	if(!$rs->EOF()) {
	 
		$user_id  			= $rs->Field("ses_userid");
	 	$fe_lng 			= $rs->Field("lng");
	 	$trans_sessionArr	= $rs->Field("sessionVars");
		
		$trans_sessionVars      = unserialize(base64_decode($trans_sessionArr));
		$_sessVars=$trans_sessionVars;
		
		if ($_sessVars["ses_userid"] == $user_id) {
			while (list($var_name,$var_value) = each($_sessVars)) {
					$session->AddVar($var_name, $var_value);
			}	
			$trans_BOlang 		= "en";
			$session->Vars["lng"] = $trans_BOlang;
			$session->Vars["uni"] = $uni;
		}
		
		return "other";
	} else {
		exit; 			
	}
}


?>