<?
function eUserRegister_onRender() {
	global $session,$event,$auth;
	


	$target_page_succes 		= "";
	$target_page_failure		= "";


	
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
    
        $objectPropRegister = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
        $template_id_sel=$objectPropRegister["template_id"];

        
          $target_page_succes 	= str_replace("k=","",$objectPropRegister["targeted_page_succes"]);
          $target_page_failure 	= str_replace("k=","",$objectPropRegister["targeted_page_failure"]);
            
            

        WebApp::addVar("Register_module_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/eUserRegister/eUserRegister.html\"/>");
        
        //selektohet template ----------------------------------------------------------------------------------------------------
         $sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
          $rs = WebApp::execQuery($sql_select);
          IF (!$rs->EOF() AND mysql_errno() == 0)
             {
                 $Register_module_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}eUserFunction/eUserRegister/'.$rs->Field("template_box").'" />';                
                
                WebApp::addVar("Register_module_TEMPLATE", $Register_module_TEMPLATE);
             }
        //------------------------------------------------------------------------------------------------------------------------
    //    echo $rs->Field("template_box");

        $tp_page_termsCond=$objectPropRegister["targeted_page_termsCond"];
        WebApp::addVar("tp_page_termsCond_isset","no");
        if ($tp_page_termsCond!="") {
            $tp_page_termsCond 	  = str_ireplace("k=","",$tp_page_termsCond); 	
            WebApp::addVar("tp_page_termsCond_isset","yes");
            WebApp::addVar("tp_page_termsCond_koord",$tp_page_termsCond);
        } 
         
        
    }
    //eShopLogin $auth	
	if (isset($_REQUEST["apprcss"]) && $_REQUEST["apprcss"]=="eUserRegister") {

		/*echo $session->Vars["idstemp"].":idstemp<br>";
		echo "$auth:auth<br>";
		echo "$target_page_succes:target_page_succes<br>";*/
			
		if ($auth == "true") {
			
			WebApp::addVar("isset_page_succes","no");
			WebApp::addVar("loginProccess","ok");
			
			if ($target_page_succes!="") {
				$workingCi = new CiManagerFe($target_page_succes);
				$propCi = $workingCi->getDocContentProperties();	
				
				if (isset($propCi["ew_content"]) && $propCi["ew_content"]!="") {				
					WebApp::addVar("isset_page_succes","yes");	
					WebApp::addVar("page_succes",$propCi["ew_content"]);	
				}	
			}
			
		} else if ($auth == "false") {		
			
			WebApp::addVar("isset_page_failure","no");	
			WebApp::addVar("loginProccess","failed");
			
			if ($target_page_failure!="") {
				$workingCi = new CiManagerFe($target_page_failure);
				$propCi = $workingCi->getDocContentProperties();	
				
				if (isset($propCi["ew_content"]) && $propCi["ew_content"]!="") {
				
					WebApp::addVar("isset_page_failure","yes");	
					WebApp::addVar("page_failure",$propCi["ew_content"]);	
				}
				
				
			}
			
		}	        
        
		WebApp::addVar("Register_module_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/eUserRegister/registerAjax.html\"/>");			 
	}    
                

    WebApp::addVar("idstempNl",$session->Vars["idstemp"]);
  	
 }
 ?>