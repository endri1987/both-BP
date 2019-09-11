<?
function SelectWorkgroupModule_onRender() {
	global $session, $global_cache_dynamic,$cacheDyn, $sessUserObj;	


	WebApp::addVar("typeOfUser", "web");
	if (isset($sessUserObj)) {
		WebApp::addVar("typeOfUser", $sessUserObj->typeOfUser);
	}

	$objectPropWorkgroup = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
	

	if (isset($objectPropWorkgroup["template_id"]) && $objectPropWorkgroup["template_id"]>0)	

		$template_id_sel=$objectPropWorkgroup["template_id"];
	elseif (isset($objectPropWorkgroup["templateID"]) && $objectPropWorkgroup["templateID"]>0)	
		$template_id_sel=$objectPropWorkgroup["templateID"];

	 $lng_id = SUBSTR($session->Vars["lang"], -1);

	

	$Grid_Workgroup = array(
		"data" 			=>  array(),
		"AllRecs" 		=> "0"
	);

    //KUSHTI PER NYJEN AKTIVE, JOAKTIVE --------------------------------------------------------------------------------------
      IF ($session->Vars["thisMode"] == "_new")
         {
          $kusht_aktiv_joaktiv = "";
         }
      ELSE
         {
          $kusht_aktiv_joaktiv  = " AND n.active".$session->Vars["lang"]."     != 1 ";
          $kusht_aktiv_joaktiv = " AND c.published".$session->Vars["lang"]." = 'Y' AND n.id_zeroNivel >= 0 ";
         }
         
         
         //	AND n.id_zeroNivel >= 0  

	WebApp::addVar("kusht_aktiv_joaktiv", $kusht_aktiv_joaktiv);


	$rs = WebApp::openRS("Zone_Menu");
/*	
if ($session->Vars["uni"] == "20151112115033192168120871755394") {	
	echo "<textarea>";
	print_r($rs);
	echo "</textarea>";	
}	
	*/
	
	
	$style_name_n1      = "nav_n1";
	$style_name_n1_sel  = "nav_n1_sel";
	$style_name_n1_cur  = "nav_n1 nav_n1_sel";
	$nr  = 0;
	
	while (!$rs->Eof()) {

		$ID0 = $rs->Field("ID0");
		$description = $rs->Field("description");

		if($session->Vars["level_0"]==$ID0)
			$Grid_Workgroup["data"][$nr]["style_class"]      = $style_name_n1_cur ;		 
		else
			$Grid_Workgroup["data"][$nr]["style_class"]      = $style_name_n1;


		$Grid_Workgroup["data"][$nr]["LinkToZone"] = "javascript:GoTo('thisPage?event=none.ch_state(k=$ID0,0,0,0,0)')";

		 $con_id          = $rs->Field("con_id");
		 $ci_description  = $rs->Field("ci_description");
		 $title           = $rs->Field("title");
		 $filename        = $rs->Field("filename");
		 $with_https      = $rs->Field("with_https");

		IF ($global_cache_dynamic == "Y") {
			$Grid_Workgroup["data"][$nr]["LinkToZone"] = $cacheDyn->get_CiTitleToUrl($con_id, 	$lng_id, 	$title, 	$filename, "", $with_https);
				  //get_CiTitleToUrl		($content_id, $lng_id=1, $title="", $filename="", $koord_level_node_param="",$with_https="n")	

		}




		$Grid_Workgroup["data"][$nr]["description"]  = $rs->Field("description");
		$Grid_Workgroup["data"][$nr]["ID0"]      	 = $ID0;
		$nr++;
		$rs->MoveNext();

	}
$Grid_Workgroup["AllRecs"] = COUNT($Grid_Workgroup["data"]);
WebApp::addVar("Grid_Workgroup", $Grid_Workgroup);
 
 $WorkgroupModule_TEMPLATE = "";

        //selektohet template ----------------------------------------------------------------------------------------------------
          $sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
          $rs = WebApp::execQuery($sql_select);
          IF (!$rs->EOF() AND mysql_errno() == 0) {
          	
          //	echo $rs->Field("template_box")."-";
          	$WorkgroupModule_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}SelectWorkgroupModule/'.$rs->Field("template_box").'" />';
          } else {
          	$WorkgroupModule_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}SelectWorkgroupModule/MyCMESwitch.html" />';
          }
        //------------------------------------------------------------------------------------------------------------------------
        

  WebApp::addVar("WorkgroupModule_TEMPLATE", $WorkgroupModule_TEMPLATE);


}
?>