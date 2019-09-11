<?
function FrontendRolesAndUsers_eventHandler($event){
	global $session,$event;
	extract($event->args);
}

function FrontendRolesAndUsers_onRender() 
{
	global $session;

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		//$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);

		$templateTypeSelected = 'default_template.html';
		if (isset($prop_arr["templateID"]) && $prop_arr["templateID"] != "") {

			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateID"]."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF()) {
				$templateTypeSelected = $rs->Field("template_box");
			}
			//------------------------------------------------------------------------------------------------------------------------
		}


		WebApp::addVar("RolesAndUsersNEM_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}FrontendRolesAndUsers/".$templateTypeSelected."\"/>");

		WebApp::addVar("slogan_title", $prop_arr['headline']);

		require_once(EASY_PATH."bo_toolsset/organogram/organogram.class.php");
		$toolObj = new organogram();

		$usersDetailsGrid=array("data" => array(), "AllRecs" => "0");
		$rolesToSelect=array();

		if(isset($prop_arr["refParentRoles"]) && is_array($prop_arr["refParentRoles"]) && count($prop_arr["refParentRoles"])>0){
			foreach($prop_arr["refParentRoles"] as $key){
				$rolesToSelect[$key] = $key;
			}
		}
		if(isset($prop_arr["rolesToSelect"]) && is_array($prop_arr["rolesToSelect"]) &&count($prop_arr["rolesToSelect"])>0){
			foreach($prop_arr["rolesToSelect"] as $key){
				$rolesToSelect[$key] = $key;				
			}
		}

		$params=array();
		$params["roles"] = $rolesToSelect;

	    $usersData = $toolObj->getListOfUsers($params);

	    $usersDetailsGrid["AllRecs"]   = count($usersData["data"]);
	    $usersDetailsGrid["data"]    = $usersData["data"];
	   	WebApp::addVar("usersListGrid",$usersDetailsGrid);

		if(isset($prop_arr['display_prop'])){
			foreach($prop_arr['display_prop'] as $val){
				switch($val){
					case '1':
						WebApp::addVar("show_user_id", "yes");
						break;
					case '2':
						WebApp::addVar("show_user_username", "yes");
						break;
					case '3':
						WebApp::addVar("show_user_first_name", "yes");
						break;
					case '4':
						WebApp::addVar("show_user_second_name", "yes");
						break;
					case '5':
						WebApp::addVar("show_user_email", "yes");
						break;	
					case '6':
						WebApp::addVar("show_user_status", "yes");
						break;
					case '7':
						WebApp::addVar("show_user_roles", "yes");
						break;
				}
			}
		}
	}
}



