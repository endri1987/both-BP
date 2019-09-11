<?
function eUserNewsletterRegister_onRender()
{
	global $session;

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);
		$templateTypeSelected = 'default_template.html';
		if (isset($prop_arr["templateType"]) && $prop_arr["templateType"] != "") {
			
			//selektohet template --------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$prop_arr["templateType"]."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF()) {
				$templateTypeSelected = $rs->Field("template_box");
			}
			//----------------------------------------------------------------------------------------------------------
		}

		WebApp::addVar("NEM_TEMPLATE","<Include SRC=\"{{NEMODULES_PATH}}eUserFunction/eUserNewsletterRegister/network/".$templateTypeSelected."\"/>");
		if(count($prop_arr) > 0)
			foreach($prop_arr as $k => $v)
				if(!is_array($v))
					WebApp::addVar($k, $v);
		

		//get salutation grid
		require_once(INCLUDE_KW_AJAX_PATH.'KwManager.Base.class.php');
		$KwObj = new KwManagerFamily($session->Vars["ses_userid"],$session->Vars["lang"]);
		$FamilyDataSourceArray = $KwObj->getSpecialFamilies("'USRTI'");
		if (count($FamilyDataSourceArray)>0) {
			foreach($FamilyDataSourceArray as $family_type_id => $infoGrArr){
				if ($family_type_id == 1 && count($infoGrArr) > 0) {
					foreach ($infoGrArr as $specialization => $dataFamily) {
						foreach ($dataFamily as $idFamily => $familyName) {

							$KwObjItemSo = $KwObj->setKwObjItem($family_type_id);
							$KwObjItemSo->setTreePositionProperties("0,".$idFamily);
							$dataItem = $KwObjItemSo->generateExtendedList();
							$GrSourcePredefined = array("data"=>array(),"AllRecs"=>"0"); $indSo = 0;
							if (count(dataItem)>0) {
								foreach ($dataItem as $idkw => $dt) {
									$GrSourcePredefined["data"][$indSo]["source_fid"] = $idFamily; // was familyid
									$GrSourcePredefined["data"][$indSo]["source_kid"] = $idkw;
									$GrSourcePredefined["data"][$indSo]["source_sel"] = "";

									$GrSourcePredefined["data"][$indSo]["source_code"] = $dt["descriptionCode"];
									$GrSourcePredefined["data"][$indSo++]["source_label"] = $dt["description"];
									IF (count($GrSourcePredefined["data"])>0)
										$GrSourcePredefined["AllRecs"] = count($GrSourcePredefined["data"]);
									WebApp::addVar("SalutationGrid",$GrSourcePredefined);
								}
							}
						}
					}
				}
			}
		}
		//get salutation grid
	}
}

