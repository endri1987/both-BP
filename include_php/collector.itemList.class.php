<?php

/*
**  A PHP class to manage doc Search
**
**  @author     Jonida Cuko <jonidacuko@arkit.info>
**  @version    1.0 (created: 05.04 June 2012)
**  @copyright  (c) 2000 -  ArkIt
**  @package    collectorBaseClass
**
**	// inicializon klasen, lexon mesazhet e ardhur nga nemi, dhe nese ka mesazh qe i ben overrite propertive te percaktuar te nemi
**
**  --- rootClass collectorBaseClass
**   |
**   |-------- LitemDataListClass
**   		|
**   		|------------ LtemTemplateListClass
**   		|
**/

class collectorBaseClass
{

	var $headline 	= "{{_title_default}}";
	
	var $collectorType	= "list"; //list-ciRelated
	var $relatedToCiId	= "";		//nese  collectorType = ciRelated, duhet te ruhen edhe ci per te cilen do gjenden dokumentat e lidhura
	var $time_period	= "";		//[1-viti akual,2-arkive]
	
	var $order 			= "no";

	var $lang	= "";
	var $lngId;

	var $thisMode	= "";
	var $uniqueid	= "";
	var $tip		= "";
	var $ses_userid		= "";
	
	var $termSearch 	= "";
	var $termfkid 		= "";
	
	var $idstemp 		= "x";
	var $sourceNemId 	= "x";
	var $order_by 		= "0";
	var $sort_by 		= "1";

	var $filterType 	= "DI";
	var $itemsFrom 	= ",1,";
	var $override_filter 	= "no";

	var $group_by 	= ",0,";
	var $targetNode 	= "1";

	var $recPages 		= "20";	//
	var $NrPage 		= "1";	//

	var $statusNem 		= "0";	//

	var $nemID		= "";
	var $objNem		= "";
	
	var $fkid		= "";
	var $nsfkid		= "";

	//layout properti
	var $templateType 			= "0";
	var $templateFileName 		= "template_default.html";
	
	var $display 		        = ",date,title,abstract,source_author,image,";
	var $display_details        = array();
	
	

	var $display_link 	        = ",date,title,image,";
	var $display_link_in        = array();
	
	
	var $FooterNav 		        = ",FooterNav,";
	var $HeaderNav 		        = ",HeaderNav,";
	var $displayFilter 		= "";
	var $headlineSearch 		= "";

	var $display_link_label = "";

	var $display_ext_link_label = "";
	var $display_ext_link = "";

	var $crdTarget 					= "";	//e ndryshme nga boshe percakton nyjen se ku do te shfaqet ci

	//inputi per listen
	var $crd 					= array();	//nyjet ne te cilat gjenden dokumentat
	var $crdKeywords 			= array();	//nyjet me te cilat dokumenta jane lidhur me ane te keywordeve
	var $crdKeywordsSubjectTopics 			= array();	//nyjet me te cilat dokumenta jane lidhur me ane te keywordeve

	var $crdFilter 				= array();	//filterData
	var $crdKeywordsFiler 		= array();	//filterData

	var $subject 		= "";	//objekti qe mban lidhjen me keyordet
	var $schedulingFromStart 	= "";	//ky dhe variabli me poshte mbushen me data per filtrim
	var $schedulingFromEnd 		= "";	//

	var $itemsFromMessage 	= "";
	var $filterDataST 	= "AND";

	var $crdMessage 					= array();	//nyjet ne te cilat gjenden dokumentat
	var $crdKeywordsMessage 			= array();	//nyjet me te cilat dokumenta jane lidhur me ane te keywordeve
	var $crdKeywordsMessageSubjectTopics 			= array();	//nyjet me te cilat dokumenta jane lidhur me ane te keywordeve

	var $schedulingFromStartMessage 	= "";	//ky dhe variabli me poshte mbushen me data per filtrim
	var $schedulingFromEndMessage 		= "";	//

	var $keyKeywords 					= array();	//id familje, id kewword brenda familjes
	var $keyKeywordsMessage 			= array();	//id familje, id kewword brenda familjes

	
	var $publish_kw 			= array();	//id familje, id kewword brenda familjes
	var $filter_by_kw 			= array();	//id familje, id kewword brenda familjes
	
	

	var $isFinished ="y";

	//Per scrolling news---------------------------------------------/
		var $JPSCROLL_width        =  "";               
		var $JPSCROLL_height       =  "";                
		var $JPSCROLL_bordercolor  =  "";                
		var $JPSCROLL_speed        =  "";                
		var $JPSCROLL_bgcolor      =  "";                
		var $JPSCROLL_pagepause    =  "";                 

		var $JPSCROLL_parameters   = array();
	//Per scrolling news---------------------------------------------/

	function collectorBaseClass() {
		global $session;
		
		$sessLANG = $session->Vars["lang"];
		if (isset($sessLANG) && $sessLANG!="") {
		
			if (preg_match("/Lng/i",$sessLANG)) {
				$lngIDCode = str_replace("Lng","",$sessLANG)*1;
				if (!defined("LNG".$lngIDCode)) {
					$lngIDCode = 1;
				} else {
					$session->Vars["lang"] = 'Lng'.$lngIDCode;					
				}
			} 
		} else {
			$session->Vars["lang"] = 'Lng1';	
		}
		$this->lang			= $session->Vars["lang"];
		
		
		$this->lngId 		= str_replace ("Lng","",$this->lang);
		
		
		
		
		$this->thisMode		= $session->Vars["thisMode"];
		
		//echo "---".$this->thisMode."-thisMode-------".$session->Vars["thisMode"]."--session------------";
	 	$this->uniqueid		= $session->Vars["uniqueid"];

		if (isset($session->Vars["tip"]))
			$this->tip = $session->Vars["tip"];
		else
			$this->tip = "2";

		if (isset($session->Vars["ses_userid"]))
			$this->ses_userid = $session->Vars["ses_userid"];
		else
			$this->ses_userid = "2";
		
		//$this->InitClass();
		//print "<br>collectorBaseClass<br>";
	}

	//kontrollohet ne rast qe jemi ne nje gjendje print, email, apo alone, 
	//nga ku inicializimi i nemit do behet me gjendjen e fundit qe eshte ruajtur ne transition
	function InitClass($idstemp = "")
	{
		global $session, $event;

		if ($idstemp=="") {
			$this->idstemp = $session->Vars["idstemp"];
		} else {	//CI-57-96-1-79
			$this->idstemp = $idstemp;
		}
		

		$this->returnNemProp();
		$this->returnObjectOtherParams();
		
		if(!defined('COLLECTOR_PERSONALIZATION') || COLLECTOR_PERSONALIZATION=="NO") {
			
		} else {
			
				if(!defined('CI_override_rights') || CI_override_rights=="NO") {
				} else {

					//kjo do te thote qe te drejtat e roleve mbi ci qe do shfaqen duhet te kontrollohen te struktura perkatese qe u shtua per ci - "profil_rights_ci"
					if (isset($this->NEM_PROP->ci_node_rights) && $this->NEM_PROP->ci_node_rights==",ci,")
						$this->get_items_by_CI_rights = "yes"; //nese aplikimi eshte konfiguruar qe ti jape te drejat roleve ne lidhje me  ci
				}			


				if(!defined('CI_Favorite') || CI_Favorite=="NO") {		//favorit,popular,none
				} else {

					if (isset($this->NEM_PROP->favorit_or_popular) && $this->NEM_PROP->favorit_or_popular==",favorit,")
						$this->get_favorite_items= "yes";
				}

				if(!defined('CI_CommentRating') || CI_CommentRating=="NO") { //popular
				} else {

					if (isset($this->NEM_PROP->favorit_or_popular) && $this->NEM_PROP->favorit_or_popular==",popular,")
						$this->get_popular_items= "yes";
				}

				if (isset($this->NEM_PROP->user_priv) && $this->NEM_PROP->user_priv==",yes,") { // user_priv	-  author contributors none
				
				}			
		}		
		
		if (isset($this->NEM_PROP->most_recent) && $this->NEM_PROP->most_recent==",yes,") {
			//time_frame_personalization-last_week last_month last_year
			if ($this->NEM_PROP->time_frame_personalization == "last_week")
				$this->time_period="3";
			if ($this->NEM_PROP->time_frame_personalization == "last_month")
				$this->time_period="4";					
			if ($this->NEM_PROP->time_frame_personalization == "last_year")
				$this->time_period="1";			
			if ($this->NEM_PROP->time_frame_personalization == "arkive")
				$this->time_period="2";	
		}		
		
	}	
	
	function returnObjectOtherParams() {
		global $session, $event;
		//$this->getNodeMessage();
		
		$objNemArr = explode("-",$this->idstemp);
		$this->type_doc =$objNemArr[0];
		$this->nemID =$objNemArr[2];
		$this->objNem =$objNemArr[4];
		WebApp::addVar("objId",		"$this->objNem");
		WebApp::addVar("type_doc",	"$this->type_doc");
		WebApp::addVar("ser",	"$this->objNem");
		WebApp::addVar("oj",	"$this->objNem");
		
		//Array ( [0] => CI [1] => 389 [2] => 96 [3] => 1 [4] => 105 ) 	
		//ne rastin me te thjesht, na duhet 
		//qe te kapim nga posti numrin e faqes, numrin e rekordeve ne faqe, dhe termin e searchit
		//dhe kjo behet duket kontrolluar qe kjo kerkese, per te filtuar listen, apo ndryshimin e faqes, apo ndyrshimin e rekordeve ne faqe
		//eshte bere pikerisht nga ky modul, dhe jo nga module te tjera qe jane ne faqe
		
		//ne rastin kur kemi te bejme me adv search, ne lista, te kontrollohet se si ruhen variblat

//rasti dinamik, kontrollojme objektin event->args, qe mbushet nga posti 

/* //RASTI I SERCHIT TE LISTAT

            [k] => 389
            [kc] => 0,16,9,0,0
            [rp108] => 10
            [ft] => Komunikimi përmes Vodafone nuk mund të bëhet më i thjeshtë
            [msv108] => msvSrc
            [ser] => 108


	// ky eshte identifikuesi modulit
	Array
	(
		[0] => CI
		[1] => 389
		[2] => 96
		[3] => 1
		[4] => 105
	)

*/		



		
		
		
		$paramMsv 		= array();
		$reset_filter = "no";
		
		


			//keto jane variblat qe vijne nga url e cachuara, ne fakt nuk eshte e nevojshme se aplikimi ke event nese gjendet ne url variabli evn=srm
			IF (isset($_GET["ser"]) && $_GET["ser"]==$this->objNem) {

				IF (isset($_GET["rp"]) && $_GET["rp"]!="" && $_GET["rp"]<50) 
					$this->recPages = $_GET["rp"];
				
				IF (isset($_GET["rpp"]) && $_GET["rpp"]!="") 
					$this->NrPage = $_GET["rpp"];
					
				
				/*IF ($event->name=="srm" && isset($_REQUEST["ft"]) && $_REQUEST["ft"]!="") {
					$this->termSearch = trim($_REQUEST["ft"]);
					$paramMsv["ft"] = $this->termSearch;
				}	// ftdb ftde		
				
				IF ($event->name=="srm" && isset($_REQUEST["ftdb"]) && $_REQUEST["ftdb"]!="") {
					$this->intervalBegin = trim($_REQUEST["ftdb"]);
					$paramMsv["fib"] = $this->intervalBegin;
				}					
				IF ($event->name=="srm" && isset($_REQUEST["ftde"]) && $_REQUEST["ftde"]!="") {
					$this->intervalEnd = trim($_REQUEST["ftde"]);
					$paramMsv["fie"] = $this->intervalEnd;
				}	*/					
					
			}
			//keto jane variblat qe vijne nga posti GoTo
			IF (isset($event->args["ser"]) && $event->args["ser"]==$this->objNem) {

				IF (isset($event->args["rp"]) && $event->args["rp"]!="" && $event->args["rp"]<50) 
					$this->recPages = $event->args["rp"];
				
				IF (isset($event->args["rpp"]) && $event->args["rpp"]!="") 
					$this->NrPage = $event->args["rpp"];
				
				IF ($event->name=="srm" && isset($event->args["fkid"]) && $event->args["fkid"]!="") {
					$this->termfkid = trim($event->args["fkid"]);
				}			
				if ($event->name=="srm" && isset($event->args["step"]) && $event->args["step"]=="1") {
						$reset_filter = "yes";
						
						IF (isset($event->args["ft"]) && $event->args["ft"]!="") {
							$this->termSearch = trim($event->args["ft"]);
							$paramMsv["ft"] = $this->termSearch;
							WebApp::addVar("msvLsttermSearch",$this->termSearch);	
							
						}

						IF (isset($event->args["ftdb"]) && $event->args["ftdb"]!="") {
							$this->intervalBegin = trim($event->args["ftdb"]);
							$paramMsv["fib"] = $this->intervalBegin;
							
							WebApp::addVar("msvLstintervalBegin",$this->intervalBegin);	
						}				
						IF (isset($event->args["ftde"]) && $event->args["ftde"]!="") {
							$this->intervalEnd = trim($event->args["ftde"]);
							
							$paramMsv["fie"] = $this->intervalEnd;
							WebApp::addVar("msvLstintervalEnd",$this->intervalEnd);	
						}
				} 
			}
			

			
					
			if (isset($event->args["msv"])) {
				$msv = $event->args["msv"];
			} elseif (isset($_GET["msv"]) || isset($_GET["msv"])) {
				$msv = $_REQUEST["msv"];
			} 
			else 
			{
				$msv = WebApp::getVar("msv");	
			}			
			
			IF (isset($msv) && ($msv!= 'undefined') && $reset_filter == "no") {
				$paramMsv 		= unserialize(base64_decode($msv));
				if (isset($paramMsv["ft"])) {
					$this->termSearch = $paramMsv["ft"];
					WebApp::addVar("msvLsttermSearch",$this->termSearch);	
				}
				if (isset($paramMsv["fib"])) {
					$this->intervalBegin = $paramMsv["fib"];
					WebApp::addVar("msvLstintervalBegin",$this->intervalBegin);	
				}			
				if (isset($paramMsv["fie"])) {
					$this->intervalEnd = $paramMsv["fie"];
					WebApp::addVar("msvLstintervalEnd",$this->intervalEnd);	
				}				
			}			
			


			$MSparams = base64_encode(serialize($paramMsv));
			//$sdfsdf = base64_encode("a:100000:{}");
			WebApp::addVar("msvLstSrc","$MSparams");				
	}
	function getNodeMessage()
	{
		global $session;

		$bsnid = WebApp::getVar("bsnid");
		IF (isset($bsnid) && ($bsnid!= 'undefined')) { //kemi mesazh te ardhur nga nyja
			$bsnid =$bsnid;
		} else {
			$bsnid = "";
		}

		$nsfkid = WebApp::getVar("nsfkid");
		IF (isset($nsfkid) && ($nsfkid!= 'undefined')) { //kemi mesazh te ardhur nga nyja
			$this->nsfkid = $nsfkid;
		}

		if ($this->thisMode=="_new")
				$flagAproved = "0";
		else	$flagAproved = "1";
	 	
		if(defined('SOA_NODE_SERVICES') && SOA_NODE_SERVICES=="Yes") {
	 	/*$getNodeSoa = "
			SELECT nem_enabled, filter_id, coordinates , nem_id
			  FROM soa_node_services
			 WHERE id_zeroNivel = '".$session->Vars["level_0"]."'
			  AND id_firstNivel = '".$session->Vars["level_1"]."'
			  AND id_secondNivel = '".$session->Vars["level_2"]."'
			  AND id_thirdNivel = '".$session->Vars["level_3"]."'
			  AND id_fourthNivel = '".$session->Vars["level_4"]."'
			  AND lng_id = '".$this->lang."'
			   AND statusInfo = '".$flagAproved."'
			   order by statusInfo";


		$rsNemsToFilter = WebApp::execQuery($getNodeSoa);
		if (!$rsNemsToFilter->EOF()) {
			$nem_id 	= $rsNemsToFilter->Field("nem_id");
			$this->nemID;
			if ($nem_id==$this->nemID)
				$this->nsfkid = $rsNemsToFilter->Field("filter_id");
		}*/
		}

		IF ($this->nsfkid) { //kemi mesazh te ardhur nga nyja
			$this->returnMessageProp($this->returnFilterProp($this->nsfkid));
			WebApp::addVar("nsfkid",$this->nsfkid);
			WebApp::addGlobalVar("nsfkid",$this->nsfkid);
		}

		$fkid = WebApp::getVar("fkid");
		IF (isset($fkid) && ($fkid!= 'undefined')) { //kemi mesazh te ardhur me extended link
			$this->returnMessageProp($this->returnFilterProp($fkid));
			WebApp::addVar("fkid","$fkid");
			WebApp::addGlobalVar("fkid",$fkid);
			$this->fkid = $fkid;
		}
	}
	function returnNemProp()
	{
		global $session, $event, $global_cache_dynamic, $cacheDyn, $mob_web;
	
		$objects = unserialize(base64_decode(WebApp::findNemProp($this->idstemp)));
		if(isset($objects->slogan_title)  && $objects->slogan_title!=""){
				$this->slogan_title = str_replace("'","&apos;",$objects->slogan_title);
 		}else{
 				$this->slogan_title = "";
 		}		
 		if(isset($objects->slogan_description)  && $objects->slogan_description!="") 
				$this->slogan_description = str_replace("'","&apos;",$objects->slogan_description);
 		else
 				$this->slogan_description = "";
 		
 		
 		if(isset($objects->kw_publish_labels) && count($objects->kw_publish_labels)>0) {
 			while (list($key,$value)=each($objects->kw_publish_labels)) {
 				$this->kw_publish_labels[$key] = $value;
 			}
		}
 		if(isset($objects->publish_kw) && $objects->publish_kw!="") {
			$dataneded =explode(",",$objects->publish_kw);  
			while (list($key,$value)=each($dataneded)) {
				if ($value!="") 
					$this->publish_kw[$value] = $value;
			}			
 		}
 		if(isset($objects->filterby_kw) && $objects->filterby_kw!="") {
			$dataneded =explode(",",$objects->filterby_kw);  
			while (list($key,$value)=each($dataneded)) {
				if ($value!="") 
					$this->filter_by_kw[$value] = $value;
			}			
 		} 		
		if (isset($objects->filterDataST) && $objects->filterDataST!="" && $objects->filterDataST=="1")
			$this->filterDataST = " OR ";
		if (isset($objects->filterDataST) && $objects->filterDataST!="" && $objects->filterDataST=="0")
			$this->filterDataST = " AND ";

		if (isset($objects->filterType) && $objects->filterType!="")
			$this->filterType = $objects->filterType;

		if (isset($objects->templateType) && $objects->templateType!="") {
			
			
			$this->templateType = $objects->templateType;
			//behet override templati per mobile, nese eshte bere set
			IF ($mob_web == "mob" && isset($objects->templateType_mobile) && $objects->templateType_mobile!="") {
				$this->templateType = $objects->templateType_mobile;
			}			
			
		
			$this->objects =$objects;	





		  
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateFileName = $rs->Field("template_box");} 
		  //---------------------------------------------------------------------------------------------------------------------		
			if($this->templateType=='0'){
				$this->templateFileName="template_default.html";
			} else $this->templateFileName=$this->templateFileName;
		
		}
		
		
		if (isset($objects->templateSearchType) && $objects->templateSearchType>=0) 
			$this->templateSearchType = $objects->templateSearchType;
		if ($mob_web == "mob" && isset($objects->templateSearchType_mobile) && $objects->templateSearchType_mobile!="") 
			$this->templateSearchType = $objects->templateSearchType_mobile;

		if (isset($this->templateSearchType) && $this->templateSearchType!="") {	
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateSearchType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateSearchFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------	
		 }
			


		if (isset($objects->templateCalendarType) && $objects->templateCalendarType>=0) 
			$this->templateCalendarType = $objects->templateCalendarType;
		if ($mob_web == "mob" && isset($objects->templateCalendarType_mobile) && $objects->templateCalendarType_mobile!="") 
			$this->templateCalendarType = $objects->templateCalendarType_mobile;
			
		if (isset($this->templateCalendarType) && $this->templateCalendarType!="") {	
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateCalendarType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateCalendarFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------	
		 }			
			
		if (isset($objects->templateHeaderNav) && $objects->templateHeaderNav>=0) 
			$this->templateHeaderNav = $objects->templateHeaderNav;
		if ($mob_web == "mob" && isset($objects->templateHeaderNav_mobile) && $objects->templateHeaderNav_mobile!="") 
			$this->templateHeaderNav = $objects->templateHeaderNav_mobile;
		if (isset($this->templateHeaderNav) && $this->templateHeaderNav!="") {
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateHeaderNav."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateHeaderFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}
		
		if (isset($objects->templateFooterNav) && $objects->templateFooterNav>=0) 
			$this->templateFooterNav = $objects->templateFooterNav;
		if ($mob_web == "mob" && isset($objects->templateFooterNav_mobile) && $objects->templateFooterNav_mobile!="") 
			$this->templateFooterNav = $objects->templateFooterNav_mobile;
		
		
		if (isset($this->templateFooterNav) && $this->templateFooterNav!="") {
			
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateFooterNav."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateFooterFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}		
		
	/*echo "objects-<textarea>";
	print_r($objects);
	echo "</textarea>";*/


		
					
		

		if (isset($objects->schedulingFromStart) && $objects->schedulingFromStart!="")
			$this->schedulingFromStart = $objects->schedulingFromStart;
		if (isset($objects->schedulingFromEnd) && $objects->schedulingFromEnd!="")
			$this->schedulingFromEnd = $objects->schedulingFromEnd;
		
		$this->actualNode = array();
		
		if (isset($objects->crd) && $objects->crd!="")
			$this->crd = explode("%M%",$objects->crd);
		else {
			$actualNode = $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"];
			$this->crd = explode("%M%",$actualNode);
			$this->actualNode[] = $actualNode;
		}
		if (isset($objects->crdTarget) && $objects->crdTarget!="")
			$this->crdTarget = $objects->crdTarget;

		$this->findKeywords($objects->subject);
		if (isset($objects->nr_art))
			$this->recPages = $objects->nr_art;

		//layout properti
		if ($objects->display!="")
		$this->display 		= $objects->display;
		else	$this->display="";
		
		
		$tmp = explode(",",$this->display);
		if (count($tmp)>0) {
		while (list($key,$val)=each($tmp)) {
			if ($val!="")
				$this->display_details[$val] = $val;
		}}
				
		
		if (isset($objects->items_from))
		$this->itemsFrom			  = $objects->items_from;
		
		if (isset($objects->targetNode)) {
		$tmp = explode(",",$objects->targetNode);
		if (count($tmp)>0) {
		while (list($key,$val)=each($tmp)) {
			if ($val!="")
				$this->targetNode = $val;
		}}}	
		
		if (isset($objects->group_by))
		$this->group_by			  = $objects->group_by;

		$this->display_link = $objects->display_link;
		
		
		
		$tmp = explode(",",$this->display_link);
		if (count($tmp)>0) {
		while (list($key,$val)=each($tmp)) {
			if ($val!="")
				$this->display_link_in[$val] = $val;
		}}
		
		
		
		
		
//---------------------------------------per rss -----------------------------------------------------------		
		if($objects->RSSText!="")	$this->RSSChannelTitle	="-".$objects->RSSText;
		else $this->RSSChannelTitle="";
		$this->Rss_node=$objects->RSSNode;
		$this->Rss_radio=$objects->RSSRadio;
		$this->Rss_text=$objects->RSSText;
		$this->rss_version=$objects->rss_version;
		$this->FooterNav=$objects->FooterNav;
		$this->HeaderNav=$objects->HeaderNav;
//-----------------------------------------------------------------------------------------------------------
		
		$this->smsrcfile_id_sl		= $objects->smsrcfile_id;
		$this->smsrcfile_id_sl_mob	= $objects->smsrcfile_id_mob;
		$this->dp_full_link_sl		= $objects->dp_full_link;
		$this->targetedpage_sl		= $objects->targetedpage;
		$this->full_link_label		= $objects->full_link_label;
		
		$this->display_link_label 	= $objects->display_link_label;

		if (isset($objects->display_ext_link_label))
		$this->display_ext_link_label = $objects->display_ext_link_label;
		if (isset($objects->display_ext_link))
		$this->display_ext_link = $objects->display_ext_link;

		$this->order_by = $objects->order_by;
		$this->sort_by = $objects->sort_by;

		$this->displayFilter 	= $objects->displayFilter;
		$this->headline   = $objects->headline;
		$this->headlineSearch   = $objects->headlineSearch;
		$this->displayAdvFilter 	= $objects->displayAdvFilter;
		$this->headlineAdvSearch   = $objects->headlineAdvSearch;
		$this->setimage   = $objects->setimage;

        $this->nr_art =   $objects->nr_art;
//Keto variabla jane per scrolling news--------------------------------------------------------------------/
       
       
        if (isset($objects->JPSCROLL_width)) 
        	$this->JPSCROLL_width        = $objects->JPSCROLL_width;
		
		if (isset($objects->JPSCROLL_height)) 
        	$this->JPSCROLL_height       = $objects->JPSCROLL_height;

		if (isset($objects->JPSCROLL_bordercolor)) 
			$this->JPSCROLL_bordercolor  = $objects->JPSCROLL_bordercolor;
		
		if (isset($objects->JPSCROLL_speed)) 
			$this->JPSCROLL_speed        = $objects->JPSCROLL_speed;

		if (isset($objects->JPSCROLL_bgcolor)) 
			$this->JPSCROLL_bgcolor      = $objects->JPSCROLL_bgcolor;
		
		if (isset($objects->JPSCROLL_pagepause)) 
			$this->JPSCROLL_pagepause    = $objects->JPSCROLL_pagepause;
		
//Keto variabla jane per scrolling news--------------------------------------------------------------------/
		/*if (isset($objects->isFinished) && $objects->isFinished!="")
				$this->isFinished = $objects->isFinished;
		}*/
		
    	if (isset($objects->nemType) && $objects->nemType=="ciRelated") {
    		

    		$this->templateFileName = "related_template_default.html";
    		
    		
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateFileName = $rs->Field("template_box");}
	
		  //---------------------------------------------------------------------------------------------------------------------		
    		
    		
    		
    		$this->relatedToCiId = $session->Vars["contentId"];
    		$this->collectorType = "ciRelated";
    		if (isset($objects->what_ci) && $objects->what_ci=="other_ci" && $objects->related_to_ci!="") {
    			$kciid = str_replace("k=","",$objects->related_to_ci);
    			if ($kciid*1==$kciid) {
    				$this->relatedToCiId = $kciid;
    			}
    		}
    	} 	
    	if (isset($objects->arkive_or_aktual) && $objects->arkive_or_aktual!="") {
    		$this->time_period = $objects->arkive_or_aktual;
    	}
    	$this->NEM_PROP = $objects; //kjo eshte kapur qe te gjitha propertite qe mund te shtohen te kapen aty ku duhen vetme nese duhen
    	
    	
    	
    	
    	
		if (isset($objects->templateSearchType) && $objects->templateSearchType>=0) {
			$this->templateSearchType = $objects->templateSearchType;
			
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateSearchType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateSearchFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}	

		if (isset($objects->templateCalendarType) && $objects->templateCalendarType>=0) {
			$this->templateCalendarType = $objects->templateCalendarType;
			
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateCalendarType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateCalendarFileName= $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}			
		if (isset($objects->templateHeaderNav) && $objects->templateHeaderNav>=0) {
			$this->templateHeaderNav = $objects->templateHeaderNav;
			$this->HeaderNav = "yes";
			
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateHeaderNav."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateHeaderFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}		
		if (isset($objects->templateFooterNav) && $objects->templateFooterNav>=0) {
			$this->templateFooterNav = $objects->templateFooterNav;
			$this->FooterNav = "yes";
			
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateFooterNav."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateFooterFileName = $rs->Field("template_box");}
		  //---------------------------------------------------------------------------------------------------------------------		
		}	    	
    	
    	
    	
		//----------------------klevi ndryshim------------------------
					
			$this->RSSNodeT 		= 'no' ;
			if ($this->Rss_node)
			{
				$RSSNodeArray = explode(",",$this->Rss_node);
				$RSS_html_listAndRSSArray = explode(",",$this->Rss_radio);
				$this->RSS_html_listAndRSSArray		= $RSS_html_listAndRSSArray[1];
				$this->RSSNode		= $RSSNodeArray[1];
			}

			if($this->RSSNode=='0' && $this->RSSNode!=""){
				$this->RSSNodeT 		= 'yes' ;
				IF (isset($_GET["rpp"]) && $_GET["rpp"]!="") 
					$this->NrPage = $_GET["rpp"];
					
				if($this->Rss_text=="")	$this->RSSText="";
				else	$this->RSSText=" ".$this->Rss_text;
				
				$this->RSS_href="?idstemp=".$session->Vars["idstemp"]."&lang=".$session->Vars["lang"]."&k=".$session->Vars["contentId"]."";
				
				IF (isset($event->args["rpp"]) && $event->args["rpp"]!=""){
					$this->RSS_href=$this->RSS_href."&rpp=".$event->args["rpp"];	
				}
				if($this->FooterNav=="" && $this->HeaderNav=="")
					$this->RSS_navE="no";
				$this->RSS_href=$this->RSS_href."&feed_version=".$this->rss_version."&uni=".$session->Vars["uni"];
				if ($this->RSS_html_listAndRSSArray=='0'){

					$this->RSSList 		= 'yes' ;
					$this->RSSNodeT 	= 'yes' ;
				}
				if ($this->RSS_html_listAndRSSArray=='1'){

					$this->RSSList 		= 'no' ;
					$this->RSSNodeT 	= 'yes' ;
				}

				WebApp::addVar("RSS_href",$this->RSS_href);
				WebApp::addVar("RSSText",$this->RSSText);

			}
			else{
				$this->RSSList 		= 'yes' ;
			}

			WebApp::addVar("RSSNode",$this->RSSNodeT);
			WebApp::addVar("RSSList",$this->RSSList);
							
		//--------------fund klevi ndryshim------------------------------    	
    	
		
	/*echo "objects-<textarea>";
	print_r($this);
	echo "</textarea>";  */  	
    	
	}

	function returnMysqlDate($readableDate)
	{
		return implode("-",array_reverse(explode("-",str_replace(".","-",str_replace("'","",$readableDate)))));

	}
	function returnMessageProp($objects)
	{

		global $session;

		/*print "<br>returnMessageProp<br><textarea cols=50 rows=4>";
		print_r($objects);
		print "</textarea><br>";*/
		
		if (isset($objects)) {
		
		if ($objects->nemid == $this->nemid) {
			if (!isset($objects->override_filter)) {
				$this->override_filter = "yes";
			} elseif ($objects->override_filter==",yes,") {

				$this->override_filter = "yes";

				if (isset($objects->items_from))
				$this->itemsFrom			  = $objects->items_from;

				if (isset($objects->filterDataST) && $objects->filterDataST!="" && $objects->filterDataST=="1")
					$this->filterDataST = " OR ";
				if (isset($objects->filterDataST) && $objects->filterDataST!="" && $objects->filterDataST=="0")
					$this->filterDataST = " AND ";


			} else {
				$this->override_filter = "no";
			}

			$this->schedulingFromStartMessage = $objects->schedulingFromStart;
			$this->schedulingFromEndMessage   = $objects->schedulingFromEnd;
			$this->crdMessage = explode("%M%",$objects->crd);
			$this->findKeywords($objects->subject,'Message');


			if ($objects->setimage!="") 
				$this->setimage = $objects->setimage;


			if ($objects->headline!="") 
				$this->headline = $objects->headline;
			if ($objects->headlineSearch!="") 
				$this->headlineSearch = $objects->headlineSearch;


				$crd = $session->Vars["level_0"].",".$session->Vars["level_1"].",".$session->Vars["level_2"].",".$session->Vars["level_3"].",".$session->Vars["level_4"];
				$this->crdKeywordsMessageSubjectTopics[][$crd] = $crd;

				$this->crdKeywordsMessage[$crd] = $crd;
			}
		}

	}

	function findKeywords($objects,$kwType='')
	{

		$crdKeywords = array();
		if ($kwType == "")
		$crdKeywords = $this->crdKeywords;
		else
		$crdKeywords = $this->crdKeywordsMessage;

		if (isset($objects) && $objects!="") {
			$ZoneIds = $objects->zones->zones_avlb;
			$ZoneIdsArray = explode(",",$ZoneIds);
			while (list($key,$key_zone_id)=each($ZoneIdsArray)) {
				eval("\$zoneObj = \$objects->zones->zone_".$key_zone_id.";");
				if (isset($zoneObj) && isset($zoneObj->selectedcord) && $zoneObj->selectedcord!="" ) {
					$koordNodeFilter = explode("-",$zoneObj->selectedcord);
					while (list($nodFilterKey,$nodeFilterValue)=each($koordNodeFilter)) {
						$nodeFilterValue = trim($nodeFilterValue);
						$kfFilterArray = explode(",",$nodeFilterValue);
						if (count($kfFilterArray)==5) {
							$crdKeywords [$nodeFilterValue] =$nodeFilterValue;
							$crdKeywordsSubjectTopics [$kfFilterArray[0]][$nodeFilterValue] =$nodeFilterValue;
						}
					}
				}
			}
			$KfIds = $objects->keyfamily->filter_avlb;
			$KfIdsArray = explode(",",$KfIds);
			while (list($key,$key_f_id)=each($KfIdsArray)) {
				eval("\$kfObj = \$objects->keyfamily->family_".$key_f_id.";");
				if (isset($kfObj) && isset($kfObj->selectedkey) && $kfObj->selectedkey!="" ) {
					$kfFilter = explode("-",$kfObj->selectedkey);
					while (list($nodFilterKey,$nodeFilterValue)=each($kfFilter)) {
						$keyKeywords [$kfObj->id] =$kfObj->id."-".$nodeFilterValue;
					}
				}
			}
		}
		if (count($crdKeywords)>0) {
			if ($kwType == "") {
					$this->crdKeywords = $crdKeywords;
					$this->crdKeywordsSubjectTopics = $crdKeywordsSubjectTopics;
			} else {
				$this->crdKeywordsMessage = $crdKeywords;
				$this->crdKeywordsMessageSubjectTopics = $crdKeywordsSubjectTopics;
			}
		}
		if (count($keyKeywords)>0) {
			if ($kwType == "")
					$this->keyKeywords = $keyKeywords;
			else	$this->keyKeywordsMessage = $keyKeywords;
		}
	}

	function returnFilterProp($filterId) {
		require_once(INCLUDE_KW_AJAX_PATH.'KwManager.Base.class.php');
		//[fkid] => 11,5	-- idFamiljes,idKeywordit
		$KwObj = new KwManagerInternalFilters($this->ses_userid,$this->lang);
		$fkidArr = explode(",",$filterId);
		$KwObj->setTreePositionProperties("0,".$fkidArr[0],"","findInfo");
		$KwObj->nomenclature_item = $fkidArr[1];
		$KwObj->getFilterInfo();
		return $KwObj->returnFilterProp();
	}

	function getMyDate($dates) {
		// [scheduling_from] => 01.11.2007
			$pieces = explode (".", $dates);

			reset ($pieces);
			$month_label = array (
				"01"=>"{{_Janar}}",
				"02"=>"{{_Shkurt}}",
				"03"=>"{{_Mars}}",
				"04"=>"{{_Prill}}",
				"05"=>"{{_Maj}}",
				"06"=>"{{_Qershor}}",
				"07"=>"{{_Korrik}}",
				"08"=>"{{_Gusht}}",
				"09"=>"{{_Shtator}}",
				"10"=>"{{_Tetor}}",
				"11"=>"{{_Nentor}}",
				"12"=>"{{_Dhjetor}}");
			$dates = $pieces[0]." ".$month_label[$pieces[1]].', '.$pieces[2];

		RETURN $dates;
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