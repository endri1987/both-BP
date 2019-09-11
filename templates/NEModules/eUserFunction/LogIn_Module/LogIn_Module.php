<?
function LogIn_Module_onRender() {
	global $session,$auth;
 
	WebApp::addVar("idstempLogin","");
	WebApp::addVar("forgot_password_html","");
	
	WebApp::addVar("include_step","");
	WebApp::addVar("loginProccess","init");	

	$target_page_succes_ci	= "";
	$target_page_failure_ci	= "";
	

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));



		if (isset($prop_arr["show_targeted_page_succes"]) && $prop_arr["show_targeted_page_succes"]=="show"
			&& isset($prop_arr["targeted_page_succes"]) && $prop_arr["targeted_page_succes"]!="") {
				$target_page_succes_ci = str_replace("k=","",$prop_arr["targeted_page_succes"]);
				WebApp::addVar("issetTargetPageSucces","yes");
				WebApp::addVar("gotToTargetPageSucces",";crd=$target_page_succes_ci");				
		}
		if (isset($prop_arr["show_targeted_page_failure"]) && $prop_arr["show_targeted_page_failure"]=="show"
			&& isset($prop_arr["targeted_page_failure"]) && $prop_arr["targeted_page_failure"]!="") {
				$target_page_failure_ci = str_replace("k=","",$prop_arr["targeted_page_failure"]);
				WebApp::addVar("issetTargetPageError","yes");
				WebApp::addVar("gotToTargetPageError",";crd=$target_page_failure_ci");				
		}
		WebApp::addVar("forgot_password_isset","no");
		if (isset($prop_arr["show_forgot_password"]) && $prop_arr["show_forgot_password"]=="show"
			&& isset($prop_arr["forgot_password"]) && $prop_arr["forgot_password"]!="") {
				$forgot_password_ci = str_replace("k=","",$prop_arr["forgot_password"]);
				$forgot_password_label = "{{_forgot_pass}}";
				if (isset($prop_arr["lbl_forgot_password"]) && $prop_arr["lbl_forgot_password"]!="") 
					$forgot_password_label = $prop_arr["lbl_forgot_password"];
				
				WebApp::addVar("forgot_password_isset","yes");
				WebApp::addVar("forgot_password_label","$forgot_password_label");				
				WebApp::addVar("forgot_password_ci","$forgot_password_ci");				
		}
		

		WebApp::addVar("register_target_isset","no");
		if (isset($prop_arr["show_register_target"]) && $prop_arr["show_register_target"]=="show"
			&& isset($prop_arr["register_target"]) && $prop_arr["register_target"]!="") {
				$register_target_ci = str_replace("k=","",$prop_arr["register_target"]);
				$register_target_label = "{{_register}}";
				if (isset($prop_arr["lbl_register_target"]) && $prop_arr["lbl_register_target"]!="") 
					$register_target_label = $prop_arr["lbl_register_target"];
				
				WebApp::addVar("register_target_isset","yes");
				WebApp::addVar("register_target_label","$register_target_label");				
				WebApp::addVar("register_target_ci","$register_target_ci");				
		}	
		
		WebApp::addVar("show_protected_ares_message","yes");
		WebApp::addVar("protected_area_message","{{_protected_area_message}}");
		if (isset($prop_arr["show_protected_ares_message"]) && $prop_arr["show_protected_ares_message"]=="hide") {		
			WebApp::addVar("show_protected_ares_message","no");
		}
		$error_login_message = "{{_error_login_message}}";
		if (isset($prop_arr["error_login_message"]) && $prop_arr["error_login_message"]!="") 
			$error_login_message = $prop_arr["error_login_message"];
		WebApp::addVar("error_login_message","$error_login_message");		
		
		$succes_login_message = "{{_succes_login_message}}";
		if (isset($prop_arr["succes_login_message"]) && $prop_arr["succes_login_message"]!="") 
			$succes_login_message = $prop_arr["succes_login_message"];
		WebApp::addVar("succes_login_message","$succes_login_message");				
		
		
		$login_headline = "{{_login_headline}}";
		if (isset($prop_arr["login_headline"]) && $prop_arr["login_headline"]!="") 
			$login_headline = $prop_arr["login_headline"];
		WebApp::addVar("login_headline","$login_headline");				
		
		$login_label = "{{_Login}}";
		if (isset($prop_arr["login_label"]) && $prop_arr["login_label"]!="") 
			$login_label = $prop_arr["login_label"];
		WebApp::addVar("login_label","$login_label");	
		
		$sign_in_label = "{{_sign_in}}";
		if (isset($prop_arr["sign_in_label"]) && $prop_arr["sign_in_label"]!="") 
			$sign_in_label = $prop_arr["sign_in_label"];
		WebApp::addVar("sign_in_label","$sign_in_label");	


		$usrname_label = "{{_usernamelogin}}";
		if (isset($prop_arr["usrname"]) && $prop_arr["usrname"]!="") 
			$usrname_label = $prop_arr["usrname"];
		WebApp::addVar("usrname_label","$usrname_label");				
		
		$password_label = "{{_passwordlogin}}";
		if (isset($prop_arr["password"]) && $prop_arr["password"]!="") 
			$password_label = $prop_arr["password"];
		WebApp::addVar("password_label","$password_label");		
		
		$fill_username_label = "{{_fill_username_data}}";
		if (isset($prop_arr["fill_username_label"]) && $prop_arr["fill_username_label"]!="") 
			$fill_username_label = $prop_arr["fill_username_label"];
		WebApp::addVar("fill_username_label","$fill_username_label");	

		$fill_password_label = "{{_fill_password_data}}";
		if (isset($prop_arr["fill_password_label"]) && $prop_arr["fill_password_label"]!="") 
			$fill_password_label = $prop_arr["fill_password_label"];
		WebApp::addVar("fill_password_label","$fill_password_label");
		
	
		
		
		$templateOfLoginDefault = "LoginFullPage.html";
		if (isset($prop_arr["templateID"]) && $prop_arr["templateID"] != "") {
		   //selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateID"]."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF()) {
				$templateOfLoginDefault = $rs->Field("template_box");
			}
			//------------------------------------------------------------------------------------------------------------------------
		}

		if (isset($_REQUEST["apprcss"]) && $_REQUEST["apprcss"]=="eUserLogin") {
			if ($auth == "true") {
				WebApp::addVar("loginProccess","ok");
			} else if ($auth == "false") {
				WebApp::addVar("loginProccess","failed");
			}	
			WebApp::addVar("include_step","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/LogIn_Module/loginAjax.html\"/>");
		
		} elseif (isset($_REQUEST["apprcss"]) && $_REQUEST["apprcss"]=="LoginUserModule") {

			WebApp::addVar("include_step","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/LogIn_Module/loginAjax.html\"/>");		 
		
		} else {	
			WebApp::addVar("include_step","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/LogIn_Module/".$templateOfLoginDefault."\"/>");
		}
	} 
	
	
	
	
	

	
	
	WebApp::addVar("idstempLogin",$session->Vars["idstemp"]);
}
?>