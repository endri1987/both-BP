<?php
require_once(INC_PATH.'personalization.functionality.class.php');
require_once(INCLUDE_AJAX_PATH . "/CiManagerFe.class.php");


function CommunityModule_onRender() {
	

	global $session,$event,$global_cache_dynamic,$cacheDyn, $mob_web, $atributeMainCi, $sessUserObj;
	

		/*$CID = WebApp::getVar("CID");	
		if (isset($CID) && ($CID!= 'undefined')){
		
		}else {*/
			$CID =$session->Vars["contentId"];
	//	}	

		$obj = new personalization("empty");
		$obj->initNemConfiguration($session->Vars["idstemp"]);

		$obj->CMID = $CID;
		$obj->getCMconfiguration();

		
		$obj->constructInterface="";
		if ($obj->MainDocType=="LI" || $obj->MainDocType=="EL") {
			//shto koment by default, perndryshe lexo configurimin e ci, nese komentet jane bere enable apo jo
			$obj->constructInterface="comments";
		} elseif ($obj->MainDocType=="CM" || $obj->MainDocType=="CC" || $obj->MainDocType=="CF" || $obj->MainDocType=="CT" || $obj->MainDocType=="CP") {
			//shto koment by default, perndryshe lexo configurimin e ci, nese komentet jane bere enable apo jo
			//CC -> community module, CF -> Lecture Discussion Item, CT -> Forum Topic Item, CP -> Topic Post Item
			$obj->constructInterface="forum";
		} else {	
		}

		$main_template_cm = "";

		if ($obj->constructInterface!="") {
		
			$obj->getCMDataList();


			
			
			$main_template_cm = "<Include SRC=\"{{NEMODULES_PATH}}CommunityModule/".$obj->listTemplate."\" />";
			
			WebApp::addVar("CMselfDocID",		"".$obj->CMmainDocID."");
			WebApp::addVar("CMmainDocID",		"".$obj->CMmainDocID."");
			WebApp::addVar("CMparentDocID",		"".$obj->CMparentDocID."");
			WebApp::addVar("CMType",			"".$obj->CMType."");
			WebApp::addVar("CMID",				"".$obj->CMID."");
			WebApp::addVar("CMUSERID",			"".$obj->CMUSERID."");
			WebApp::addVar("CMtitle",			"".$obj->CMtitle."");
			WebApp::addVar("CMdescription",		"".$obj->CMdescription."");

			
			IF ($global_cache_dynamic == "Y") {
					$hrefToNav = $cacheDyn->get_CiTitleToUrl($CID, $obj->lngId);
					WebApp::addVar("hrefToNav","$hrefToNav");
					$nav_template_cm = "<Include SRC=\"{{NEMODULES_PATH}}CommunityModule/nav_module_cached.html\" />";
					
			} else {
					$nav_template_cm = "<Include SRC=\"{{NEMODULES_PATH}}CommunityModule/nav_module_dynamic.html\" />";
					
			}
			WebApp::addVar("nav_template_cm",$nav_template_cm);
		


			if ($obj->CMType>1) {
				 $workingCi = new CiManagerFe($CID, $session->Vars["lang"]);
				 $workingCi->parseDocumentToDisplayPreviewMode();
/*echo "workingCi---<textarea>";
print_r($workingCi);
echo "</textarea>";*/

				if (isset($workingCi->properties_structured["TiContent"]) && count($workingCi->properties_structured["TiContent"]) >0) {
					WebApp::addVar("cm_content","");	
                }
				if (isset($workingCi->properties_structured["DC"]["abstractToDisplay"]) && $workingCi->properties_structured["DC"]["abstractToDisplay"] !="") {
					WebApp::addVar("abstractToDisplay",$workingCi->properties_structured["DC"]["abstractToDisplay"]);	
                }
				
				/*if (isset($atributeMainCi) && count($atributeMainCi)>0) {
					WebApp::addVar("cm_content",$atributeMainCi["ew_content"]);
					WebApp::addVar("cm_actualNodeDescription",$atributeMainCi["actualNodeDescription"]);
				}*/
			}
		}
		
		WebApp::addVar("main_template_cm",$main_template_cm);	
		
		WebApp::addVar("k",$session->Vars["contentId"]);	
		WebApp::addVar("kc",$session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"]);	
		
/*		
//cm_list_detail_forum.html	

	echo "<textarea>";

	print_r($obj);	
	
	echo "</textarea>";
echo $obj->constructInterface.":constructInterface<br>";
echo $obj->constructInterface.":constructInterface<br>";
echo $obj->CMType.":CMType<br>";			
echo $obj->ci_mi_id.":ci_mi_id<br>";
echo $obj->listTemplate.":listTemplate<br>";	
if ($session->Vars["uni"]=="0150925092338192168120422472945") {
}	
	*/	

}

	
?>