<?
function eUserForgotPass_onRender() {
	global $session,$event;

        WebApp::addVar("idstemp","");
        if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
            WebApp::addVar("idstempNl",$session->Vars["idstemp"]);

            $prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
            $templateTypeSelected = 'default_template.html';
            if (isset($prop_arr["templateID"]) && $prop_arr["templateID"] != "") {

                //selektohet template --------------------------------------------------------------------------------------
                $sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateID"]."'";
                $rs = WebApp::execQuery($sql_select);
                IF (!$rs->EOF()) {
                    $templateTypeSelected = $rs->Field("template_box");
                }
                //----------------------------------------------------------------------------------------------------------
            }
            WebApp::addVar("NEM_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/eUserForgotPass/".$templateTypeSelected."\"/>");



            $headline = "{{_headline}}";
            if (isset($confProp["headline"]) && $confProp["headline"]!="")
                $headline = $confProp["headline"];
            WebApp::addVar("headline","$headline");

            $forgot_password_desc = "";
            if (isset($confProp["forgot_password_desc"]) && $confProp["forgot_password_desc"]!="")
                $forgot_password_desc = $confProp["forgot_password_desc"];

            if ($forgot_password_desc!="") {
                WebApp::addVar("isset_forgot_password_desc","yes");
                WebApp::addVar("forgot_password_desc","$forgot_password_desc");
            }


            $button_submit = "{{_button_submit}}";
            if (isset($confProp["button_submit"]) && $confProp["button_submit"]!="")
                $button_submit = $confProp["button_submit"];
            WebApp::addVar("button_submit","$button_submit");

            $succes_send_token = "{{_succes_send_token}}";
            if (isset($confProp["succes_send_token"]) && $confProp["succes_send_token"]!="")
                $succes_send_token = $confProp["succes_send_token"];
            WebApp::addVar("succes_send_token","$succes_send_token");

            $error_send_token = "{{_error_send_token}}";
            if (isset($confProp["error_send_token"]) && $confProp["error_send_token"]!="")
                $error_send_token = $confProp["error_send_token"];
            WebApp::addVar("error_send_token","$error_send_token");

            $error_email_not_found = "{{error_email_not_found}}";
            if (isset($confProp["error_email_not_found"]) && $confProp["error_email_not_found"]!="")
                $error_email_not_found = $confProp["error_email_not_found"];
            WebApp::addVar("error_email_not_found","$error_email_not_found");




            $button_cancel = "{{_button_cancel}}";
            if (isset($confProp["button_cancel"]) && $confProp["button_cancel"]!="")
                $button_cancel = $confProp["button_cancel"];
            WebApp::addVar("button_cancel","$button_cancel");

            $email_label = "{{_email_label}}";
            if (isset($confProp["email_label"]) && $confProp["email_label"]!="")
                $email_label = $confProp["email_label"];
            WebApp::addVar("email_label","$email_label");

            $empty_email_address = "{{_empty_email_address}}";
            if (isset($confProp["empty_email_address"]) && $confProp["empty_email_address"]!="")
                $empty_email_address = $confProp["empty_email_address"];
            WebApp::addVar("empty_email_address","$empty_email_address");

            $mail_not_mach = "{{_mail_not_mach}}";
            if (isset($confProp["mail_not_mach"]) && $confProp["mail_not_mach"]!="")
                $mail_not_mach = $confProp["mail_not_mach"];
            WebApp::addVar("mail_not_mach","$mail_not_mach");

            $valid_email_address = "{{_valid_email_address}}";
            if (isset($confProp["valid_email_address"]) && $confProp["valid_email_address"]!="")
                $valid_email_address = $confProp["valid_email_address"];
            WebApp::addVar("valid_email_address","$valid_email_address");

            WebApp::addVar("show_tp_back_to_login","no");
            if (isset($confProp["show_tp_back_to_login"]) && $confProp["show_tp_back_to_login"]=="show") {

                $back_to_login_label = "{{_back_to_login_label}}";

                if (isset($confProp["back_to_login_label"]) && $confProp["back_to_login_label"]!="")
                    $back_to_login_label = $confProp["back_to_login_label"];

                WebApp::addVar("show_tp_back_to_login","ajax");
                WebApp::addVar("back_to_login_label","$back_to_login_label");

                if (isset($confProp["tp_back_to_login"]) && $confProp["tp_back_to_login"]!="") {
                    $tp_back_to_login_ci = str_replace("k=","",$confProp["tp_back_to_login"]);
                    WebApp::addVar("show_tp_back_to_login","yes");
                    WebApp::addVar("back_to_login_label","$back_to_login_label");
                    WebApp::addVar("tp_back_to_login_ci","$tp_back_to_login_ci");
                }
            }

            WebApp::addVar("tp_succes_isset","no");
            WebApp::addVar("tp_succes_koord","");
            if (isset($confProp["show_tp_succes"]) && $confProp["show_tp_succes"]=="show") {
                if(isset($confProp["tp_succes"]) && $confProp["tp_succes"]!="")
                    $tp_succes 	= str_replace("k=","",$confProp["tp_succes"]);
                if ($tp_succes!="") {
                    WebApp::addVar("tp_succes_isset","yes");
                    WebApp::addVar("tp_succes_koord","k=".$tp_succes);
                }
            }

            WebApp::addVar("tp_failure_isset","no");
            WebApp::addVar("tp_failure_koord","");
            $tp_failure 	= "";
            if (isset($confProp["show_tp_failure"]) && $confProp["show_tp_failure"]=="show") {
                if(isset($confProp["tp_failure"]) && $confProp["tp_failure"]!="")
                    $tp_failure 	= str_replace("k=","",$confProp["tp_failure"]);
                if ($tp_failure!="") {
                    WebApp::addVar("tp_failure_isset","yes");
                    WebApp::addVar("tp_failure_koord","k=".$tp_failure);
                }
            }



        }
		
  	
 }
 ?>