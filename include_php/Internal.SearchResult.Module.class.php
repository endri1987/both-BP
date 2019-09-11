<?
global $node_nedded_data;
//require_once(INCLUDE_AJAX_PATH.'NodeManager.class.php');
//require_once(INCLUDE_KW_AJAX_PATH.'KwManager.Base.class.php');
require_once(INCLUDE_AJAX_PATH.'NodeManager.class.php');
require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");
require_once(INC_PATH.'personalization.functionality.class.php');

class InternalSearchResultParams
{
	
	var $param = array();
	var $recPages	= 20;
	var $NrPage	= 1;
	var $bsnid = 87;

	var $nsfkid = "";
	var $fkid = "";
	var $termSearch = "";
	var $termFromSearchModule = "";
	var $dataMoment = "";
	var $sers = "";
	var $sts = "";
	var $sql_type = "default";	//union, select
	
	var $restrict_search_to_nodes = "5";
	var $concat_levels_with = "/";
	var $error_code = "5";
	var $error_code_description	= array(1=>"FoundResults",2=>"ResultsEmtpy",3=>"TermSearchEmpty",4=>"InvalidTerm",5=>"SomeErrorOccurred");
	/****************************************************************************************************************
	*****	$this->error_code = 1; //jemi ok, kemi gjetur rezultatet
	*****	$this->error_code = 2; //nuk ka rezultate per termin e dhene te kerkimit
	*****	$this->error_code = 3; //Nuk ka term kerkimi te definuar
	*****	$this->error_code = 4; //mbas procesimit te termit te kerkimit nuk na ngeli asnje term per te kerkuar
	****************************************************************************************************************/



	function InternalSearchResultParams($sql_type="default", $guess_by="no", $restrict_search_to_nodes="", $allNodesToSearch=array())
	{
		global $event,$session,$global_cache_dynamic;


		if (isset($guess_by) && $guess_by=="yes") {
				$this->guess_by = "yes";
				$this->error_code = 1;
		} elseif (isset($guess_by) && $guess_by=="type_categorization") {
				$this->guess_by = "type_categorization";
				$this->error_code = 1;
		} else	$this->guess_by = "no";



		if (isset($session->Vars["tip"]) && $session->Vars["tip"]>0)
			$this->tip = $session->Vars["tip"];
		if (isset($session->Vars["ses_userid"]) && $session->Vars["ses_userid"]>0)
			$this->userSystemID = $session->Vars["ses_userid"];





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
        
        
        
//echo        $this->lngId;
        
        $this->thisMode = $session->Vars["thisMode"];
        $this->uniqueid = $session->Vars["uniqueid"];


		
		$this->dataMoment = date("Y-m-d");
		//msv mban vetem filtrat e kerkimit, qe vijne ne executation time
		
		$this->sql_type 	= $sql_type;
		
		if ($restrict_search_to_nodes!=""){
			$this->restrict_search_to_nodes = $restrict_search_to_nodes;
		} /*else {
			$this->restrict_search_to_nodes = $session->Vars["level_0"].",0,0,0,0";
		}*/
		
		//echo "<br>InternalSearchResultParams:$restrict_search_to_nodes<br>";
		
		if (count($allNodesToSearch)>0) {
			$this->find_result_to_nodes = $allNodesToSearch;
			$this->find_result_to_nodes["koord"][$session->Vars["level_0"].",0,0,0,0"] = $session->Vars["level_0"].",0,0,0,0";
		} 

		if (isset($_GET["ser"]) || isset($_GET["ser"]))
			$this->sers =$_GET["ser"];
		elseif (isset($_GET["sers"]) || isset($_GET["sers"]))
			$this->sers =$sers;
		else { 
			$sers = WebApp::getVar("sers");
			IF (isset($sers) && ($sers!= 'undefined')) { //kemi mesazh te ardhur nga nyja
				$this->sers =$sers;
			}	
		}
		
		$objId = explode("-",$session->Vars["idstemp"]);
		$this->objId = $objId[4] ;
		$this->sts = $objId[2] ;

		if (isset($event->args["msv"])) {
			$msv = $event->args["msv"];
			//echo "111";
		} elseif (isset($_GET["msv"]) || isset($_GET["msv"])) {
			$msv = $_REQUEST["msv"];
			//echo "222";
		
		} elseif (ISSET($_REQUEST["search"]) AND ($_REQUEST["search"] != "")) 
		{
			$search_term_sel  = SUBSTR($_REQUEST["search"], 0, 50);
			$search_term_sel  = STR_REPLACE(array("src","<",">"), array("","",""), $search_term_sel);
			$this->termSearch = HTMLSPECIALCHARS($search_term_sel);
		} 
		else 
		{
			$msv = WebApp::getVar("msv");	
			//echo "444444";
		}
		
	
		IF (isset($msv) && ($msv!= 'undefined') && $event->name!='search') {
			$paramMsv 		= unserialize(base64_decode($msv));
			if (isset($paramMsv["termFromSearchModule"])) {
				$this->termFromSearchModule = $paramMsv["termFromSearchModule"];
				IF ($this->sers == $this->objId	&& isset($paramMsv["termSearch"])) {
					$this->termSearch = $paramMsv["termSearch"];
				}
			}
			if (isset($paramMsv["termSearch"])) {
				$this->termSearch = $paramMsv["termSearch"];
			}
		}	
		$this->InitProp();
		$this->NemProp();	

		WebApp::addVar("objId",$this->objId);

		$termFromSearchModule = WebApp::getVar("termFromSearchModule"); //inicializo nga node services
		IF (isset($termFromSearchModule) && $termFromSearchModule!= 'undefined' && $termFromSearchModule!= '') {
			$this->termFromSearchModule = $termFromSearchModule;
		}
		
		if ($this->sers == $this->objId) {
			
			if (isset($event->args["rpp"]))
				$this->NrPage = $event->args["rpp"];
			elseif (isset($_GET["rpp"]) || isset($_GET["rpp"]))
				$this->NrPage = $_REQUEST["rpp"];
			else { 
				$rpp = WebApp::getVar("rpp");
				IF (isset($rpp) && ($rpp!= 'rpp') && ($rpp!= 'undefined')) { //kemi mesazh te ardhur nga nyja
					$this->NrPage = $rpp;
				}	
			}			
			
			if (isset($event->args["rp".$sers]))
				$this->recPages = $event->args["rp".$sers];
			elseif (isset($event->args["rp"]))
				$this->recPages = $event->args["rp"];
			elseif (isset($_GET["rp".$sers]) || isset($_GET["rp".$sers]))
				$this->recPages =  $_REQUEST["rp"];
			else { 
				$rp = WebApp::getVar("rp");
				IF (isset($rp) && ($rp!= 'rp') && ($rp!= 'undefined')) { //kemi mesazh te ardhur nga nyja
					$this->recPages = $rp;
				}	
			}				
		}

		if (isset($this->param["displayFilter"]) && $this->param["displayFilter"]=="yes")
			WebApp::addVar("displayFilter","yes");
		else
			WebApp::addVar("displayFilter","no");
			
			
		$resetSearch="no";
		IF ($event->name=="srm" && isset($event->args["s4"]) && $event->args["s4"]!="") {
			$this->termSearch = $event->args["s4"];
			$resetSearch="yes";
		}
		IF ($resetSearch== 'yes') {
			$this->NrPage = 1;
		}
		
		if ($this->termSearch=="") {
			$this->termSearch = $this->termFromSearchModule;
		}			
		
		if ($this->termSearch !="" ) {	
			$this->proccesTermToSearch($this->termSearch);
			if ($this->error_code==1) {
				
		//echo $this->guess_by."--$guess_by--HERE--".$this->error_code."<br>";
				
				if ($this->guess_by == "type_categorization") {
				
					global $sessUserObj;
				
					$this->URLS_EXTENDED["details_lecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".LECTURE_DETAILS.";idElC={{CID}})');";
					if (isset($sessUserObj->tp_specific_container) && $sessUserObj->tp_specific_container!="") {
						$targetDetails 	= str_replace("k=","",$sessUserObj->tp_specific_container);
						//$this->URLS_EXTENDED["details_course"] 	= "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$targetDetails.";idElC={{CID}})');";
						$this->URLS_EXTENDED["details_lecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$targetDetails.";idElC={{CID}})');";
					} 				
				
				
	//	echo $sessUserObj->tp_specific_container."--tp_specific_container<br>";
	//	echo $this->URLS_EXTENDED["details_lecture"]."--details_lecture<br>";
				
				
				
				

	//	echo $this->guess_by."--$guess_by--HERE--".$this->error_code."<br>";
					$arrayCat = array();

					$arrayCat[0]["label"] = "{{_lectures_and_resources_search}}";

					
					$categorizationGrid = array();
					$types = array();
					$typesToExlude = array();
					$types['PR'] = 'PR';
					$types['EC'] = 'EC';
					$types['EL'] = 'EL';
				//	$types['TC'] = 'TC';
					
					
					$typesToExlude = $types;
					
					
					$typesToExlude['PR'] = "'PR'";
					$typesToExlude['EC'] = "'EC'";
					$typesToExlude['EL'] = "'EL'";
					$typesToExlude['TC'] = "'TC'";					
					$typesToExlude['TE'] = "'TE'";					
					
					
					$nrResults = 0;
					
					
				//	if (isset($sessUserObj->pathLectureInfo["pathLectureNav"]) && count($sessUserObj->pathLectureInfo["pathLectureNav"])>0)
				//		$types = array_merge($types,$sessUserObj->pathLectureInfo["pathLectureNav"]);

					if (isset($sessUserObj->pathLectureInfo["pathSearchTypes"]) && count($sessUserObj->pathLectureInfo["pathSearchTypes"])>0)
						$types = array_merge($types,$sessUserObj->pathLectureInfo["pathSearchTypes"]);

					$key = 0;
					
					$index = 1;
					$arrayCat[$index]["label"] = "{{_lectures_and_modules_search}}";
					if (count($types)>0)
							$arrayCat[$index]["types"] = "'".implode("','",$types)."'";						
					else	$arrayCat[$index]["types"] = "'PR','EC','EL','TC','TE'";		
					$arrayCat[$index]["keyGrid"] = "$index";		
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index,ZONE_AUTHORING);
					
					
					
					$nrResults +=$arrayCat[$index]["nrRes"];
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];	
					
					$index++;
					
					
					
					
					/*$index = 1;
					$arrayCat[$index]["label"] = "{{_tutorials_search}}";
					if (count($types)>0)
							$arrayCat[$index]["types"] = "'".implode("','",$types)."'";						
					else	$arrayCat[$index]["types"] = "'TC','TE'";		
					$arrayCat[$index]["keyGrid"] = "$index";		
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index,ZONE_AUTHORING);
					
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];	
					$typesToExlude['TC'] = "'TC'";					
					$typesToExlude['TC'] = "'TC'";					
					
					$index++;	*/				
					
					
				

					$arrayCat[$index]["label"] = "{{_resources_search}}";
					$arrayCat[$index]["types"] = "'RI'";
					$arrayCat[$index]["keyGrid"] = $index;	
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index,ZONE_AUTHORING);
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];
						
					$nrResults +=$arrayCat[$index]["nrRes"];
					$typesToExlude[$arrayCat[$index]["types"]]= $arrayCat[$index]["types"];
					$index++;
					

					$arrayCat[$index]["label"] = "{{_forum_search}}";
					$arrayCat[$index]["types"] = "'CM'";
					$arrayCat[$index]["keyGrid"] = $index;	
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index);
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];	
					$nrResults +=$arrayCat[$index]["nrRes"];
					$typesToExlude[$arrayCat[$index]["types"]]= $arrayCat[$index]["types"];
					$index++;



					$arrayCat[$index]["label"] = "{{_news_search}}";
					$arrayCat[$index]["types"] = "'NI'";
					$arrayCat[$index]["keyGrid"] = $index;	
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index);
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];
					$nrResults +=$arrayCat[$index]["nrRes"];
					$typesToExlude[$arrayCat[$index]["types"]]= $arrayCat[$index]["types"];
					$index++;
					
					
					$arrayCat[$index]["label"] = "{{_faq_search}}";
					$arrayCat[$index]["types"] = "'FQ'";
					$arrayCat[$index]["keyGrid"] = $index;	
					$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index);
					if ($arrayCat[$index]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[$index];
					$nrResults +=$arrayCat[$index]["nrRes"];
					$typesToExlude[$arrayCat[$index]["types"]]= $arrayCat[$index]["types"];
					$index++;					
					
					
					/*$arrayCat[2]["label"] = "{{_news_search}}";
					$arrayCat[2]["types"] = "'NI'";
					$arrayCat[2]["keyGrid"] = 2;	
					$arrayCat[2]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[2]["types"],2);
					if ($arrayCat[2]["nrRes"]>0)
						$categorizationGrid["data"][$key++] = $arrayCat[2];	*/
						
						
					if ($session->Vars["level_0"] !=ZONE_AUTHORING) {
					
					
						$arrayCat[$index]["label"] = "{{_zone_related_search}}";
						
						if (isset($typesToExlude) && count($typesToExlude)>0)
								$arrayCat[$index]["types"] = implode(",",$typesToExlude);
						else	$arrayCat[$index]["types"] = "";
						$arrayCat[$index]["keyGrid"] = $index;	
						
		/*	echo "<textarea>";
			print_r($rs_list);
			echo "</textarea>";		*/				
						
						
						$arrayCat[$index]["nrRes"] = "".$this->CreateAllDataCategorizationByTypeToDisplayAjax($arrayCat[$index]["types"],$index,$session->Vars["level_0"],"exludeDocTypes");
						$nrResults +=$arrayCat[$index]["nrRes"];
						if ($arrayCat[$index]["nrRes"]>0)
							$categorizationGrid["data"][$key++] = $arrayCat[$index];	
						$index++;					
					}
					
					
					$categorizationGrid["AllRecs"] = count($categorizationGrid["data"]);
					WebApp::addVar("categorizationGrid", $categorizationGrid);						
					
					
					
					WebApp::addVar("nrTotalResults", "".$nrResults);						
					

				/*	echo "categorizationGrid<textarea>";	
					print_r($categorizationGrid);
					echo "</textarea>";		*/
					
					
				} else {
					

							if (isset($this->find_result_to_nodes["koord"]) && count($this->find_result_to_nodes["koord"])>0) {
							reset($this->find_result_to_nodes["koord"]);
							while (list($keyk,$valK)=each($this->find_result_to_nodes["koord"])) {

								$nodes_restriction_to_sql 		= " AND n4.id_zeroNivel = '".$session->Vars["level_0"]."' ";
								$nodes_restriction_to_sql_ECC 	= " AND n4.id_zeroNivel = '".ZONE_AUTHORING."' ";
								if ($valK=="") {
								} else {
									$restrict_search_to_nodes = explode(",",$valK);
									if (count($restrict_search_to_nodes)==5) {
											if ($restrict_search_to_nodes[4]>0) {
											$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
											AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
											AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
											AND n4.id_thirdNivel = '".$restrict_search_to_nodes[3]."' 
											AND n4.id_fourthNivel = '".$restrict_search_to_nodes[4]."' 
											";
											} elseif ($restrict_search_to_nodes[3]>0) {
											$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
											AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
											AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
											AND n4.id_thirdNivel = '".$restrict_search_to_nodes[3]."' 
											";
											} elseif ($restrict_search_to_nodes[2]>0) {
											$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
											AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
											AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
											";
											} elseif ($restrict_search_to_nodes[1]>0) {
											$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
											AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
											";
											} else {
											$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' ";
											}
											$nodes_restriction_to_sql_ECC = $nodes_restriction_to_sql;
									}			
								}
								if ($this->guess_by == "yes") {			

									$nrRes = $this->CreateAllDataToDisplayAjax($nodes_restriction_to_sql,$nodes_restriction_to_sql_ECC,$keyk);
									$this->find_result_to_nodes["cnt"][$keyk] = $nrRes;


								} else {
									$slqCount = str_replace("#nodes_restriction_replace_sql_ecc#",$nodes_restriction_to_sql_ECC,$this->thisTemplateSqlToGetResult);
									$slqCount = str_replace("#nodes_restriction_replace_sql#",$nodes_restriction_to_sql,$slqCount);

									$rs_cnt = WebApp::execQuery($slqCount);
									$this->find_result_to_nodes["cnt"][$keyk] = $rs_cnt->Field("cnt");
								}
							}}



								if ($this->guess_by == "yes") {

								} elseif ($this->guess_by == "categorizationByType") {

								} else  {
									WebApp::addVar("rpp",$this->NrPage);
									WebApp::addVar("rp",$this->recPages);

									if ($this->error_code == 1) {
										$this->constructNavListHtml();
										$this->constructAdvHtmlnavigation();
										WebApp::addVar("gridDataSrc",$this->gridDataSrc);	
									}

									//PER AUDIT TRAIL dhe per MSV qe kalon si variabel
									$msvAuditTrail = array();
									$msvAuditTrail["sts"] = 			$this->sts;			//	sts			nem id
									$msvAuditTrail["gp"] = 				$this->NrPage;		//	gp			search_faqja_korrente
									$msvAuditTrail["Cnt"] = 			$this->CountItems;	//	CountItems	search_nr_rekordeve


									$msvToApplication = array();
									$msvToApplication["termSearch"] = 		$this->termSearch;	//	termSearch	search_nr_rekordeve

									$recPages	= $this->recPages;
									$NrPage		= $this->NrPage;
									$CountItems	= $this->CountItems;
									$TotPage	= $this->TotPage;
									$FromRecs	= $this->FromRecs;
									$ToRecs		= $this->ToRecs;

									$MSparams = base64_encode(serialize($msvToApplication));
									$msvToApplication = array();

									//$sdfsdf = base64_encode("a:100000:{}");
									WebApp::addVar("msvSrc","$MSparams");
								}
			}

				} else {
				//	return;
				}


		} else {
				$this->error_code = 3;
		}
		
		if (isset($this->error_code_description[$this->error_code]))
			WebApp::addVar("error_code_to_html", $this->error_code_description[$this->error_code]);
		else
			WebApp::addVar("error_code_to_html", "SomeErrorOccurred");
		
		
		WebApp::addVar("kc","{{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}}");
		WebApp::addVar("k","{{contentId}}");
		WebApp::addVar("termSearch","$this->termSearch");
	}

	function returnMessageLayoutProp($id) {
		$gProp = $this->returnFilterProp($id);
		if(isset($gProp["headline"]) && $gProp["headline"] !="")
			$this->param["headline"] = $gProp["headline"];
		if(isset($gProp["headlineSearch"]) && $gProp["headlineSearch"] !="") {
			$this->param["headlineSearch"] = $headlineSearch;
		}
	}
	
	function returnFilterProp($filterId) {
		global $session;
		require_once(INCLUDE_KW_AJAX_PATH.'KwManager.Base.class.php');
		//[fkid] => 11,5	-- idFamiljes,idKeywordit
		$KwObj = new KwManagerInternalFilters($session->Vars["ses_userid"],$session->Vars["lang"]);
		$fkidArr = explode(",",$filterId);
		$KwObj->setTreePositionProperties("0,".$fkidArr[0],"","findInfo");
		$KwObj->nomenclature_item = $fkidArr[1];
		$KwObj->getFilterInfo();
		return $KwObj->returnFilterProp();
	}	
	function InitProp()
	{
		$this->param["headline"] 		= "{{_search_headline_default}}";
		$this->param["headlineSearch"] 	= "{{_search_headline}}";
		
		$this->param["displayFilter"] 	= "";
		$this->param["userDefined"] 	= "yes";		
		$this->param["term"] 			= "";
		
		
		//fushat qe do te shfaqen
		$this->param["display_date"] = "yes";
		$this->param["display_source"] = "yes";
		$this->param["display_author"] = "yes";
		$this->param["display_abstract"] = "yes";
		$this->param["display_keywords"] = "";
		$this->param["display_image"] = "yes";
		
		//fushat qe do te marrin link
		$this->param["linkToTitle"] = "yes";
		$this->param["linkToImage"] = "";
		$this->param["linkTolabelUserDefined"] = "";
		$this->param["linkToNode"] 		= "yes";
		
	}
	function NemProp() {
		global $session;
		
		$gProp = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
		
        //SHTESA PER TEMPLATET -----------------------------------------------------------------------------------------------------------------------
          $SearchResultModule_TEMPLATE = "";
          $template_id_sel      = "";
          $template_id_mob_sel  = "";
		  IF (isset($gProp["template_id"]) && $gProp["template_id"]!="")
		     {$template_id_sel = $gProp["template_id"];}
		  IF (isset($gProp["template_id_mob"]) && $gProp["template_id_mob"]!="")
		     {$template_id_mob_sel = $gProp["template_id_mob"];}
          IF ($template_id_mob_sel == "")
             {$template_id_mob_sel = $template_id_sel;}
 
          GLOBAL $mob_web;
          IF ($mob_web == "mob") //po e shtoje kot kete variabel per momentin
             {
              $template_id = $template_id_mob_sel;
             }
          ELSE
             {
              $template_id = $template_id_sel;
             }
     
          IF ($template_id != "")
             {
              $sql_templ = "SELECT template_box FROM template_list WHERE template_id = '".$template_id."'";
              $rs_templ  = WebApp::execQuery($sql_templ);
              IF (!$rs_templ->EOF() AND mysql_errno() == 0)
                 {$SearchResultModule_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}SearchResultModule/'.$rs_templ->Field("template_box").'" />';}
             }
          ELSE
             {
              IF (defined("SEARCHRESULTMODULE_TEMPLATE") AND (SEARCHRESULTMODULE_TEMPLATE != "0"))
                 {$SearchResultModule_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}SearchResultModule/template'.SEARCHRESULTMODULE_TEMPLATE.'.html" />';}
                     
              IF (defined("SEARCHRESULTMODULE_TEMPLATE") AND (SEARCHRESULTMODULE_TEMPLATE == "0"))
                 {$SearchResultModule_TEMPLATE = '<Include SRC="{{NEMODULES_PATH}}SearchResultModule/template_default.html" />';}
             }
          
          WebApp::addVar("SearchResultModule_TEMPLATE", $SearchResultModule_TEMPLATE);
        //SHTESA PER TEMPLATET -----------------------------------------------------------------------------------------------------------------------

		if(isset($gProp["headline"]) && $gProp["headline"]!="")	
			$this->param["headline"] = $gProp["headline"];
		
		if(isset($gProp["headlineSearch"]) && $gProp["headlineSearch"]!="")	
			$this->param["headlineSearch"] = $gProp["headlineSearch"];

		if(isset($gProp["setimage"]) && $gProp["setimage"]!="")	
			$this->param["setimage"] = $gProp["setimage"];

		if(isset($gProp["dateL"]))		
			$this->param["display_date"] = $gProp["dateL"];
		
	//	$this->param["title"] = $gProp["title"];
		
		if(isset($gProp["author"]))		
			$this->param["display_author"] = $gProp["author"];
		if(isset($gProp["source"]))		
			$this->param["display_source"] = $gProp["source"];
		if(isset($gProp["abstract"]))		
			$this->param["display_abstract"] = $gProp["abstract"];
		if(isset($gProp["display_title"]))		
			$this->param["linkToTitle"] = $gProp["display_title"];
		if(isset($gProp["labelLink"]))		
			$this->param["linkTolabelUserDefined"] = $gProp["labelLink"];
		if(isset($gProp["link_to_node"]))		
			$this->param["linkToNode"] = $gProp["link_to_node"];
		if(isset($gProp["displayFilter"]))		
			$this->param["displayFilter"] = $gProp["displayFilter"];
		if(isset($gProp["term"]))		
			$this->param["term"] = $gProp["term"];
		if(isset($gProp["userDefined"]) && $gProp["userDefined"]!="")	
			$this->param["userDefined"] = $gProp["userDefined"];
		if(isset($gProp["link"]) && $gProp["link"]!="")	
			$this->param["link"] = $gProp["link"];
	

		
		if(isset($gProp["image"]))				$this->param["display_image"] = $gProp["image"];
		if(isset($gProp["display_image"]))		$this->param["linkToImage"] = $gProp["display_image"];
		
		if(isset($gProp["keywords"]))			$this->param["display_keywords"] = $gProp["keywords"];
		
		if(isset($gProp["publish_kw"]) && $gProp["publish_kw"]!="") {
			$dataneded = explode(",",$gProp["publish_kw"]); ;
			while (list($key,$value)=each($dataneded)) {
				if ($value!="") {
					$this->param["publish_kw"][$value] = $value;
					if (isset($gProp["kw_label_".$value]))
						$this->param["kw_labels"][$value] = $gProp["kw_label_".$value];
					else
						$this->param["kw_labels"][$value] = "";
				}
			}	
		}

		if(isset($gProp["rec_page"]) && $gProp["rec_page"]!="")	
			$this->recPages = $gProp["rec_page"];
			
		$this->order_by = $gProp["order_by"];
		$this->sort_by = $gProp["sort_by"];

	}	
}


class InternalSearchResultModule extends InternalSearchResultParams
{
	var $search_what = "1";	//1 or 2. 1 means everywhere,2 means in selected node and its childs

	var $CountItems	= 1;
	var $TotPage	= 1;
	var $FromRecs	= 1;
	var $ToRecs	= 1;
	var $limitToSql	= " limit 0,20 ";
	var $listData = array();

	var $InvalidTerm= "no";
	
	var $SP	= " OR "; //Inkuzive or exsluziv

	var $SWO	= "*"; //SearchWeightOperator

	//SearchWeight
	/*var $SW	= array(
								"ti"=>1.2,
								"nkw"=>1.1,
								"kw_1"=>1,
								"kw_2"=>1,
								"kw_3"=>1,
								"kw_4"=>1,
								"ab"=>0.8,
								"so"=>0.7,
								"au"=>0.6,
								"txt"=>0.5
								);*/
	



	var $SW	= array(
								"ti"=>1,
								"nkw"=>1,
								"kw_1"=>1,
								"kw_2"=>1,
								"kw_3"=>1,
								"kw_4"=>1,
								"ab"=>0.8,
								"so"=>0.7,
								"au"=>0.6,
								"txt"=>0.5
								);
	

	//SearchHalfWeight
	var $SHW	= array(
								"ti"=>1,
								"kw_1"=>0.8,
								"kw_2"=>0.8,
								"kw_3"=>0.8,
								"kw_4"=>0.8,
								"ab"=>0.4,
								"so"=>0.3,
								"au"=>0.2,
								"txt"=>0.1

								);	

	//var $stopList 	= array("please","insert","content","document","title"); 
	var $stopList 	= array(); 
    var $abbList 	= array();
	var $singleWordsHighlight 	= array(); 
	var $singleWordsHighlightPattern 	= array(); 

	var $singleWords 	= array(); 
	var $singleWordsS 	= array(); 

	//inicializohet classa e searchit me termin e searchit, numrin re rekodeve ne faqe, me orderin ne te cilin do shfaqet item pas prioritetit
	function proccesTermToSearch($termFromSearchModule) {
		global $session;

		$singleWords = array();
		$singleWordsS = array();
		$singleWordInStopList = array();
		$singleWordsSInStopList = array();
		
		$allTearmsToSearch = array();
		$order_nr_array = array();
		$termFromSearchModule = trim($termFromSearchModule);			
		
		$stopListL["German"] 	= array("script","ab","aber","aehnlich","ähnlich","alle","allem","aller","alles","allg","als","also","am","an","and","andere","anderen","anderes","auch","auf","aus","außer","been","bei","beim","bereits","bevor","bin","bis","bist","bzw","da","dabei","dadurch","dafuer","daher","damit","dann","daran","darauf","daraus","darum","das","dass","davon","davor","dazu","dein","deine","dem","den","denen","denn","dennoch","der","derem","deren","des","deshalb","dessen","die","dies","diese","diesem","diesen","dieser","dieses","doch","dort","du","durch","eben","eher","ein","eine","einem","einen","einer","eines","einig","einige","er","erst","erste","erster","es","etc","etwa","etwas","euer","eure","for","für","ganz","gar","geben","geht","gibt","habe","haben","hat","hatte","hatten","hattest","hattet","hier","ich","ihm","ihr","ihre","ihrem","ihren","ihrer","ihres","im","in","ist","ja","je","jede","jedem","jeden","jeder","jedes","jedoch","jene","jenem","jenen","jener","jenes","jetzt","kann","kannst","koennen","koennt","kommen","kommt","können","könnt","man","mehr","mein","meine","meist","mich","mit","möchte","moechte","müssen","muss","musst","nachdem","nein","nicht","nichts","noch","nun","nur","ob","oder","off","ohne","per","schon","sehr","sehrwohl","seid","sein","seine","seinem","seinen","seiner","seines","seit","sich","sie","sind","so","sodaß","solch","solche","solchem","solchen","solcher","solches","soll","sollen","sollst","sollt","sollte","sollten","solltest","sonst","soviel","soweit","sowie","sowohl","statt","steht","über","um","und","uns","unser","unsere","unseren","unseres","unter","vom","von","vor","wann","war","warum","was","weiter","weitere","welche","welchem","welchen","welcher","welches","wenn","wer","werde","werden","werdet","weshalb","wie","wieder","wieso","wieviel","wir","wird","wirst","wo","woher","wohin","wurde","wurden","zu","zum","zur");
		$stopListL["Shqip"] 	= array("script","cdo","për","tjetër","tjeter","dhe","në","më","të","së","ne","ju","ai","ajo","si","e","këto","keto","me","kemi" ,"vet","për","jonë","është","pranë","drejt","cilave","thuajse","plotë","asaj","çka","bën","janë","vetëm","disa","nga","mund","cilat","por","por","edhe","shumë","një","kjo","saj");	
		$stopListL["English"] 	= array("script","a", "about", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "amoungst", "amount", "an", "and", "another", "any", "anyhow", "anyone", "anything", "anyway", "anywhere", "are", "around", "as", "at", "back", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom", "but", "by", "call", "can", "cannot", "cant", "co", "computer", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven", "else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "i", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own", "part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thick", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves");  
		$stopListL["French"] 	= array("script","a", "adieu", "afin", "ah", "ai", "aie", "aient", "aies", "aille", "ainsi", "ait", "all", "alla", "allais", "allait", "allant", "alle", "aller", "allerent", "allez", "allons", "alors", "apres", "après", "as", "assez", "au", "au-dela", "au-delà", "au-dessous", "au-dessus", "aucun", "aucune", "aucunes", "aucuns", "aupres", "auprès", "auquel", "aura", "aurai", "aurais", "aurez", "auront", "aussi", "aussitôt", "autant", "autour", "autre", "autres", "autrui", "aux", "auxquelles", "auxquels", "av", "avaient", "avais", "avait", "aval", "avant", "avec", "avez", "avoir", "avons", "ayant", "ayez", "ayons", "bah", "bas", "beaucoup", "bien", "bonté", "bout", "but", "c'", "c'est-a-dire", "c'est-à-dire", "ca", "car", "ce", "ceci", "cela", "celle", "celle-ci", "celle-la", "celle-là", "celles", "celles-ci", "celles-la", "celles-là", "celui", "celui-ci", "celui-la", "celui-là", "cependant", "ces", "cet", "cette", "ceux", "ceux-ci", "ceux-la", "ceux-là", "chacun", "chacune", "chaque", "chez", "chut", "ci", "circa", "combien", "comme", "comment", "commme", "compte", "contre", "crac", "crainte", "côtè", "d", "d'", "dans", "de", "deca", "dedans", "dehors", "dela", "delà", "depuis", "des", "desquelles", "desquels", "dessous", "dessus", "devant", "deçà", "dire", "divers", "diverses", "donc", "dont", "du", "duquel", "durant", "dès", "dépens", "dépit", "e", "elle", "elle-meme", "elle-même", "elles", "elles-memes", "elles-mêmes", "en", "entre", "envers", "es", "est", "et", "etaient", "etais", "etait", "etant", "ete", "etes", "etiez", "etions", "etre", "eu", "eurent", "eut", "eux", "eux-memes", "eux-mêmes", "fai", "faire", "fais", "faisais", "faisait", "faisant", "faisons", "fait", "faites", "fasse", "faute", "fera", "ferai", "ferais", "feras", "ferez", "ferons", "firent", "fit", "font", "furent", "fut", "he", "helas", "holà", "hors", "hé", "hélas", "il", "ils", "irai", "irais", "iras", "irons", "iront", "j'", "je", "jusqu'", "jusque", "l'", "la", "laquelle", "le", "lequel", "les", "lesquelles", "lesquels", "leur", "leurs", "lieu", "loin", "lors", "lorsqu'", "lorsque", "lui", "lui-meme", "lui-même", "m'", "ma", "mains", "maintes", "maints", "mais", "malgre", "malgré", "me", "merci", "mes", "mien", "mienne", "miennes", "miens", "milieu", "moi", "moi-meme", "moi-même", "moins", "mon", "moyen", "même", "mêmes", "na", "ne", "neanmoins", "ni", "nom.", "nombre", "non", "nos", "notre", "notres", "nous", "nous-memes", "nous-mêmes", "néanmoins", "nôtre", "nôtres", "on", "ont", "or", "ou", "ouais", "ouïe", "où", "oü", "par", "par-dela", "par-delà", "par-dessus", "parce", "parmi", "part", "partant", "partir", "pas", "passe", "passé", "pendant", "personne", "peu", "peut-être", "plein", "plupart", "plus", "plusieurs", "plutot", "plutôt", "pour", "pourquoi", "pourvu", "pres", "prises", "proche", "proie", "près", "puis", "puisqu'", "puisque", "période", "qu'", "quand", "que", "quel", "quelconque", "quelle", "quelles", "quelqu'un", "quelque", "quelques", "quelques-unes", "quelques-uns", "quels", "qui", "quiconque", "quoi", "quoique", "revoici", "revoila", "revoilà", "rien", "s'", "sa", "sais", "sans", "sauf", "se", "sein", "selon", "sens", "sera", "serai", "serais", "seras", "serez", "serons", "seront", "ses", "si", "sien", "sienne", "siennes", "siens", "signe", "sinon", "soi", "soi-meme", "soi-même", "soient", "sois", "soit", "sommes", "son", "sont", "souci", "sous", "soyez", "soyons", "suis", "sur", "surtout", "sus", "ta", "tandis", "tant", "te", "tel", "telle", "telles", "tels", "tes", "toc", "toi", "ton", "tous", "tout", "toute", "toutes", "travers", "trop", "trève", "tu", "un", "une", "unes", "uns", "va", "vais", "vas", "vers", "voici", "voie", "voila", "voilà", "vont", "vos", "votre", "votres", "vous", "vous-meme", "vous-memes", "vous-même", "vous-mêmes", "vu", "vue", "vôtre", "vôtres", "y", "à", "ça", "étant", "êt", "être");  
	
		$lngID_c = strtoupper ($session->Vars["lang"])."_Name";
		if (defined($lngID_c) && constant($lngID_c)!="" && is_array($stopListL[constant($lngID_c)])) {
			$this->stopList =	array_merge($this->stopList,$stopListL[constant($lngID_c)]);	
		}

		$allTearmsToSearch = explode(' ',$termFromSearchModule);
		//ketu kapen fjalet tek e tek, pastrohen nga disa karaktere

		if (count($allTearmsToSearch)>=1) {
			
			while (list($key, $singleTerm) = each($allTearmsToSearch)) {
				
				$singleTerm =stripslashes($singleTerm);
				$singleTerm = trim($singleTerm);
				
				if (strlen($singleTerm)>=3) {
				
					if (!in_array(strtolower($singleTerm),$this->stopList)) {
						
						$singleTerm = preg_replace("#\/|\;|\:|<|>|\[|\{|\]|\}|\.|\?|'|\"|\(|]|\)|\,#is", "", $singleTerm, -1);
						$singleTerm = preg_replace("#-#is", " ", $singleTerm, -1);
						//$singleWords  [$singleTerm]="*".$singleTerm."*";
						$singleWords  [$singleTerm]="".$singleTerm."*";
						$singleWordsS [$singleTerm]=$singleTerm;
						
						$this->singleWordsHighlightPattern [$singleTerm] = "#".$singleTerm."#i";
						$this->singleWordsHighlight [$singleTerm] = "<span class=\"highlight\">".$singleTerm."</span>";
						
					} else {
						$singleTerm = preg_replace("#\/|\;|\:|<|>|\[|\{|\]|\}|\.|\?|'|\"|\(|]|\)|\,#is", "", $singleTerm, -1);
						$singleTerm = preg_replace("#-#is", " ", $singleTerm, -1);
						
						//$singleWordInStopList	[$singleTerm]	=	"".$singleTerm."*";
						$singleWordInStopList	[$singleTerm]	=	"*".$singleTerm."*";
						$singleWordsSInStopList [$singleTerm]	=	$singleTerm;
					}
				}
			}
			//if (count($singleWordsS)>=1) 
				$WordsImplodedByOR = implode("|",$singleWordsS);
			/*elseif (count($singleWordsSInStopList)>=1) {
				$WordsImplodedByOR = implode("|",$singleWordsSInStopList);
				$singleWords  = $singleWordInStopList;
			}*/
			
		} else {
			if (strlen($termFromSearchModule)>=3 && (!in_array(strtolower($termFromSearchModule),$this->stopList))) {
				$termFromSearchModule = preg_replace("#\/|\;|\:|<|>|\[|\{|\]|\}|\.|\?|'|\"|\(|]|\)|\,#is", "", $termFromSearchModule, -1);
				$termFromSearchModule = preg_replace("#-#is", " ", $termFromSearchModule, -1);
				$singleWords []="*".$termFromSearchModule."*";
				$this->singleWordsHighlightPattern [] = "#".$termFromSearchModule."#i";
				$this->singleWordsHighlight [] = "<span class=\"highlight\">".$termFromSearchModule."</span>";
			}			
		}

		//formohen kushte per searchin, qe kane lidhje me termin e searchit
		$ln = $session->Vars["lang"];
		$md = $session->Vars["thisMode"];
		
		//formohen kushte per searchin, qe kane lidhje me gjendjen e aplikimit
		$stateCondition="";
		$expire_condition="";
		
		if ($session->Vars["thisMode"]=='') {
			$expire_condition=" 
				AND
				if(c.scheduling = 'Y', 
						(c.scheduling_from<='".$this->dataMoment."' AND ('".$this->dataMoment."'<=c.scheduling_to OR c.scheduling_to='0000-00-00'))
						, 1)
						 ";
			
			$stateCondition=" 
				AND c.state".$ln." not in (0,5,7) 
				AND c.content".$ln."".$md." IS NOT NULL
				AND n4.active".$ln." != 1
				AND n4.state".$ln." != 7 
				AND n4.description".$ln."".$md." IS NOT NULL ";
		} else {
			$stateCondition=" 
				AND c.content".$ln."".$md." IS NOT NULL
				AND c.state".$ln." not in (7)";
		}	
		
		WebApp::addVar("stateCondition", $stateCondition);
		WebApp::addVar("expire_condition", $expire_condition);
		
		$conditionSearchArray = array();
		$conditionSearchUnionArray = array();
		$this->singleWords = $singleWords;	
		$this->singleWordsS = $singleWordsS;	
		
		if (count($singleWords)==0) {
			$this->error_code = 4;
			$this->CountItems = -1;
		
		} else {
			
			$valSearchClearFromStopListWords = implode(" ",$singleWords);
			
			if ($this->sql_type == "default") {
				$conditionSearchArray [] = 
				"
							match (ctxt.keywords_1_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (ctxt.keywords_2_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (ctxt.keywords_3_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (ctxt.keywords_4_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (ctxt.nodekw_".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)

						OR  match (ctxt.context".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (c.title".$ln.") 					against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (c.description".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (c.doc_source".$ln.") 			against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
						OR  match (c.source_author".$ln.") 			against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)
				";			
				$conditionSearch  = implode(" ".$this->SP." ",$conditionSearchArray);
				$conditionSearchFull  = "\n AND (\n".$conditionSearch."\n) \n";
			} else {

				
				
				
				$conditionSearchUnionArray ["ctxt.keywords_1_".$ln.$md] 	= " match(ctxt.keywords_1_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["ctxt.keywords_2_".$ln.$md] 	= " match(ctxt.keywords_2_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["ctxt.keywords_3_".$ln.$md] 	= " match(ctxt.keywords_3_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["ctxt.keywords_4_".$ln.$md] 	= " match(ctxt.keywords_4_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["ctxt.nodekw_".$ln.$md] 		= " match(ctxt.nodekw_".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";

				$conditionSearchUnionArray ["ctxt.context".$ln.$md] 		= " match(ctxt.context".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["c.title".$ln] 				= " match(c.title".$ln.") 				against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["c.description".$ln.$md] 	= " match(c.description".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["c.doc_source".$ln] 			= " match(c.doc_source".$ln.") 			against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";
				$conditionSearchUnionArray ["c.source_author".$ln] 		= " match(c.source_author".$ln.") 		against ('".ValidateVarFun::f_real_escape_string($valSearchClearFromStopListWords)."' IN BOOLEAN MODE)";

				$this->conditionSearchUnionArray = $conditionSearchUnionArray;

			}

				$this->ln_search = $ln;
				//$this->ln_searchID = substr("LNG","",$this->ln_search);
				$this->md_search = $md;

				$this->stateCondition_search = $stateCondition;
				$this->expire_condition_search = $expire_condition;		
				$this->conditionSearchFull_search = $conditionSearchFull;		

				$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$session->Vars["level_0"]."' ";
				$nodes_restriction_to_sql_ECC = " AND n4.id_zeroNivel = '".ZONE_AUTHORING."' ";

			//	echo $this->restrict_search_to_nodes."<br>";
				
		
				if ($this->restrict_search_to_nodes!="") {

					$restrict_search_to_nodes = explode (",",$this->restrict_search_to_nodes);
					if (count($restrict_search_to_nodes)==5) {
						if ($restrict_search_to_nodes[4]>0) {
						$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
						AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
						AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
						AND n4.id_thirdNivel = '".$restrict_search_to_nodes[3]."' 
						AND n4.id_fourthNivel = '".$restrict_search_to_nodes[4]."' 
						";
						} elseif ($restrict_search_to_nodes[3]>0) {
						$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
						AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
						AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
						AND n4.id_thirdNivel = '".$restrict_search_to_nodes[3]."' 
						";
						} elseif ($restrict_search_to_nodes[2]>0) {
						$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
						AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
						AND n4.id_secondNivel = '".$restrict_search_to_nodes[2]."' 
						";
						} elseif ($restrict_search_to_nodes[1]>0) {
						$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' 
						AND n4.id_firstNivel = '".$restrict_search_to_nodes[1]."' 
						";
						} else {
						$nodes_restriction_to_sql = " AND n4.id_zeroNivel = '".$restrict_search_to_nodes[0]."' ";
						}
					$nodes_restriction_to_sql_ECC = $nodes_restriction_to_sql;
					} 
				}

				/*	echo "<pre>KETU COUNTI LISTEN\n";
					print_r($this->restrict_search_to_nodes);
					print_r("restrict_search_to_nodes\n");
					print_r($nodes_restriction_to_sql);
					print_r(":nodes_restriction_to_sql\n");
					print_r($nodes_restriction_to_sql_ECC);
					print_r("nodes_restriction_to_sql_ECC\n");
					echo "</pre>";*/		
		
		
		
		$this->nodes_restriction_to_sql = $nodes_restriction_to_sql;		
		$this->nodes_restriction_to_sql_ECC = $nodes_restriction_to_sql_ECC;
		
		
		
		//echo $this->sql_type."--<br>";
		
		if ($this->sql_type == "default") {
			$getCnt = "	

				SELECT count(distinct c.content_id) as ct
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
					   LEFT JOIN 	content_text  	AS ctxt ON (ctxt.content_id = c.content_id)
					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 

			WHERE p.profil_id in ('".$session->Vars["tip"]."')
			  ".$this->nodes_restriction_to_sql."	
			  AND c.searchable = 'Y'
				  ".$stateCondition."
				  ".$expire_condition."
				  ".$conditionSearchFull."";	
				  
				  
				  $this->thisTemplateSqlToGetResult =  "	

				SELECT count(distinct c.content_id) as ct
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
					   LEFT JOIN 	content_text  	AS ctxt ON (ctxt.content_id = c.content_id)
					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 

			WHERE p.profil_id in ('".$session->Vars["tip"]."')
			  #nodes_restriction_replace_sql#
			  AND c.searchable = 'Y'
				  ".$stateCondition."
				  ".$expire_condition."
				  ".$conditionSearchFull."";	
				  
		} else {//sql do implementohet me union
		

			$union_sql_data_array =array();
			
			$union_sql_data_array ["keywords_1"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.keywords_1_".$ln.$md]."
						)
			";
			
			$union_sql_data_array ["keywords_2"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.keywords_2_".$ln.$md]."
						)
			";	
			
			$union_sql_data_array ["keywords_3"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.keywords_3_".$ln.$md]."
						)
			";				

			$union_sql_data_array ["keywords_4"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.keywords_4_".$ln.$md]."
						)
			";				
			
			$union_sql_data_array ["nodekw"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.nodekw_".$ln.$md]."
						)
			";	
			
			$union_sql_data_array ["context"] ="
						(
						SELECT  ctxt.content_id
							   FROM  content_text as ctxt
							   where ".$conditionSearchUnionArray["ctxt.context".$ln.$md]."
						)
			";				

			$union_sql_data_array ["title"] ="
						(
						SELECT  c.content_id
							   FROM  content as c
							   where ".$conditionSearchUnionArray["c.title".$ln]."
						)
			";	
			
			
			$union_sql_data_array ["description"] ="
						(
						SELECT  c.content_id
							   FROM  content as c
							   where ".$conditionSearchUnionArray["c.description".$ln.$md]."
						)
			";	
			
			$union_sql_data_array ["doc_source"] ="
						(
						SELECT  c.content_id
							   FROM  content as c
							   where ".$conditionSearchUnionArray["c.doc_source".$ln]."
						)
			";	
			
			$union_sql_data_array ["source_author"] ="
						(
						SELECT  c.content_id
							   FROM  content as c
							   where ".$conditionSearchUnionArray["c.source_author".$ln]."
						)
			";	

			$union_sql_main_part = implode(" UNION ALL ",$union_sql_data_array);

			 $this->thisTemplateSqlToGetResultAjax =  "	
				SELECT distinct content_id
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 
				 				SELECT distinct c.content_id
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
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      #nodes_restriction_replace_sql#
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition."
				 		UNION DISTINCT
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							
				 							
				 							JOIN	content			AS c	ON (    p.content_id  = c.content_id)


				 							JOIN	nivel_4			AS n4	ON (    c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 																)
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      #nodes_restriction_replace_sql_ecc#
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition."
				 )";					
				
			 $this->thisTemplateSqlToGetResult =  "	
				SELECT count(distinct content_id) as cnt
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 
				 				SELECT distinct c.content_id
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
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      #nodes_restriction_replace_sql#
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition."
				 		UNION DISTINCT
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p				 							
				 							JOIN	content			AS c	ON     p.content_id  = c.content_id
				 							JOIN	nivel_4			AS n4	ON     c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      #nodes_restriction_replace_sql_ecc#
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition.")";	
				  
			$getCnt = "	
				SELECT count(distinct content_id) as ct
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 				SELECT distinct c.content_id
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
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      ".$this->nodes_restriction_to_sql."
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition."
				 		UNION DISTINCT
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							JOIN	content			AS c	ON p.content_id  = c.content_id
				 							JOIN	nivel_4			AS n4	ON     c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			      ".$this->nodes_restriction_to_sql_ECC."
				 			  AND c.searchable = 'Y'
				 				  ".$stateCondition."
				 				  ".$expire_condition.")";
				 			
		}
			
	/*	if ($this->guess_by == "type_categorization") {
		
		} else*/
		
		
		if ($this->guess_by == "yes" || $this->guess_by == "type_categorization") {

							
						$tt = stripslashes($termFromSearchModule);
						$tt = preg_replace("#\/|\;|\:|<|>|\[|\{|\]|\}|\.|\?|'|\"|-|\(|]|\)|\,#is", "", $termFromSearchModule, -1);
						$tt = trim($tt);							
							
							$orderNrArrayWeight ["ctxt.keywords_1_".$ln.$md]= " 
								match(ctxt.keywords_1_".$ln.$md.") against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_1"]."
								";

							$orderNrArrayWeight ["ctxt.keywords_2_".$ln.$md] 	= " 
								match(ctxt.keywords_2_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_2"]." ";

							$orderNrArrayWeight ["ctxt.keywords_3_".$ln.$md] 	= " 
								match(ctxt.keywords_3_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_3"]." ";

							$orderNrArrayWeight ["ctxt.keywords_4_".$ln.$md] 	= " 
								match(ctxt.keywords_4_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_4"]." ";

							$orderNrArrayWeight ["ctxt.nodekw_".$ln.$md] 		= " 
								match(ctxt.nodekw_".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["nkw"]." ";


							$orderNrArrayWeight ["c.title".$ln] 				= " 
								match(c.title".$ln.") 				against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ti"]." ";

							$orderNrArrayWeight ["c.description".$ln.$md] 	= " 
								match(c.description".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ab"]." ";	 

							$orderNrArrayWeight ["c.doc_source".$ln] 			= " 
								match(c.doc_source".$ln.") 			against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["so"]." ";

							$orderNrArrayWeight ["c.source_author".$ln] 		= " 
								match(c.source_author".$ln.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["au"]." ";

							$orderNrArrayWeight ["ctxt.context".$ln.$md] 		= " 
								match(ctxt.context".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["txt"]." ";

							$this->orderNrArrayWeight = $orderNrArrayWeight;


							$mtchExct["c.title".$ln]			= "match(c.title".$ln.")			against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.description".$ln.$md]	= "match(c.description".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.doc_source".$ln]		= "match(c.doc_source".$ln.")		against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.source_author".$ln]	= "match(c.source_author".$ln.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.context".$ln.$md]	= "match(ctxt.context".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$mtchExct["ctxt.keywords_1_".$ln.$md]= "match(ctxt.keywords_1_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.keywords_2_".$ln.$md]= "match(ctxt.keywords_2_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.keywords_3_".$ln.$md]= "match(ctxt.keywords_3_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$mtchExct["ctxt.keywords_4_".$ln.$md]= "match(ctxt.keywords_4_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.nodekw_".$ln.$md]	 = "match(ctxt.nodekw_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$this->mtchExct = $mtchExct;
							
						/*	echo "<textarea>";
							print_r($this->mtchExct);
							echo "</textarea>";*/
		
		
		
		} else {
			
					$rs_cnt = WebApp::execQuery($getCnt);
					$this->cntX =$getCnt;
			

					IF($rs_cnt->Field("ct") > 0) {

						$this->CountItems = $rs_cnt->Field("ct");
						$tt = stripslashes($termFromSearchModule);
						$tt = preg_replace("#\/|\;|\:|<|>|\[|\{|\]|\}|\.|\?|'|\"|-|\(|]|\)|\,#is", "", $termFromSearchModule, -1);
						$tt = trim($tt);

						if ($this->sql_type == "default") {				
							$order_nr_array []= "
							match (ctxt.keywords_1_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_1"]." ";
							$order_nr_array []= "
							match (ctxt.keywords_2_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_2"]." ";
							$order_nr_array []= "
							match (ctxt.keywords_3_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_3"]." ";
							$order_nr_array []= "
							match (ctxt.keywords_4_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_4"]." ";
							$order_nr_array []= "
							match (ctxt.nodekw_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["nkw"]." ";
							$order_nr_array []= "
							match (c.title".$ln.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ti"]." ";
							$order_nr_array []= "
							match (c.description".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ab"]." ";
							$order_nr_array []= "
							match (c.doc_source".$ln.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["so"]." ";
							$order_nr_array []= "
							match (c.source_author".$ln.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["au"]." ";
							$order_nr_array []= "
							match (ctxt.context".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["txt"]." ";

							$order_nr  = implode(" + ",$order_nr_array);
							//WebApp::addVar("order_nr",$order_nr);
							$this->order_nr_search = $order_nr;	
						} else {

							$orderNrArrayWeight ["ctxt.keywords_1_".$ln.$md]= " 
								match(ctxt.keywords_1_".$ln.$md.") against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_1"]."
								";

							$orderNrArrayWeight ["ctxt.keywords_2_".$ln.$md] 	= " 
								match(ctxt.keywords_2_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_2"]." ";

							$orderNrArrayWeight ["ctxt.keywords_3_".$ln.$md] 	= " 
								match(ctxt.keywords_3_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_3"]." ";

							$orderNrArrayWeight ["ctxt.keywords_4_".$ln.$md] 	= " 
								match(ctxt.keywords_4_".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["kw_4"]." ";

							$orderNrArrayWeight ["ctxt.nodekw_".$ln.$md] 		= " 
								match(ctxt.nodekw_".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["nkw"]." ";


							$orderNrArrayWeight ["c.title".$ln] 				= " 
								match(c.title".$ln.") 				against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ti"]." ";

							$orderNrArrayWeight ["c.description".$ln.$md] 	= " 
								match(c.description".$ln.$md.") 	against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["ab"]." ";	 

							$orderNrArrayWeight ["c.doc_source".$ln] 			= " 
								match(c.doc_source".$ln.") 			against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["so"]." ";

							$orderNrArrayWeight ["c.source_author".$ln] 		= " 
								match(c.source_author".$ln.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["au"]." ";

							$orderNrArrayWeight ["ctxt.context".$ln.$md] 		= " 
								match(ctxt.context".$ln.$md.") 		against ('".ValidateVarFun::f_real_escape_string($tt)."') ".$this->SWO." ".$this->SW["txt"]." ";

							$this->orderNrArrayWeight = $orderNrArrayWeight;
							$mtchExct["c.title".$ln]			= "match(c.title".$ln.")			against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.description".$ln.$md]	= "match(c.description".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.doc_source".$ln]		= "match(c.doc_source".$ln.")		against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["c.source_author".$ln]	= "match(c.source_author".$ln.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.context".$ln.$md]	= "match(ctxt.context".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$mtchExct["ctxt.keywords_1_".$ln.$md]= "match(ctxt.keywords_1_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.keywords_2_".$ln.$md]= "match(ctxt.keywords_2_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.keywords_3_".$ln.$md]= "match(ctxt.keywords_3_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$mtchExct["ctxt.keywords_4_".$ln.$md]= "match(ctxt.keywords_4_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";
							$mtchExct["ctxt.nodekw_".$ln.$md]	 = "match(ctxt.nodekw_".$ln.$md.")	against ('\"".ValidateVarFun::f_real_escape_string($tt)."\"' IN BOOLEAN MODE) ";

							$this->mtchExct = $mtchExct;
						}

						$this->error_code = 1;
						$this->CreateLimitToSql();				
						$this->CreateAllDataToDisplay();
				} else {
					$this->error_code = 2;
					$this->CountItems =0;
				}
			}
		}
		
	}
	function highlightWords ($input_words) {	
		
		//$input_words = preg_replace ($this->singleWordsHighlightPattern, $this->singleWordsHighlight, $input_words,-1);
		return $input_words;
	}	
	
	
	
	function CreateAllDataCategorizationByTypeToDisplayAjax($typeRestiction="",$tab_index,$level_0="",$includeOrExludeDocTypesGigen="include") {

			global $session;
			
			$nrRes = $mainGridData["AllRecs"];
			
			$union_sql_data_array =array();	
			$union_sql_data_array ["title"] ="
						(
						SELECT  c.content_id, 
									".$this->mtchExct["c.title".$this->ln_search]." as weightUsedToSortEx,
									".$this->orderNrArrayWeight["c.title".$this->ln_search]." as weightUsedToSort,
									0 as weightUsedToSortKW,
									0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.title".$this->ln_search]."
						)
			";	
			
			
			$union_sql_data_array ["description"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.description".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.description".$this->ln_search.$this->md_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.description".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["doc_source"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.doc_source".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.doc_source".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.doc_source".$this->ln_search]."
						)
			";	
			
			$union_sql_data_array ["source_author"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.source_author".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.source_author".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.source_author".$this->ln_search]."
						)
			";	



			$union_sql_data_array ["nodekw"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort, 
						".$this->orderNrArrayWeight["ctxt.nodekw_".$this->ln_search.$this->md_search]." as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.nodekw_".$this->ln_search.$this->md_search]."
						)
			";			
			$union_sql_data_array ["keywords_1"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_1_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_1_".$this->ln_search.$this->md_search]."
						)
			";
			
			$union_sql_data_array ["keywords_2"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_2_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_2_".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["keywords_3"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_3_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_3_".$this->ln_search.$this->md_search]."
						)
			";				

			$union_sql_data_array ["keywords_4"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_4_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_4_".$this->ln_search.$this->md_search]."
						)
			";				
			

			
			$union_sql_data_array ["context"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						0 as weightUsedToSortKW,
						".$this->orderNrArrayWeight["ctxt.context".$this->ln_search.$this->md_search]." as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.context".$this->ln_search.$this->md_search]."
						)
			";				

			$union_sql_main_part = implode(" UNION ALL ",$union_sql_data_array);

			
	//	$includeOrExludeDocTypesGigen="include") {	exludeDocTypes $includeInTypesGiven		
			$typeRestrictionSql = "";
			if ($typeRestiction!="") {
			
				if ($includeOrExludeDocTypesGigen=="exludeDocTypes")
					$typeRestrictionSql = " AND ci_type not in (".$typeRestiction.")";
				else
					$typeRestrictionSql = " AND ci_type in (".$typeRestiction.")";
			}
			
			$levelRestrictionSql = "";
			if ($level_0!="") {
			
				$levelRestrictionSql = " AND c.id_zeroNivel = '".$level_0."'";
			} else {
						
				$levelRestrictionSql = " AND c.id_zeroNivel in ('".$session->Vars["level_0"]."','".ZONE_AUTHORING."')";
			}
		//, $session->Vars["level_0"]	
			$this->limitToSql = "limit 0,100";
			$cnt_data_get_documents = "	

				SELECT content_id, sum(weightUsedToSortEx) as weightUsedToSortEx, sum(weightUsedToSort) as weightUsedToSortTot, sum(weightUsedToSortKW) as weightUsedToSortKWTot, sum(weightUsedToSortL) as weightUsedToSortLTot
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 
				 				SELECT distinct c.content_id
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
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			  AND c.searchable = 'Y'
				 			  ".$levelRestrictionSql.$typeRestrictionSql."
								  ".$this->stateCondition_search."
								  ".$this->expire_condition_search."
				  )

				GROUP BY content_id
				  ORDER BY weightUsedToSortEx DESC, weightUsedToSortTot DESC, weightUsedToSortKWTot DESC, weightUsedToSortLTot desc 				
					".$this->limitToSql."			  
				  ";		
/*
								  
					 		UNION DISTINCT
				 				
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							
				 							
				 							JOIN	content			AS c	ON (    p.content_id  = c.content_id)


				 							JOIN	nivel_4			AS n4	ON (    c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 																)
*/

			$rs_list = WebApp::execQuery($cnt_data_get_documents);
			
			/*echo "<textarea>";
			print_r($rs_list);
			echo "</textarea>";*/
			
			
			
			$this->cnt_data_get_documents =$cnt_data_get_documents;
			$iorder = 1;
			
			$mainGridData = array();
			$doc_id_set = array();
			$ind=0;
			while (!$rs_list->EOF())  {
				$idX		= $rs_list->Field("content_id");
				$mainGridData["data"][$ind]["CIID"] = $idX;
				$doc_id_set[$ind]= $idX;
				$ind++;
				$rs_list->MoveNext();
			}	
	
			$mainGridData["AllRecs"] = count($mainGridData["data"]);	
			$nrRes = $mainGridData["AllRecs"];
			
			$this->findCisExtendedInformation($doc_id_set);
			
			$keyGrid = preg_replace("#,#","_",$tab_index);
			WebApp::addVar("categorizedByTypeResults_$keyGrid", $mainGridData);
			$this->ln_searchID = SUBSTR($this->ln_search, -1);
			$this->thisMode = $session->Vars["thisMode"];
		
	
		return $nrRes;		
	}	
	
	
	function CreateAllDataToDisplayAjax($nodes_restriction_to_sql,$nodes_restriction_to_sql_ECC,$tab_index) {

			global $session;
			
			$nrRes = $mainGridData["AllRecs"];
			
			$union_sql_data_array =array();	
			$union_sql_data_array ["title"] ="
						(
						SELECT  c.content_id, 
									".$this->mtchExct["c.title".$this->ln_search]." as weightUsedToSortEx,
									".$this->orderNrArrayWeight["c.title".$this->ln_search]." as weightUsedToSort,
									0 as weightUsedToSortKW,
									0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.title".$this->ln_search]."
						)
			";	
			
			
			$union_sql_data_array ["description"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.description".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.description".$this->ln_search.$this->md_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.description".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["doc_source"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.doc_source".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.doc_source".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.doc_source".$this->ln_search]."
						)
			";	
			
			$union_sql_data_array ["source_author"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.source_author".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.source_author".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.source_author".$this->ln_search]."
						)
			";	



			$union_sql_data_array ["nodekw"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort, 
						".$this->orderNrArrayWeight["ctxt.nodekw_".$this->ln_search.$this->md_search]." as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.nodekw_".$this->ln_search.$this->md_search]."
						)
			";			
			$union_sql_data_array ["keywords_1"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_1_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_1_".$this->ln_search.$this->md_search]."
						)
			";
			
			$union_sql_data_array ["keywords_2"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_2_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_2_".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["keywords_3"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_3_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_3_".$this->ln_search.$this->md_search]."
						)
			";				

			$union_sql_data_array ["keywords_4"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_4_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_4_".$this->ln_search.$this->md_search]."
						)
			";				
			

			
			$union_sql_data_array ["context"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						0 as weightUsedToSortKW,
						".$this->orderNrArrayWeight["ctxt.context".$this->ln_search.$this->md_search]." as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.context".$this->ln_search.$this->md_search]."
						)
			";				

			$union_sql_main_part = implode(" UNION ALL ",$union_sql_data_array);
			
			$this->limitToSql = "limit 0,5";
			$cnt_data_get_documents = "	

				SELECT content_id, sum(weightUsedToSortEx) as weightUsedToSortEx, sum(weightUsedToSort) as weightUsedToSortTot, sum(weightUsedToSortKW) as weightUsedToSortKWTot, sum(weightUsedToSortL) as weightUsedToSortLTot
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 
				 				SELECT distinct c.content_id
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
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			  ".$nodes_restriction_to_sql."
				 			  AND c.searchable = 'Y'
								  ".$this->stateCondition_search."
								  ".$this->expire_condition_search."
								  
								  
					 		UNION DISTINCT
				 				
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							
				 							
				 							JOIN	content			AS c	ON (    p.content_id  = c.content_id)


				 							JOIN	nivel_4			AS n4	ON (    c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 																)
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			  ".$nodes_restriction_to_sql_ECC."
				 			  AND c.searchable = 'Y'
				 				  ".$this->stateCondition_search."
				 				  ".$this->expire_condition_search."									  
								  
								  
				  )

				GROUP BY content_id
				  ORDER BY weightUsedToSortEx DESC, weightUsedToSortTot DESC, weightUsedToSortKWTot DESC, weightUsedToSortLTot desc 				
					".$this->limitToSql."			  
				  ";		



			$rs_list = WebApp::execQuery($cnt_data_get_documents);
			$this->cnt_data_get_documents =$cnt_data_get_documents;
			$iorder = 1;
			
			$mainGridData = array();
			$doc_id_set = array();
			$ind=0;
			while (!$rs_list->EOF())  {
				$idX		= $rs_list->Field("content_id");
				$mainGridData["data"][$ind]["CIID"] = $idX;
				$doc_id_set[$ind]= $idX;
				$ind++;
				$rs_list->MoveNext();
			}	
	
			$mainGridData["AllRecs"] = count($mainGridData["data"]);	
			$nrRes = $mainGridData["AllRecs"];
			
			$this->findCisExtendedInformation($doc_id_set);
			
			$keyGrid = preg_replace("#,#","_",$tab_index);
			WebApp::addVar("searchResultMinGridAjaxData_$keyGrid", $mainGridData);
			$this->ln_searchID = SUBSTR($this->ln_search, -1);
			$this->thisMode = $session->Vars["thisMode"];
		
	
		return $nrRes;		
	}	
	
	
	function findCisExtendedInformation($doc_id_set) {
	
			global $session, $sessUserObj;

			if (isset($doc_id_set) && is_array($doc_id_set) && count($doc_id_set)>0)
				$valDocIds = implode(",",$doc_id_set);
			else return;
			
			//$sessUserObj->getProgrammeTree();
			//$sessUserObj->POTS = $sessUserObj->programeTreeStructure;
			
			
			$sessUserObj->getOverallProgrammeTree();
			$sessUserObj->constuctStructuredDefaultEcc();
			

/*
			
echo "<textarea>";	
print_r($sessUserObj->pathLectureInfo);
echo "</textarea>";

echo "<textarea>";	
print_r($sessUserObj->POTS);
echo "</textarea>";



echo "<textarea>";	
print_r($sessUserObj->POTS);
echo "</textarea>";


*/

			$listData = array();
			if(defined('ACTIVATE_ESHOP') && ACTIVATE_ESHOP=="Y") {
				$listData =  CiManagerFe::getEshopStoreConfigurationForItems($valDocIds, $this->lngId,$this->userSystemID); //viewed_in_search,	viewed_in_list,	viewed
			} 	
			
			
			
			if(!defined('CI_Favorite') || CI_Favorite=="NO") {		//favorit,popular,none
			} else {

			if(defined('COLLECTOR_PERSONALIZATION') && COLLECTOR_PERSONALIZATION=="YES") {		//favorit,popular,none
					$favoritesCiData = personalization::areCisInUserFavorites($valDocIds,$this->lngId,$this->userSystemID);	//$ci_id, $lang, $uid 
					
				}
			}			

			
			


			$cnt_data = "	
					SELECT DISTINCT  		 
							c.content_id as content_id,coalesce(ecc_reference*1, orderContent) as ordF, ecc_reference,
							c.ci_type as ci_type,
					
							c.title".$this->ln_search." AS title,
							c.filename".$this->ln_search." AS filename,
						
							COALESCE(c.with_https,      'n') as with_https,
							
							if((c.description".$this->ln_search.$this->md_search." IS NULL OR c.description".$this->ln_search.$this->md_search." = ''), '', 
							   IF (CHAR_LENGTH(c.description".$this->ln_search.$this->md_search.") > 320, CONCAT(LEFT(c.description".$this->ln_search.$this->md_search.", 320),'...'), c.description".$this->ln_search.$this->md_search.")) AS description,	
							if((c.doc_source".$this->ln_search." IS NULL 
									OR c.doc_source".$this->ln_search." = ''),'', c.doc_source".$this->ln_search.") 
								AS source,	
							if((c.source_author".$this->ln_search." IS NULL 
									OR c.source_author".$this->ln_search." = ''),'', 
									c.source_author".$this->ln_search.") 
								AS source_author,
							
							IF((c.imageSm_id >0),c.imageSm_id,'') 			as imageSm_id,
							coalesce(c.imageSm_id_name, '') 				as imageSm_id_name,

							IF((c.imageSm_id_mob >0),c.imageSm_id_mob,'') 	as imageSm_id_mob,
							coalesce(c.imageSm_id_mob_name, '') 			as imageSm_id_mob_name,

							IF((c.imageBg_id >0),c.imageBg_id,'') 			as imageBg_id,
							coalesce(c.imageBg_id_name, '') 				as imageBg_id_name,

							IF((c.imageBg_id_mob >0),c.imageBg_id_mob,'') 	as imageBg_id_mob,
							coalesce(c.imageBg_id_mob_name, '') 			as imageBg_id_mob_name,							
							
							c.id_zeroNivel as n0v, 
							c.id_firstNivel as n1v, 
							c.id_secondNivel as n2v, 
							c.id_thirdNivel as n3v, 
							c.id_fourthNivel as n4v,
							
								coalesce(ecc_reference, '') 		as ecc_reference, 				
								coalesce(reference, '') 			as reference,
								coalesce(reference_format, '') 		as reference_format,
								
								coalesce(identifier_key, '') 		as identifier_key,
								coalesce(identifier_type, '') 		as identifier_type,
								
								coalesce(doc_id, '') 				as doc_id,
								coalesce(category_kw_id, '') 		as category_kw_id,
                                coalesce(category_kw_id_extra, '') 	as category_kw_id_extra,
								coalesce(doc_type, '') 				as doc_type,							
							
							IF(scheduling_from IS NULL,'', Date_Format(scheduling_from, '%d.%m.%Y')) 
								AS scheduling_from,
															
			coalesce(z_EccE_availability_information.parent_ci_pr,'') as parent_ci_pr,
			coalesce(z_EccE_availability_information.parent_ci_ec,'') as parent_ci_ec,
			coalesce(z_EccE_availability_information.parent_ci_el,'') as parent_ci_el,
			coalesce(z_EccE_availability_information.parent_ci_tc,'') as parent_ci_tc,
							
			coalesce(z_EccE_availability_information.availability_period_type,'') as availability_period_type,
			coalesce(z_EccE_availability_information.availability_from,'') as availability_from,
			coalesce(z_EccE_availability_information.availability_to,'') as availability_to,
							
			coalesce(z_EccE_availability_information.registration_period_type,'') as registration_period_type,
			coalesce(z_EccE_availability_information.registration_from,'') as registration_from,
			coalesce(z_EccE_availability_information.registration_to,'') as registration_to
			
		FROM content as c 
  		 						
			   LEFT JOIN ci_elearning_extended on c.content_id = ci_elearning_extended.content_id 
					 AND ci_elearning_extended.lng_id = '".$this->lngId."' AND ci_elearning_extended.statusInfo = '1'
  		 						
			   LEFT JOIN z_EccE_availability_information on c.content_id = z_EccE_availability_information.contentId 


				   WHERE c.content_id in (".$valDocIds.")";		
	
		$rs = WebApp::execQuery($cnt_data);		

		$ln = $session->Vars["lang"];
		while (!$rs->EOF())  {

			$idX 				= $rs->Field("content_id");
			$item_ci_id			= $idX;
			$scheduling_from 	= $rs->Field("scheduling_from");
			$item_ci_type		= $rs->Field("ci_type");
			
			$parent_ci_pr = $rs->Field("parent_ci_pr");
			$parent_ci_ec = $rs->Field("parent_ci_ec");
			$parent_ci_el = $rs->Field("parent_ci_el");
		//	$parent_ci_tc = $rs->Field("parent_ci_tc");

			$lev4 = $rs->Field("n4v");
			$lev3 = $rs->Field("n3v");
			$lev2 = $rs->Field("n2v");
			$lev1 = $rs->Field("n1v");
			$lev0 = $rs->Field("n0v");
		
/*
    [PR_coord_r] => Array
        (
            [3_2] => 180
            [3_3] => 2168
            [3_4] => 2385
            [3_5] => 2868
        ) 
        $sessUserObj->POTS["ci"][$cid]["ND"]
        $sessUserObj->POTS["ci"][$cid]["ND"]


                    ["ND"] => Postgraduate Spine Surgery Education Programme
                    [type_of_programe] => programe
                    
                    
                   $sessUserObj->POTS["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3]
                    
                    
                    
                    

*/		


					
			$hrefToDocTarget = "";
			$scheduling_from = $listData[$id]["scheduling_from"];
			$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$idX.")');";
			$description = 	$listData[$id]["description"];

			if ($global_cache_dynamic == "Y") {
					$hrefTo = $cacheDyn->get_CiTitleToUrl($idX, $this->ln_searchID, $titleCI, $filenameCI,"",$with_httpsCI);
			} elseif (defined("Caching_Metatags") AND (Caching_Metatags == "Y")) {
				if ($application_is_cached=="yes") {
					$destination= $cache->CgetCachedName($idX);
					if ($destination!="undefined") 
						$hrefTo = APP_URL.$cache->CACHE_PHP_PATH.$destination.".php";
				}
			}				
			
			if ($lev0!=$session->Vars["level_0"] && ($item_ci_type=="EL" || $item_ci_type=="EC" || $item_ci_type=="EL")) {
				$hrefTo = $this->URLS_EXTENDED["details_lecture"];
				
				
				
				


/*


					$this->URLS_EXTENDED["details_lecture"] = "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".LECTURE_DETAILS.";idElC={{CID}})');";
					if (isset($sessUserObj->tp_specific_container) && $sessUserObj->tp_specific_container!="") {
						$targetDetails 	= str_replace("k=","",$sessUserObj->tp_specific_container);
						//$this->URLS_EXTENDED["details_course"] 	= "javascript:GoTo('thisPage?event=none.ch_state(kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$targetDetails.";idElC={{CID}})');";
						$this->URLS_EXTENDED["details_lecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$targetDetails.";idElC={{CID}})');";
					} 			




define("PROGRAME_DETAILS_0",		"4833");
define("BUNDLE_DETAILS_0",			"4837");
define("CURRENT_TOPICS_DETAILS_0",	"4836");

define("COURSE_DETAILS_0",			"4834");
define("LECTURE_DETAILS_0",			"4835");


                    [type_of_programe] => programe
                    [type_of_programe] => oot_boundle
                    [type_of_programe] => boundle
                    [type_of_programe] => current_topic




*/
				
				
				
				
				
			}
			




		
			$listData[$idX]["is_favorites"]	 	= $favoritesCiData[$idX]["is_favorites"];

			$fullPathInfo = array();
			$listData[$idX]["full_path_lecture"] 		= "";
			
			$zoneRef = $session->Vars["level_0"];
			
			if ($item_ci_type=="PR") { 

				
				$constandCiIncludeCoord = "";
				if (isset($sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1])) {
					$cid = $sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					
					
					$constandCiIncludeCoord = "";
					
					if (isset($sessUserObj->POTS["ci"][$cid]["type_of_programe"]) && $session->Vars["level_0"] != $lev0) {
						
						$typeOfProgram = $sessUserObj->POTS["ci"][$cid]["type_of_programe"];
						
						
						
						if ($typeOfProgram=="programe") {
						
							if(defined("PROGRAME_DETAILS_$zoneRef") && constant("PROGRAME_DETAILS_$zoneRef")>0) {
								$constandCiIncludeCoord = constant("PROGRAME_DETAILS_$zoneRef");
							} elseif(defined("PROGRAME_DETAILS") && constant("PROGRAME_DETAILS")>0) {
								$constandCiIncludeCoord = constant("PROGRAME_DETAILS");
							} 
						} elseif ($typeOfProgram=="oot_boundle") {	
							
							if(defined("OOT_BOUNDLE_DETAILS_$zoneRef") && constant("OOT_BOUNDLE_DETAILS_$zoneRef")>0) {
								$constandCiIncludeCoord = constant("OOT_BOUNDLE_DETAILS_$zoneRef");
							} elseif(defined("OOT_BOUNDLE_DETAILS") && constant("OOT_BOUNDLE_DETAILS")>0) {
								$constandCiIncludeCoord = constant("OOT_BOUNDLE_DETAILS");
							} 
						}elseif ($typeOfProgram=="boundle") {	
							
							if(defined("BUNDLE_DETAILS_$zoneRef") && constant("BUNDLE_DETAILS_$zoneRef")>0) {
								$constandCiIncludeCoord = constant("BUNDLE_DETAILS_$zoneRef");
							} elseif(defined("BUNDLE_DETAILS") && constant("BUNDLE_DETAILS")>0) {
								$constandCiIncludeCoord = constant("BUNDLE_DETAILS");
							} 
						} elseif ($typeOfProgram=="current_topic") {	
							
							if(defined("CURRENT_TOPICS_DETAILS_$zoneRef") && constant("CURRENT_TOPICS_DETAILS_$zoneRef")>0) {
								$constandCiIncludeCoord = constant("CURRENT_TOPICS_DETAILS_$zoneRef");
							} elseif(defined("CURRENT_TOPICS_DETAILS") && constant("CURRENT_TOPICS_DETAILS")>0) {
								$constandCiIncludeCoord = constant("CURRENT_TOPICS_DETAILS");
							} 
						}
					
					}
				}
				
				
				
				
				
				if ($constandCiIncludeCoord!="") {
					$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$constandCiIncludeCoord.";idElC={{CID}})');";
				}

			} elseif ($item_ci_type=="EC") { 

				if (isset($sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1])) {
					$cid = $sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				}

				
				if (isset($sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2])) {
					$cid = $sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				}			
			
				$constandCiIncludeCoord = "";
				if ($session->Vars["level_0"] != $lev0) {
					if(defined("COURSE_DETAILS_$zoneRef") && constant("COURSE_DETAILS_$zoneRef")>0) {
						 $constandCiIncludeCoord = constant("COURSE_DETAILS_$zoneRef");
					} elseif(defined("COURSE_DETAILS") && constant("COURSE_DETAILS")>0) {
						$constandCiIncludeCoord = constant("COURSE_DETAILS");
					} 				
				}
				if ($constandCiIncludeCoord!="") {
						$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$constandCiIncludeCoord.";idElC={{CID}})');";
				}
			
			
			} elseif (in_array($item_ci_type,$sessUserObj->pathLectureInfo["lectureAllowedTypes"])) {
				//$fullPathInfo[] = $sessUserObj->POTS[$parent_ci_pr]["title"];


				if (isset($sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1])) {
					$cid = $sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				}
				
				if (isset($sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2])) {
					$cid = $sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				}
				
				if (isset($sessUserObj->POTS["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3])) {
					$cid = $sessUserObj->POTS["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				}	
				
				if ($item_ci_type=="EL") { 
					$constandCiIncludeCoord = "";
					if ($session->Vars["level_0"] != $lev0) {
						if(defined("LECTURE_DETAILS_$zoneRef") && constant("LECTURE_DETAILS_$zoneRef")>0) {
							$constandCiIncludeCoord = constant("LECTURE_DETAILS_$zoneRef");
						} elseif(defined("LECTURE_DETAILS") && constant("LECTURE_DETAILS")>0) {
							$constandCiIncludeCoord = constant("LECTURE_DETAILS");
						} 				
					}
					if ($constandCiIncludeCoord!="") {
							$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$constandCiIncludeCoord.";idElC={{CID}})');";
					}				
				
				} 
			
			} elseif ($item_ci_type=="NI" || $item_ci_type=="CM") {
			
				
				if (isset($sessUserObj->programeTreeStructure["TC"][$lev0][$lev1][$lev2][$lev3])) {
					
					$cid = $sessUserObj->programeTreeStructure["TC"][$lev0][$lev1][$lev2][$lev3];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					
					if (isset($sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2])) {
						$cid = $sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2];
						$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					}					
				
				} elseif (isset($sessUserObj->POTS["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3])) {
					
					if (isset($sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2])) {
						$cid = $sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2];
						$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					}					
				
					$cid = $sessUserObj->POTS["EL"][$lev0."_".$lev1."_".$lev2."_".$lev3];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];				
				
				} elseif (isset($sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2])) {
				
					$cid = $sessUserObj->POTS["EC"][$lev0."_".$lev1."_".$lev2];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					
				} elseif (isset($sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1])) {
				
					$cid = $sessUserObj->POTS["PR_coord_r"][$lev0."_".$lev1];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
					
				} elseif (isset($sessUserObj->programeTreeStructure["TT"][$lev0][$lev1])) {
					$cid = $sessUserObj->programeTreeStructure["TT"][$lev0][$lev1];
					$fullPathInfo[] = $sessUserObj->POTS["ci"][$cid]["ND"];
				} 			
			}

			$listData[$idX]["hrefToDoc"] = $hrefTo;
			$listData[$idX]["hrefToDocTarget"] = $hrefToDocTarget;			
			
			if (count($fullPathInfo)>0)
			$listData[$idX]["full_path_lecture"]	= implode(" / ",$fullPathInfo);

			$listData[$idX]["CID"] 			= $idX;
			$listData[$idX]["id"] 			= $idX;
			
			$titleCI 			= $rs->Field("title");
			$filenameCI 		= $rs->Field("filename");
			$with_httpsCI 		= $rs->Field("with_https");
			
			$listData[$idX]["title"] 		= $rs->Field("title");
			$listData[$idX]["ci_type"] 		= $rs->Field("ci_type");
			
			$listData[$idX]["description"] 		= $rs->Field("description");
			$listData[$idX]["source"] 			= trim($rs->Field("source"));
			$listData[$idX]["source_author"] 	= trim($rs->Field("source_author"));

			if ($scheduling_from=='00.00.0000')	$scheduling_from = "";
			$listData[$idX]["scheduling_from"] = $scheduling_from;

			//($coord,$ci_type,$cidFlow)

			$koord_level_node 	 	 = "$lev0,$lev1,$lev2,$lev3,$lev4";
			$listData[$idX]["koord_level_node"] = "$lev0,$lev1,$lev2,$lev3,$lev4";

			$listData[$idX]["dp_linkNivelLabel"] 	= "no";
			$listData[$idX]["dp_labelUserDefinedLink"] = "no";
	
					
			$hrefToDocTarget = "";
			$scheduling_from = $listData[$idX]["scheduling_from"];
			$hrefTo = "javascript:GoTo('thisPage?event=none.ch_state(k=".$idX.")');";
			$description = 	$listData[$idX]["description"];

			if ($global_cache_dynamic == "Y") {
				$hrefTo = $cacheDyn->get_CiTitleToUrl($idX, $this->ln_searchID, $titleCI, $filenameCI,"",$with_httpsCI);
			} elseif (defined("Caching_Metatags") AND (Caching_Metatags == "Y")) {
				if ($application_is_cached=="yes") {
						$destination= $cache->CgetCachedName($idX);
						if ($destination!="undefined") 
							$hrefTo = APP_URL.$cache->CACHE_PHP_PATH.$destination.".php";
				}
			}		

			$titleToDisplay = $listData[$idX]["title"];
			$listData[$idX]["titleToAlt"] = $titleToDisplay;
			$titleToDisplay =  $this->highlightWords($titleToDisplay);
			$listData[$idX]["titleToDisplay"] = $titleToDisplay;
			$listData[$idX]["ew_title"] =$listData[$idX]["title"];
		
			$listData[$idX]["linkToTitle"] = "no";
			if ($this->param["linkToTitle"]=="yes") 
				$listData[$idX]["linkToTitle"] = "yes";

			$listData[$idX]["dp_abst"] = "no";
			if ($description!="" && $this->param["display_abstract"]=="yes") {
				
				$description = 	$listData[$idX]["description"];
				$description = strip_tags($description);
				$description =  $this->highlightWords($description);
				$listData[$idX]["abstractToDisplay"] = $description;
				$listData[$idX]["dp_abst"] = "yes";
			}
			
		//	$listData[$idX]["exist_abst"] = $listData[$idX]["dp_abst"];
			
			
			$listData[$idX]["dp_date"] = "no";
			if ($scheduling_from!="" && $this->param["display_date"]=="yes") {
					$listData[$idX]["dateToDisplay"] = $scheduling_from;
					$listData[$idX]["dp_date"] = "yes";
					
			} 
			$listData[$idX]["dp_source"] = "no";
			if ($listData[$idX]["source"]!="" && $this->param["display_source"]=="yes") {
					$listData[$idX]["sourceToDisplay"] = $listData[$idX]["source"];
					$listData[$idX]["dp_source"] = "yes";
			}  else 	$listData[$idX]["sourceToDisplay"] = "";			
			
			$listData[$idX]["dp_author"] = "no";
			if ($listData[$idX]["source_author"]!="" ) { //&& $this->param["display_author"]=="yes"
					$listData[$idX]["AuthorToDisplay"] = $listData[$idX]["source_author"];
					$listData[$idX]["dp_author"] = "yes";
			} else 	$listData[$idX]["AuthorToDisplay"] ="";
			
			$listData[$idX]["ew_source_author"] = $listData[$idX]["AuthorToDisplay"];
			
			$listData[$idX]["dp_image"] = "no";
			$listData[$idX]["linkToimage"] = "no";
			
			IF ($mob_web == "mob") {
				
				$imageSm_id = $rs->Field("imageSm_id_mob");
				$imageBg_id = $rs->Field("imageBg_id_mob");	
				$imageSm_id_name = $rs->Field("imageSm_id_mob_name");
				$imageBg_id_name = $rs->Field("imageBg_id_mob_name");	
				if ($imageSm_id>0) {
					$listData[$idX]["image_rep"] = $imageSm_id;
					$listData[$idX]["imgIDName"] = $imageSm_id_name;
				} elseif ($imageBg_id>0) {
					$listData[$idX]["image_rep"] = $imageBg_id;
					$listData[$idX]["imgIDName"] = $imageBg_id_name;
				}
			} ELSE {
				$imageSm_id = $rs->Field("imageSm_id");
				$imageBg_id = $rs->Field("imageBg_id");
				$imageSm_id_name = $rs->Field("imageSm_id_name");
				$imageBg_id_name = $rs->Field("imageBg_id_name");
				if ($imageSm_id>0) {
					$listData[$idX]["image_rep"] = $imageSm_id;
					$listData[$idX]["imgIDName"] = $imageSm_id_name;
				} elseif ($imageBg_id>0) {
					$listData[$idX]["image_rep"] = $imageBg_id;
					$listData[$idX]["imgIDName"] = $imageBg_id_name;
				}				
			}
			if ($this->param["display_image"]=="yes") {
				
				
				$img_ci = "";
				if ($listData[$idX]["image_rep"]!="") {
					$img_ci = $listData[$idX]["image_rep"];
				} elseif (isset($this->find_result_to_nodes["img"][$koord_level_node])) {
					$img_ci = $this->find_result_to_nodes["img"][$koord_level_node];
				}
				
				
				if ($img_ci!="") {
					if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") {
						CiManagerFe::get_SL_CACHE_INDEX($img_ci, "","");		
					} else {
						$imageToDisplay = APP_URL."show_image.php?file_id=".$img_ci;
						if ($global_cache_dynamic == "Y") {
								$imageToDisplay = $cacheDyn->get_SlDocTitleToUrl($img_ci, $listData[$idX]["imgIDName"]);
						} else {
								if ($application_is_cached=="yes") {
									$destination= $cache->CgetSlItemCachedName($limg_ci, 0);
									if ($destination!="undefined") 
										$imageToDisplay = $cache->PUBLIC_FILEURL.$destination;
								}
						}		
						$listData[$idX]["srcImageToDisplay"] = $imageToDisplay;
						$listData[$idX]["dp_image"] = "yes";
						if ($this->param["linkToImage"]=="yes") {
							$listData[$idX]["linkToimage"] = "yes";
						}
					}
				}
			}
			
			
			
	$listData[$idX]["ecc_reference"]		= $rs->Field("ecc_reference");	
					$listData[$idX]["reference_format"]		= $rs->Field("reference_format");	
					$listData[$idX]["reference"]			= $rs->Field("reference");
                    
					$listData[$idX]["identifier_key"]		= $rs->Field("identifier_key");	
                    $identifier_key                     = $rs->Field("identifier_key"); 
                    
					$listData[$idX]["identifier_type"]		= $rs->Field("identifier_type");
                    $identifier_type                    = $rs->Field("identifier_type"); 


					$listData[$idX]["selectedPMID"] = "";
					$listData[$idX]["selectedDOI"] = "";
					$listData[$idX]["selectedISBN"] = "";
					$listData[$idX]["selectedISSN"] = "";
					$listData[$idX]["selectedURL"] = "";

					if 	   ( $identifier_type=="PMID")				$listData[$idX]["selectedPMID"] = "selected";
					elseif ( $identifier_type=="DOI")				$listData[$idX]["selectedDOI"] = "selected";
					elseif ( $identifier_type=="ISBN")				$listData[$idX]["selectedISBN"] = "selected";
					elseif ( $identifier_type=="ISSN")				$listData[$idX]["selectedISSN"] = "selected";
					elseif ( $identifier_type=="URL")          	 	$listData[$idX]["selectedURL"] = "selected";				
					
					
					$ecc_doc_id =  $rs->Field("doc_id");	
					$listData[$idX]["ecc_doc_id"]			= $ecc_doc_id;	
					
					
					
					$listData[$idX]["ecc_doc_type"]			= $rs->Field("doc_type");	
					$listData[$idX]["category_kw_id"]		= $rs->Field("category_kw_id");	
                    $listData[$idX]["category_kw_id_extra"]	= $rs->Field("category_kw_id_extra");	
					
					if ($listData[$idX]["category_kw_id"]!="") {
						$tmp = CiManagerFe::getEccCat($listData[$idX]["category_kw_id"],$this->lngId);
						$listData[$idX] = array_merge($listData[$idX], $tmp);
					}
                    if ($listData[$idX]["category_kw_id_extra"]!="") {
						$tmp = CiManagerFe::getEccCatRel($listData[$idX]["category_kw_id_extra"],$this->lngId);
						$listData[$idX] = array_merge($listData[$idX], $tmp);
					}
					
                    
					if (trim($listData[$idX]["reference_format"]) =="")
						 $listData[$idX]["exist_reference_format"] = "";
					else $listData[$idX]["exist_reference_format"] = "yes";					
					
					if (isset($listData[$idX]["ecc_doc_id"]) && $listData[$idX]["ecc_doc_id"]>0) {
						$ecc_doc_id = $listData[$idX]["ecc_doc_id"];
						$slDataToGlobalVar = CiManagerFe::get_SL_CACHE_INDEX($ecc_doc_id);
						$sl_file_nedded_data["data"][$ecc_doc_id] = $slDataToGlobalVar;						
					}
					
					$listData[$idX]["link_url"] 		= "javascript:;";
					$listData[$idX]["stream_url"] 		= "javascript:;";
					$listData[$idX]["extension"] 		= "ref";
					$listData[$idX]["ico_type"] 		= "ref";
					$listData[$idX]["allowed_preview"] 	= "no";
					$listData[$idX]["allowed_download"] = "no";

					if (isset($sl_file_nedded_data["data"][$ecc_doc_id]["ico_type"])) {
						
						$listData[$idX]["ico_type"] 				= $sl_file_nedded_data["data"][$ecc_doc_id]["ico_type"];
						$listData[$idX]["extension"] 				= $sl_file_nedded_data["data"][$ecc_doc_id]["extension"];
						$listData[$idX]["link_url"] 				= $sl_file_nedded_data["data"][$ecc_doc_id]["link_url"];
						$listData[$idX]["stream_url"] 				= $sl_file_nedded_data["data"][$ecc_doc_id]["stream_url"];
						$listData[$idX]["allowed_download"] = "yes";
						
						if (
								$listData[$idX]["extension"]=="pdf" 
							 || $listData[$idX]["extension"]=="mp4"
							 || $listData[$idX]["extension"]=="mp3"
							 || $listData[$idX]["ico_type"]=="image"
							 || $listData[$idX]["ico_type"]=="html"
						) {
							$listData[$idX]["allowed_preview"] = "yes";
						} 						
						
						$tmpRef["file_info"]["ico_type"] 		= $docInfo["ico_type"];					
						if ($listData[$idX]["ico_type"]=="video") {
							$getNr = "SELECT count(1) as exist_virtual_slide_id
										 FROM ci_elearning_virtual_slide 
										WHERE content_id = '".$this->idci."' 
										  AND lng_id = '".$this->lngId."' 
										  AND statusInfo in (0)
									 ORDER BY virtual_slide_order";		
							$rsNr=WebApp::execQuery($getNr);		
							IF (!$rsNr->EOF() AND mysql_errno() == 0) {
								$nrVirtualSlide	= $rsNr->Field("exist_virtual_slide_id");
								if ($nrVirtualSlide>=1) 
									$listData[$idX]["ico_type"] = "virtual-slide";
								$listData[$idX]["allowed_preview"] = "yes";
							}		
						}					
					} else {
						if (($identifier_type=="PMID" || $identifier_type=="URL" ) &&  $identifier_key != "") {
							$listData[$idX]["allowed_preview"] 	= "yes";							
						} 
					}	
						
				
			

			//$listData[$idX] = array_merge($properties,$listData[$idX]);

			$tmpInfoArray["list"]["CI_DATA"][$item_ci_id] = $listData[$item_ci_id];
			$tmpInfoArray["groupedType"][$item_ci_type][$item_ci_id] = $item_ci_id;		
			
			$gridDataSrc["data"][0] 	= $listData[$idX];
			$gridDataSrc["AllRecs"] 	= 1;		
			WebApp::addVar("listRow_".$idX,$gridDataSrc);			
		
			$rs->MoveNext();
		}


	/*	echo "HERE-$valDocIds-<textarea>";	
		print_r($listData);
		echo "</textarea>";	*/

		
		return;
		
		
		
		if ($this->thisMode =="_new") $this->thisModeCode = "0";
		else						  $this->thisModeCode = "1";
		
		$this->display_details["extended"] = "yes";
	//	if (isset($this->display_details["extended"]) || isset($this->display_details["content"])) {
			
			if (isset($tmpInfoArray["groupedType"]) && count($tmpInfoArray["groupedType"])>0) {
				
				while (list($ci_type_grouped,$ci_type_ids)=each($tmpInfoArray["groupedType"])) {

					$idsCiGroupedByType = implode(",",$ci_type_ids);
					$properties_extended = array();
					
					if ($ci_type_grouped=="EI") {
						$properties_extended = CiManagerFe::getEIMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="ED") {
							$properties_extended = CiManagerFe::getEDMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="LK") {
							$properties_extended = CiManagerFe::getLKMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="PI") {
							$properties_extended = CiManagerFe::getPIMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="PH") {
						//$properties_extended = CiManagerFe::getPHMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="PL") {
						$properties_extended = CiManagerFe::getPLMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="LB") {
						$properties_extended = CiManagerFe::getLBMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="EL") {							
						$properties_extended = CiManagerFe::getELMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
						
						
					} elseif ($ci_type_grouped=="EC") {
						$properties_extended = CiManagerFe::getECMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="PR") {
						$properties_extended = CiManagerFe::getPRMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					} elseif ($ci_type_grouped=="RI") {
						$properties_extended = CiManagerFe::getRIMultiProperties($this->thisModeCode, $idsCiGroupedByType,$this->lang,$this->lngId);
					}
	
					if (count($properties_extended)>0) {
						
						reset($properties_extended);
						while (list($ciExtendedID,$ciExtendedIDprop)=each($properties_extended)) {
							
							if ($ci_type_grouped=="EL") {
								
								
								
								if (isset($ciExtendedIDprop["Lecturer_id"]) && $ciExtendedIDprop["Lecturer_id"]>0) {
									$tmpInfoArray["Crms"][$ciExtendedIDprop["Lecturer_id"]] = $ciExtendedIDprop["Lecturer_id"];
									
									
									//if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") 
									//										CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");
									//elseIF ($global_cache_dynamic == "Y")	$linkPath = $cacheDyn->get_SlDocTitleToUrl($ciExtendedIDprop["file_id"], $ciExtendedIDprop["filename"]);
								}	
								
							} elseif ($ci_type_grouped=="ED") {
								if (isset($ciExtendedIDprop["file_id"]) && $ciExtendedIDprop["file_id"]>0) {
									$properties[$content_id]["hrefToOther"]				= $properties["filename"];
									$properties[$content_id]["hrefToDocTargetOther"]	= "_blank";
									$linkPath = $ciExtendedIDprop["link_path"];
									if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") 
																			CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");
									elseIF ($global_cache_dynamic == "Y")	$linkPath = $cacheDyn->get_SlDocTitleToUrl($ciExtendedIDprop["file_id"], $ciExtendedIDprop["filename"]);
									
									$ciExtendedIDprop["hrefToDocTargetOther"] = "_blank";
									$ciExtendedIDprop["hrefToDocOther"]		  = $linkPath;	
								}	
							} elseif ($ci_type_grouped=="EI") {
								if (isset($ciExtendedIDprop["ew_ei_datefrom"]) && $ciExtendedIDprop["ew_ei_datefrom"]!="") {
									$tempDataDOC = explode(".",$ciExtendedIDprop["ew_ei_datefrom"]);
									$this->ew_ei_datefrom[$ciExtendedID]["ew_ei_datefrom"] = $tempDataDOC["2"]."-".$tempDataDOC["1"]."-".$tempDataDOC["0"];
									$this->ew_ei_datefrom[$ciExtendedID]["ew_ei_dateto"] = $tempDataDOC["2"]."-".$tempDataDOC["1"]."-".$tempDataDOC["0"];
								}
								if (isset($ciExtendedIDprop["ew_ei_dateto"]) && $ciExtendedIDprop["ew_ei_dateto"]!="") {
									$tempDataDOC = explode(".",$ciExtendedIDprop["ew_ei_dateto"]);
									$this->ew_ei_datefrom[$ciExtendedID]["ew_ei_dateto"] = $tempDataDOC["2"]."-".$tempDataDOC["1"]."-".$tempDataDOC["0"];
								} 
							} elseif ($ci_type_grouped=="LB") {
								//link_path
								if (isset($ciExtendedIDprop["file_id"]) && $ciExtendedIDprop["file_id"]>0) {

									$properties[$content_id]["hrefToOther"]				= $properties["filename"];
									$properties[$content_id]["hrefToDocTargetOther"]	= "_blank";
										
									$linkPath = $ciExtendedIDprop["link_path"];
									if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") 
																			CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");
									elseIF ($global_cache_dynamic == "Y") 	$linkPath = $cacheDyn->get_SlDocTitleToUrl($ciExtendedIDprop["file_id"], $ciExtendedIDprop["filename"]);

									$ciExtendedIDprop["hrefToDocTargetOther"] = "_blank";
									$ciExtendedIDprop["hrefToDocOther"]		  = $linkPath;	
								}	
								
							} elseif ($ci_type_grouped=="PI") {								
								//link_path
								if (isset($ciExtendedIDprop["file_id"]) && $ciExtendedIDprop["file_id"]>0) {									
										
									$properties[$content_id]["hrefToOther"]				= $properties["filename"];
									$properties[$content_id]["hrefToDocTargetOther"]	= "_blank";
									
									global $sl_file_nedded_data;
									$properties_file= CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");	
										
									$linkPath = $ciExtendedIDprop["link_path"];									
									if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") 
																			CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");
									elseIF ($global_cache_dynamic == "Y") 	$linkPath = $cacheDyn->get_SlDocTitleToUrl($ciExtendedIDprop["file_id"], $ciExtendedIDprop["filename"]);
																		
									$ciExtendedIDprop["hrefToDocTargetOther"] = "_blank";
									$ciExtendedIDprop["hrefToDocOther"]		  = $linkPath;	
								}	
								
							} elseif ($ci_type_grouped=="PP") {								
								//link_path
								if (isset($ciExtendedIDprop["file_id"]) && $ciExtendedIDprop["file_id"]>0) {									
										
									$properties[$content_id]["hrefToOther"]				= $properties["filename"];
									$properties[$content_id]["hrefToDocTargetOther"]	= "_blank";									

									$properties_file= CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");		
									
									$linkPath = $ciExtendedIDprop["link_path"];									
									if(defined('ACTIVATE_SL_CACHE_INDEX') && ACTIVATE_SL_CACHE_INDEX=="Y") 
																			CiManagerFe::get_SL_CACHE_INDEX($ciExtendedIDprop["file_id"], "","");
									elseIF ($global_cache_dynamic == "Y") 	$linkPath = $cacheDyn->get_SlDocTitleToUrl($ciExtendedIDprop["file_id"], $ciExtendedIDprop["filename"]);
																		
									$ciExtendedIDprop["hrefToDocTargetOther"] = "_blank";
									$ciExtendedIDprop["hrefToDocOther"]		  = $linkPath;	
								}	
							} elseif ($ci_type_grouped=="RI") {	
							
								if (isset($ciExtendedIDprop["ecc_reference"]) && $ciExtendedIDprop["ecc_reference"]!="")
									$ciExtendedIDprop["ecc_reference_display"] = "yes";	
								if (isset($ciExtendedIDprop["reference_format"]) && $ciExtendedIDprop["reference_format"]!="")
									$ciExtendedIDprop["reference_format_display"] = "yes";	
								if (isset($ciExtendedIDprop["identifier_key"]) && $ciExtendedIDprop["identifier_key"]!="")
									$ciExtendedIDprop["identifier_key_display"] = "yes";	
								if (isset($ciExtendedIDprop["ecc_doc_id"]) && $ciExtendedIDprop["ecc_doc_id"]>0)
									$ciExtendedIDprop["ecc_doc_id_display"] = "yes";	
							}
							
							if (is_array($ciExtendedIDprop) && count($ciExtendedIDprop)>0) {
								
								//$tmpInfoArray["list"]["CI_DATA"]
								$tmpInfoArray["list"]["CI_DATA"][$ciExtendedID] 	= array_merge($tmpInfoArray["list"]["CI_DATA"][$ciExtendedID], $ciExtendedIDprop);	
							
								$gridDataSrc["data"][0] 	= $tmpInfoArray["list"]["CI_DATA"][$ciExtendedID];
								$gridDataSrc["AllRecs"] 	= 1;		
								WebApp::addVar("listRow_".$ciExtendedID,$gridDataSrc);
							}
						}
					}
				}
			//}
		}		

				
		if (isset($tmpInfoArray["Crms"]) && count($tmpInfoArray["Crms"])>0) {
			$sessUserObj->getCrmsProfile(implode(",",$tmpInfoArray["Crms"]));
		}
			
		
		
		
		
		
	}	
	
	
	
	
	function CreateAllDataToDisplay() {
		global $session, $node_nedded_data,$global_cache_dynamic,$cacheDyn, $mob_web;
					

		if ($this->param["display_keywords"] == "yes") {
		
			$KwObj = new KwManagerFamily($session->Vars["ses_userid"],$session->Vars["lang"]);
			$FamilyDataArray = $KwObj->getAllFamilyData();

			if (count($FamilyDataArray)>0 && count($this->param["publish_kw"])>0) {
				while (list($grID,$infoGrArr)=each($FamilyDataArray)) {

					if (isset($infoGrArr["ids"]) && count($infoGrArr["ids"])>0 && $grID!=5 && $grID!=6) {
						reset($infoGrArr["ids"]);
						while (list($idFamily,$descriptionFamily)=each($infoGrArr["ids"])) {
							
							if (in_array ($idFamily,$this->param["publish_kw"]) && $this->param["kw_labels"][$idFamily] == "") {
								$this->param["kw_labels"][$idFamily] = $descriptionFamily;
							}
						}	
					}
				}
			}	
		}

		IF ($global_cache_dynamic == "Y") {
		} elseif (defined("Caching_Metatags") AND (Caching_Metatags == "Y")) {
			$application_is_cached = "no";
			if ($session->Vars["parseBox"]=="true" && $global_cache_dynamic == "Y") {
				$cache = new Caching();
				$cache->domain	= APP_URL;
				$cache->lang	= $session->Vars["lang"];
				$cache->readFromConfig();	

				if ($session->Vars["parseBox"]=="true" && $cache->error_code == "0") {
					$application_is_cached = "yes";
				}
			}
		}
		
		

/*
		//KRIJOHEN VARIABLAT E NAVIGIMIT
		$CountItems		= $this->CountItems;
		$FromRecs		= $this->FromRecs;
		$ToRecs			= $this->ToRecs;
		$NrPage			= $this->NrPage;
		$TotPage		= $this->TotPage;
		$recPages		= $this->recPages;
*/		
		$gridDataSrc = array(
			"data" 			=>  array(),
			"dataNAV" 			=>  array(),
			"AllRecs" 		=> "0"
		);	

		$listData = array();
		$listData = array();
		
		$id = 0;
		




		$this->URLS_EXTENDED["details_course"] 	= "javascript:GoTo('thisPage?event=none.ch_state(k=".COURSE_DETAILS.";idElC={{CID}})');";
		$this->URLS_EXTENDED["details_lecture"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".LECTURE_DETAILS.";idElC={{CID}})');";





		$doc_id_set =array();
		if ($this->sql_type == "default") {	
			$cnt_data_get_documents = "SELECT DISTINCT c.content_id as content_id, 
												".$this->order_nr_search." AS weightUsedToSort
												
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
									   LEFT JOIN 	content_text  	AS ctxt ON (ctxt.content_id = c.content_id)
									   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 


								WHERE p.profil_id in ('".$session->Vars["tip"]."')
								  ".$this->nodes_restriction_to_sql."
								  AND c.searchable = 'Y'
									  ".$this->stateCondition_search."
									  ".$this->expire_condition_search."
									  ".$this->conditionSearchFull_search."		

								ORDER BY weightUsedToSort DESC
									".$this->limitToSql."";			
									
			$rs_list = WebApp::execQuery($cnt_data_get_documents);
			$this->cnt_data_get_documents =$cnt_data_get_documents;
			$iorder = 1;
			while (!$rs_list->EOF())  {
				$idX 				= $rs_list->Field("content_id");
				$doc_id_set[$iorder++]= $idX;
				$rs_list->MoveNext();
			}	
		
		} else {
		
			$union_sql_data_array =array();
			$union_sql_data_array ["title"] ="
						(
						SELECT  c.content_id, 
									".$this->mtchExct["c.title".$this->ln_search]." as weightUsedToSortEx,
									".$this->orderNrArrayWeight["c.title".$this->ln_search]." as weightUsedToSort,
									0 as weightUsedToSortKW,
									0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.title".$this->ln_search]."
						)
			";	
			
			
			$union_sql_data_array ["description"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.description".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.description".$this->ln_search.$this->md_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.description".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["doc_source"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.doc_source".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.doc_source".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.doc_source".$this->ln_search]."
						)
			";	
			
			$union_sql_data_array ["source_author"] ="
						(
						SELECT  c.content_id,
						".$this->mtchExct["c.source_author".$this->ln_search]." as weightUsedToSortEx,
						".$this->orderNrArrayWeight["c.source_author".$this->ln_search]." as weightUsedToSort,
						0 as weightUsedToSortKW,0 as weightUsedToSortL
							   FROM  content as c
							   where ".$this->conditionSearchUnionArray["c.source_author".$this->ln_search]."
						)
			";	



			$union_sql_data_array ["nodekw"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort, 
						".$this->orderNrArrayWeight["ctxt.nodekw_".$this->ln_search.$this->md_search]." as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.nodekw_".$this->ln_search.$this->md_search]."
						)
			";			
			$union_sql_data_array ["keywords_1"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_1_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_1_".$this->ln_search.$this->md_search]."
						)
			";
			
			$union_sql_data_array ["keywords_2"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_2_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_2_".$this->ln_search.$this->md_search]."
						)
			";	
			
			$union_sql_data_array ["keywords_3"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_3_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_3_".$this->ln_search.$this->md_search]."
						)
			";				

			$union_sql_data_array ["keywords_4"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						".$this->orderNrArrayWeight["ctxt.keywords_4_".$this->ln_search.$this->md_search]."  as weightUsedToSortKW,
						0 as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.keywords_4_".$this->ln_search.$this->md_search]."
						)
			";				
			

			
			$union_sql_data_array ["context"] ="
						(
						SELECT  ctxt.content_id,
						0 as weightUsedToSortEx, 
						0 as weightUsedToSort,
						0 as weightUsedToSortKW,
						".$this->orderNrArrayWeight["ctxt.context".$this->ln_search.$this->md_search]." as weightUsedToSortL
							   FROM  content_text as ctxt
							   where ".$this->conditionSearchUnionArray["ctxt.context".$this->ln_search.$this->md_search]."
						)
			";				


			$union_sql_main_part = implode(" UNION ALL ",$union_sql_data_array);
			$cnt_data_get_documents = "	

				SELECT content_id, sum(weightUsedToSortEx) as weightUsedToSortEx, sum(weightUsedToSort) as weightUsedToSortTot, sum(weightUsedToSortKW) as weightUsedToSortKWTot, sum(weightUsedToSortL) as weightUsedToSortLTot
					FROM (
							".$union_sql_main_part."
						 ) AS table_search
				 WHERE content_id in (
				 
				 				SELECT distinct c.content_id
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
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			  ".$this->nodes_restriction_to_sql."
				 			  AND c.searchable = 'Y'
								  ".$this->stateCondition_search."
								  ".$this->expire_condition_search."
								  
								  
					 		UNION DISTINCT
				 				
				 				SELECT distinct c.content_id
				 				 FROM				profil_rights_ci	AS p
				 							
				 							
				 							JOIN	content			AS c	ON (    p.content_id  = c.content_id)


				 							JOIN	nivel_4			AS n4	ON (    c.id_zeroNivel   = n4.id_zeroNivel
				 																AND c.id_firstNivel  = n4.id_firstNivel
				 																AND c.id_secondNivel = n4.id_secondNivel
				 																AND c.id_thirdNivel  = n4.id_thirdNivel
				 																AND c.id_fourthNivel = n4.id_fourthNivel
				 																)
				 					   LEFT JOIN 	ei_data 		AS cei 	ON c.content_id = cei.content_id 
				 
				 			WHERE p.profil_id in ('".$session->Vars["tip"]."')
				 			  ".$this->nodes_restriction_to_sql_ECC."
				 			  AND c.searchable = 'Y'
				 				  ".$this->stateCondition_search."
				 				  ".$this->expire_condition_search."									  
								  
								  
				  )

				GROUP BY content_id
				  ORDER BY weightUsedToSortEx DESC, weightUsedToSortTot DESC, weightUsedToSortKWTot DESC, weightUsedToSortLTot desc 				
					".$this->limitToSql."			  
				  ";		

			$rs_list = WebApp::execQuery($cnt_data_get_documents);

	



			$this->cnt_data_get_documents =$cnt_data_get_documents;
			$iorder = 1;
			
			$mainGridData = array();
			$ind=0;
			while (!$rs_list->EOF())  {
				$idX		= $rs_list->Field("content_id");
				$mainGridData["data"][$ind]["CIID"] = $idX;
				$doc_id_set[$ind]= $idX;
				$ind++;
				$rs_list->MoveNext();
			}	
		}
		
		$mainGridData["AllRecs"] = count($mainGridData["data"]);		
		WebApp::addVar("searchResultMinGridData", $mainGridData);	
		
		//id_zeroNivel, id_firstNivel, id_secondNivel, id_thirdNivel,id_fourthNivel
		
		$this->ln_searchID = SUBSTR($this->ln_search, -1);
		$this->thisMode = $session->Vars["thisMode"];


		global $sessUserObj;	
		$searchTabNavInfo = array();
		$CiInf = array();
			
		$valDocStr = implode(",",$doc_id_set);
		$this->findCisExtendedInformation($doc_id_set);	
		$this->listDataDet = $tmpInfoArray;

		$this->listData = $mainGridData["data"];
		if (count($listData)>0)	{ 
			$gridDataSrc["data"] = $listData;
			$gridDataSrc["AllRecs"] = count($listData);
			
			$gridDataSrc["dataNAV"]["CntI"] =$this->CountItems;
			$gridDataSrc["dataNAV"]["fromI"]=$this->FromRecs;
			$gridDataSrc["dataNAV"]["toI"] 	=$this->ToRecs;
			$gridDataSrc["dataNAV"]["pg"] 	=$this->NrPage;
			$gridDataSrc["dataNAV"]["pgCnt"]=$this->TotPage;
			$gridDataSrc["dataNAV"]["pgRec"]=$this->recPages;
		}

		$this->gridDataSrc = $gridDataSrc;
		
		
	/*	echo "listDataDet<textarea>";
		print_r($this->listDataDet);
		echo "</textarea>";		*/
		
		
		
		
		
	}
	function CreateLimitToSql () {	
		
		$this->TotPage =ceil($this->CountItems/$this->recPages);
		$this->FromRecs = $this->recPages*($this->NrPage-1)+1;
		$this->ToRecs = $this->recPages * $this->NrPage;
		if ($this->ToRecs > $this->CountItems)		
			$this->ToRecs = $this->CountItems;		
		$beginfrom = $this->FromRecs-1;
		$this->limitToSql = " LIMIT ".$beginfrom .",".$this->recPages;
	}
	
	function constructAdvHtmlnavigation ()
	{

		$CountItems		= $this->CountItems;
		$FromRecs		= $this->FromRecs;
		$ToRecs			= $this->ToRecs;
		$NrPage			= $this->NrPage;
		$TotPage		= $this->TotPage;
		$recPages		= $this->recPages;

		$number     = $this->recPages;
		$count_all  = $this->CountItems;
		$start_from = $this->NrPage;

	
		$gridPageSrcNrParams = array();

		$gridDataSrcNrParams = array(
			"data" 			=>  array(),
			"AllRecs" 		=> "0"
		);		

		if ($number){
		$total_pages = @ceil($count_all / $number);
		$current_page = $this->NrPage;
		$pages = array();

		//Nqs Nr i rec per faqe eshte me i madh se 10 nav i avancuar
		IF ($total_pages > 10){

			//Blloku i majte
			$pages_start = 1;
			$pages_max = $current_page >= 5 ? 3 : 5;

			for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};rpp=".$i.";rp={{recPage}};msv={{msv}};ser={{objId}})')";	

				$gridPageSrcNrParams[$i]["linkItem"] = $link_gtp;
				$gridPageSrcNrParams[$i]["pagNr"] = $i;		

				if($i == $current_page){
					$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
					$gridPageSrcNrParams[$i]["selected"] = "yes";
				} else {
						$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
						$gridPageSrcNrParams[$i]["selected"] = "no";
					}
			}
			$pages []= '...';

			//Blloku i mesit
			if($current_page > 4 && $current_page < ($total_pages - 3)){
				$pages_start = $current_page - 1;
				$pages_max = $current_page + 1;
			for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};rpp=".$i.";rp={{recPage}};msv={{msv}};ser={{objId}})')";	

				$gridPageSrcNrParams[$i]["linkItem"] = $link_gtp;
				$gridPageSrcNrParams[$i]["pagNr"] = $i;			

				if($i == $current_page){
					$gridPageSrcNrParams[$i]["selected"] = "yes";
					$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
				} else {
					$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
					$gridPageSrcNrParams[$i]["selected"] = "no";
					}
				}
					$pages []= '...';
			}
			//Blloku i djathte
			$pages_start = $current_page <= $total_pages - 4 ? $total_pages - 2 : $total_pages - 4;
			$pages_max = $total_pages;
			  for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};rpp=".$i.";rp={{recPage}};msv={{msv}};ser={{objId}})')";	
				$gridPageSrcNrParams[$i]["linkItem"] = $link_gtp;
				$gridPageSrcNrParams[$i]["pagNr"] = $i;			 
				 if($i == $current_page){
						$gridPageSrcNrParams[$i]["selected"] = "yes";
						$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\">".$i." </a>";
				  } else {
						$gridPageSrcNrParams[$i]["selected"] = "no";
						$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\">".$i." </a>";
				  }
			  }

		} ELSE {
	
			//Nqs eshte me i vogel se 10
			for ($i = 1; $i <= $total_pages; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{k}};kc={{kc}};rpp=".$i.";rp={{recPage}};msv={{msv}};ser={{objId}})')";	
				$gridPageSrcNrParams[$i]["linkItem"] = $link_gtp;
				$gridPageSrcNrParams[$i]["pagNr"] = $i;			 

			   //if ((($i - 1) * $number) != $start_from){
				  if($i == $current_page){
						$gridPageSrcNrParams[$i]["selected"] = "yes";
						$pages [] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
					  } else {
						$gridPageSrcNrParams[$i]["selected"] = "no";
						$pages [] = "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
					  }
				  }
			}
			
			if (count($pages) > 1) {
				$navByPage= "yes";
				$go_to_page_html = implode(" | ",$pages);

			} else {
				$navByPage= "no";
				$go_to_page_html = "";
			}

			WebApp::addVar("navByPage",$navByPage);
			WebApp::addVar("go_to_page_html",$go_to_page_html);

		}

		if (count($gridPageSrcNrParams)>0)	{ 
			$gridDataSrcNrParams["data"] = $gridPageSrcNrParams;
			$gridDataSrcNrParams["AllRecs"] = count($gridPageSrcNrParams);
		}

		$this->gridPageSrcNr = $gridDataSrcNrParams;	

		$this->pages_nr = $navByPage;
		$this->navByPage = $navByPage;
		$this->go_to_page_html = $go_to_page_html;	
	}
	
	function BlockNavigationTotalItems($blNoToDisplay=0,$stepBack=0) 
	{

		//KRIJOHEN VARIABLAT E NAVIGIMIT
		$CountItems		= $this->CountItems;
		$FromRecs		= $this->FromRecs;
		$ToRecs			= $this->ToRecs;
		$NrPage			= $this->NrPage;
		$TotPage		= $this->TotPage;
		$recPages		= $this->recPages;
		
		$blockToDisplayDataToGrid = array();
		$blockToDisplay = array();
		
		if(isset($NrPage) && $NrPage!="")
				$goToBlock = $NrPage;
		else 
				$goToBlock = 1;

		$NrPage = $goToBlock;
		if($CountItems > 0) {
			
			$bk=1;
		
			if ($blNoToDisplay > 0) {
				if ($stepBack > 0) {
					if ($NrPage - $stepBack> 0)
						$bk=$NrPage - $stepBack;
					if (($TotPage-$bk)<$blNoToDisplay && ($TotPage-$blNoToDisplay+1)>0)
						$bk = $TotPage-$blNoToDisplay+1;
				}
			} else {
				$blNoToDisplay = $TotPage;
			}

			for($k=$bk; $k<=ceil($TotPage); $k++) {
					$nav[$k] = $k;
			}
			
			$navToGrid = array();
			
			for($k=1; $k<=ceil($TotPage); $k++) {
					$navToGrid[$k] = $k;
			}			
			
			$blockNavigation = array_chunk($nav,$blNoToDisplay);
			while (list($k,$v)=each($blockNavigation)) {
				if(in_array($goToBlock,$blockNavigation[$k])) {
					while (list($kl,$vl)=each($blockNavigation[$k])) {
						if($goToBlock == $vl) {
							$classe = "yes";	$selClass = " selClass";	$selClassOpt = " selected=\"selected\"";
						} else { 
							$classe = "";		$selClass = "";			$selClassOpt = "";
						}

						$blockToDisplay["data"][$kl]["goto"]  =  $vl;
						$blockToDisplay["data"][$kl]["isSel"] =  $classe;
						$blockToDisplay["data"][$kl]["selClass"] =  $selClass;
						$blockToDisplay["data"][$kl]["selClassOpt"] =  $selClassOpt;
					}
				} 
			}
			
			while (list($kl,$vl)=each($navToGrid)) {
				if($goToBlock == $vl) {
					$isSel = "yes";
				} else { 
					$isSel = "";
				}

				$blockToDisplayDataToGrid["data"][$kl]["goto"]  =  $vl;
				$blockToDisplayDataToGrid["data"][$kl]["isSel"] =  $classe;
			}
		}
		
		$blockToDisplay["AllRecs"] = count($blockToDisplay["data"]);
		$this->gridblockToDisplay = $blockToDisplay;	

		$blockToDisplayDataToGrid["AllRecs"] = count($blockToDisplayDataToGrid["data"]);
		$this->blockToDisplayDataToGrid = $blockToDisplayDataToGrid;	
	}	
	
	
	function constructNavListHtml()
	{
		global $session;

		$recPages	= $this->recPages;
		$NrPage		= $this->NrPage;
		$CountItems	= $this->CountItems;
		$TotPage	= $this->TotPage;
		$FromRecs	= $this->FromRecs;
		$ToRecs		= $this->ToRecs;



		$number     = $this->recPages;
		$count_all  = $this->CountItems;
		$start_from = $this->NrPage;

		if ($number){
		
		$total_pages = @ceil($count_all / $number);
		$current_page = $this->NrPage;
		$pages = array();

		//Nqs Nr i rec per faqe eshte me i madh se 10 nav i avancuar
		IF ($total_pages > 10){

			//Blloku i majte
			$pages_start = 1;
			$pages_max = $current_page >= 5 ? 3 : 5;

			for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{contentId}};kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};rpp=".$i.";rp={{recPage}};bsnid={{bsnid}};fkid={{fkid}};nsfkid={{nsfkid}};msv={{MSparams}};)')";	

				if($i == $current_page){
					$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
				} else {
					$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
					}
			}
				$pages []= '...';

			//Blloku i mesit
			if($current_page > 4 && $current_page < ($total_pages - 3)){
				$pages_start = $current_page - 1;
				$pages_max = $current_page + 1;
			for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{contentId}};kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};rpp=".$i.";rp={{recPage}};bsnid={{bsnid}};fkid={{fkid}};nsfkid={{nsfkid}};msv={{MSparams}};)')";	
				if($i == $current_page){
					$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
				} else {
					$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
					}
				}
					$pages []= '...';
			}
			//Blloku i djathte
			$pages_start = $current_page <= $total_pages - 4 ? $total_pages - 2 : $total_pages - 4;
			$pages_max = $total_pages;
			  for($i = $pages_start; $i <= $pages_max; $i++){
				$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{contentId}};kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};rpp=".$i.";rp={{recPage}};bsnid={{bsnid}};fkid={{fkid}};nsfkid={{nsfkid}};msv={{MSparams}};)')";
				  if($i == $current_page){
						$pages[] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\">".$i." </a>";
				  } else {
						$pages []= "<a class=\"link_item\" href=\"".$link_gtp."\">".$i." </a>";
				  }
			  }

		} ELSE {

		//Nqs eshte me i vogel se 10
		for ($i = 1; $i <= $total_pages; $i++){
			$link_gtp = "javascript:GoTo('thisPage?event=none.srm(k={{contentId}};kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};rpp=".$i.";rp={{recPage}};bsnid={{bsnid}};fkid={{fkid}};nsfkid={{nsfkid}};msv={{MSparams}};)')";	
			//if ((($i - 1) * $number) != $start_from){
			  if($i == $current_page){
					$pages [] = "<a class=\"link_item_sel\" href=\"".$link_gtp."\" >".$i." </a>";
				  } else {
					$pages [] = "<a class=\"link_item\" href=\"".$link_gtp."\" >".$i." </a>";
				  }
			  }
		}
			if (count($pages) > 1) {
				$navByPage= "yes";
				$go_to_page_html = implode(" | ",$pages);

			} else {
				$navByPage= "no";
				$go_to_page_html = "";
			}

			WebApp::addVar("navByPage",$navByPage);
			WebApp::addVar("go_to_page_html",$go_to_page_html);

		}

		$nav_articles = $FromRecs.'-'.$ToRecs.' / '.$CountItems;
		
		WebApp::addVar("FromRecs","$FromRecs");
		WebApp::addVar("ToRecs","$ToRecs");
		WebApp::addVar("CountItems","$CountItems");

		
		WebApp::addVar("nav_articles","$nav_articles");
		WebApp::addVar("TotPage","$TotPage");
		WebApp::addVar("NrPage","$NrPage");
		WebApp::addVar("recPage","$recPages");
		
		
		if (($NrPage-1) > 0) 
				$previews_page=$NrPage-1;
		else	$previews_page = '';

		if (($NrPage+1) <= $TotPage) 
				$next_page=$NrPage+1;
		else	$next_page = '';


		WebApp::addVar("previewsPage","$previews_page");
		WebApp::addVar("nextPage","$next_page");

		WebApp::addVar("CountItems",$CountItems);

		//pjesa qe percakton ndryshimin e numrit te rekordeve ne faqe
		$selected_5 = "";
		$selected_10 = "";
		$selected_20 = "";
		$selected_50 = "";
		$selected_100 = "";
		eval("\$selected_".$recPages." = 'selected=\"selected\"';");
		$rec_page_options = "
				<option value=\"3\" ".$selected_3.">&nbsp;3</option>
				<option value=\"5\" ".$selected_5.">&nbsp;5</option>
				<option value=\"10\" ".$selected_10.">&nbsp;10</option>
				<option value=\"20\" ".$selected_20.">&nbsp;20</option>
				<option value=\"50\" ".$selected_50.">&nbsp;50</option>
				<option value=\"100\" ".$selected_100.">100</option>";
		WebApp::addVar("rec_page_options",$rec_page_options);

		$nrDec = strlen($TotPage);
		
		if ($beginFrom < 1) $beginFrom = 1;
		$step = 1;
		for ($i=$beginFrom;$i<=$TotPage;$i=$i+$step) {
			if ($NrPage == $i)		$selected_page = " selected=\"selected\"";
			else 					$selected_page = "";
			$goto_page .= "<option value=\"$i\"".$selected_page.">&nbsp;$i</option>";
		}

		WebApp::addVar("goto_page_options",$goto_page);

	}	
	function returnMiliSeconds ($start,$endt,$procesName) {
		list($usec1, $sec1) = explode(" ", $start);
		list($usec2, $sec2) = explode(" ", $endt);
		$micro_sec = ($sec2 - $sec1)*1000000 + ($usec2 - $usec1);
		$micro_sec =  round($micro_sec / 1000);	//return milisecs
		return "KOHA: $micro_sec - $procesName\n";
	}		

}

?>