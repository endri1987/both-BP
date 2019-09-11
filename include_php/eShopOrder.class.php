<?php
//INCLUDE(dirname(__FILE__)."/eshop_promocion.php");

//var ido = "91"; 1 - ebooks
/**
 *  A PHP class to order.
 *
 *  @author     Jonida Cuko <jonidacuko@arkit.info>
 *  @version    1.0 (created: 01 May 2015)
 *  @copyright  (c) 2000 - 2015 ArkIt
 *  
 *  per cdo session qe krijohet nga nje user i caktuar 
 *  (me session kuptohet gjithe navigimi qe nga hapja e faqes ne browser nga nje user i caktuar,
 *  derisa sa useri ndryshon faqen apo mbyll browserin), ru
 *  ruhet cdo order qe ben ai ne sherbimet qe kane infrastrukturen per te suportuar orderin.
 *  Dhe per cdo sherbim te ndryshem ruhet nje rekord per cdo session tek tabela eshop_basket 
 *  dhe disa rekorde tek tabela eshop_basket, ku cdo rekord ketu i korrespondon nje item te zgjedhur
 *
**/

define("urlToSubmitToBktConstants",			"https://testvpos.asseco-see.com.tr/fim/est3Dgate"); 
define("clientIdToBktConstants",			"520021937"); 
define("storetypeToBktConstants",			"3d_pay_hosting"); 
define("storekeyBktConstants",				"123456"); 
define("ARIKVO_DAYS_LIMIT",					"7"); 

define("AllowedBktErrorTimes",				"10"); 


define("PortalIndentifikues",				"Open Operating Theatre OOT"); 
define("PortalIndentifikuesLangCode", "en"); 


require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");

class eShopOrder 
{
	var $orderid	= "";
	var $OrderKey	= "";
	var $articleInBasket	= "0";
	var $basketInfo 	= array();
	var $actualBasket 	= array();
	var $basketInfoState 	= array();
	
	var $error_code_basket		= 1; //succes
	var $error_codes_basket 	= array();

	var $error_code_eshop		= 1; //succes
	var $error_codes_eshop 		= array();	
	
	var $lang		= "Lng1";
	var $lngId		= 1;
	var $idstemp	= "";
	var $thisMode	= "";
	var $uniqueid	= "";
	var $ses_userid	= "";
	
	var $orderCheckoutRelatedData 		= array();
	var $allIdsToBasket		= '';
	var $allIdsToBasketArray	= array();
	var $dataGeneral			= array();
	var $allowedTypes			= array();
	
	var $checkoutClient	= "bkt"; //Paypal|Raiffaisen|bkt
	var $bktErrorCodes = array
	   (
			"000"	=> "Approved",
			"101"	=> "Invalid card parameters",
			"105"	=> "Not approved by emitent"
	   );			

	var $RaiffaisenErrorCodes = array
	   (
	   );		

	var $RaiffaisenConstants = array
	   (
			"urlToSubmitTo"	=> urlToSubmitToConstants,		
			"ipRestriction"	=> ipRestrictionConstants,			

			"Version"		=> "1",		
			"MerchantID"	=> MerchantIDConstants,		
			"TerminalID"	=> TerminalIDConstants,	

			"TotalAmount"	=> "",
			"Currency"		=> "008",	//008 ALL
			"locale"		=> "sq",	
			"PurchaseTime"	=> "",			
			"OrderKey"		=> "",			
			"Signature"		=> ""			
	   );		
	
	var $bktConstants = array
	   (
			"urlToSubmitTo"	=> urlToSubmitToBktConstants,		
			"clientid"		=> clientIdToBktConstants,	
			"storetype"		=> storetypeToBktConstants,	
			"storekey"		=> storekeyBktConstants,	

			"Signature"		=> ""			
	   );
	   
	   
	
	/*****************************************************
	*** ABSTRACT CONSTRUCTOR OF THE CLASS ****************
	******************************************************/
	function eShopOrder($idstemp="")
	{
		global $session;
		
		if ($idstemp=="") {
			$this->idstemp = $session->Vars["idstemp"];
		} else {	//CI-57-96-1-79
			$this->idstemp = $idstemp;
		}

		$sessLANG = $session->Vars["lang"];
		if (isset($sessLANG) && $sessLANG!="") {
		
			if (eregi("Lng",$sessLANG)) {
				$lngIDCode = eregi_replace("Lng","",$sessLANG)*1;
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
		
		$this->lngId 		= eregi_replace ("Lng","",$this->lang);
		$this->thisMode		= $session->Vars["thisMode"];
	 	$this->uniqueid		= $session->Vars["uni"];

		if (isset($session->Vars["tip"]))
			$this->tip = $session->Vars["tip"];
		if (isset($session->Vars["ses_userid"])) 
			$this->ses_userid = $session->Vars["ses_userid"];
		$this->userSystemID=$this->ses_userid;
		
		
		$this->allowedTypes["bk"] = "bk";
		//parent::collectorBookBase();
		
		$tplY 			= new WebBox("buildNavX");
		$message_file 	= NEMODULES_PATH."eShop/eShop.mesg";
		$tplY->parse_msg_file($message_file);
		extract($GLOBALS["tplVars"]->Vars[0]);
		
		$this->initUserProfile();
		$this->initUserPreferences();
		$this->getLastExchangeRate();	
		
		if (isset($this->preferences) && count($this->preferences)) {
		while (list($key,$data)=each($this->preferences)) {
			WebApp::addVar($key,"".$data);
		}}
		
		
		
  /*  echo "<textarea>eShopOrder";
    print_r($this->uniqueid.":uniqueid");
    print_r($session->Vars["uniqueid"].":session-uniqueid");
    echo "</textarea>";*/		
		
		
	}
	/**UserProfile*****************************************************************/
	function initUserPreferences(){
		global $session;
/*		
		$this->preferences = array();
		if ($this->userSystemID!=2) {
		
			$getIfEx =" SELECT coalesce(users_extended.currency_id) as monedhID, currency_name
						  FROM users_extended 
						  JOIN eshop_currency 
						    ON eshop_currency.currency_id = users_extended.currency_id
						   AND active = 'y' 
						 WHERE UserId ='".$this->userSystemID."'";	
			$rs_getIfEx = WebApp::execQuery($getIfEx);
			if(!$rs_getIfEx->EOF())  {
				$monedhID = $rs_getIfEx->Field("monedhID");
				if ($monedhID!="" && $monedhID>0) {
					$this->preferences["defaultMonedhe"] = $monedhID;
					$this->preferences["sessionMonedhe"] = $monedhID;
					
					$this->preferences["currencySelected"] = $rs_getIfEx->Field("currency_name");
				}
			}
		}

		$getIfEx =" SELECT coalesce(z_session_preferences.monedhID) as monedhID, currency_name 
					  FROM z_session_preferences 
						  JOIN eshop_currency 
						    ON eshop_currency.currency_id = z_session_preferences.monedhID
						   AND active = 'y' 
					 WHERE uni = '".$session->Vars["uni"]."'";	
		$rs_getIfEx = WebApp::execQuery($getIfEx);
		if(!$rs_getIfEx->EOF())  {
			$monedhID = $rs_getIfEx->Field("monedhID");
			if ($monedhID!="" && $monedhID>0) {
				$this->preferences["sessionMonedhe"] = $monedhID;
				$this->preferences["currencySelected"] = $rs_getIfEx->Field("currency_name");
			}
		}
		
		if (!isset($this->preferences["sessionMonedhe"])) {
			
			$getIfEx =" SELECT coalesce(monedhID) as monedhID, currency_name 
							  FROM eshop_currency 

						 WHERE active = 'y' AND is_reference = 'y'";	
			$rs_getIfEx = WebApp::execQuery($getIfEx);
			if(!$rs_getIfEx->EOF())  {
				$monedhID = $rs_getIfEx->Field("monedhID");
				if ($monedhID!="" && $monedhID>0) {
					$this->preferences["sessionMonedhe"] = $monedhID;
					$this->preferences["currencySelected"] = $rs_getIfEx->Field("currency_name");
				}
			} else {
				$this->preferences["sessionMonedhe"] = 5;
				$this->preferences["currencySelected"] = "LEK";
			}
			
			
			
			
			
			
			
			$this->preferences["sessionMonedhe"] = 5;
		}
		
		WebApp::addVar("sessionMonedhe",$this->preferences["sessionMonedhe"]);
		*/
		
		
				$this->preferences["sessionMonedhe"] = 1;
				$this->preferences["currencySelected"] = "EURO";		
		
		
		
	}
		
	function initUserProfile(){
		global $session;
		$this->idstemp = $session->Vars["idstemp"];
		$this->returnNemProp();	
		$this->profile_step_id=$this->NEM_PROP["profile_step_id"];
		$this->prepareLinksFromNemProp();
	}
	
	
	
	function controlUserAdressBook($idaddress) 
	{
		/*$id_address = 0;
		
		$getIfEx =" SELECT id_address FROM user_address WHERE id_address = '".$idaddress."' AND id_user ='".$this->userSystemID."'";	
		$rs_getIfEx = WebApp::execQuery($getIfEx);
		if(!$rs_getIfEx->EOF()) 
			$id_address = $rs_getIfEx->Field("id_address");
		
		return $id_address;*/
	}
	function GetMaxUserAdressBook() 
	{
		/*$getMax ="SELECT  max(id_address) as id_max FROM user_address";			
		$rs_max = WebApp::execQuery($getMax);
		if(!$rs_max->EOF())		$id_max = $rs_max->Field("id_max") + 1;
		else 					$id_max = 1;
		return $id_max; */
	}

	function makeDefaultUserAdressBook($id_address="") 
	{
		/*$updateAdr = "UPDATE user_address SET address_used = '0'
					   WHERE id_user    		= '".$this->userSystemID."'";
		WebApp::execQuery($updateAdr);		

		$updateAdr = "UPDATE user_address SET address_used = '1'
					   WHERE id_address      	= '".$id_address."' AND id_user = '".$this->userSystemID."'";
		WebApp::execQuery($updateAdr);	*/	
	}	
	function deleteUserAdressBook($id_address="") 
	{
		//$del_adrs="DELETE FROM user_address WHERE id_address = '".$id_address."' AND id_user = '".$this->userSystemID."'";
		//WebApp::execQuery($del_adrs);	
	}	




	function saveAdressBook($id_address="",$prop) {
		/*if ($id_address!="" && $id_address>0) {
		
		} else {
			$id_address = $this->GetMaxUserAdressBook();
			$del_adrs="INSERT INTO user_address (id_address,id_user) VALUES ('".$id_address."','".$this->userSystemID."')";
			WebApp::execQuery($del_adrs);				
		}
		
		$updateAdr = "UPDATE user_address
							  SET address_name     		= '".ValidateVarFun::f_real_escape_string($prop['name'])."',
								  address_street 			= '".ValidateVarFun::f_real_escape_string($prop['address'])."',
								  address_city 			= '".ValidateVarFun::f_real_escape_string($prop['city'])."',
								  address_zip 			= '".ValidateVarFun::f_real_escape_string($prop['zipcode'])."',
								  address_country_id 	= '".ValidateVarFun::f_real_escape_string($prop['country_id'])."',
								  address_phone 			= '".ValidateVarFun::f_real_escape_string($prop['tel'])."',
								  address_email 			= '".ValidateVarFun::f_real_escape_string($prop['email'])."'
								  
							WHERE id_address      	= '".$id_address."' AND 
								  id_user    		= '".$this->userSystemID."'";
		WebApp::execQuery($updateAdr);	*/   		
	}	
	function editAdressBook($id_address=""){

		/*$index=0;
		$gridDataAdrs["data"][$index]["id_address"] 		=""; 
		$gridDataAdrs["data"][$index]["address_name"] 		=""; 
		$gridDataAdrs["data"][$index]["address_street"] 		=""; 
		$gridDataAdrs["data"][$index]["address_street_ext"] 		=""; 
		$gridDataAdrs["data"][$index]["address_city"] 		=""; 
		$gridDataAdrs["data"][$index]["address_state"]		=""; 
		$gridDataAdrs["data"][$index]["address_zip"] 		=""; 
		$gridDataAdrs["data"][$index]["address_country"] 	=""; 
		$gridDataAdrs["data"][$index]["address_phone"] 		=""; 
		$gridDataAdrs["data"][$index]["address_email"] 		=""; 
		$gridDataAdrs["data"][$index]["address_type"] 		=""; 
		$gridDataAdrs["data"][$index]["address_used"] 		=""; 


		if ($id_address!="" && $id_address>0) {
			
			
			///get data
			$getInfo ="
				SELECT   id_address,
				
						 COALESCE(address_name,			'') AS address_name,
						 COALESCE(address_street,			'') AS address_street,
						 COALESCE(address_city,			'') AS address_city,
						 COALESCE(address_state,			'') AS address_state,
						 COALESCE(address_country,		'') AS address_country,
						 COALESCE(address_zip,			'') AS address_zip,
						 COALESCE(address_phone,			'') AS address_phone,
						 COALESCE(address_email,			'') AS address_email,
						 COALESCE(address_used,			'') AS address_used,
						 COALESCE(address_country_id,		'') AS address_country_id,
					 COALESCE(z_countries_code.country_name,'') AS country_name

				FROM user_address 		
		   LEFT JOIN ".ESHOP_STORE_DB.".z_countries_code ON z_countries_code.id = user_address.address_country_id
				WHERE id_user		= '".$this->userSystemID."'  
				  AND id_address	= '".$id_address."'";	
			
			$rs_info = WebApp::execQuery($getInfo);
	
			
			
			while (!$rs_info->EOF()) {
				
				$gridDataAdrs["data"][$index]["id_address"] 			= $rs_info->Field("id_address");
				$gridDataAdrs["data"][$index]["address_name"] 			= $rs_info->Field("address_name");
				$gridDataAdrs["data"][$index]["address_street"] 			= $rs_info->Field("address_street");
				$gridDataAdrs["data"][$index]["address_street_ext"] 			= $rs_info->Field("address_street_ext");
				$gridDataAdrs["data"][$index]["address_city"] 			= $rs_info->Field("address_city");
				$gridDataAdrs["data"][$index]["address_state"]			= $rs_info->Field("address_state");
				$gridDataAdrs["data"][$index]["address_zip"] 			= $rs_info->Field("address_zip");
				$gridDataAdrs["data"][$index]["address_country"] 		= $rs_info->Field("address_country");
				$gridDataAdrs["data"][$index]["address_phone"] 			= $rs_info->Field("address_phone");
				$gridDataAdrs["data"][$index]["address_email"] 			= $rs_info->Field("address_email");
				$gridDataAdrs["data"][$index]["address_type"] 			= $rs_info->Field("address_type");
				$gridDataAdrs["data"][$index]["address_country_id"] 		= $rs_info->Field("address_country_id");
				$this->AdrBk["deliveryCountryId"] = $rs_info->Field("address_country_id");
				$rs_info->MoveNext();
				
			}	
		}

		$gridDataAdrs["AllRecs"] = count($gridDataAdrs["data"]);
		WebApp::addVar("editAdressBookGrid",$gridDataAdrs);*/
	}	
	function userAddressBook(){

			
/*
CREATE TABLE `user_address` (
  `id_address` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `address_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_email` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_phone` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street_ext` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_zip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_city` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_state` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_country` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_country_id` int(11) DEFAULT NULL,
  `address_type` enum('s','b','o') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'o',
  `address_used` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_address`)
)
*/			
			$getInfo ="
				SELECT   id_address,
						 id_user,
						 IF(address_name is NULL,' ',address_name) as address_name,
						 IF(address_email is NULL,' ',address_email) as address_email,						 
						 IF(address_phone is NULL,' ',address_phone) as address_phone,
						 
						 IF(address_street is NULL,' ',address_street) as address_street,
						 IF(address_street_ext is NULL,' ',address_street_ext) as address_street_ext,
						 
						 IF(address_zip is NULL,' ',address_zip) as address_zip,
						 IF(address_city is NULL,' ',address_city) as address_city,
						 IF(address_state is NULL,' ',address_state) as address_state,
						 IF(address_country is NULL,' ',address_country) as address_country,
						 
						 IF(address_used is NULL,'0',address_used) as address_used,
						 address_type,
						 COALESCE(address_country_id,		'') AS address_country_id,
						COALESCE(z_countries_code.country_name,'') AS country_name

				FROM user_address 				  				 
		   LEFT JOIN ".ESHOP_STORE_DB.".z_countries_code ON z_countries_code.id = user_address.address_country_id
			   WHERE id_user ='".$this->userSystemID."'  
			ORDER BY address_name";	
			
			$rs_info = WebApp::execQuery($getInfo);
			$index=0;
			while (!$rs_info->EOF()) {
				
				$gridDataAdrs["data"][$index]["id_address"] 		= $rs_info->Field("id_address");
				$gridDataAdrs["data"][$index]["address_name"] 		= $rs_info->Field("address_name");
				$gridDataAdrs["data"][$index]["address_street"] 		= $rs_info->Field("address_street");
				$gridDataAdrs["data"][$index]["address_street_ext"] 		= $rs_info->Field("address_street_ext");
				$gridDataAdrs["data"][$index]["address_city"] 		= $rs_info->Field("address_city");
				$gridDataAdrs["data"][$index]["address_state"]		= $rs_info->Field("address_state");
				$gridDataAdrs["data"][$index]["address_zip"] 		= $rs_info->Field("address_zip");
				$gridDataAdrs["data"][$index]["address_country"] 	= $rs_info->Field("address_country");
				$gridDataAdrs["data"][$index]["address_phone"] 		= $rs_info->Field("address_phone");
				$gridDataAdrs["data"][$index]["address_email"] 		= $rs_info->Field("address_email");
				//$gridDataAdrs["data"][$index]["address_type"] 		= $rs_info->Field("address_type");
				$gridDataAdrs["data"][$index]["address_used"] 		= $rs_info->Field("address_used");
				$gridDataAdrs["data"][$index]["country_name"] 		= $rs_info->Field("country_name");
				
				
				$index++;
				$rs_info->MoveNext();
			}	
		$gridDataAdrs["AllRecs"] = count($gridDataAdrs["data"]);
		WebApp::addVar("myAdressBookGrid",$gridDataAdrs);
	}		

	function getUserOrderStatusClassification($workflow_status)
	{
		$returnData = array();
		if ($workflow_status == "partial_stock") { 
			//duhet te dale interface qe i lejon userit, 
				//-te konfirmoje orderin e pjeshem 
				//-ta anulloje ate
				//-ose ta ktheje ne wish list, ne menyre te tille qe kur librat te gjendet ai te beje blerjen e plote
			$status_class		= "orange";
			$status_label 		= "{{_st_partial_stock_needs_confirm}}";
			$status_label_sh 	= "{{_st_partial_stock_needs_confirm_sh}}";

		} elseif ($workflow_status == "Delivered") {  
			//orderi eshte dorezuar

			$status_class 		= "green";
			$status_label 		= "{{_st_delivered_succesfully}}";
			$status_label_sh 	= "{{_st_delivered_succesfully_sh}}";


		} elseif ($workflow_status == "VoidOrder" 
			   || $workflow_status == "Canceled" 
			   || $workflow_status == "VoidOrderFromUser" 
			) {  
			//orderi eshte anulluar

			$status_class 		= "red";
			$status_label 		= "{{_st_canceled_succesfully}}";
			$status_label_sh 	= "{{_st_canceled_succesfully_sh}}";

		} elseif ($workflow_status == "PreOrderToCheck" 
			   || $workflow_status == "preOrder" 
			   || $workflow_status == "empty_stock" 
			   || $workflow_status == "checkedWithBkt" 
			) {  
			//orderi eshte anulluar

			$status_class		= "grey";
			$status_label		= "{{_st_waiting_to_be_proccessed}}";
			$status_label_sh	= "{{_st_waiting_to_be_proccessed_sh}}";
			

		} else {
			//orderi eshte ne proces
			$status_class		= "yellow";
			$status_label		= "{{_st_stock_is_confirmed_in_her_way}}";
			$status_label_sh	= "{{_st_stock_is_confirmed_in_her_way_sh}}";
		}

		$returnData["status_class"] 			= $status_class;
		$returnData["status_label"] 			= $status_label;	
		$returnData["status_label_sh"] 			= $status_label_sh;	
		
		return $returnData;
//'PreOrderToCheck',
//'preOrder',

//'checkedWithBkt',

//'confirmed_stock',
//'postOrder',
//'DorezoNePost',

//'Delivered',

//'empty_stock',
//'VoidOrder',
//'Canceled',

//'partial_stock',
//'PostOrderFromUser',
//'VoidOrderFromUser',		
	}

	function myOrderDetails($oid="")
	{
		if ($oid=="") {
			$oid = $this->orderid;
		}
		
		$this->error_code = 0;	
		$this->orderKeyData[$this->orderKey] = array();
		$this->IGI = array();







			$getProfileRelatedData = "
					SELECT 				id_satelit, orderKey, orderid,currency_id,
							
										COALESCE(payer_first_name,          '') AS payer_first_name,

										COALESCE(payer_last_name,           '') AS payer_last_name,
										COALESCE(payer_title,              	'') AS payer_title,
										COALESCE(payer_email,              	'') AS payer_email,
										COALESCE(payer_contact_phone,		'') AS payer_contact_phone,

										COALESCE(bll_name,					'') AS bll_name,
										COALESCE(bll_email,					'') AS bll_email,
										COALESCE(bll_phone,					'') AS bll_phone,								
										COALESCE(bll_address_street,        '') AS bll_address_street,
										COALESCE(bll_address_street_ext,	'') AS bll_address_street_ext,
										COALESCE(bll_address_zip,			'') AS bll_address_zip,
										COALESCE(bll_address_city,			'') AS bll_address_city,
										COALESCE(bll_address_state,			'') AS bll_address_state,
										COALESCE(bll_address_country,		'') AS bll_address_country,

										COALESCE(dlv_name,					'') AS dlv_name,
										COALESCE(dlv_email,					'') AS dlv_email,
										COALESCE(dlv_phone,					'') AS dlv_phone,
										COALESCE(dlv_address_street,		'') AS dlv_address_street,
										COALESCE(dlv_address_street_ext,	'') AS dlv_address_street_ext,
										COALESCE(dlv_address_zip,			'') AS dlv_address_zip,
										COALESCE(dlv_address_city,			'') AS dlv_address_city,
										COALESCE(dlv_address_state,			'') AS dlv_address_state,
										COALESCE(dlv_address_country,		'') AS dlv_address_country,
										
										date_format(PurchaseTime,'%d.%m.%Y') as PurchaseTime,
		
										COALESCE(TotalAmount,				'0') AS TotalAmount,
										COALESCE(PriceDelivery,				'0') AS PriceDelivery,
										COALESCE(PriceProducts,				'0') AS PriceProducts,
						 
										COALESCE(callback_TranCode,			'empty') AS callback_TranCode,									 
										COALESCE(callback_ApprovalCode,		'empty') AS callback_ApprovalCode,
										COALESCE(callback_ProxyPan,			'empty') AS callback_ProxyPan,
										
										workflow_status,
										
										COALESCE(kodi_postes,			'empty') AS kodi_postes,
										COALESCE(kodi_postes_gjurmim,	'empty') AS kodi_postes_gjurmim,
										
										COALESCE(z_method_payment.description_of_method,						'') AS menyra_pageses_label,
										COALESCE(z_method_payment.payment_method,			'') AS menyra_pageses,
										

										COALESCE(z_method_delivery.description_of_method,						'') AS menyra_dorezimit_label,
										COALESCE(z_method_delivery.delivery_method,			'') AS menyra_dorezimit
										
										
										
									
										


					   FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
					   
					   LEFT JOIN ".ESHOP_STORE_DB.".z_method_payment
			 	 			  ON eshop__checkout_cart.menyra_pageses = z_method_payment.payment_method
			 
					   LEFT JOIN ".ESHOP_STORE_DB.".z_method_delivery
			 	 			  ON eshop__checkout_cart.menyra_dorezimit = z_method_delivery.delivery_method
			 	   
								   WHERE id_satelit= '".SATELIT_ID."'
								     AND orderid = '".$oid."'
								     ";		

			$relatedData = WebApp::execQuery($getProfileRelatedData);	
			if(!$relatedData->EOF()) {

	/*	global $objBookGlossar;
			
			
			if (isset($objBookGlossar->isSetGlobalObj) && $objBookGlossar->isSetGlobalObj=="yes") {
			} else {
				$objBookGlossar = new collectorBookBase();
			}	*/

					
					
					$currency_id 		= $relatedData->Field("currency_id");
					
					
					$this->IGI["currencyCheckOrderId"] 		= $currency_id;
					$this->IGI["currencyCheckSelectedDesc"] = $this->exchange["active"][$currency_id];						
					$this->IGI["currencySelectedDesc"] 		= $this->IGI["currencyCheckSelectedDesc"];						
					
					
					$workflow_status 	= $relatedData->Field("workflow_status");
					
					$returnData = $this->getUserOrderStatusClassification($workflow_status);
	
					$this->IGI["status_class"] 			= $returnData["status_class"];
					$this->IGI["status_label"] 			= $returnData["status_label"];
					$this->IGI["status_label_sh"] 		= $returnData["status_label_sh"];
					
					$this->IGI["menyra_dorezimit"] 			= $menyra_dorezimit;
					
					$this->IGI["menyra_pageses"] 			= $relatedData->Field("menyra_pageses");
					$this->IGI["menyra_pageses_label"] 		= $relatedData->Field("menyra_pageses_label");
					
					$this->IGI["menyra_dorezimit"] 			= $relatedData->Field("menyra_dorezimit");
					$this->IGI["menyra_dorezimit_label"] 	= $relatedData->Field("menyra_dorezimit_label");
			
					$this->IGI["workflow_status"] 			= $workflow_status;
					$this->IGI["orderid"] 					= $oid;
					$this->IGI["orderKey"] 					= $relatedData->Field("orderKey");
					$this->IGI["PurchaseTime"] 				= $relatedData->Field("PurchaseTime");
					$this->IGI["TotalAmount"] 				= $relatedData->Field("TotalAmount");
					$this->IGI["PriceDelivery"] 			= $relatedData->Field("PriceDelivery");
					$this->IGI["PriceProducts"] 			= $relatedData->Field("PriceProducts");
					$this->IGI["callback_TranCode"] 		= $relatedData->Field("callback_TranCode");
					$this->IGI["callback_ApprovalCode"] 	= $relatedData->Field("callback_ApprovalCode");
					$this->IGI["callback_ProxyPan"] 		= $relatedData->Field("callback_ProxyPan");
					$this->IGI["workflow_status"] 			= $relatedData->Field("workflow_status");
				
					$this->IGI["kodi_postes"] 				= $relatedData->Field("kodi_postes");
					$this->IGI["trackingCode"] 				= $relatedData->Field("kodi_postes_gjurmim");
				
					$this->IGI["payer_first_name"] 			= $relatedData->Field("payer_first_name");
					$this->IGI["payer_last_name"]     		= $relatedData->Field("payer_last_name");
					$this->IGI["payer_title"]         		= $relatedData->Field("payer_title");
					$this->IGI["payer_email"]         		= $relatedData->Field("payer_email");
					$this->IGI["payer_contact_phone"] 		= $relatedData->Field("payer_contact_phone");

					$this->IGI["bll_name"] 					= $relatedData->Field("bll_name");
					$this->IGI["bll_email"] 				= $relatedData->Field("bll_email");
					$this->IGI["bll_phone"] 				= $relatedData->Field("bll_phone");
					$this->IGI["bll_address_street"] 		= $relatedData->Field("bll_address_street");
					$this->IGI["bll_address_street_ext"] 	= $relatedData->Field("bll_address_street_ext");
					$this->IGI["bll_address_zip"]        	= $relatedData->Field("bll_address_zip");
					$this->IGI["bll_address_city"]       	= $relatedData->Field("bll_address_city");
					$this->IGI["bll_address_state"]      	= $relatedData->Field("bll_address_state");
					$this->IGI["bll_address_country"]    	= $relatedData->Field("bll_address_country");
					
					
						//----------------------------------------------------//
						$billing_info["data"][0] = array();
						$billing_info["data"][0]["full_name"] 			= $this->IGI["bll_name"];
						$billing_info["data"][0]["address_phone"] 		= $this->IGI["bll_phone"];
						$billing_info["data"][0]["address_email"] 		= $this->IGI["bll_email"];	

						$billing_info["data"][0]["address_street"] 		= $this->IGI["bll_address_street"];
						$billing_info["data"][0]["address_zip"] 		= $this->IGI["bll_address_zip"];
						$billing_info["data"][0]["address_city"] 		= $this->IGI["bll_address_city"];

						$billing_info["data"][0]["address_state"]		= $this->IGI["bll_address_state"];
						$billing_info["data"][0]["address_country"] 		= $this->IGI["bll_address_country"];	
						
						$billing_info["AllRecs"] = count($billing_info["data"]);	
						WebApp::addVar("billing_info_Grid", $billing_info);	
					
					
					

					$this->IGI["dlv_name"] 					= $relatedData->Field("dlv_name");
					$this->IGI["dlv_email"]					= $relatedData->Field("dlv_email");
					$this->IGI["dlv_phone"]					= $relatedData->Field("dlv_phone");
					$this->IGI["dlv_address_street"] 		= $relatedData->Field("dlv_address_street");
					$this->IGI["dlv_address_street_ext"] 	= $relatedData->Field("dlv_address_street_ext");
					$this->IGI["dlv_address_zip"]     		= $relatedData->Field("dlv_address_zip");
					$this->IGI["dlv_address_city"]    		= $relatedData->Field("dlv_address_city");
					$this->IGI["dlv_address_city"]    		= $relatedData->Field("dlv_address_city");
					$this->IGI["dlv_address_state"]   		= $relatedData->Field("dlv_address_state");
					$this->IGI["dlv_address_country"] 		= $relatedData->Field("dlv_address_country");
					$this->IGI["dlv_address_country_id"] 	= $relatedData->Field("dlv_address_country_id");
					
					 	
						
	

						//----------------------------------------------------//
						$shipping_info["data"][0] = array();
						$shipping_info["data"][0]["full_name"] 			= $this->IGI["dlv_name"];
						$shipping_info["data"][0]["address_phone"] 		= $this->IGI["dlv_phone"];
						$shipping_info["data"][0]["address_email"] 		= $this->IGI["dlv_email"];	

						$shipping_info["data"][0]["address_street"] 	= $this->IGI["dlv_address_street"];
						$shipping_info["data"][0]["address_zip"] 		= $this->IGI["dlv_address_zip"];
						$shipping_info["data"][0]["address_city"] 		= $this->IGI["dlv_address_city"];

						$shipping_info["data"][0]["address_state"]		= $this->IGI["dlv_address_state"];
						$shipping_info["data"][0]["address_country"] 	= $this->IGI["dlv_address_country"];						

						
						$shipping_info["AllRecs"] = count($shipping_info["data"]);	
						WebApp::addVar("shipping_info_Grid", $shipping_info);			
					
					
					
					

					$tmpArticleDetails["data"]=array();
					$inn = 0;
					
					$totalPrice 	= 0;
					$totalWeight 	= 0;
					$totalWeightB 	= 0;

					$getBooksList = " SELECT id_item, quantity,price,description_item, id_promocion, is_available,
											 description_item, code_item,
											 if (is_available='n',1,2) as ord
											 
										FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
										JOIN ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
										  ON eshop__checkout_cart.id_satelit =  eshop__checkout_cart_articles.id_satelit
										 AND eshop__checkout_cart.orderid =  eshop__checkout_cart_articles.orderid
									   WHERE eshop__checkout_cart.id_satelit= '".SATELIT_ID."'
										 AND eshop__checkout_cart.orderid = '".$oid."'
									ORDER BY ord";

					$rs = WebApp::execQuery($getBooksList);		
					WHILE (!$rs->EOF()) {
						
						$id_item 		= $rs->Field("id_item");
						$is_available 	= $rs->Field("is_available");
						$quantity 		= $rs->Field("quantity");

						
						$tmpArticleDetails["data"][$inn]["id_item"] 			= $id_item;
						$tmpArticleDetails["data"][$inn]["is_available"] 		= $rs->Field("is_available");
						$tmpArticleDetails["data"][$inn]["description_item"] 	= $rs->Field("description_item");
						$tmpArticleDetails["data"][$inn]["code_item"] 			= $rs->Field("code_item");
						$tmpArticleDetails["data"][$inn]["quantity"] 			= $quantity;
						
						$tmpArticleDetails["data"][$inn]["price"] 				= $rs->Field("price");
						$tmpArticleDetails["data"][$inn]["NewPrice"]			= $rs->Field("price");
						$tmpArticleDetails["data"][$inn]["NewQuantity"]			= $quantity;
						
						
						$totalWeightB +=$quantity*$b_weight;	
						
						if ($workflow_status=="partial_stock") {
							
							
							$objBookGlossar->getBookDetaje($id_item);
							$this->basketbookDetails[$id_item] = $objBookGlossar->bookDetails[$id_item];			
							$objBookGlossar->ConstructBookBuyOnlineDetails($id_item);
							$this->dataBIstock[$id_item] = $objBookGlossar->dataBIstock[$id_item];
							$b_weight = $this->basketbookDetails[$id_item]["b_weight"];
							$tmpArticleDetails["data"][$inn]["b_weight"] 			= $b_weight;
							
							
						$tmpArticleDetails["data"][$inn]["emertimi_librit"] 			= $this->basketbookDetails[$id_item]["emertimi_librit"];
							
							
							
							if ($is_available=="y") {
							   $nrArtStateY++;
								$totalPrice  +=$quantity*$rs->Field("price");
								$totalWeight +=$quantity*$b_weight;	
							} elseif ($is_available=="n") {		
								$nrArtStateN++;
								$tmpArticleDetails["data"][$inn]["NewPrice"]		= "0";	
								$tmpArticleDetails["data"][$inn]["NewQuantity"]		= "0";	
								$tmpArticleDetails["data"][$inn]["cls"]			= " rmv";
							} else {										 	
								$nrArtStateX++;	
								$this->error_code = 2;	
								$tmpArticleDetails["data"][$inn]["cls"]			= " error";
							} 
							$tmpArticleDetails["data"][$inn]["Nprice"] = "".$tmpArticleDetails["data"][$inn]["NewPrice"]*$tmpArticleDetails["data"][$inn]["NewQuantity"];
						
						} 

						$tmpArticleDetails["data"][$inn]["Oprice"] = "".number_format($tmpArticleDetails["data"][$inn]["price"]*$quantity,2);
						$inn++;
						$rs->MoveNext();
					}

					$this->error_code = 1;	
			}	//menyra_dorezimit
		//echo "$totalWeightB -  $totalWeight<br>";
		
		if ($workflow_status=="partial_stock") {
		
			$difDelivery = $this->getParcialDeliveryMethod($oid,$totalWeight);
			$newPriceDelivery = $this->IGI["PriceDelivery"] - $difDelivery;

			$this->IGI["NewTotalWeight"] 	= $totalWeight;
			$this->IGI["NewPriceProducts"] 	= $totalPrice;
			$this->IGI["newPriceDelivery"] 	= $newPriceDelivery;
			$this->IGI["newTotalAmount"] 	= $this->IGI["NewPriceProducts"]+$this->IGI["newPriceDelivery"];

			$updatePricePromo = "UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
								  SET PartialPriceDelivery     = '".$this->IGI["newPriceDelivery"]."',
									  PartialPriceProducts     = '".$this->IGI["NewPriceProducts"]."',
									  PartialTotalAmount     	= '".$this->IGI["newTotalAmount"]."'

								WHERE id_satelit      	= '".SATELIT_ID."' AND 
									  orderid    		= '".$oid."'     ";
			WebApp::execQuery($updatePricePromo);	
		}
								
		$tmpArticleDetails["AllRecs"] 	= count($tmpArticleDetails["data"]);
		WebApp::addVar("orderDetailsItems",$tmpArticleDetails);	
		
		
		$this->tmpArticleDetails = $tmpArticleDetails;
		

		$orderDetails["data"][0] = $this->IGI;
		$orderDetails["AllRecs"] = count($orderDetails["data"]);
		WebApp::addVar("orderDetails",$orderDetails);	
		
		while (list($key,$value)=each($this->IGI)) {
			WebApp::addVar($key,"".$value);	
		}
			
	}
	function getParcialDeliveryMethod($orderid,$totalWeight)
	{
		//$totalWeight = 29999;
		$TotalPriceDelivery = 0;
	
		$selectedRulesDelivery = array();
		$getPostaDeliveryT = "SELECT checkT.id_tarif, checkT.id_subTarif, checkT.tarif_type, checkT.price_type, checkT.delivery_to_type, 
										checkT.tarif_type_extended, max_allowed_weight, price,
										coalesce(price_partial,'') as price_partial
										
								FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_extra_tarif as checkT
								JOIN ".ESHOP_STORE_DB.".z_posta_delivery_types as delTp
								  ON checkT.id_tarif = delTp.id
								 AND checkT.delivery_to_type = delTp.delivery_to_type

							   WHERE checkT.id_satelit	= '".SATELIT_ID."'
								 AND checkT.orderid		= '".$orderid."'
								 AND checkT.user_selection   ='y'
								 AND checkT.tarif_type = 'delivery'";

		$rs_PostaDeliveryT = WebApp::execQuery($getPostaDeliveryT);	
		if (!$rs_PostaDeliveryT->EOF()) { 
		
			
			$priceBefore 			= $rs_PostaDeliveryT->Field("price");
			$priceAfter 			= $priceBefore;
			
			$id_tarif 				= $rs_PostaDeliveryT->Field("id_tarif");
			$id_subTarif 			= $rs_PostaDeliveryT->Field("id_subTarif");
			$tarif_type 			= $rs_PostaDeliveryT->Field("tarif_type");
			$price_type 			= $rs_PostaDeliveryT->Field("price_type");
			$delivery_to_type 		= $rs_PostaDeliveryT->Field("delivery_to_type");
			$tarif_type_extended 	= $rs_PostaDeliveryT->Field("tarif_type_extended");
			
			$max_allowed_weight =  	$rs_PostaDeliveryT->Field("max_allowed_weight");
			
			$priceAfter =  	$rs_PostaDeliveryT->Field("price_partial");
			
			if ($price_partial!="") {
				return 	$priceBefore-$priceAfter;
			} else {
					//echo "$max_allowed_weight:max_allowed_weight<br>$totalWeight:totalWeight<br>";	
					if ($totalWeight<$max_allowed_weight) {
						$getDeliveryMethodPrice = "
							SELECT price

							  FROM ".ESHOP_STORE_DB.".z_posta_delivery_tarifa 
							 WHERE status = 'y' 
							   AND z_posta_delivery_tarifa.id = '".$id_tarif."' 
							   AND delivery_to_zone			 = '".$tarif_type_extended."'
							   AND pesha_from	<= '".$totalWeight."' 
							   AND pesha_to		>= '".$totalWeight."'";	

							$rs_PostaDeliveryTP = WebApp::execQuery($getDeliveryMethodPrice);	
							if(!$rs_PostaDeliveryTP->EOF()) {

								$priceAfter				=  $rs_PostaDeliveryTP->Field("price");
								//echo "$price:PRICE<br><br>";
								$updatePricePromo = "UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart_extra_tarif
													  SET price_partial     = '".$priceAfter."'
													  
													WHERE id_satelit      	= '".SATELIT_ID."' AND 
														  orderid    		= '".$orderid."'     AND 
														  id_tarif    		= '".$id_tarif."'     AND 
														  id_subTarif    	= '".$id_subTarif."'     AND 
														  price_type    	= '".$price_type."'     AND 
														  delivery_to_type  = '".$delivery_to_type."'     AND 
														  tarif_type      	= '".$tarif_type."'";
								WebApp::execQuery($updatePricePromo);	
							} else {
								//echo "NUK U GJET";
							}
						} else {
						//error code
					}
						//echo "$priceBefore:$priceAfter -getParcialDeliveryMethod<br><br>";
						return 	$priceBefore-$priceAfter		   ;
						   
				/*$rs_PostaDeliveryTP = WebApp::execQuery($getDeliveryMethodPrice);	
				if(!$rs_PostaDeliveryTP->EOF()) {
				
					$price 				=  $rsDeliveryTarif->Field("price");
					
					$id_subTarif		= $rsDeliveryTarif->Field("id_type");
					
					echo "$price:price<br><br>";							
					if ($totalWeight<$max_allowed_weight) {
					
					} else {
					
						$getDeliveryTarifRule = "
								SELECT id_type,delivery_to_zone,pesha_from,pesha_to,price, pesha_njesiToFormule
								  FROM ".ESHOP_STORE_DB.".z_posta_delivery_tarifa_formule
								 WHERE z_posta_delivery_tarifa_formule.id = '".$id_tarif."' 
								   AND pesha_from <= '".$totalWeight."' 
								   AND pesha_to	  >= '".$totalWeight."' 
								";	
					$rsDeliveryTarifRule    = WebApp::execQuery($getDeliveryTarifRule);
					if(!$rsDeliveryTarifRule->EOF()) {

						$pesha_from 			= $rsDeliveryTarifRule->Field("pesha_from");
						
						$ricalculate_remaing 	= $pesha_from-1;
						$totalWeightToFormule 	= $totalWeight-$pesha_from;
						$pesha_njesiToFormule 	= $rsDeliveryTarifRule->Field("pesha_njesiToFormule");
						
						$priceFormule = $rsDeliveryTarifRule->Field("price");
						
						$cntToFormule 		= ceil($totalWeightToFormule/$pesha_njesiToFormule);
						$pricecalculated 	= $priceFormule*$cntToFormule;
						
						echo "$pricecalculated:pricecalculatedTot<br><br>";							
							$pricecalculated += $rsDeliveryTarifLeft->Field("price");
												
						
						echo "$pricecalculated:pricecalculatedTot<br><br>";							
						
						
						$TotalPriceDelivery = $pricecalculated;
					}									
				}*/
		} }
	}	
	
	function partialOrderExecute($action,$oid){
			$getProfileRelatedData = "
					SELECT	id_satelit, orderKey, orderid, workflow_status,
					
							COALESCE(PartialPriceDelivery,          '') AS PartialPriceDelivery,
							COALESCE(PartialPriceProducts,          '') AS PartialPriceProducts,
							COALESCE(PartialTotalAmount,         	'') AS PartialTotalAmount						

					   FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
								   WHERE id_satelit= '".SATELIT_ID."'
								     AND orderid = '".$oid."'";		

			$relatedData = WebApp::execQuery($getProfileRelatedData);	
			if(!$relatedData->EOF()) {

					$this->IGI["orderid"] 					= $oid;
					$this->IGI["orderKey"] 					= $relatedData->Field("orderKey");
					$this->IGI["workflow_status"] 			= $relatedData->Field("workflow_status");
					
					$this->IGI["PartialPriceDelivery"] 		= $relatedData->Field("PartialPriceDelivery");
					
					$this->IGI["PartialPriceProducts"] 		= $relatedData->Field("PartialPriceProducts");
					$this->IGI["PartialTotalAmount"] 		= $relatedData->Field("PartialTotalAmount");
					
					$this->error_code = 1;	
					if ($this->IGI["workflow_status"]!='partial_stock') {
						$this->error_code = 31;	//orderi eshte ne nje status qe nuk perputhet me kerkesens
					} else {}
			} else {
					$this->error_code = 30;	//nuk u gjet orderi, kerkese e parregullt
			}


			INCLUDE_ONCE(BP_INC_PATH.'bkt.api.request.class.php');
			if ($this->error_code == 1) { //kerkese e rregullt

				if ($_REQUEST["action"]=='2') { //confirm partial order
					$ApiBktR 		= new BktApiRequest($this->IGI["orderKey"],$_REQUEST["apprcss"],$this->IGI["workflow_status"]);
					$ApiBktR->PartialPostAuthorizationQueryXml($this->IGI["PartialTotalAmount"]); 			//tentohet te behet cancelimit
					$ApiBktR->controlRequestBktResponse();
					if ($ApiBktR->errorCommunicationCode == "ok") {
							$this->StatusQueryXmlData = $ApiBktR->lg;
							$this->ControlPartialPostAuthorizationQueryXml();
					} 
				}	

				if ($_REQUEST["action"]=='3') { //cancel partial order
					$ApiBktR 		= new BktApiRequest($this->IGI["orderKey"],$_REQUEST["apprcss"],$this->IGI["workflow_status"]);
					$ApiBktR->VoidQueryXml(); 			//tentohet te behet cancelimit
					$ApiBktR->controlRequestBktResponse();
					if ($ApiBktR->errorCommunicationCode == "ok") {
							$this->StatusQueryXmlData = $ApiBktR->lg;
							$this->ControlVoidQueryXml();
					} 
				}	
			}
		
		
		
	/*	echo "ControlPostauthorizationQueryXml<textarea>";
		print_r($ApiBktR);
		print_r($this);
		echo "</textarea>";	*/	
		
		
		

	}	
	
	
/*
`workflow_status` enum(
	'PreOrderToCheck',
	'preOrder',
		'checkedWithBkt',
		'confirmed_stock',
		'postOrder',
		'DorezoNePost',
		'Delivered',
		
		'empty_stock',
		'VoidOrder',
		'Canceled',
		
		'partial_stock',
	
	'ConfirmFromUser',
	'CanceledFromUser',
	
	'ErrorOrder') ,

*/	
	
	/*******Control Bkt Api Communication Status *******************************/
	function eshop__checkout_cart_workflow ()
	{
		$updateStepRelatedData= "
				UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
				   SET workflow_status = '".$this->PPBAH["workflow_status"]."',
				   	   workflow_status_code_error = '".$this->PPBAH["transaction_step"]."'
				 WHERE orderKey    = '".$this->IGI["orderKey"]."'";
		 WebApp::execQuery($updateStepRelatedData);		
	}
	function save_checkout_cart_workflow ($workflow_status_actual,$workflow_status)
	{
		$insertCartHistory = "INSERT INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_workflow 
							(	orderKey, 				workflow_status_actual, 		workflow_status, 		record_userId )
					VALUES 
							(	'".$this->IGI["orderKey"]."', '".$workflow_status_actual."', '".$workflow_status."', '".$this->userSystemID."')";
		WebApp::execQuery($insertCartHistory);		
	}		
	function eshop__checkout_cart_history ()
	{
		$insertCartHistory = "REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_history 
				( 	orderKey,
					transaction_step, transaction_description,
					workflow_status_actual, workflow_status,

					ORIG_TRANS_AMT, AUTH_DTTM, CAPTURE_AMT, CAPTURE_DTTM, VOID_DTTM,

					Response,AuthCode,HostRefNum,ProcReturnCode,
					TransId,ErrMsg,record_userId
				)
				VALUES 
				(	'".$this->IGI["orderKey"]."',
					'".$this->PPBAH["transaction_step"]."', '".$this->PPBAH["transaction_description"]."',
					'".$this->PPBAH["actualWorkflowStatus"]."', '".$this->PPBAH["workflow_status"]."',

					'".$this->PPBAH["ORIG_TRANS_AMT"]."','".$this->PPBAH["AUTH_DTTM"]."','".$this->PPBAH["CAPTURE_AMT"]."','".$this->PPBAH["CAPTURE_DTTM"]."','".$this->PPBAH["VOID_DTTM"]."',

					'".$this->PPBAH["Response"]."','".$this->PPBAH["AuthCode"]."','".$this->PPBAH["HostRefNum"]."','".$this->PPBAH["ProcReturnCode"]."',
					'".$this->PPBAH["TransId"]."','".$this->PPBAH["ErrMsg"]."','".$this->userSystemID."'
				)";
		WebApp::execQuery($insertCartHistory);		
	}	
	function ControlVoidQueryXml ()
	{
			$workflow_status_actual	=  "partial_stock";
			$workflow_status_new	=  "VoidOrderFromUser";

			$this->PPBAH["ORIG_TRANS_AMT"]				= $this->StatusQueryXmlData["extra"][0]["ORIG_TRANS_AMT"];	
			$this->PPBAH["AUTH_DTTM"]					= $this->StatusQueryXmlData["extra"][0]["AUTH_DTTM"];	
			$this->PPBAH["CAPTURE_AMT"]					= $this->StatusQueryXmlData["extra"][0]["CAPTURE_AMT"];	
			$this->PPBAH["CAPTURE_DTTM"]				= $this->StatusQueryXmlData["extra"][0]["CAPTURE_DTTM"];	
			$this->PPBAH["VOID_DTTM"]					= $this->StatusQueryXmlData["extra"][0]["VOID_DTTM"];	
			$this->PPBAH["VOID_DTTM"]					= $this->StatusQueryXmlData["extra"][0]["VOID_DTTM"];	
			$this->PPBAH["transaction_step"]			= "succes";	//'succes','failed'
			$this->PPBAH["transaction_description"]		= "";			//pershkrimi i errorit
			
			$this->PPBAH["actualWorkflowStatus"]		= $workflow_status_actual;	
			$this->PPBAH["workflow_status"] 			= $workflow_status_new;
			
			$updateWorkflowStatus = "
				UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
				   SET 
						workflow_status				= '".$workflow_status_new."',
						workflow_status_code_error = 'Succes'
				 WHERE id_satelit= '".SATELIT_ID."'
				   AND orderKey = '".$this->IGI["orderKey"]."'
				   AND workflow_status = '".$workflow_status_actual."'";

			WebApp::execQuery($updateWorkflowStatus);		
		
			$this->eshop__checkout_cart_workflow();	
			$this->eshop__checkout_cart_history();	
			$this->save_checkout_cart_workflow($workflow_status_actual,$workflow_status_new);
			
		//	exec("php ".INC_PATH."saveEmailNotifications.php  ".$this->IGI["orderKey"]." ".$workflow_status_new." >/dev/null &");						
	}
	function ControlPartialPostAuthorizationQueryXml ()
	{
		if ($this->StatusQueryXmlData["ProcReturnCode"] =="00") {
		
			$workflow_status_actual	=  "partial_stock";
			$workflow_status_new	=  "PostOrderFromUser";

			$this->PPBAH["ProcReturnCode"]			= 	$this->StatusQueryXmlData["ProcReturnCode"];	
			$this->PPBAH["Response"]				= 	$this->StatusQueryXmlData["Response"];	
			$this->PPBAH["AuthCode"]				= 	$this->StatusQueryXmlData["AuthCode"];	
			$this->PPBAH["HostRefNum"]				= 	$this->StatusQueryXmlData["HostRefNum"];	
			$this->PPBAH["TransId"]					= 	$this->StatusQueryXmlData["TransId"];	
			$this->PPBAH["ErrMsg"]					= 	$this->StatusQueryXmlData["ErrMsg"];	

			$this->PPBAH["ORIG_TRANS_AMT"]				= 	$this->StatusQueryXmlData["extra"][0]["ORIG_TRANS_AMT"];	
			$this->PPBAH["AUTH_DTTM"]					= 	$this->StatusQueryXmlData["extra"][0]["AUTH_DTTM"];	
			$this->PPBAH["CAPTURE_AMT"]					= 	$this->StatusQueryXmlData["extra"][0]["CAPTURE_AMT"];	
			$this->PPBAH["CAPTURE_DTTM"]				= 	$this->StatusQueryXmlData["extra"][0]["CAPTURE_DTTM"];	
			$this->PPBAH["VOID_DTTM"]					= 	$this->StatusQueryXmlData["extra"][0]["VOID_DTTM"];	
			
			$this->PPBAH["transaction_step"]			= 	"succes";	//'succes','failed'
			$this->PPBAH["transaction_description"]		= 	"";	//pershkrimi i errorit
			$this->PPBAH["actualWorkflowStatus"]		= 	$workflow_status_actual;	
			$this->PPBAH["workflow_status"]				= 	$workflow_status_new;	
	
			$updateWorkflowStatus = "
				UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
				   SET 
						workflow_status				= '".$workflow_status_new."',
						workflow_status_code_error = 'Succes'
				 WHERE id_satelit= '".SATELIT_ID."'
				   AND orderKey = '".$this->IGI["orderKey"]."'
				   AND workflow_status = '".$workflow_status_actual."'";

			WebApp::execQuery($updateWorkflowStatus);				


			$this->eshop__checkout_cart_workflow();	
			$this->eshop__checkout_cart_history();	
			$this->save_checkout_cart_workflow($workflow_status_actual,$workflow_status_new);

	//exec("php ".INC_PATH."saveEmailNotifications.php  ".$this->IGI["orderKey"]." ".$workflow_status_new." >/dev/null &");
		}
	}		
	/*******Porosite e mia *******************************/
	function myLastOrder(){

		$getSatelitOrders = "SELECT eshop__checkout_cart.orderid,

									orderKey,
									workflow_status,
									
									payer_id,
									
									coalesce(bll_name, '') as  bll_name,
									coalesce(bll_email, payer_email) as  bll_email,
									coalesce(bll_phone, payer_contact_phone) as  bll_phone,
									
									
									coalesce(payer_title, '') as  payer_title,
									coalesce(payer_first_name, '') as  payer_first_name,
									coalesce(payer_last_name, '') as  payer_last_name,
									coalesce(payer_title, '') as  payer_title,
														
									bll_address_street,bll_address_zip,bll_address_city,bll_address_state,
									
									coalesce(bll_address_country, bll_address_state) as  bll_address_country,
									     
									date_format(PurchaseTime,'%d.%m.%Y  %h:%i:%s') as PurchaseTime,
									
									PriceDelivery,PriceProducts,TotalAmount
								
									     
									FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
							
								  
								  WHERE eshop__checkout_cart.id_satelit= '".SATELIT_ID."'
								     AND eshop__checkout_cart.payer_id= '".$this->userSystemID."'
								     
							    ORDER BY PurchaseTime desc
							    limit 0,1";


		$rsSatelitOrders = WebApp::execQuery($getSatelitOrders);	
		
		if (!$rsSatelitOrders->EOF()) {
			$this->orderid  = $rsSatelitOrders->Field("orderid");		
		}

	}

	function myOrders(){
			
		
		
		
		
		
		
		$displaySatelist["data"] = array();
		$indexSPU = 0;
		$getSatelitOrders = "SELECT eshop__checkout_cart.orderid,

									orderKey,
									workflow_status,
									
									payer_id,
									
									coalesce(bll_name, '') as  bll_name,
									coalesce(bll_email, payer_email) as  bll_email,
									coalesce(bll_phone, payer_contact_phone) as  bll_phone,
									
									
									coalesce(payer_title, '') as  payer_title,
									coalesce(payer_first_name, '') as  payer_first_name,
									coalesce(payer_last_name, '') as  payer_last_name,
									coalesce(payer_title, '') as  payer_title,
														
									bll_address_street,bll_address_zip,bll_address_city,bll_address_state,
									
									coalesce(bll_address_country, bll_address_state) as  bll_address_country,
									     
									date_format(PurchaseTime,'%d.%m.%Y  %h:%i:%s') as PurchaseTime,
									
									PriceDelivery,PriceProducts,TotalAmount
								
									     
									FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
							
								  
								  WHERE eshop__checkout_cart.id_satelit= '".SATELIT_ID."'
								     AND eshop__checkout_cart.payer_id= '".$this->userSystemID."'
								     
							    ORDER BY PurchaseTime desc";



		$rsSatelitOrders = WebApp::execQuery($getSatelitOrders);	
		
		WHILE (!$rsSatelitOrders->EOF()) {
			$inn =0;
			$orderid  = $rsSatelitOrders->Field("orderid");
			
					$getBooksList = " SELECT id_item, quantity,price,description_item, id_promocion, is_available,
											 description_item, code_item,
											 if (is_available='n',1,2) as ord
											 
										FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
										JOIN ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
										  ON eshop__checkout_cart.id_satelit =  eshop__checkout_cart_articles.id_satelit
										 AND eshop__checkout_cart.orderid =  eshop__checkout_cart_articles.orderid
									   WHERE eshop__checkout_cart.id_satelit= '".SATELIT_ID."'
										 AND eshop__checkout_cart.orderid = '".$orderid."'
									ORDER BY ord";

					$rs = WebApp::execQuery($getBooksList);		
					WHILE (!$rs->EOF()) {
						
						$id_item 		= $rs->Field("id_item");
						$is_available 	= $rs->Field("is_available");
						$quantity 		= $rs->Field("quantity");

						
						$tmpArticleDetails["data"][$inn]["id_item"] 			= $id_item;
						$tmpArticleDetails["data"][$inn]["description_item"] 	= $rs->Field("description_item");
						$tmpArticleDetails["data"][$inn]["code_item"] 			= $rs->Field("code_item");
					
						$inn++;
						$rs->MoveNext();
					}
					
					$tmpArticleDetails["AllRecs"] 	= count($tmpArticleDetails["data"]);
					WebApp::addVar("orderDetailsItems_$orderid",$tmpArticleDetails);						

					
					
			
			$displaySatelist["data"][$indexSPU]["orderid"] = $rsSatelitOrders->Field("orderid");
			$displaySatelist["data"][$indexSPU]["orderKey"] = $rsSatelitOrders->Field("orderKey");
			
			$displaySatelist["data"][$indexSPU]["payer_id"] 			= $rsSatelitOrders->Field("payer_id");
			$displaySatelist["data"][$indexSPU]["BillContactInfo"] 		= $rsSatelitOrders->Field("bll_name");
			$displaySatelist["data"][$indexSPU]["bll_phone"] 			= $rsSatelitOrders->Field("bll_phone");
			$displaySatelist["data"][$indexSPU]["bll_email"] 			= $rsSatelitOrders->Field("bll_email");
			
		
/*
								
									coalesce(payer_title, '') as  payer_title,
									coalesce(payer_first_name, '') as  payer_first_name,
									coalesce(payer_last_name, '') as  payer_last_name,
									coalesce(payer_title, '') as  payer_title,
*/		
		
		
			$billing_info["data"] = array();
			
			$billing_info["data"][0]["full_name"] 			= $rsSatelitOrders->Field("bll_name");
			
			//if ($rsSatelitOrders->Field("bll_name")=="") {
				$billing_info["data"][0]["full_name"] = $rsSatelitOrders->Field("payer_first_name")." ".$rsSatelitOrders->Field("payer_last_name");
			//}
			
			$billing_info["data"][0]["address_phone"] 		= $rsSatelitOrders->Field("bll_phone");
			$billing_info["data"][0]["address_email"] 		= $rsSatelitOrders->Field("bll_email");	

			$billing_info["data"][0]["address_street"] 		= $rsSatelitOrders->Field("bll_address_street");
			$billing_info["data"][0]["address_zip"] 		= $rsSatelitOrders->Field("bll_address_zip");
			$billing_info["data"][0]["address_city"] 		= $rsSatelitOrders->Field("bll_address_city");

			$billing_info["data"][0]["address_state"]		= $rsSatelitOrders->Field("bll_address_state");
			$billing_info["data"][0]["address_country"] 	= $rsSatelitOrders->Field("bll_address_country");
			$billing_info["data"][0]["address_phone"] 		= $rsSatelitOrders->Field("bll_phone");
			$billing_info["data"][0]["address_email"] 		= $rsSatelitOrders->Field("bll_email");

			$billing_info["AllRecs"] = count($billing_info["data"]);	
			WebApp::addVar("billing_info_Grid_$orderid", $billing_info);	
			
		
	/*	echo "<br>billing_info-<textarea>";
		print_r($billing_info);
		echo "</textarea>";		*/
			
			
								
			$displaySatelist["data"][$indexSPU]["labelClr"] 			= $rsSatelitOrders->Field("labelClr");
			$displaySatelist["data"][$indexSPU]["labelSt"] 				= $rsSatelitOrders->Field("labelSt");
			
			
			
			$displaySatelist["data"][$indexSPU]["PriceProducts"] 		= $rsSatelitOrders->Field("PriceProducts");
			$displaySatelist["data"][$indexSPU]["PriceDelivery"] 		= $rsSatelitOrders->Field("PriceDelivery");
			$displaySatelist["data"][$indexSPU]["TotalAmount"] 			= $rsSatelitOrders->Field("TotalAmount");
			
			$displaySatelist["data"][$indexSPU]["PurchaseTime"] = $rsSatelitOrders->Field("PurchaseTime");
			
			$indexSPU++;
			$rsSatelitOrders->MoveNext();
		}

		$displaySatelist["AllRecs"] = count($displaySatelist["data"]);
		WebApp::addVar("ReadyToCheckAndDeliverGrid",$displaySatelist);
		
		
		
	
	}
	function myOrdersArchive(){
		$displaySatelist["data"] = array();
		$indexSPU = 0;
		$getSatelitOrders = "SELECT eshop__checkout_cart.orderid,

									orderKey,
									workflow_status,
									
									payer_id,bll_name,bll_email,bll_phone,
									date_format(PurchaseTime,'%d.%m.%Y') as PurchaseTime,
									     
									     dlv_name,dlv_email,dlv_phone,dlv_address_street,dlv_address_street_ext,dlv_address_zip,
									     dlv_address_city,dlv_address_state,dlv_address_country,dlv_address_country_id,
									     
									     PriceDelivery,PriceProducts,TotalAmount,
									     
									     if (workflow_status in ('DorezoNePost','Delivered'),'1',
											 if (workflow_status in ('VoidOrder','Canceled'),'2',
														'3'
											 )									     
									     ) as ord,
									     
									     if (workflow_status in ('preOrder'),'1',
										 if (workflow_status in ('checkedWithBkt'),'2',
											 if (workflow_status in ('confirmed_stock'),'3',
											 if (workflow_status in ('postOrder'),'4',
											 if (workflow_status in ('DorezoNePost'),'5',
											 if (workflow_status in ('Delivered'),'6',
												 if (workflow_status in ('empty_stock'),'7',
												 if (workflow_status in ('VoidOrder'),'8',
												 if (workflow_status in ('Canceled'),'9',
													 if (workflow_status in ('partial_stock'),'10',
													 if (workflow_status in ('ConfirmFromUser'),'11',
														'12'

													 )
													 )
												 )
												 )
												 )
											 )
											 )
											 )
											 )
										 )									     
									     ) as ord1
									     
								
									     
									     
									FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
							   LEFT JOIN ".ESHOP_STORE_DB.".eshop__checkout_notification
							   		  ON eshop__checkout_notification.id_satelit = eshop__checkout_cart.id_satelit
							   		 AND eshop__checkout_notification.orderid 	 = eshop__checkout_cart.orderid
							   		 AND eshop__checkout_notification.notif_step_type 	 = eshop__checkout_cart.workflow_status
								   WHERE eshop__checkout_cart.id_satelit= '".SATELIT_ID."'
								     AND eshop__checkout_cart.payer_id= '".$this->userSystemID."'
								     AND PurchaseTime <  DATE_ADD(NOW(), INTERVAL -".ARIKVO_DAYS_LIMIT." DAY)
								     
							    ORDER BY ord,ord1";




		$rsSatelitOrders = WebApp::execQuery($getSatelitOrders);	
		

		
		
		
		
		WHILE (!$rsSatelitOrders->EOF()) {
			
			$displaySatelist["data"][$indexSPU]["orderid"] = $rsSatelitOrders->Field("orderid");
			$displaySatelist["data"][$indexSPU]["workflow_status"] = $rsSatelitOrders->Field("workflow_status");
			$displaySatelist["data"][$indexSPU]["workflow_status_label"] = "{{_".$rsSatelitOrders->Field("workflow_status")."_label}}";
			
			
			$displaySatelist["data"][$indexSPU]["tr_class"] = "red";
			$ord= $rsSatelitOrders->Field("ord");
			if ($ord==1)
				$displaySatelist["data"][$indexSPU]["tr_class"] = "orange";
			elseif ($ord==2)
				$displaySatelist["data"][$indexSPU]["tr_class"] = "green";
			elseif ($ord==3)
				$displaySatelist["data"][$indexSPU]["tr_class"] = "blue";
			elseif ($ord==4)
				$displaySatelist["data"][$indexSPU]["tr_class"] = "red";	
			
			
			$existNotification = $rsSatelitOrders->Field("existNotification");
			$displaySatelist["data"][$indexSPU]["existNotification"] = $existNotification;
			if ($existNotification=="exist") {
				$displaySatelist["data"][$indexSPU]["notif_subject"] 	= $rsSatelitOrders->Field("notif_subject");
				$displaySatelist["data"][$indexSPU]["sent_from"] 		= $rsSatelitOrders->Field("sent_from");
				$displaySatelist["data"][$indexSPU]["sent_to"] 			= $rsSatelitOrders->Field("sent_to");
				$displaySatelist["data"][$indexSPU]["is_send"] 			= $rsSatelitOrders->Field("is_send");
				$displaySatelist["data"][$indexSPU]["date_send"] 		= $rsSatelitOrders->Field("date_send");
			}
			
			  
			$displaySatelist["data"][$indexSPU]["transaction_step"] = $rsSatelitOrders->Field("transaction_step");
			$displaySatelist["data"][$indexSPU]["orderKey"] = $rsSatelitOrders->Field("orderKey");
			
			$displaySatelist["data"][$indexSPU]["payer_id"] 			= $rsSatelitOrders->Field("payer_id");
			$displaySatelist["data"][$indexSPU]["BillContactInfo"] 		= $rsSatelitOrders->Field("bll_name");
			$displaySatelist["data"][$indexSPU]["bll_phone"] 			= $rsSatelitOrders->Field("bll_phone");
			$displaySatelist["data"][$indexSPU]["bll_email"] 			= $rsSatelitOrders->Field("bll_email");
			
			
			$displaySatelist["data"][$indexSPU]["dlv_name"] 			= $rsSatelitOrders->Field("dlv_name");
			$displaySatelist["data"][$indexSPU]["dlv_email"] 			= $rsSatelitOrders->Field("dlv_email");
			$displaySatelist["data"][$indexSPU]["dlv_phone"] 			= $rsSatelitOrders->Field("dlv_phone");
			
			$displaySatelist["data"][$indexSPU]["dlv_address_street"] 			= $rsSatelitOrders->Field("dlv_address_street");
			$displaySatelist["data"][$indexSPU]["dlv_address_zip"] 			= $rsSatelitOrders->Field("dlv_address_zip");
			$displaySatelist["data"][$indexSPU]["dlv_address_city"] 			= $rsSatelitOrders->Field("dlv_address_city");
			$displaySatelist["data"][$indexSPU]["dlv_address_state"] 			= $rsSatelitOrders->Field("dlv_address_state");
			$displaySatelist["data"][$indexSPU]["dlv_address_country"] 			= $rsSatelitOrders->Field("dlv_address_country");
			
			
			
			$displaySatelist["data"][$indexSPU]["PriceProducts"] 		= $rsSatelitOrders->Field("PriceProducts");
			$displaySatelist["data"][$indexSPU]["PriceDelivery"] 		= $rsSatelitOrders->Field("PriceDelivery");
			$displaySatelist["data"][$indexSPU]["TotalAmount"] 			= $rsSatelitOrders->Field("TotalAmount");
			
			$displaySatelist["data"][$indexSPU]["PurchaseTime"] = $rsSatelitOrders->Field("PurchaseTime");
			
			$indexSPU++;
			$rsSatelitOrders->MoveNext();
		}

		$displaySatelist["AllRecs"] = count($displaySatelist["data"]);
		WebApp::addVar("MyOrdersArchiveGrid",$displaySatelist);
		
		
		/*echo "<br>MyOrdersArchiveGrid-<textarea>";
		print_r($displaySatelist);
		echo "</textarea>";	*/			
	}	
	/**End UserProfile*****************************************************************/
	function prepareLinksFromNemProp()
	{
		global $objBookGlossar;
		
		$dtkoord = $this->setURLs["koord"];


/*echo "<textarea>";
print_r($this->NEM_PROP);
echo "</textarea>";*/

//	0,26,0,0,0)')
		
		
		WebApp::addVar("go_to_mainList_flag","no");
		if (isset($this->NEM_PROP["targeted_phoneList"]) && $this->NEM_PROP["targeted_phoneList"]!="") {
			
			$go_to_targeted_list = preg_replace("#k=#","",$this->NEM_PROP["targeted_phoneList"]);
			$objBookGlossar->setURLs["profile"]["koord"]["mainList"] = $go_to_targeted_detaje;
			$objBookGlossar->setURLs["profile"]["url"]["mainList"] 		= "javascript:GoTo('thisPage?event=none.srm(k=".$go_to_targeted_list.")')";
			
			WebApp::addVar("go_to_mainList_target_k",$go_to_targeted_list);
			
			
			WebApp::addVar("go_to_mainList_target",$objBookGlossar->setURLs["profile"]["url"]["mainList"]);
			if ($objBookGlossar->setURLs["isCached"]== "yes") {
				$objBookGlossar->setURLs["profile"]["urlC"]["mainList"] = $objBookGlossar->generatedCachedUrl($go_to_targeted_list);
				WebApp::addVar("go_to_mainList_target",$objBookGlossar->setURLs["profile"]["urlC"]["mainList"]);
			}
			WebApp::addVar("go_to_mainList_flag","yes");
		}			
		
		
		WebApp::addVar("go_to_orderList_flag","no");
		if (isset($this->NEM_PROP["targeted_orderList"]) && $this->NEM_PROP["targeted_orderList"]!="") {
			
			$go_to_targeted_detaje = preg_replace("#k=#","",$this->NEM_PROP["targeted_orderList"]);
			$objBookGlossar->setURLs["profile"]["koord"]["orderList"] = $go_to_targeted_detaje;
			$objBookGlossar->setURLs["profile"]["url"]["orderList"] 		= "javascript:GoTo('thisPage?event=none.srm(k=".$go_to_targeted_detaje.")')";
			WebApp::addVar("go_to_orderList_target",$objBookGlossar->setURLs["profile"]["url"]["orderList"]);
			if ($objBookGlossar->setURLs["isCached"]== "yes") {
				$objBookGlossar->setURLs["profile"]["urlC"]["orderList"] = $objBookGlossar->generatedCachedUrl($go_to_targeted_detaje);
				WebApp::addVar("go_to_orderList_target",$objBookGlossar->setURLs["profile"]["urlC"]["orderList"]);
			}
			WebApp::addVar("go_to_orderList_flag","yes");
		}		
	
		WebApp::addVar("go_to_orderDetaje_flag","no");
		if (isset($this->NEM_PROP["targeted_detaje"]) && $this->NEM_PROP["targeted_detaje"]!="") {
			
			$go_to_targeted_detaje = preg_replace("#k=#","",$this->NEM_PROP["targeted_detaje"]);
			
			$objBookGlossar->setURLs["profile"]["koord"]["orderDetails"] = $go_to_targeted_detaje;
			$objBookGlossar->setURLs["profile"]["url"]["orderDetails"] 		= "javascript:GoTo('thisPage?event=none.srm(oid={{orderid}};kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};k=".$go_to_targeted_detaje.")')";
			
			WebApp::addVar("go_to_orderDetaje_target",$objBookGlossar->setURLs["profile"]["url"]["orderDetails"]);
			if ($objBookGlossar->setURLs["isCached"]== "yes") {
				$objBookGlossar->setURLs["profile"]["urlC"]["orderDetails"] = $objBookGlossar->generatedCachedUrl($go_to_targeted_detaje,$dtkoord["ActualPage"])."?oid={{orderid}}";
				WebApp::addVar("go_to_orderDetaje_target",$objBookGlossar->setURLs["profile"]["urlC"]["orderDetails"]);
			}
			WebApp::addVar("go_to_orderDetaje_flag","yes");
		}		
		
		
		
		WebApp::addVar("go_to_basket_flag","no");
		if (isset($this->NEM_PROP["targeted_basket"]) && $this->NEM_PROP["targeted_basket"]!="") {
			
			$go_to_basket_target = preg_replace("#k=#","",$this->NEM_PROP["targeted_basket"]);
			
			$objBookGlossar->setURLs["basket"]["koord"]["basket_full"] = $go_to_basket_target;
			$objBookGlossar->setURLs["basket"]["url"]["basket_full"] 		= "javascript:GoTo('thisPage?event=none.srm(k=".$go_to_basket_target.")')"; //kc={{level_0}},{{level_1}},{{level_2}},{{level_3}},{{level_4}};
			WebApp::addVar("go_to_basket_target",$objBookGlossar->setURLs["basket"]["url"]["basket_full"]);
			if ($objBookGlossar->setURLs["isCached"]== "yes") {
				$objBookGlossar->setURLs["basket"]["urlC"]["basket_full"] = $objBookGlossar->generatedCachedUrl($go_to_basket_target);
				WebApp::addVar("go_to_basket_target",$objBookGlossar->setURLs["basket"]["urlC"]["basket_full"]);
			}
			
			WebApp::addVar("go_to_basket_flag","yes");
		}

		WebApp::addVar("go_to_checkout_flag","no");
		if (isset($this->NEM_PROP["targeted_checkout"]) && $this->NEM_PROP["targeted_checkout"]!="") {
			$go_targeted_checkout = preg_replace("#k=#","",$this->NEM_PROP["targeted_checkout"]);
			
			$objBookGlossar->setURLs["basket"]["koord"]["checkout_module_full"] = $go_targeted_checkout;
			$objBookGlossar->setURLs["basket"]["url"]["checkout_module_full"] 		= "javascript:GoTo('thisPage?event=none.srm(k=".$go_targeted_checkout.")')";
			
			WebApp::addVar("go_to_checkout_target",$objBookGlossar->setURLs["basket"]["url"]["checkout_module_full"]);
			
			if ($objBookGlossar->setURLs["isCached"]== "yes") {
				$objBookGlossar->setURLs["basket"]["urlC"]["checkout_module_full"] 		= $objBookGlossar->generatedCachedUrl($go_targeted_checkout);
				WebApp::addVar("go_to_checkout_target",$objBookGlossar->setURLs["basket"]["urlC"]["checkout_module_full"]);
			}
			
			WebApp::addVar("go_to_checkout_flag","yes");
		}
		
		WebApp::addVar("go_to_browse_flag","no");	
		$this->setURLs = $objBookGlossar->setURLs;
	}
	function InitBasket($idstemp="")
	{
		global $session;
		if ($idstemp=="") {
			$this->idstemp = $session->Vars["idstemp"];
		} else {	//CI-57-96-1-79
			$this->idstemp = $idstemp;
		}
		$this->returnNemProp();
		$this->prepareLinksFromNemProp();
	}	
	function returnNemProp()
	{
		global $session;
		$objectsTmp = unserialize(base64_decode(WebApp::findNemProp($this->idstemp)));
		
			if (is_array($objectsTmp)) {
			
			//	$objects =  (object)$objectsTmp;
				$objects =$objectsTmp;
			} else {
			//	$objects =$objectsTmp;
					$objects =  (array)$objectsTmp;
			}			
		
		if (isset($objects->templateType) && $objects->templateType!="") {
			$this->templateType = $objects->templateType;
		  //selektohet template -------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$this->templateType."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
			   {$this->templateFileName = $rs->Field("template_box");} 
		  //---------------------------------------------------------------------------------------------------------------------		
		}	
		
		
    	$this->NEM_PROP = $objects; //kjo eshte kapur qe te gjitha propertite qe mund te shtohen te kapen aty ku duhen vetme nese duhen
	}	
	
	
	/*******Basket Funcionality *******************************/
	function BasketRefreshCell() 
	{
				
				//PROMOCIONI ------------------------------------------------------------
					/*$promocioni_array = cmimi_promocional("", $this->orderid);
					if (isset($promocioni_array) && count($promocioni_array)>0) {
					While (list($id_item,$val)=each($promocioni_array)) {

						IF ($val["cmimi_i_userit"] > 0)
						   {

								$promocion_id_sel = "'".$val["pro_id"]."'";
								$cmimi_i_userit = "'".$val["cmimi_i_userit"]."'";

								$updatePricePromo = "UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles
													  SET id_promocion     	= '".$val["pro_id"]."',
														  price 			= '".$val["cmimi_i_userit"]."'
													WHERE orderid      		= '".$this->orderid."' AND 
														  item_type    		= '".$item_type."'     AND 
														  id_item      		= '".$id_item."'";
								WebApp::execQuery($updatePricePromo);	                    
							}
						ELSE
						   {
								$sqlArticles = "SELECT COALESCE(cmimi1, '') AS cmimi_real 
												  FROM ".ESHOP_STORE_DB.".eshop_artikulli 
												 WHERE ".ESHOP_STORE_DB.".artikulli.id_artikulli = '".$id_item."'";
								$rsA = WebApp::execQuery($sqlArticles);  									
								$updatePricePromo = "UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles
													  SET id_promocion     	= NULL,
														  price 			= '".$rsA->Field("cmimi_real")."'
													WHERE orderid      		= '".$this->orderid."' AND 
														  item_type    		= '".$item_type."'     AND 
														  id_item      		= '".$id_item."'";
								WebApp::execQuery($updatePricePromo);	                    
						   }
					}}*/
				//PROMOCIONI ------------------------------------------------------------		
	}
	
	function controlAndFixBasketStateAfterRegistration($userID)
	{
		IF ($userID != 2) {
              	$orderid_uni = "0";
				$orderid_user = "0";
			  //KAPIM ORDERIN E FUNDIT TE UNIT AKTUAL --------------------------------------------------------------------------
				$sql_order = "SELECT orderid as orderid_uni
								FROM ".ESHOP_STORE_DB.".eshop__basket
							   WHERE uniqueid = '".$this->uniqueid."' AND order_type='basket' AND order_step='current_basket' ";	
				$rso       = WebApp::execQuery($sql_order);
				IF (!$rso->EOF()) $orderid_uni = $rso->Field("orderid_uni");

				if ($orderid_uni>0) {
					//BEHET LIDHJA E USERIT ME BASKETIN AKTUAL
					$updCurrBasket = "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET payer_id = '".$userID."' WHERE orderid = '".$orderid_uni."' ";
					WebApp::execQuery($updCurrBasket);	                   
				}
		}
		//per userin e loguar popullojme vlerat nga profili i tij --------------------------------------------------------------
	}
	function controlAndFixBasketStateAfterLogin()
	{
		/*echo "<textarea>controlAndFixBasketStateAfterLogin";
		print_r($this);
		echo "</textarea>";*/
		
		
		IF ($this->ses_userid != 2) {
              	
              	
              	$orderid_uni = "0";
              	$orderid_user = "0";
              //KAPIM ORDERIN E FUNDIT TE USERIT NGA SESSIONE TE KALUAR--------------------------------------------------------------------------
			    $sql_order = "SELECT orderid AS orderid_user
                                FROM ".ESHOP_STORE_DB.".eshop__basket
                               WHERE payer_id = '".$this->ses_userid."' AND order_type='basket' AND order_step='current_basket' ";	
                $rso	   = WebApp::execQuery($sql_order);
                
                IF (!$rso->EOF())
                					$orderid_user = $rso->Field("orderid_user");
                
              //KAPIM ORDERIN E FUNDIT TE UNIT AKTUAL --------------------------------------------------------------------------
			    $sql_order = "SELECT orderid as orderid_uni
                                FROM ".ESHOP_STORE_DB.".eshop__basket
                               WHERE uniqueid = '".$this->uniqueid."' AND order_type='basket' AND order_step='current_basket' ";	
                $rso       = WebApp::execQuery($sql_order);
                IF (!$rso->EOF())
                					$orderid_uni = $rso->Field("orderid_uni");
                
               // trigger_error($orderid_uni);
               // trigger_error($orderid_user);
                
                
                if ($orderid_uni>0 && $orderid_user>0) {
                	//DO TI BEJME MERGE ORDERIT TE SESSIONIT TE KALUAR ME ORDERIN AKTUAL TE LIDHUR ME SESSIONIN
                	
					$updCurrBasket = "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET uniqueid = '".$this->uniqueid."' WHERE orderid = '".$orderid_user."' ";
					WebApp::execQuery($updCurrBasket);	 

					//fshihet orderi i lidhur me unin ne rastin e userit te paloguar
					$delActualUniBasket = "DELETE FROM  ".ESHOP_STORE_DB.".eshop__basket WHERE eshop__basket.orderid = '".$orderid_uni."' ";
					WebApp::execQuery($delActualUniBasket);	 

					$updArt = "UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles SET orderid = '".$orderid_user."' WHERE orderid = '".$update_Art."' ";
					WebApp::execQuery($updArt);	 						

					$updExtraTar = "UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif SET orderid = '".$orderid_user."' WHERE orderid = '".$orderid_uni."' ";
					WebApp::execQuery($updExtraTar);	 
						//basket behen merge ne basketin e fundit te krijuar nga useri
                } else if ($orderid_user>0) {
                		//KA VETEM BASKET NGA SESSIONI I FUNDIT I LIDHUR ME USERIN, BEHET UPDATE UNI QE TE KONDIDEROHET ACTUAL BASKET
 						$updCurrBasket = "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET uniqueid = '".$this->uniqueid."' WHERE orderid = '".$orderid_user."' ";
						WebApp::execQuery($updCurrBasket);	                		
                } else if ($orderid_uni>0) {
 						//BEHET LIDHJA E USERIT ME BASKETIN AKTUAL
 						$updCurrBasket = "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET payer_id = '".$this->ses_userid."' WHERE orderid = '".$orderid_uni."' ";
						
					//	trigger_error($updCurrBasket);
						
						WebApp::execQuery($updCurrBasket);	                   
                }
		}
		//per userin e loguar popullojme vlerat nga profili i tij --------------------------------------------------------------
	}
	function getBasketState()
	{
			$getBasketNr = "
				SELECT count(1) as  cnt
				  FROM ".ESHOP_STORE_DB.".eshop__basket as o, ".ESHOP_STORE_DB.".eshop__basket_articles as oi
				 WHERE o.uniqueid = oi.uniqueid 
				   AND o.orderid = oi.orderid 
				   AND order_type='basket' 
				   AND order_step='current_basket' 
				   AND o.uniqueid = '".$this->uniqueid."'";
			$rsBasketNr = WebApp::execQuery($getBasketNr);
			$this->articleInBasket = $rsBasketNr->Field("cnt");
			
			if ($this->articleInBasket>0) $this->getViewBasketState();
	}
	function controlForActualBasket()
	{
   /* echo "<textarea>";
    print_r($this->uniqueid);
    echo "</textarea>";*/

		$this->basketInfo["basketStep"] = "empty";
		IF ($this->ses_userid != 2) {
			$getBasketNr = "
				SELECT o.orderid, o.orderKey, order_type, order_step, count(1) as nrArt, o.uniqueid
				  FROM ".ESHOP_STORE_DB.".eshop__basket as o
				  JOIN ".ESHOP_STORE_DB.".eshop__basket_articles as oi
				    ON  o.orderid = oi.orderid 
				
				 WHERE order_type='basket' 
				   AND order_step='current_basket' 
				   AND payer_id = '".$this->ses_userid."'
			  GROUP BY o.orderid";
			$rsBasketNr = WebApp::execQuery($getBasketNr);

		} else {
			$getBasketNr = "
				SELECT o.orderid as orderid, order_type,o.orderKey as orderKey, order_step, count(1) as nrArt, o.uniqueid
				  FROM ".ESHOP_STORE_DB.".eshop__basket as o
				  JOIN ".ESHOP_STORE_DB.".eshop__basket_articles as oi
				    ON  o.orderid = oi.orderid 
				
				 WHERE order_type='basket' 
				   AND order_step='current_basket' 
				   AND o.uniqueid = '".$this->uniqueid."'
				   GROUP BY o.orderid";
			$rsBasketNr = WebApp::execQuery($getBasketNr);
		}
   /* echo "<textarea>";
    print_r($rsBasketNr);
    echo "</textarea>";
		
    echo "<textarea>";
    print_r($this->uniqueid);
    echo "</textarea>";	*/	
		IF (!$rsBasketNr->EOF() AND mysql_errno() == 0) {		


			$uniqueid =$rsBasketNr->Field("uniqueid"); 	

			$this->orderid =$rsBasketNr->Field("orderid"); 	
			
			//ketu do shtohet dhe uni qe ka ardhur ne where
			$controlIfOrderExist = "SELECT transaction_step, TranCode, TranCodeAllowedTimes, TranCodeDescription

									  FROM ".ESHOP_STORE_DB.".eshop__basket
									 WHERE orderid = '".$this->orderid."'
									   AND TranCode !=0";

			//e gjitha kjo duhet te thirret edhe nga procesim ne callback url
			$resp_controlIfOrderExist = WebApp::execQuery($controlIfOrderExist);

			if (!$resp_controlIfOrderExist->EOF()) { //perdor orderid e paperfunduar				

					$this->basketDeclined["TranCode"]				= $resp_controlIfOrderExist->Field("TranCode"); 
					$this->basketDeclined["TranCodeAllowedTimes"]	= $resp_controlIfOrderExist->Field("TranCodeAllowedTimes"); 
					$this->basketDeclined["TranCodeDescription"]    = $resp_controlIfOrderExist->Field("TranCodeDescription"); 

					$this->getError_ISO8583();
					
					$this->basketDeclined["titleCode"]				= $this->Error_ISO8583[$this->basketDeclined["TranCode"]]["name"]; 
					$this->basketDeclined["descCode"]				= $this->Error_ISO8583[$this->basketDeclined["TranCode"]]["desc"]; 
			}			
			
			$this->orderKey =$rsBasketNr->Field("orderKey"); 	
			
			$this->actualBasket[$this->orderid]["order_type"]=$rsBasketNr->Field("order_type"); 
			$this->actualBasket[$this->orderid]["order_step"]=$rsBasketNr->Field("order_step"); 
			
			$this->basketInfo["basketStep"] = "existBasket";
			
			$nrArticles = $rsBasketNr->Field("nrArt"); 
			$this->articleInBasket = $nrArticles;
			
			IF ($this->ses_userid != 2) 
					$this->basketInfo["basketStep"] = "with_articles";
			else	$this->basketInfo["basketStep"] = "with_articles_not_loged_in";	
			
			
	/*	echo $this->uniqueid.":uniqueid<br>";
		echo "$uniqueid:uniqueid<br>";
		echo $this->ses_userid.":ses_userid<br>";*/
			
			IF ($this->ses_userid != 2) { // && $uniqueid!=$this->uniqueid
					$updateUni = "UPDATE ".ESHOP_STORE_DB.".eshop__basket
									      SET uniqueid     = '".$this->uniqueid."'
									    WHERE orderid      = '".$this->orderid."' ";
					WebApp::execQuery($updateUni);				
			}
			
		} 	
		
		
   /* echo "<textarea>-updateUni-";
    print_r($this->orderid.":orderid");
    print_r($this->uniqueid.":uniqueid");
    print_r($this);
    print_r($updateUni);
    echo "</textarea>";	*/	
		
		
		

	}
	function initBasketState()
	{
		$this->controlForActualBasket();
		if ($this->articleInBasket>0) {
			$this->getViewBasketState();
		} 
	}		
	function putInBasket()
	{
		//process_order = 1 - procesohet me paypal
		global $event;
		
		//$this->uniqueid = $_REQUEST["uni"];
		if (isset($_REQUEST["id_art"]) && $_REQUEST["id_art"]>0) {

			$id_item   = $_REQUEST["id_art"];
			$item_type = $_REQUEST["itemType"];	
			if ($this->orderid!="" && $this->orderid>0) {
			} else {	
				//krijo orderid te ri
					$getMaxSesssion = "SELECT max(orderid) as max_id_level FROM ".ESHOP_STORE_DB.".eshop__basket";
					$MaxSesssion    = WebApp::execQuery($getMaxSesssion);
					if(!$MaxSesssion->EOF()) 	$this->orderid = $MaxSesssion->Field("max_id_level") + 1;
					else 						$this->orderid = 1;	

					$insertFirstRecordSession = "REPLACE INTO ".ESHOP_STORE_DB.".eshop__basket 
																(orderid,				uniqueid, 			   payer_id, 			   id_satelit)
													  VALUES (".$this->orderid.",	'".$this->uniqueid."', '".$this->ses_userid."', '".SATELIT_ID."')";
					WebApp::execQuery($insertFirstRecordSession);
			}
			$existInBasket = "SELECT count(1) as exist 
								from ".ESHOP_STORE_DB.".eshop__basket_articles 
							   WHERE orderid   = '".$this->orderid."' AND 
									 item_type = '".$item_type."'     AND 
									 id_item   = '".$id_item."'";

			$rs_existInBasket = WebApp::execQuery($existInBasket);
			if ($rs_existInBasket->Field("exist") == 0) {
				$insertToBasket = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_articles (orderid,              item_type,        id_item,        uniqueid, 			   id_satelit)
															   VALUES ('".$this->orderid."', '".$item_type."', '".$id_item."', '".$this->uniqueid."', '".SATELIT_ID."')";
				WebApp::execQuery($insertToBasket);	
			}
			$data = CiManagerFe::getEshopStoreConfigurationForItems($id_item, $this->lngId); 
			if (isset($data[$id_item]["activateEshop"]) && $data[$id_item]["activateEshop"]=='y') {

				$updateQuantity = "UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles
									  SET quantity     = 1,
										  price		   = '".$data[$id_item]["price"]."'
									WHERE orderid      = '".$this->orderid."' AND 
										  item_type    = '".$item_type."'     AND 
										  id_item      = '".$id_item."'";
				WebApp::execQuery($updateQuantity);	
			}
		}
	}
	function changeQuantityBasket()
	{

		if ($this->orderid!="" && $this->orderid>0 && $this->orderid == $_REQUEST["bskid"]) {
				$itemQnt = $_POST["itemQnt"]*1;	
				if (isset($_POST["itemType"]) &&  in_array($_POST["itemType"],$this->allowedTypes) && $itemQnt>0) {
				
						$item_type = $_POST["itemType"];	
						$id_art    = $_POST["id_art"]*1;
						$itemSubId = $_POST["itemSubId"]*1;					
						
						$updateBasketOrderStatus= "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles
							   SET quantity     = '".$itemQnt."'
							 WHERE orderid      = '".$this->orderid."' 
							   AND id_item      = '".$id_art."'";
						
						WebApp::execQuery($updateBasketOrderStatus);							
				}
				//$this->BasketRefreshCell();
		} 
		else 
		{
			$this->error_code_eshop = '2'; //nuk u gjet nje bakset valid :changeQuantityBasket
			$this->error_codes_eshop[] = "changeQuantityBasket:noBasketValid";
		}
	}	
	function removeFromBasket()
	{
		if ($this->orderid!="" && $this->orderid>0 && $this->orderid == $_REQUEST["bskid"]) {
				$item_type = $_REQUEST["itemType"];	
				$id_art    = $_REQUEST["id_art"]*1;	
				$removeFromBasket = "
				DELETE FROM ".ESHOP_STORE_DB.".eshop__basket_articles
					  WHERE orderid   = '".$this->orderid."' 
						AND item_type = '".$item_type."'
						AND id_item   = '".$id_art."'";
				WebApp::execQuery($removeFromBasket);	
				
				$basketS = "SELECT COUNT(1) as nr from ".ESHOP_STORE_DB.".eshop__basket_articles WHERE orderid   = '".$this->orderid."' ";
				
		

			$rs_existInBasket = WebApp::execQuery($basketS);
			if ($rs_existInBasket->Field("nr") == 0) {
				
				$deleteFrom = "DELETE FROM ".ESHOP_STORE_DB.".eshop__basket WHERE orderid   = '".$this->orderid."' ";
				WebApp::execQuery($deleteFrom);			
			}				
				
				
				
				
		} else {
			$this->error_code_eshop = '2'; //nuk u gjet nje bakset valid :removeFromBasket
			$this->error_codes_eshop[] = "removeFromBasket:noBasketValid";
		}
	}
	function saveBasketDataRelated($paramsToControlByModule)
	{	
		
		/*if (isset($_GET["uni"]) && $_GET["uni"]!="") 
				$step = $_GET["step"];

		$this->uniqueid = $_GET["uni"];
		
		if (isset($_GET["bskid"]) && $_GET["bskid"]!="") {
			
			$orderID = $_GET["bskid"];
			$getExist = "SELECT uniqueid 
						   FROM ".ESHOP_STORE_DB.".eshop__basket 
						  WHERE orderid = '".$orderID."'
						  OR orderKey = '".$orderID."'";
			$rsExist = WebApp::execQuery($getExist);
			
			if (!$rsExist->EOF()) { */
			
	//	echo 	$this->basketInfo["basketStep"]." -saveBasketDataRelated- ".$this->orderid;	
				
$step = $paramsToControlByModule["step"];				
if ($step == 4) {


		//$this->uniqueid = $_GET["uni"];
		
		/*if (isset($_GET["bskid"]) && $_GET["bskid"]!="") {
			
			$orderID = $_GET["bskid"];
			$getExist = "SELECT uniqueid 
						   FROM ".ESHOP_STORE_DB.".eshop__basket 
						  WHERE orderid = '".$orderID."'
						  OR orderKey = '".$orderID."'";
			$rsExist = WebApp::execQuery($getExist);*/
					
					/*$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET enteredStepData 	= '".($step+1)."'
						 WHERE orderID = '".$this->orderid."'
						   AND enteredStepData 	<= '".$step."'";

					//echo $updateStep."--";
					WebApp::execQuery($updateStep);	*/
					exec("php ".APP_PATH."include_php/saveInvoices.php ".$this->orderid." >/dev/null &");	
					
				//	echo "php ".APP_PATH."include_php/saveInvoices.php ".$this->orderid."";
					
					
					$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET enteredStepData 	= 5
						 WHERE orderID = '".$this->orderid."'
						   AND enteredStepData 	<= '".$step."'";

					//echo $updateStep."--";
					WebApp::execQuery($updateStep);	
					
						$getBasketNr = "
								SELECT orderKey
								  FROM ".ESHOP_STORE_DB.".eshop__basket 
								 WHERE orderId = '".$this->orderid."'";

						$rsBasketNr = WebApp::execQuery($getBasketNr);
						IF (!$rsBasketNr->EOF() AND mysql_errno() == 0) {		
							$this->orderKey =$rsBasketNr->Field("orderKey"); 
							$this->callBackFictivePayementFrom($this->orderKey);
						}
					}
										
					return 10;
				//}		
		
		if (isset($this->basketInfo["basketStep"]) &&  $this->orderid>0) {		
				
				if (isset($_GET["step"]) && $_GET["step"]!="") 						$step = $_GET["step"];
				else																$step = 1;
				
				if (isset($_GET["itemId"]) && $_GET["itemId"]!="") 						$itemId = $_GET["itemId"];
				else																	$itemId = "";
			
//echo 	$step." -step:itemId- ".$itemId;					
				
				if ($step == 1 && $itemId>0) {
					
					$getInfo ="
								SELECT   COALESCE(address_name,				'') AS address_name,
										 COALESCE(address_street,				'') AS address_street,
										 COALESCE(address_city,				'') AS address_city,
										 COALESCE(address_state,				'') AS address_state,
										 COALESCE(address_zip,				'') AS address_zip,
										 COALESCE(address_country,			'') AS address_country,
										 COALESCE(address_phone,				'') AS address_phone,
										 COALESCE(address_email,				'') AS address_email,
										 COALESCE(address_used,				'') AS address_used,
										 COALESCE(address_country_id,		'') AS address_country_id,
										 COALESCE(country_name,				'') AS country_name
								FROM user_address 				  				 
						   LEFT JOIN ".ESHOP_STORE_DB.".z_countries_code ON z_countries_code.id = user_address.address_country_id
							   WHERE id_user ='".$this->userSystemID."'  AND id_address='".$_REQUEST["itemId"]."' ";	

					$rs_info = WebApp::execQuery($getInfo);
					if (!$rs_info->EOF()) {

						 $full_name 			= $rs_info->Field("address_name");
						 $address_email 			= $rs_info->Field("address_email");
						 $address_phone 			= $rs_info->Field("address_phone");

						 $address_street 		= $rs_info->Field("address_street");
						 $address_zip 			= $rs_info->Field("address_zip");
						 $address_city 			= $rs_info->Field("address_city");

						 $address_state			= $rs_info->Field("address_state");
						 $address_country 		= $rs_info->Field("country_name");
						 $address_country_id 	= $rs_info->Field("address_country_id");					

						 $updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET	bll_name 		= '".ValidateVarFun::f_real_escape_string($full_name)."',
										bll_email 		= '".ValidateVarFun::f_real_escape_string($address_email)."',
										bll_phone 		= '".ValidateVarFun::f_real_escape_string($address_phone)."',

										bll_address_street 		= '".ValidateVarFun::f_real_escape_string($address_street)."',
										bll_address_zip 		= '".ValidateVarFun::f_real_escape_string($address_zip)."',
										bll_address_city 		= '".ValidateVarFun::f_real_escape_string($address_city)."',
										bll_address_state 		= '".ValidateVarFun::f_real_escape_string($address_state)."',
										bll_address_country 	= '".ValidateVarFun::f_real_escape_string($address_country)."',
										bll_address_country_id 	= '".ValidateVarFun::f_real_escape_string($address_country_id)."'

								  WHERE orderid = '".$this->orderid."'";
						 //echo $updateStepRelatedData."--";
						 WebApp::execQuery($updateStepRelatedData);	
					}
				} else if ($step == 2 && isset($_REQUEST["itemId"]) && $_REQUEST["itemId"]>0) {
					
					$getInfo ="
								SELECT   COALESCE(address_name,				'') AS address_name,
										 COALESCE(address_street,				'') AS address_street,
										 COALESCE(address_city,				'') AS address_city,
										 COALESCE(address_state,				'') AS address_state,
										 COALESCE(address_zip,				'') AS address_zip,
										 COALESCE(address_country,			'') AS address_country,
										 COALESCE(address_phone,				'') AS address_phone,
										 COALESCE(address_email,				'') AS address_email,
										 COALESCE(address_used,				'') AS address_used,
										 COALESCE(address_country_id,		'') AS address_country_id,
										 COALESCE(country_name,				'') AS country_name
								FROM user_address 				  				 
						   LEFT JOIN ".ESHOP_STORE_DB.".z_countries_code ON z_countries_code.id = user_address.address_country_id
							   WHERE id_user ='".$this->userSystemID."'  AND id_address='".$_REQUEST["itemId"]."' ";	

					$rs_info = WebApp::execQuery($getInfo);
					if (!$rs_info->EOF()) {

						 $full_name 			= $rs_info->Field("address_name");
						 $address_email 			= $rs_info->Field("address_email");
						 $address_phone 			= $rs_info->Field("address_phone");

						 $address_street 		= $rs_info->Field("address_street");
						 $address_zip 			= $rs_info->Field("address_zip");
						 $address_city 			= $rs_info->Field("address_city");

						 $address_state			= $rs_info->Field("address_state");
						 $address_country 		= $rs_info->Field("country_name");
						 $address_country_id 	= $rs_info->Field("address_country_id");					

					$updatePriceT = "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
						   SET user_selection 	= 'n'
						 WHERE id_satelit		= '".SATELIT_ID."' AND orderid			='".$this->orderid."'";
					WebApp::execQuery($updatePriceT);		
					
					
					$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET enteredStepData 	= '".($step+1)."'
						 WHERE orderID = '".$this->orderid."'";

					//echo $updateStep."--";
					WebApp::execQuery($updateStep);						
					

						 $updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET	dlv_name 		= '".ValidateVarFun::f_real_escape_string($full_name)."',
										dlv_email 		= '".ValidateVarFun::f_real_escape_string($address_email)."',
										dlv_phone 		= '".ValidateVarFun::f_real_escape_string($address_phone)."',

										dlv_address_street 		= '".ValidateVarFun::f_real_escape_string($address_street)."',
										dlv_address_zip 		= '".ValidateVarFun::f_real_escape_string($address_zip)."',
										dlv_address_state 		= '".ValidateVarFun::f_real_escape_string($address_city)."',
										bll_address_state 		= '".ValidateVarFun::f_real_escape_string($address_state)."',
										dlv_address_country 	= '".ValidateVarFun::f_real_escape_string($address_country)."',
										dlv_address_country_id 	= '".ValidateVarFun::f_real_escape_string($address_country_id)."'

								  WHERE orderid = '".$this->orderid."'";
						 //echo $updateStepRelatedData."--";
						 WebApp::execQuery($updateStepRelatedData);	
					}
				} else if ($step == 3) {
					//sameAdrres	same
					
					$updatePriceT = "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif SET user_selection 	= 'n'
						 WHERE orderid ='".$this->orderid."'";
					WebApp::execQuery($updatePriceT);	
					
					$updateStep= "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET enteredStepData = '".($step)."'
						 		   WHERE orderID = '".$this->orderid."'";				
						 
					if (isset($paramsToControlByModule["paymentMethod"]) && $paramsToControlByModule["paymentMethod"]!="") {
						$updatepaymentMethod= "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket
							   SET menyra_pageses 	= '".($paramsToControlByModule["updatepaymentMethod"])."'
							 WHERE orderID = '".$this->orderid."'";		
							 WebApp::execQuery($updateStep);	
					}
						
					if (isset($paramsToControlByModule["currency"]) && $paramsToControlByModule["currency"]!=""
						&& isset($this->exchange["active"][$paramsToControlByModule["currency"]])) {
							$updatecurrency= "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket
							   SET currency_id = '".($paramsToControlByModule["currency"])."'
							 WHERE orderID = '".$this->orderid."'";
							 WebApp::execQuery($updatecurrency);	
					}
					
					
					if (isset($paramsToControlByModule["delivery_method"]) && $paramsToControlByModule["delivery_method"]=="me_poste") {
					
						$updatepaymentMethod= "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET menyra_dorezimit = 'me_poste'
											    WHERE orderID = '".$this->orderid."'";		
						WebApp::execQuery($updatepaymentMethod);							
						
						if (isset($paramsToControlByModule["extraPrice"]) && $paramsToControlByModule["extraPrice"]!=""
													 && $paramsToControlByModule["extraPrice"]>0) {
								$updatePriceT = "
									UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
									   SET user_selection 	= 'y'
									 WHERE id_satelit		= '".SATELIT_ID."' AND orderid			='".$this->orderid."'
									   AND id_tarif			='".$paramsToControlByModule["extraPrice"]."' AND tarif_type		='extra'";
								WebApp::execQuery($updatePriceT);	
						}
						
						reset($paramsToControlByModule);
						While (list($pk,$vp)=each($paramsToControlByModule)) {
									//ready_to_payment
									$deliveryPriceId = $vp;
									if (isset($paramsToControlByModule["deliveryPrice_$deliveryPriceId"])) {

										$dataTr = explode("-",$paramsToControlByModule["deliveryPrice_$deliveryPriceId"]);
										if ($dataTr[0]==$deliveryPriceId) {

											$id_subTarif = $dataTr[1];

												if ($dataTr[3] == "pr")		$price_type = "fixed";
												else						$price_type = "calculated";

												 $updatePriceT = "
													UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
													   SET user_selection 		= 'y',
														   tarif_type_extended	= '".ValidateVarFun::f_real_escape_string($dataTr[2])."'
													 WHERE id_satelit		= '".SATELIT_ID."' AND orderid			='".$this->orderid."'
													   AND id_tarif			='".$vp."'	AND id_subTarif		='".$id_subTarif."'
													   AND price_type		='".$price_type."' AND tarif_type		='delivery'";
												WebApp::execQuery($updatePriceT);
										}
									}							


						} //while	
						
						
							
					} else {
									
						$updatepaymentMethod= "UPDATE ".ESHOP_STORE_DB.".eshop__basket SET menyra_dorezimit = 'ne_dyqan'
											    WHERE orderID = '".$this->orderid."'";		
						WebApp::execQuery($updatepaymentMethod);										
								
						$updatePriceT = "DELETE FROM  ".ESHOP_STORE_DB.".eshop__basket_extra_tarif WHERE  orderid ='".$this->orderid."'";
						WebApp::execQuery($updatePriceT);	
					}
					
				
					

					
				
					
				} else if ($step == 4) {

					
					
					

					
					
					
				
/*echo "<textarea>";	
print_r($this);
echo "</textarea>";	*/							
								
					
					
					
					
					
					/*$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET enteredStepData 	= '".($step+1)."'
						 WHERE orderID = '".$this->orderid."'
						   AND enteredStepData 	<= '".$step."'";

					//echo $updateStep."--";
					WebApp::execQuery($updateStep);	*/
					exec("php ".APP_PATH."include_php/saveInvoices.php ".$this->orderid." >/dev/null &");	
					
					//echo "php ".APP_PATH."include_php/saveInvoices.php ".$this->orderid."";
					
					
					$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET enteredStepData 	= 5
						 WHERE orderID = '".$this->orderid."'
						   AND enteredStepData 	<= '".$step."'";

					//echo $updateStep."--";
					WebApp::execQuery($updateStep);	
					
					
					
					
					
					
							$getBasketNr = "
								SELECT orderKey
								  FROM ".ESHOP_STORE_DB.".eshop__basket 
								 WHERE orderId = '".$this->orderid."'";





						$rsBasketNr = WebApp::execQuery($getBasketNr);
						
						
	
						
						
						
						
						IF (!$rsBasketNr->EOF() AND mysql_errno() == 0) {		
							$this->orderKey =$rsBasketNr->Field("orderKey"); 
							$this->callBackFictivePayementFrom($this->orderKey);
						}
					
					
					
					
					
					
					return 10;
				} else if ($step == 6) {
				//	if (isset($paramsToControlByModule["agreeCheck"]) && $paramsToControlByModule["agreeCheck"]==1) {
							
						//exec("php ".APP_PATH."BP/saveInvoices.php $orderID >/dev/null &");	
							$updateStep= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET transaction_step 	= 'sent_to_payment_gateway'
								 WHERE orderID = '".$orderID."'";;
							WebApp::execQuery($updateStep);		
							
						//passthru("php ".APP_PATH."BP/saveInvoices.php $orderID");	//,$results,$status kjo mund te jete edhe exec
						
						exec("php ".APP_PATH."BP/saveInvoices.php $orderID 2>&1", $output);
				}				
				
				$updateStep= "
					UPDATE ".ESHOP_STORE_DB.".eshop__basket
					   SET enteredStepData 	= '".($step+1)."'
					 WHERE orderID = '".$this->orderid."'
					   AND enteredStepData 	<= '".$step."'";

				//echo $updateStep."--";
				WebApp::execQuery($updateStep);	
				return  ($step+1);
		}
	}		
	function displayBasketInfo()
	{
		
		$errorsGrid["data"] = array();
		$ind = 0;
		$this->basketInfo["basketStateFlag"] = "error";
		if (isset($this->basketInfoGrids["categorizedBasketGrid"])) {
		
			WebApp::addVar("categorizedBasketGrid",$this->basketInfoGrids["categorizedBasketGrid"]);
			if (isset($this->basketInfoGrids["itemsTypeBasketGrid"])) {
				reset($this->basketInfoGrids["itemsTypeBasketGrid"]);
				while (list($key,$data)=each($this->basketInfoGrids["itemsTypeBasketGrid"])) {
					WebApp::addVar("itemsTypeBasketGrid_".$key,$data);
				}
			}
			if (isset($this->basketInfoGrids["itemsBasketGrid"])) {
				reset($this->basketInfoGrids["itemsBasketGrid"]);
				while (list($key,$data)=each($this->basketInfoGrids["itemsBasketGrid"])) {
					WebApp::addVar("itemsBasketGrid_".$key,$data);
				}
			}	
			
			if (isset($this->basketInfoGrids["deliveryInfoGrid"])) {
				WebApp::addVar("deliveryInfoGrid",$this->basketInfoGrids["deliveryInfoGrid"]);
			}	 			
 			
 			if (isset($this->error_code_basket) && $this->error_code_basket>1) {
 				$this->basketInfo["basketStateFlag"] = "error";
 				while (list($key,$data)=each($this->error_codes_basket)) {
 					$errorsGrid["data"][$ind]["id"] = $key;
 					$errorsGrid["data"][$ind]["desc"] = $data;
 					$ind++;
 				}
 			} else {
 				//JEMI GATI PER PREVIEW PARA PAYEMENT
 				if ($this->basketInfo["enteredStepData"]>=4) {
 					$this->basketInfo["basketStateFlag"] = "preview_before_payement";
 					$this->prepareSecureCheckout();
 				} 				
 			}

 			
			$errorsGrid["AllRecs"] = count($errorsGrid["data"]);
			WebApp::addVar("errorsGrid",$errorsGrid);
			
			if (isset($this->orderCheckoutRelatedData)) {
				reset($this->orderCheckoutRelatedData);
				while (list($key,$data)=each($this->orderCheckoutRelatedData)) {
					WebApp::addVar($key,"".$data);
				}
			}	
			
			if (isset($this->basketInfo)) {
				reset($this->basketInfo);
				while (list($key,$data)=each($this->basketInfo)) {
					WebApp::addVar($key,"".$data);
					WebApp::addVar("tot_".$key,"".$data);
				}
			}	
			
			if (isset($this->basketDeclined) && count($this->basketDeclined)>0) {
					$tmp["data"][0] = $this->basketDeclined;
					
					$tmp["AllRecs"] = count($tmp["data"]);
					WebApp::addVar("basketDeclinedGrid",$tmp);							
			}	
		}
	}	
	function controllBasketState()
	{

		// control that shipping addres dhe delivery addres
		// control that basket is not empty
		// control that postaTarifs has been choosed
		IF ($this->ses_userid != 2) {

		} else {

			$this->error_code_basket	= 2; //useri eshte i paloguar
			$this->error_codes_basket[$this->error_code_basket] = "{{_useri_must_login}}";            
		}


		$this->basketInfo["TotalDeliveryPrice"]    = "0";
		$this->basketInfo["LekTotalDeliveryPrice"] = "0";

		$this->basketInfo["TotalPriceForBooks"]    = "0";
		$this->basketInfo["LekTotalPriceForBooks"] = "0";	


	    $this->basketInfoState["step"]  = "ready_to_payment";	
		
		if ($this->basketInfo["artictleInBasketTot"]==0) {
			  $this->basketInfoState["step_basket_articles"] = "basket_empty";	
		} elseif ($this->basketInfo["artictleInBasketFinished"]==0) {
			   $this->basketInfoState["step_basket_articles"] = "basket_full";	
		} elseif ($this->basketInfo["artictleInBasketFinished"]>0) {
			  $this->basketInfoState["step_basket_articles"] = "basket_partial";	
		}

		/*$controlCountryIdHasBeenSet = "SELECT COUNT(1) as exist
										FROM ".ESHOP_STORE_DB.".eshop__basket
										JOIN ".ESHOP_STORE_DB.".z_countries_code
										  ON eshop__basket.dlv_address_country_id = z_countries_code.id
									   WHERE id_satelit		= '".SATELIT_ID."'
										 AND orderid 		= '".$this->orderid."' ";
		
		$rs_existDelivery = WebApp::execQuery($controlCountryIdHasBeenSet);								 
		if ($rs_existDelivery->Field("exist") == 0) {
			
			$this->basketInfoState["step_delivery_zone"] = "empty";	
			
			$this->error_code_basket	= 10; //nuk eshte percaktuar countryId me zgjedhje ne liste 
			$this->error_codes_basket[$this->error_code_basket] = "{{_no_countryIdSelectedFromList}}";            
			
		} else {
			$this->basketInfoState["step_delivery_zone"] = "ok";	
		}*/
		
		//GJENDET TOTALI I LIBRAVE NE BASKET
		$getPostaDeliveryA = "SELECT sum(price*quantity) as TotalPriceForBooks, sum(Lekprice*quantity) as LekTotalPriceForBooks

										FROM ".ESHOP_STORE_DB.".eshop__basket
										JOIN ".ESHOP_STORE_DB.".eshop__basket_articles
										  ON eshop__basket.id_satelit = eshop__basket_articles.id_satelit
										 AND eshop__basket.orderid = eshop__basket_articles.orderid
									   WHERE eshop__basket.id_satelit	= '".SATELIT_ID."'
										 AND eshop__basket.orderid		= '".$this->orderid."'

										 GROUP BY eshop__basket.orderid";

		$rs_PostaDeliveryA = WebApp::execQuery($getPostaDeliveryA);	

		$this->basketInfo["TotalPriceForBooks"]    = $rs_PostaDeliveryA->Field("TotalPriceForBooks");
		$this->basketInfo["LekTotalPriceForBooks"] = $rs_PostaDeliveryA->Field("LekTotalPriceForBooks");		
		
		//GJENDET TOTALI I LIBRAVE NE BASKET
		

            
        if ($this->basketInfo["menyra_dorezimit"] == "me_poste") {    
										 
				$controlPostaDeliveryT = "SELECT COUNT(1) as exist
												FROM ".ESHOP_STORE_DB.".eshop__basket
												JOIN ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
												  ON eshop__basket.id_satelit 	= eshop__basket_extra_tarif.id_satelit
												 AND eshop__basket.orderid 		= eshop__basket_extra_tarif.orderid
											   WHERE eshop__basket.id_satelit	= '".SATELIT_ID."'
												 AND eshop__basket.orderid		= '".$this->orderid."'
												 AND (tarif_type = 'delivery')
												 AND user_selection = 'y'";

				$rs_existPostaDeliveryT = WebApp::execQuery($controlPostaDeliveryT);	
				if ($rs_existPostaDeliveryT->Field("exist") == 0) {

					$this->basketInfoState["step_basket_posta_tarif"] = "empty";	
					$this->error_code_basket	= 11; //nuk eshte percaktuar delivery method
					$this->error_codes_basket[$this->error_code_basket] = "{{_no_delivery_method_choosed}}";  

				} else {

					$this->basketInfoState["step_basket_posta_tarif"] = "ok";	
					$getPostaDeliveryT = "SELECT sum(price) as deliveryPrice, sum(Lekprice) as LekDeliveryPrice

													FROM ".ESHOP_STORE_DB.".eshop__basket
													JOIN ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
													  ON eshop__basket.id_satelit = eshop__basket_extra_tarif.id_satelit
													 AND eshop__basket.orderid = eshop__basket_extra_tarif.orderid
												   WHERE eshop__basket.id_satelit	= '".SATELIT_ID."'
													 AND eshop__basket.orderid		= '".$this->orderid."'
													 AND user_selection = 'y'
													 GROUP BY eshop__basket.orderid";

					$rs_PostaDeliveryT = WebApp::execQuery($getPostaDeliveryT);	

					$this->basketInfo["TotalDeliveryPrice"]    = $rs_PostaDeliveryT->Field("deliveryPrice");
					$this->basketInfo["LekTotalDeliveryPrice"] = $rs_PostaDeliveryT->Field("LekDeliveryPrice");

					if ($deliveryPrice == 0 && $tarif_type=='delivery') {
						$this->basketInfoState["step_posta_price"] = "empty";	
						$this->error_code_basket	= 12; //nuk eshte percaktuar delivery method
						$this->error_codes_basket[$this->error_code_basket] = "{{_no_delivery_price}}";            
					} else {
						$this->basketInfoState["step_posta_price"] = "ok";	
					}			
				}	
			}
		
			$this->basketInfo["TotalPriceAndDelivery"]  	= $this->basketInfo["TotalDeliveryPrice"]+$this->basketInfo["TotalPriceForBooks"];	
			$this->basketInfo["LekTotalPriceAndDelivery"]  	= $this->basketInfo["LekTotalDeliveryPrice"]+$this->basketInfo["LekTotalPriceForBooks"];	
			
			$updateStep= "
				UPDATE ".ESHOP_STORE_DB.".eshop__basket
				   SET 

					   exchange_rate 		= '".$this->basketInfo["currencyExchangeRate"]."',

					   PriceProducts 		= '".$this->basketInfo["TotalPriceForBooks"]."',
					   LekPriceProducts 	= '".$this->basketInfo["LekTotalPriceForBooks"]."',

					   PriceDelivery 		= '".$this->basketInfo["TotalDeliveryPrice"]."',
					   LekPriceDelivery 	= '".$this->basketInfo["LekTotalDeliveryPrice"]."',

					   TotalAmount 			= '".$this->basketInfo["TotalPriceAndDelivery"] ."',
					   LekTotalAmount 		= '".$this->basketInfo["LekTotalPriceAndDelivery"] ."'
				 WHERE orderID = '".$this->orderid."'";

			//echo $updateStep."--";
			WebApp::execQuery($updateStep);					

			$this->basketInfo["TotalDeliveryPriceFormated"]    = number_format($this->basketInfo["TotalDeliveryPrice"],2);
			$this->basketInfo["TotalPriceForBooksFormated"]    = number_format($this->basketInfo["TotalPriceForBooks"],2);
			$this->basketInfo["TotalPriceAndDeliveryFormated"] = number_format($this->basketInfo["TotalPriceAndDelivery"],2);	
		
		
		
		
		$getOrderCheckoutRelatedDAta = "
			 SELECT COALESCE(enteredStepData,             '1') AS enteredStepData, 
			 		COALESCE(orderKey, '') as erKey
			   FROM ".ESHOP_STORE_DB.".eshop__basket
			  WHERE orderID = '".$this->orderid."'";	
			
		$relatedData = WebApp::execQuery($getOrderCheckoutRelatedDAta);		
		IF (!$relatedData->EOF() AND mysql_errno() == 0) {		
			$this->basketInfo["enteredStepData"] =$relatedData->Field("enteredStepData");
		}
	}
	function basketOrderToInvoice()
	{
		
		

										
/*										COALESCE(z_method_payment.description_of_method,						'') AS menyra_pageses_label,
										COALESCE(z_method_payment.payment_method,			'') AS menyra_pageses,
										

										COALESCE(z_method_delivery.description_of_method,						'') AS menyra_dorezimit_label,
										COALESCE(z_method_delivery.delivery_method,			'') AS menyra_dorezimit		
		
		
		
		*/
		$getBasketNr = "
				SELECT o.orderid as orderid, order_type, order_step, count(1) as nrArt,payer_id, orderKey
				  FROM ".ESHOP_STORE_DB.".eshop__basket as o
				  JOIN ".ESHOP_STORE_DB.".eshop__basket_articles as oi
				    ON  o.orderid = oi.orderid 
				
				 WHERE order_type='basket' 
				   AND order_step='current_basket' 
				   AND o.orderId = '".$this->orderId."'
				   GROUP BY o.orderid";
		
		
		
		
		
		$rsBasketNr = WebApp::execQuery($getBasketNr);
		IF (!$rsBasketNr->EOF() AND mysql_errno() == 0) {		
			$this->orderKey =$rsBasketNr->Field("orderKey"); 	
			$this->orderid =$rsBasketNr->Field("orderid"); 	
			$this->ses_userid = $rsBasketNr->Field("payer_id");  
			
			$this->actualBasket[$this->orderid]["order_type"]=$rsBasketNr->Field("order_type"); 
			$this->actualBasket[$this->orderid]["order_step"]=$rsBasketNr->Field("order_step"); 
			$this->basketInfo["basketStep"] = "empty";
			
			$nrArticles = $rsBasketNr->Field("nrArt"); 
			$this->articleInBasket = $nrArticles;
			if ($nrArticles>0) {
				$this->getViewBasketState();
				$this->InitProfileUser();
				$this->getDeliveryInfo();
				$this->controllBasketState();
				$this->displayBasketInfo();
			}
			WebApp::addVar("PurchaseTime",date("d.m.Y"));
			WebApp::addVar("orderKey",$this->orderKey);
			 
		}  else {
		}
	}		

	function getCiNeededVar($contentToFindProp=""){
		global $session;
		
		if ($contentToFindProp=="") 
			$contentToFindProp = $session->Vars["contentId"];
			
		$workingCi = new CiManagerFe($contentToFindProp,$session->Vars["lang"]);
		$workingCi->parseDocumentToDisplay();	

		$this->mixedNeededData[$contentToFindProp] = $workingCi->properties_parsed;
		$this->eshopStore[$contentToFindProp]	 	 = $workingCi->eshopStoreInfo;
		
	}

	
	function getViewBasketState()
	{
		global $objBookGlossar;
			
		if ($this->orderid!="" && $this->orderid>0 && $this->articleInBasket>0) {
			
		/*	if (isset($objBookGlossar->isSetGlobalObj) && $objBookGlossar->isSetGlobalObj=="yes") {
			} else {
				$objBookGlossar = new collectorBookBase();
			}	
			$objBookGlossar->preferences = $this->preferences;*/
					
			$descriptionItems = array();
			$previewItem = array();
			$traitedItem = array();
			$dataArray = array();
			$dataArrayD = array();
			
			$indData = 0;
			$indDataD = 0;
			
			$totalPriceLek				= 0;
			$totalPrice					= 0;
			$totalWeight				= 0;
			$artictleInBasketFinished 	= 0;
			$artictleInBasketTot		= 0;

			
			$getBasketInformation = "
				SELECT oi.item_type, o.orderid, count(1) as nrItem, coalesce(currency_id,'') as currency_id,
				
										COALESCE(z_method_payment.description_of_method,						'') AS menyra_pageses_label,
										COALESCE(z_method_payment.payment_method,			'') AS menyra_pageses,
										
										COALESCE(z_method_delivery.description_of_method,						'') AS menyra_dorezimit_label,
										COALESCE(z_method_delivery.delivery_method,			'') AS menyra_dorezimit					
						
				  FROM ".ESHOP_STORE_DB.".eshop__basket as o
				  
			   LEFT JOIN ".ESHOP_STORE_DB.".z_method_payment
					  ON o.menyra_pageses = z_method_payment.payment_method

			   LEFT JOIN ".ESHOP_STORE_DB.".z_method_delivery
					  ON o.menyra_dorezimit = z_method_delivery.delivery_method
				  
				  JOIN ".ESHOP_STORE_DB.".eshop__basket_articles as oi
				    ON  o.orderid = oi.orderid 
				 WHERE order_type='basket' 
				   AND order_step='current_basket' 
				   AND o.orderid = '".$this->orderid."'
				    GROUP BY o.orderid, oi.item_type";
			$rsGetdata = WebApp::execQuery($getBasketInformation);	
			
			
					/*	echo "<textarea>";
						print_r($rsGetdata);
						echo "</textarea>";	*/		
			
			
			
			
			while (!$rsGetdata->EOF()) {
				
				$this->orderid = $rsGetdata->Field("orderid"); 
				
				/*$this->basketInfo["currencyOrderId"] 		= 5;
				$this->basketInfo["currencySelectedDesc"] 	= $this->exchange["active"][5];
				$this->basketInfo["currencyExchangeRate"] = 1;
				
				
				$this->basketInfo["menyra_pageses"] 	= $rsGetdata->Field("menyra_pageses"); 
				$this->basketInfo["menyra_dorezimit"] 	= $rsGetdata->Field("menyra_dorezimit"); 
				
				//CURRENCYSELECTEDTOORDER
				$currency_id = $rsGetdata->Field("currency_id");
				
				if ($currency_id!="" && $currency_id>0) {
					if (isset($this->exchange["active"][$currency_id])) {
						$this->basketInfo["currencyOrderId"] 		= $currency_id;
						$this->basketInfo["currencySelectedDesc"] 	= $this->exchange["active"][$currency_id];
					}
				} else {
					if (isset($this->preferences["sessionMonedhe"]) && $this->preferences["sessionMonedhe"]>0
						&& isset($this->exchange["active"][$this->preferences["sessionMonedhe"]])) {
						$currency_id = $this->preferences["sessionMonedhe"];
						$this->basketInfo["currencyOrderId"] 		= $currency_id;
						$this->basketInfo["currencySelectedDesc"] 	= $this->exchange["active"][$currency_id];						
					} 					
				}
				if (isset($this->exchange["rate"][$this->basketInfo["currencyOrderId"]])) {
					$this->basketInfo["currencyExchangeRate"] 	= $this->exchange["rate"][$this->basketInfo["currencyOrderId"]];
				}	
				
				
				//	$this->exchange["data"][$ind]["currency_code"]	*/
				
				$this->basketInfo["currency_code"] 			= "978";
				$this->basketInfo["currencySelectedDesc"]	= "Euro";
			
				//$this->basketInfo["currencyOrderId"] 
				$dataArrayC["data"] = array();
				$dataArrayC["data"][0]["currencyOrderId"] 		= $this->basketInfo["currencyOrderId"] ;
				$dataArrayC["data"][0]["currencySelectedDesc"]	= $this->basketInfo["currencySelectedDesc"];

				$dataArrayC["AllRecs"] = count($dataArrayC["data"]);
				WebApp::addVar("currencySelectedGrid",$dataArrayC);		
				//CURRENCYSELECTEDTOORDER
				
				
                //cmimi promocional ---------------------------------------------------------------------------------------------------------
                //  IF (!ISSET($promocioni_array))
                //     {
                //      //E EGZEKUTOJME VETEM NJE HERE SEPSE PROMOCIONIN E KAPIM NE RANG BASKET --------
                //      $promocioni_array = cmimi_promocional("", $this->orderid);
                //     }
                ////cmimi promocional ---------------------------------------------------------------------------------------------------------
				//
				$item_type	= $rsGetdata->Field("item_type");
				
			//	if ($item_type=="bk") {

						$dataArray["data"][$indData]["tp"] = "category_one";
						$dataArray["data"][$indData]["orderid"] = $this->orderid;
						$dataArray["data"][$indData]["itemType"] = $item_type;
						$dataArray["data"][$indData]["nrItem"] = $rsGetdata->Field("nrItem");

						$dataArray["data"][$indData]["_review_for_itemType"] = "{{_review_for_".$item_type."}}";
						$dataArray["data"][$indData]["_label_for_itemType"] = "{{_label_for_".$item_type."}}";

						$previewItem["categorised"][$item_type] = $dataArray["data"][$indData];
						$sqlArticles = "SELECT eshop__basket_articles.id_item as idArt,
											   eshop__basket_articles.quantity as quantity
										  FROM ".ESHOP_STORE_DB.".eshop__basket_articles 
										 WHERE eshop__basket_articles.orderid = '".$this->orderid."'

										GROUP BY eshop__basket_articles.id_item
										ORDER BY eshop__basket_articles.record_timestamp desc";

						$rsA = WebApp::execQuery($sqlArticles); 
						
						
						
						
						
						
					/*	echo "<textarea>";
						print_r($rsA);
						echo "</textarea>";*/
						
						
						
						while (!$rsA->EOF()) {
								
									$idArt = $rsA->Field("idArt");
									$quantity = $rsA->Field("quantity");
							
									$dataArrayD["data"][$indDataD]["idArt"] = $idArt;
									$dataArrayD["data"][$indDataD]["srcImageToDisplay"] = "{{APP_URL}}graphics/spacer.gif";
									$dataArrayD["data"][$indDataD]["is_set_ew_imgsmall"] = "no";	
									$dataArrayD["data"][$indDataD]["aliasPP"] = "".($indDataD+1)."";
									
									$this->getCiNeededVar($idArt);
	
									$dataSubItem = array();
									$iSubItem = 0;

												
									
									$dataSubItem["data"][$iSubItem] = $this->mixedNeededData[$idArt];
									
									$dataSubItem["data"][$iSubItem]["quantity"] 			= $quantity;
									$dataSubItem["data"][$iSubItem]["artikulli_name"] = $dataSubItem["data"][$iSubItem]["ew_title"];
									
									$dataSubItem["data"][$iSubItem]["code"] = $dataSubItem["data"][$iSubItem]["ci_type"].".".$idArt;
									
									
									
									$dataSubItem["data"][$iSubItem]["stoku_promocional"] 	= 10;
									$dataSubItem["data"][$iSubItem]["stock_real"] 			= 10;
									$dataSubItem["data"][$iSubItem]["stok_error"] 			= "in_stock";

									
									$cmimi1 = $this->eshopStore[$idArt]["price"];
									
									$aktual_price = $cmimi1;
									$real_price   = $cmimi1;									
									$cmimi_promocional = $this->eshopStore[$idArt]["price_promotional"];
									
									$dataSubItem["data"][$iSubItem]["real_price"]	= number_format($real_price,2);
									$dataSubItem["data"][$iSubItem]["monedha"]		= $this->eshopStore[$idArt]["currency_id"];
									
									
									$promocion_id_sel = "NULL";
									$Lek_list_price = $this->eshopStore[$idArt]["price"];

									
									$updateBasketOrderStatus= "
										UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles
										   SET price        = '".$aktual_price."',
										       Lekprice		= '".$Lek_list_price."',
										       id_promocion =  ".$promocion_id_sel.",
										       description_item = '".ValidateVarFun::f_real_escape_string($this->mixedNeededData[$idArt]["ew_title"])."',
										       code_item = '".$idArt."'
										       
										 WHERE orderid   = '".$this->orderid."' AND 
										       id_item   = '".$idArt."'";

									WebApp::execQuery($updateBasketOrderStatus);    									


/*
ALTER TABLE eshop__basket_articles 		add column  `Lekprice` double(10,2) DEFAULT NULL after price;
ALTER TABLE eshop__basket_extra_tarif 	add column  `Lekprice` double(10,2) DEFAULT NULL after price;
*/									
									
								//	
										$dataSubItem["data"][$iSubItem]["description_item"] 		= $this->basketbookDetails[$idArt]["emertimi_libritToEshop"];
									
										$dataSubItem["data"][$iSubItem]["aktual_price"] 		= number_format($aktual_price,2);
										
										$dataSubItem["data"][$iSubItem]["alias"] = "".($iSubItem+1)."";										
										$dataSubItem["data"][$iSubItem]["stoku"] = "".$stoku."";										
										$dataSubItem["data"][$iSubItem]["stok_error"] = "in_stock";										
										
										$max_stock_allowed = ".ESHOP_STORE_DB.".STOCK_LOW;
										$price_per_item= 0;
										
										$stoku_promocional = 0;
										$stock_real = $this->basketbookDetails[$idArt]["cmimi_promocional"];
										$stock_real = 20;
										
										if ($stoku_promocional>0 && $stoku_promocional!="") 
										{
											$stock_real = $stoku_promocional;
											$dataSubItem["data"][$iSubItem]["stoku"] = "".$stock_real."";
										}
										
										if ($stock_real <= 0) {
											
											$dataSubItem["data"][$iSubItem]["stok_error"] = "article_out_of_stock";
											$max_stock_allowed = $stock_real;
											$artictleInBasketFinished++;
										
										} else {
										
											if ($stock_real > 0 && $stock_real < ".ESHOP_STORE_DB.".STOCK_LOW) {
												$dataSubItem["data"][$iSubItem]["stok_error"] = "lower_stock";
												$max_stock_allowed = $stock_real;
											}
											
											if ($quantity<=$stock_real && $quantity>0) {
												
												$artictleInBasketTot++;
												$totalWeight +=$quantity*$dataSubItem["data"][$iSubItem]["b_weight"];
												
												
												$price_per_item = $quantity*$aktual_price;
												$totalPrice += $price_per_item;
												
												
												$totalPriceLek  += $quantity*$Lek_list_price;
												$descriptionItems[] = utf8_encode($emertimItem)." X $quantity = ".$price_per_item;

											} else {
												$dataSubItem["data"][$iSubItem]["stok_error"] = "article_out_of_stock_quantity";	
												$artictleInBasketFinished++;
											}
										}
										
										$dataSubItem["data"][$iSubItem]["max_stock_allowed"] = $max_stock_allowed;
										$dataSubItem["data"][$iSubItem]["price_per_item"] = "".number_format($price_per_item,2)."";	
										
									$iSubItem++;



									$dataSubItem["AllRecs"] = count($dataSubItem);
									//$this->basketInfoGrids["itemsBasketGrid"]["data"][$idArt] = $dataSubItem; 
									$this->basketInfoGrids["itemsBasketGrid"][$idArt] = $dataSubItem;
									
									$dataArrayD["data"][$indDataD]["nrSubItems"] 	= "".$iSubItem."";									
									$previewItem["items"][$item_type][$idArt] = $dataArrayD["data"][$indDataD];		
								
									$indDataD++;	
									$rsA->MoveNext();
								}

						$dataArrayD["AllRecs"] = count($dataArrayD["data"]);
						//WebApp::addVar("itemsTypeBasketGrid_".$item_type,$dataArrayD);	
						
						$this->basketInfoGrids["itemsTypeBasketGrid"][$item_type] = $dataArrayD; 
			//	}

				$indData++;				
				$rsGetdata->MoveNext();
			}
	
		$dataArray["AllRecs"] = count($dataArray);		
		$this->basketInfoGrids["categorizedBasketGrid"] = $dataArray; 

		$this->basketInfo["TotalPriceForBooks"]			= $totalPrice;
		$this->basketInfo["LekTotalPriceForBooks"]		= $totalPriceLek;
		
		$this->basketInfo["TotalPriceForBooksFormated"]	= number_format($totalPrice,2);
	
		
		
		
		$this->basketInfo["totalWeight"] 				= $totalWeight;
		$this->basketInfo["artictleInBasketTot"]		= $artictleInBasketTot;
		$this->basketInfo["artictleInBasketFinished"]	= $artictleInBasketFinished;
		
		$this->basketInfoGrids["previewItem"] = $previewItem;
		}
	}	
	function getDeliveryInfo()
	{
		$this->getDeliveryMethod();//nga adressa ketij funksioni do ti kalohen si parametra nese eshte national apo international dhe ne varesi te kesaj do percaktohet cmimi
		$this->getPaymentMethod();//nga adressa ketij funksioni do ti kalohen si parametra nese eshte national apo international dhe ne varesi te kesaj do percaktohet cmimi
	}
	

	function getPaymentMethod()
	{
		$dataArray["data"] = array();
		$tmp["data"] = array();
		$ind=0;
		
		$getPaymentMethod = "
				SELECT id, payment_method, description_of_method,  coalesce(eshop__basket.menyra_pageses,'') as sel
				  FROM ".ESHOP_STORE_DB.".z_method_payment
			 LEFT JOIN ".ESHOP_STORE_DB.".eshop__basket
			 	 	ON eshop__basket.menyra_pageses = z_method_payment.payment_method
			 	   AND eshop__basket.id_satelit	= '".SATELIT_ID."'
			 	   AND eshop__basket.orderid		= '".$this->orderid."'
				WHERE status = 'y' 
				ORDER BY description_of_method";	
				
				
		$rsPaymentMethod = WebApp::execQuery($getPaymentMethod);
		while (!$rsPaymentMethod->EOF()) { //perdor orderid e paperfunduar

			$id = $rsPaymentMethod->Field("id");
			$ipayment_method = $rsPaymentMethod->Field("payment_method");

			$dataArray["data"][$ind]["id"]				= $id;
			$dataArray["data"][$ind]["payment_method"]	= $ipayment_method;
			$dataArray["data"][$ind]["descriptionM"]	= $rsPaymentMethod->Field("description_of_method");
			$dataArray["data"][$ind]["isSelected"]		= "";
			
			
			if ($rsPaymentMethod->Field("sel")==$ipayment_method) {
				$dataArray["data"][$ind]["isSelected"]	= " checked=\"checked\"";
				$this->basketInfo["paymentMethod"]		= $payment_method;
				
				$tmp["data"][0] = $dataArray["data"][$ind];
				$tmp["AllRecs"] = 1;
				
				WebApp::addVar("paymentMethodPreviewGrid",$tmp);	
			}
			
			$ind++;
			$rsPaymentMethod->moveNext();
		}	
		
		$dataArray["AllRecs"] = count($dataArray["data"]);
		WebApp::addVar("paymentMethodGrid",$dataArray);			
			
		//paymentMethodGrid
		
		
		
		
		$dataArray["data"] = array();
		$tmp["data"] = array();
		$ind=0;
		
		$getPaymentMethod = "
				SELECT id, delivery_method, description_of_method,  coalesce(eshop__basket.menyra_dorezimit,'') as sel
				  FROM ".ESHOP_STORE_DB.".z_method_delivery
			 LEFT JOIN ".ESHOP_STORE_DB.".eshop__basket
			 	 	ON eshop__basket.menyra_dorezimit = z_method_delivery.delivery_method
			 	   AND eshop__basket.id_satelit	= '".SATELIT_ID."'
			 	   AND eshop__basket.orderid		= '".$this->orderid."'
				WHERE status = 'y' 
				ORDER BY description_of_method";	
				
				
		$rsPaymentMethod = WebApp::execQuery($getPaymentMethod);
		while (!$rsPaymentMethod->EOF()) { //perdor orderid e paperfunduar

			$id = $rsPaymentMethod->Field("id");
			$ipayment_method = $rsPaymentMethod->Field("delivery_method");

			$dataArray["data"][$ind]["id"]				= $id;
			$dataArray["data"][$ind]["delivery_method"]	= $ipayment_method;
			$dataArray["data"][$ind]["descriptionM"]	= $rsPaymentMethod->Field("description_of_method");
			$dataArray["data"][$ind]["isSelected"]		= "";
			
			
			if ($rsPaymentMethod->Field("sel")==$ipayment_method) {
				$dataArray["data"][$ind]["isSelected"]	= " checked=\"checked\"";
				$this->basketInfo["paymentMethod"]		= $delivery_method;
				
				$tmp["data"][0] = $dataArray["data"][$ind];
				$tmp["AllRecs"] = 1;
				
				WebApp::addVar("deliveryMethodPreviewGrid",$tmp);	
			}
			
			$ind++;
			$rsPaymentMethod->moveNext();
		}	
		
		$dataArray["AllRecs"] = count($dataArray["data"]);
		WebApp::addVar("deliveryMethodGrid",$dataArray);	
				
		
		
		
		
		
	}
	

	
	
	
	function getDeliveryMethod()
	{

			$totalWeight 		= $this->basketInfo["totalWeight"];
			$deliveryCountryId 	= $this->basketInfo["deliveryCountryId"] ;
/*
			totalWeight: 
			nga adresa duhet ti vije parametri national apo international, dhe per international duhet ti vije edhe zone_id [1|2|3|4]

			typeDelivery: 	national:international
			typeDelivery: 	national:international

			funkioni duhet te ktheje tarifen e delivery ose nje mesazh error ne rast qe nuk gjendet
				rasti i pare eshte qe pesha kalone limitet per llojin e transportit
				
kufuzimet per max_allowed_weight per tipet e delivery jane si me poshte				

id	delivery_to_type	type_delivery	max_allowed_weight
1	national			standart	30000
2	national			EBQ			10000
3	national			ENQ			10000
4	international		standart	30000
5	international		EMS			30000

		$totalWeight = 1;
		$totalWeight = 21;
		$totalWeight = 52;
		$totalWeight = 250;
		$totalWeight = 499;
		$totalWeight = 1000;
		$totalWeight = 1500;
		$totalWeight = 2000;
		$totalWeight = 2500;
		$totalWeight = 3000;
		$totalWeight = 3500;
		$totalWeight = 4000;
		$totalWeight = 4500;
		$totalWeight = 5000;
				
		$totalWeight = 5500;
		$totalWeight = 6000;
		$totalWeight = 6500;
		$totalWeight = 7000;
		$totalWeight = 7500;
		$totalWeight = 8000;
		$totalWeight = 8500;		
		$totalWeight = 9000;
		$totalWeight = 9500;		
		
		$totalWeight = 10000;
		
		$totalWeight = 15000;
		$totalWeight = 20000;
		$totalWeight = 30000;
		
		$totalWeight = 15000;
	
		$totalWeight = 17200;
	*/
		
		$this->basketInfoGrids["deliveryInfo"]= array();
		
		$TotalPriceDelivery = 0;
		$TotalPriceDeliveryLek = 0;
	
	
		if ($deliveryCountryId=="") {
			return;
		} else if ($deliveryCountryId==2) {
			$typeDelivery = "national";
			$this->basketInfoGrids["deliveryInfo"]["typeDelivery"] = "{{_national}}";
			$this->basketInfoGrids["deliveryInfo"]["continent_description"]	="";
		} else {
			
			$typeDelivery = "international";
			$this->basketInfoGrids["deliveryInfo"]["typeDelivery"] = "{{_international}}";
			
			$getZonesForDelivery = "SELECT zona_id_standart,zona_id_ems, continent_description
			
									  FROM ".ESHOP_STORE_DB.".z_countries_code
									  JOIN ".ESHOP_STORE_DB.".z_continents_zones 
									    ON z_continents_zones.continent_id = z_countries_code.continent_code
									   WHERE  z_countries_code.id = '".$deliveryCountryId."'";
			
			$rsZonesForDelivery = WebApp::execQuery($getZonesForDelivery);
			if (!$rsZonesForDelivery->EOF()) { //perdor orderid e paperfunduar
				
				$this->basketInfoGrids["deliveryInfo"]["continent_description"]	= "(".$rsZonesForDelivery->Field("continent_description").")";
				$allowedDM["zona_id_standart"]	=$rsZonesForDelivery->Field("zona_id_standart");
				$allowedDM["zona_id_ems"]	=$rsZonesForDelivery->Field("zona_id_ems");
				
				if ($rsZonesForDelivery->Field("zona_id_standart")==0) {
					$allowedDM["standart"]	="default";
				} else if ($rsZonesForDelivery->Field("zona_id_standart")>=1 && $rsZonesForDelivery->Field("zona_id_standart")<=4) {
					$allowedDM["standart"] = "zona_".$rsZonesForDelivery->Field("zona_id_standart");
				} else {
					//gabim
				}
				
				if ($rsZonesForDelivery->Field("zona_id_ems")==0) {
					$allowedDM["ems"]	="default";
				} else if ($rsZonesForDelivery->Field("zona_id_ems")>=1 && $rsZonesForDelivery->Field("zona_id_ems")<=4) {
					$allowedDM["ems"] = "zona_".$rsZonesForDelivery->Field("zona_id_ems");
				} else {
					//gabim
				}				
			}
		}
		
		$selectedRulesDelivery = array();
		$getPostaDeliveryT = "SELECT id_tarif, tarif_type, user_selection, price_type
										FROM ".ESHOP_STORE_DB.".eshop__basket
										JOIN ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
										  ON eshop__basket.id_satelit = eshop__basket_extra_tarif.id_satelit
										 AND eshop__basket.orderid = eshop__basket_extra_tarif.orderid
									   WHERE eshop__basket.id_satelit	= '".SATELIT_ID."'
										 AND eshop__basket.orderid		= '".$this->orderid."'
										  AND user_selection   ='y'";

		$rs_PostaDeliveryT = WebApp::execQuery($getPostaDeliveryT);	
		while (!$rs_PostaDeliveryT->EOF()) { 
		
			$id_tarif 		= $rs_PostaDeliveryT->Field("id_tarif");
			$tarif_type 	= $rs_PostaDeliveryT->Field("tarif_type");
			$price_type 	= $rs_PostaDeliveryT->Field("price_type");
			
			$selectedRulesDelivery[$id_tarif][$tarif_type]=$price_type;
			$rs_PostaDeliveryT->moveNext();
		}
	
		$updatePriceT = "
			UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
			   SET user_selection 	= 't'			 
			 WHERE id_satelit		= '".SATELIT_ID."'
			   AND orderid			='".$this->orderid."'
			   AND user_selection   ='y'";
		WebApp::execQuery($updatePriceT);		
		

		
//  `tarif_type` enum('delivery','extra') COLLATE utf8_unicode_ci DEFAULT 'delivery',
		$indE = 0;
		$dataArrayE["data"] = array();
		$getDeliveryExtra = "
				SELECT id,delivery_to_type,type_delivery,description_of_delivery,extra_price,coalesce(delivery_time) as delivery_time 
				  FROM ".ESHOP_STORE_DB.".z_posta_delivery_types
	
				WHERE status = 'y' AND tarif_type = 'extra' AND delivery_to_type = '".$typeDelivery."'
				ORDER BY delivery_to_type, type_delivery";	
				
		$rsDeliveryExtra = WebApp::execQuery($getDeliveryExtra);
		while (!$rsDeliveryExtra->EOF()) { //perdor orderid e paperfunduar

			$idTarif												= $rsDeliveryExtra->Field("id");
			$id_subTarif											= 0;
			$description_of_delivery								= $rsDeliveryExtra->Field("description_of_delivery");
			$description_delivery_time								= $rsDeliveryExtra->Field("delivery_time");
			$dataArrayE["data"][$indE]["id"] 						= $idTarif;
			$dataArrayE["data"][$indE]["description_of_delivery"] 	= $description_of_delivery;
			$dataArrayE["data"][$indE]["description_delivery_time"] = $description_delivery_time;
			
			$dataArrayE["data"][$indE]["isSelected"] ="";
			$dataArrayE["data"][$indE]["preview"] ="no";
			if (isset($selectedRulesDelivery[$idTarif]["extra"])) {
				$dataArrayE["data"][$indE]["isSelected"] =" checked=\"checked\"";
				$dataArrayE["data"][$indE]["preview"] ="yes";
			}
			
			$extra_price = $rsDeliveryExtra->Field("extra_price");
			$dataArrayE["data"][$indE]["extra_price"] 		= number_format($extra_price,2);
			

			
			$insertToBasketT = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_extra_tarif (id_satelit,			orderid,				id_tarif,	id_subTarif,			price_type,		delivery_to_type,		tarif_type,		user_selection,		price)
																				VALUES ('".SATELIT_ID."',	'".$this->orderid."',	'".$idTarif."',	'".$id_subTarif."',					'fixed',		'".$typeDelivery."',	'extra'	,	'n'	,				'".$extra_price."')";
			WebApp::execQuery($insertToBasketT);	

			$extra_price_translated = $extra_price;
			$tmpData = eShopOrder::getTransatedPrice($this->basketInfo["currencyOrderId"],$extra_price_translated);
			if ($tmpData["existE"] == "yes") {
				$extra_price_translated = $tmpData["translatedVal"]; 	
			}		
			
			$dataArrayE["data"][$indE]["display_price"] 	= number_format($extra_price_translated,2);
			
			$updatePriceT = "
				UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
				   SET price 			= '".$extra_price_translated."',
				   	   Lekprice 		= '".$extra_price."',		
				   	   description_row 	= '".$description_of_delivery."',
				   	   description_delivery_time 	= '".$description_delivery_time."'
				 WHERE id_satelit		= '".SATELIT_ID."'
				   AND orderid			='".$this->orderid."'
				   AND id_tarif				='".$idTarif."'
				   AND id_subTarif	='".$id_subTarif."'
				   AND price_type			='fixed'";
			WebApp::execQuery($updatePriceT);

			$updatePriceT = "
				UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
				   SET user_selection 	= 'y'
				 WHERE id_satelit		= '".SATELIT_ID."'
				   AND orderid			='".$this->orderid."'
				   AND id_tarif				='".$idTarif."'
				   AND id_subTarif	='".$id_subTarif."'
				   AND price_type		='fixed'
				   AND user_selection   ='t'";
			WebApp::execQuery($updatePriceT);			

			$indE++;
			$rsDeliveryExtra->moveNext();
		}
		
		$dataArrayE["AllRecs"] = count($dataArrayE["data"]);
		WebApp::addVar("deliveryExtraGrid",$dataArrayE);	
		
		
		$indStore = 0;
		$dataArrayStore["data"] = array();
		
		$indStoreP = 0;
		$dataArrayStoreP["data"] = array();
		
		$getDeliveryStore = "
				SELECT id,delivery_to_type,type_delivery,description_of_delivery,0 as price,coalesce(delivery_time) as delivery_time 
				  FROM ".ESHOP_STORE_DB.".z_posta_delivery_types
	
				WHERE status = 'y' AND tarif_type = 'store' 
				ORDER BY delivery_to_type, type_delivery";	
				
		$rsDeliveryStore = WebApp::execQuery($getDeliveryStore);
		while (!$rsDeliveryStore->EOF()) { //perdor orderid e paperfunduar

			$idTarif												= $rsDeliveryStore->Field("id");
			$id_subTarif											= 0;
			$description_of_delivery								= $rsDeliveryStore->Field("description_of_delivery");
			$description_delivery_time								= $rsDeliveryStore->Field("delivery_time");
			$dataArrayStore["data"][$indStore]["id"] 						= $idTarif;
			$dataArrayStore["data"][$indStore]["description_of_delivery"] 	= $description_of_delivery;
			$dataArrayStore["data"][$indStore]["description_delivery_time"] = $description_delivery_time;
			
			$dataArrayStore["data"][$indStore]["isSelected"] ="";
			$dataArrayStore["data"][$indStore]["preview"] ="no";

			
			$store_price = $rsDeliveryStore->Field("price");
			$dataArrayStore["data"][$indStore]["store_price"] 				= "".number_format($store_price,2);
			

			if (isset($selectedRulesDelivery[$idTarif]["store"])) {
				$dataArrayStore["data"][$indStore]["isSelected"] =" checked=\"checked\"";
				$dataArrayStore["data"][$indStore]["preview"] ="yes";
				$dataArrayStoreP["data"][$indStoreP++] = $dataArrayStore["data"][$indStore];
			}


			$insertToBasketT = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_extra_tarif (id_satelit,			orderid,				id_tarif,	id_subTarif,			price_type,		delivery_to_type,		tarif_type,		user_selection,		price)
																				VALUES ('".SATELIT_ID."',	'".$this->orderid."',	'".$idTarif."',	'".$id_subTarif."',					'fixed',		'".$typeDelivery."',	'store'	,	'n'	,				'".$store_price."')";
			WebApp::execQuery($insertToBasketT);	

			$extra_price_translated = $store_price;
			$tmpData = eShopOrder::getTransatedPrice($this->basketInfo["currencyOrderId"],$extra_price_translated);
			if ($tmpData["existE"] == "yes") {
				$extra_price_translated = $tmpData["translatedVal"]; 	
			}	
			$dataArrayStore["data"][$indStore]["display_price"] 	= number_format($extra_price_translated,2);
			$updatePriceT = "
				UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
				   SET Lekprice 	= '".$store_price."',
				       price 		= '".$extra_price_translated."',
				   	   description_row 	= '".$description_of_delivery."',
				   	   description_delivery_time 	= '".$description_delivery_time."'
				 WHERE id_satelit		= '".SATELIT_ID."'
				   AND orderid			='".$this->orderid."'
				   AND id_tarif				='".$idTarif."'
				   AND id_subTarif	='".$id_subTarif."'
				   AND price_type			='fixed'";
			WebApp::execQuery($updatePriceT);
			$updatePriceT = "
				UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
				   SET user_selection 	= 'y'
				 WHERE id_satelit		= '".SATELIT_ID."'
				   AND orderid			='".$this->orderid."'
				   AND id_tarif				='".$idTarif."'
				   AND id_subTarif	='".$id_subTarif."'
				   AND price_type		='fixed'
				   AND user_selection   ='t'";
			WebApp::execQuery($updatePriceT);			
			$indStore++;
			$rsDeliveryStore->moveNext();
		}
		
		$dataArrayStore["AllRecs"] = count($dataArrayStore["data"]);
		WebApp::addVar("deliveryStoreGrid",$dataArrayStore);	
		
		$dataArrayStoreP["AllRecs"] = count($dataArrayStoreP["data"]);
		WebApp::addVar("deliveryStoreChoosedGrid",$dataArrayStoreP);					
		
		
		$this->basketInfoGrids["deliveryStoreGrid"] = $dataArrayStore;	
		$this->basketInfoGrids["deliveryStoreChoosedGrid"] = $dataArrayStoreP;	

		$ind = 0;
		$dataArray["data"] = array();
		$indCh = 0;
		$dataArrayChoosed["data"] = array();
		

		$getDeliveryMethod = "
				SELECT id,delivery_to_type,type_delivery,status,description_of_delivery,delivery_time, max_allowed_weight,coalesce(delivery_time) as delivery_time 
				FROM ".ESHOP_STORE_DB.".z_posta_delivery_types
				WHERE status = 'y' AND tarif_type = 'delivery' AND delivery_to_type = '".$typeDelivery."'
				ORDER BY delivery_to_type, type_delivery";	
				
		$rsDeliveryMethod = WebApp::execQuery($getDeliveryMethod);
		$nrDeliveryFounds = 0;
		while (!$rsDeliveryMethod->EOF()) { //perdor orderid e paperfunduar

			$id = $rsDeliveryMethod->Field("id");
			$idTarif = $id;
			
			$type_delivery_rec = $rsDeliveryMethod->Field("type_delivery");
			$max_allowed_weight = $rsDeliveryMethod->Field("max_allowed_weight");
			$description_of_delivery = $rsDeliveryMethod->Field("description_of_delivery");
			$description_delivery_time = $rsDeliveryMethod->Field("delivery_time");
	
		//	echo "$id:$max_allowed_weight<br>";

			if ($totalWeight<$max_allowed_weight) {
				
				$dataArray["data"][$ind]["allowed_weight"]			= "calculate";
				$dataArray["data"][$ind]["continent_description"] 	= $allowedDM["continent_description"];
				$dataArray["data"][$ind]["delivery_time"] 			= $description_delivery_time;
				
				$filterData = "";
				if ($typeDelivery == "international") {
					if ($type_delivery_rec=="standart")
						$filterData = " AND delivery_to_zone = '".$allowedDM["standart"]."'";
					if ($type_delivery_rec=="EMS")
						$filterData = " AND delivery_to_zone = '".$allowedDM["ems"]."'";
				}
				
				//gjej cmimin per delivery, brenda normave
				$getDeliveryTarif = "
						SELECT id_type,delivery_to_zone,pesha_from,pesha_to,price
						  FROM ".ESHOP_STORE_DB.".z_posta_delivery_tarifa
						 WHERE status = 'y' AND id = $id ".$filterData."
						   AND pesha_from	<= '".$totalWeight."' 
						   AND pesha_to		>= '".$totalWeight."' ";	

				$indT = 0;
				$dataArrayT["data"] = array();		

				$rsDeliveryTarif    = WebApp::execQuery($getDeliveryTarif);
				while (!$rsDeliveryTarif->EOF()) { //perdor orderid e paperfunduar	

					$id_subTarif						= $rsDeliveryTarif->Field("id_type");
					$dataArrayT["data"][$indT]["id_type"] = $rsDeliveryTarif->Field("id_type");
					$dataArrayT["data"][$indT]["delivery_to_zone"] = $rsDeliveryTarif->Field("delivery_to_zone");
					
					$price =  $rsDeliveryTarif->Field("price");
					$dataArrayT["data"][$indT]["price"] = number_format($price,2);
					
					$TotalPriceDelivery = $dataArrayT["data"][$indT]["price"];
					
					
					$insertToBasketT = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_extra_tarif (id_satelit,			orderid,				id_tarif,	id_subTarif,		price_type,		delivery_to_type,		tarif_type,		user_selection,		price)
																   						VALUES ('".SATELIT_ID."',	'".$this->orderid."',	'".$idTarif."',	'".$id_subTarif."',	'fixed',		'".$typeDelivery."',	'delivery'	,	'n'	,				'".$price."')";
					WebApp::execQuery($insertToBasketT);	
					
					$extra_price_translated = $price;
					$tmpData = eShopOrder::getTransatedPrice($this->basketInfo["currencyOrderId"],$extra_price_translated);
					if ($tmpData["existE"] == "yes") {
						$extra_price_translated = $tmpData["translatedVal"]; 	
					}		
					$TotalPriceDeliveryLek = $extra_price_translated;
					$dataArrayT["data"][$indT]["display_price"] 	= number_format($extra_price_translated,2);
					

					$updatePriceT = "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
						   SET Lekprice 		= '".$price."',		
						        price 			= '".$extra_price_translated."',
				   	   			description_row 	= '".$description_of_delivery."',
				   	   			description_delivery_time 	= '".$description_delivery_time."'
						 WHERE id_satelit		= '".SATELIT_ID."'
						   AND orderid			='".$this->orderid."'
						   AND id_tarif				='".$idTarif."'
						   AND id_subTarif	='".$id_subTarif."'
						   AND price_type		='fixed'";
					WebApp::execQuery($updatePriceT);
					
					$updatePriceT = "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
						   SET user_selection 	= 'y'
						 WHERE id_satelit		= '".SATELIT_ID."'
						   AND orderid			='".$this->orderid."'
						   AND id_tarif				='".$idTarif."'
						   AND id_subTarif	='".$id_subTarif."'

						   AND price_type		='fixed'
						   AND user_selection   ='t'";
					WebApp::execQuery($updatePriceT);
					
					$dataArray["data"][$ind]["allowed_weight"] 			= "u_gjet";
					
					$dataArray["data"][$ind]["isSelected"] 				= "";
					if (isset($selectedRulesDelivery[$idTarif]["delivery"])  && $selectedRulesDelivery[$idTarif]["delivery"]=="fixed") {
						$dataArray["data"][$ind]["isSelected"] 			= " checked=\"checked\"";
						$dataArray["data"][$ind]["preview"] ="yes";
						$dataArray["data"][$ind]["price"] =$price;
						$dataArray["data"][$ind]["display_price"] = number_format($extra_price_translated,2);
					}
					
					$nrDeliveryFounds++;

					$indT++;
					$rsDeliveryTarif->moveNext();
				}
				
				if ($dataArray["data"][$ind]["allowed_weight"] == "calculate") {

					$getDeliveryTarifRule = "
							SELECT id_type,delivery_to_zone,pesha_from,pesha_to,price, pesha_njesiToFormule
							  FROM ".ESHOP_STORE_DB.".z_posta_delivery_tarifa_formule
							 WHERE id = $id  ".$filterData."
							   AND pesha_from <= '".$totalWeight."' 
							   AND pesha_to	  >= '".$totalWeight."' 
							   ORDER BY pesha_to desc";	

					$indTR = 0;
					$dataArrayTR["data"] = array();		

					$rsDeliveryTarifRule    = WebApp::execQuery($getDeliveryTarifRule);
					while (!$rsDeliveryTarifRule->EOF()) { //perdor orderid e paperfunduar	

						$pesha_from = $rsDeliveryTarifRule->Field("pesha_from");
						
						$delivery_to_zone = $rsDeliveryTarifRule->Field("delivery_to_zone");
						$ricalculate_remaing = $pesha_from-1;
						
						$totalWeightToFormule = $totalWeight-$pesha_from;
						
						$pesha_njesiToFormule = $rsDeliveryTarifRule->Field("pesha_njesiToFormule");
						$priceFormule = $rsDeliveryTarifRule->Field("price");
						
						$cntToFormule = ceil($totalWeightToFormule/$pesha_njesiToFormule);
						$pricecalculated = $priceFormule*$cntToFormule;
						
						
						$dataArrayTR["data"][$indTR]["id_type"] = $rsDeliveryTarifRule->Field("id_type");
						$dataArrayTR["data"][$indTR]["delivery_to_zone"] 	= $rsDeliveryTarifRule->Field("delivery_to_zone");	
						
					
						//gjej cmimin per delivery, brenda normave
						$getDeliveryTarifLeftPart = "
								SELECT id_type,delivery_to_zone,pesha_from,pesha_to,price
								  FROM ".ESHOP_STORE_DB.".z_posta_delivery_tarifa
								 WHERE status = 'y' AND id = $id  and delivery_to_zone = '".$delivery_to_zone."'
								   AND pesha_from	<= '".$ricalculate_remaing."' 
								   AND pesha_to		>= '".$ricalculate_remaing."' ";	

						$rsDeliveryTarifLeft    = WebApp::execQuery($getDeliveryTarifLeftPart);
										
						
						if (!$rsDeliveryTarifLeft->EOF()) { //perdor orderid e paperfunduar	
							$pricecalculated += $rsDeliveryTarifLeft->Field("price");
						}							
						
					//	echo "$pricecalculated:pricecalculatedTot<br><br>";							
						
						
						$dataArrayTR["data"][$indTR]["price"] = number_format($pricecalculated,2);
						$TotalPriceDelivery = $pricecalculated;
						
						
						$insertToBasketT = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_extra_tarif (id_satelit,			orderid,				id_tarif,		price_type,		delivery_to_type,		tarif_type,		user_selection,		price)
																							VALUES ('".SATELIT_ID."',	'".$this->orderid."',	'".$idTarif."',	'calculated',		'".$typeDelivery."',	'delivery'	,	'n'	,				'".$pricecalculated."')";
						WebApp::execQuery($insertToBasketT);	

						$extra_price_translated = $pricecalculated;
						$tmpData = eShopOrder::getTransatedPrice($this->basketInfo["currencyOrderId"],$extra_price_translated);
						if ($tmpData["existE"] == "yes") {
							$extra_price_translated = $tmpData["translatedVal"]; 	
						}
						$dataArrayTR["data"][$indTR]["display_price"] 	= number_format($extra_price_translated,2);
						$TotalPriceDeliveryLek = $extra_price_translated;
						
						$updatePriceT = "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
							   SET price 			= '".$extra_price_translated."',
							       Lekprice 		= '".$pricecalculated."',	
				   	  			   description_row 	= '".$description_of_delivery."'
							 WHERE id_satelit		= '".SATELIT_ID."'
							   AND orderid			='".$this->orderid."'
							   AND id_tarif			='".$idTarif."'
							   AND price_type		='calculated'";
						WebApp::execQuery($updatePriceT);

						$updatePriceT = "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
							   SET user_selection 	= 'y'
							 WHERE id_satelit		= '".SATELIT_ID."'
							   AND orderid			='".$this->orderid."'
							   AND id_tarif			='".$idTarif."'
							   AND price_type		='calculated'
							   AND user_selection   ='t'";
						WebApp::execQuery($updatePriceT);							
						
						$dataArrayTR["data"][$indTR]["allowed_weight"] 		= "u_gjet";
						$nrDeliveryFounds++;
						
						$dataArray["data"][$ind]["isSelected"] ="";
						$dataArray["data"][$ind]["preview"] ="no";
						if (isset($selectedRulesDelivery[$idTarif]["delivery"])  && $selectedRulesDelivery[$idTarif]["delivery"]=="calculated") {
							$dataArray["data"][$ind]["isSelected"] 			= " checked=\"checked\"";
							$dataArray["data"][$ind]["preview"] ="yes";
							$dataArray["data"][$ind]["price"] =$pricecalculated;
							$dataArray["data"][$ind]["display_price"] = number_format($extra_price_translated,2);
						}						
						
						$indTR++;
						$rsDeliveryTarifRule->moveNext();						
					}							   

					$dataArrayTR["AllRecs"] = count($dataArrayTR["data"]);
					WebApp::addVar("deliveryTarifRuleGrid_$id",$dataArrayTR);
					$this->basketInfo["deliveryTarifRuleGrid_"][$id] = $dataArrayTR;
				}
				
				$dataArrayT["AllRecs"] = count($dataArrayT["data"]);
				WebApp::addVar("deliveryTarifGrid_$id",$dataArrayT);
				$this->basketInfoGrids["deliveryInfoTarif"][$id] = $dataArrayT;
			
			} else {
				$dataArray["data"][$ind]["allowed_weight"] 			= "error";
			}
		
			$dataArray["data"][$ind]["id"] = $id;
			$dataArray["data"][$ind]["max_allowed_weight"] 			= $max_allowed_weight;
			$dataArray["data"][$ind]["delivery_to_type"] 			= $rsDeliveryMethod->Field("delivery_to_type");
			$dataArray["data"][$ind]["type_delivery"] 				= $rsDeliveryMethod->Field("type_delivery");
			$dataArray["data"][$ind]["description_of_delivery"] 	= $rsDeliveryMethod->Field("description_of_delivery");
			$dataArray["data"][$ind]["delivery_time"] 				= $rsDeliveryMethod->Field("delivery_time");
			
			
			if ($dataArray["data"][$ind]["preview"] == "yes") {
				$dataArrayChoosed["data"][$indCh++] = $dataArray["data"][$ind];
			}
			
			$ind++;
			$rsDeliveryMethod->moveNext();
		}
		
/*	echo "basketInfoGrids<textarea>";	
	print_r($dataArray);
	print_r($dataArrayChoosed);
	echo "</textarea>";	*/	
		
		$dataArray["AllRecs"] = count($dataArray["data"]);
		WebApp::addVar("deliveryTypeGrid",$dataArray);	
		
		$dataArrayChoosed["AllRecs"] = count($dataArrayChoosed["data"]);
		WebApp::addVar("deliveryTypeChoosedGrid",$dataArrayChoosed);	

		$this->basketInfoGrids["deliveryTypeChoosedGrid"] = $dataArrayChoosed;	
		$this->basketInfoGrids["deliveryTypeGrid"] = $deliveryTypeGrid;	
		
		//$this->basketInfo["totals"]["DeliveryPrice"] = $TotalPriceDelivery;
		$updatePriceT = "
			UPDATE ".ESHOP_STORE_DB.".eshop__basket_extra_tarif
			   SET user_selection 	= 'n'
			 WHERE id_satelit		= '".SATELIT_ID."'
			   AND orderid			='".$this->orderid."'
			   AND user_selection   ='t'";
		WebApp::execQuery($updatePriceT);
		
		$this->basketInfoGrids["deliveryInfoGrid"]["data"][0] = $this->basketInfoGrids["deliveryInfo"];
		$this->basketInfoGrids["deliveryInfoGrid"]["AllRecs"] = 1;
	}	
	function changeBasketState()
	{					
	}		
	function changeBasketStatusPrepareToPAYPAL($max_id,$po,$oid)
	{					
	}	
	function saveProfileDataRelated($paramsToControlByModule)
	{	
			if (isset($_GET["step"]) && $_GET["step"]!="") 
					$step = $_GET["step"];
			else	$step = 1;
		
				if ($this->ses_userid !=2) {

					if ($step == 2) {
						$updateStepRelatedData= "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket
							   SET	bll_name 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_name"])."',
									bll_email 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_email"])."',
									bll_phone 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_phone"])."',

									bll_address_street 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_adr_street"])."',
									bll_address_street_ext 	= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_adr_street_ext"])."',
									bll_address_zip 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_adr_zip"])."',
									bll_address_city 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_adr_city"])."',
									bll_address_country 	= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["bll_adr_country"])."'
							  WHERE orderid = '".$orderID."'";
						//echo $updateStepRelatedData."--";
						WebApp::execQuery($updateStepRelatedData);				
					}	

					if ($step == 3) {
						//sameAdrres	same
						$updateStepRelatedData= "
							UPDATE ".ESHOP_STORE_DB.".eshop__basket
							   SET	dlv_name 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_name"])."',
									dlv_email 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_email"])."',
									dlv_phone 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_phone"])."',									

									dlv_address_street 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_adr_street"])."',
									dlv_address_street_ext 	= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_adr_street_ext"])."',
									dlv_address_zip 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_adr_zip"])."',
									dlv_address_city 		= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_adr_city"])."',
									dlv_address_country 	= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_adr_country"])."',
									dlv_address_country_id 	= '".ValidateVarFun::f_real_escape_string($paramsToControlByModule["dlv_address_country_id"])."'
							  WHERE orderID = '".$orderID."'";
						//echo $updateStepRelatedData."--";
						WebApp::execQuery($updateStepRelatedData);
					}		
					$this->InitProfileUser();
				}
		return $step;
	}	
	function getCoundryCode()
	{
		$id = 0;	
		$countriesGrid["data"] = array();	
		$getInfo ="SELECT country_iso2_code, id,	country_name								
					 FROM ".ESHOP_STORE_DB.".z_countries_code 
				 ORDER BY country_name";		
		$rs_info= WebApp::execQuery($getInfo);
		while (!$rs_info->EOF()) {
			
			$idc 				= $rs_info->Field("id");
			$country_iso2_code	= $rs_info->Field("country_iso2_code");
			$country_name		= $rs_info->Field("country_name");
			$countriesGrid["data"][$id]["id"]			= $idc;			
			$countriesGrid["data"][$id]["iso2_code"]	= $country_iso2_code;			
			$countriesGrid["data"][$id]["country_name"]	= ucwords(strtolower($country_name));	
			
			if (isset($this->basketInfo["deliveryCountryId"] ) && $idc ==$this->basketInfo["deliveryCountryId"] )
					$countriesGrid["data"][$id]["sel"]	= " selected=\"selected\"";	
			else	$countriesGrid["data"][$id]["sel"]	= "";	
			
			
			if (isset($this->AdrBk["deliveryCountryId"] ) && $idc ==$this->AdrBk["deliveryCountryId"] )
					$countriesGrid["data"][$id]["selAdr"]	= " selected=\"selected\"";	
			else	$countriesGrid["data"][$id]["selAdr"]	= "";				
			
			$this->countriesData[$idc]["iso2_code"]			= $country_iso2_code;	
			$this->countriesData[$idc]["country_name"]		= ucwords(strtolower($country_name));
			$id++;
			$rs_info->MoveNext();
		} 
		
		$countriesGrid["AllRecs"] = count($countriesGrid["data"]);	
		WebApp::addVar("countriesGrid", $countriesGrid);				
	}
	function InitProfileUser($orderId="")
	{
		    //inicializojme me bosh ---------------------------------------------------------------------------
              $this->orderCheckoutRelatedData["payer_first_name"]                = "";
		      $this->orderCheckoutRelatedData["payer_last_name"]                 = "";
		      $this->orderCheckoutRelatedData["payer_title"]                     = "";
		      $this->orderCheckoutRelatedData["payer_email"]                     = "";
		      $this->orderCheckoutRelatedData["payer_contact_phone"]             = "";

  		      $this->orderCheckoutRelatedData["contact_phone"]                      = "";
    
		      $this->orderCheckoutRelatedData["bll_name"]          				= "";
		      $this->orderCheckoutRelatedData["bll_email"]          				= "";
		      $this->orderCheckoutRelatedData["bll_phone"]          				= "";
		      $this->orderCheckoutRelatedData["bll_address_street"]              = "";
		      $this->orderCheckoutRelatedData["bll_address_street_ext"]          = "";
		      $this->orderCheckoutRelatedData["bll_address_zip"]                 = "";
		      $this->orderCheckoutRelatedData["bll_address_city"]                = "";
		      $this->orderCheckoutRelatedData["bll_address_state"]                = "";
		      $this->orderCheckoutRelatedData["bll_address_country"]             = "";

		      $this->orderCheckoutRelatedData["dlv_name"]          				= "";
		      $this->orderCheckoutRelatedData["dlv_email"]          				= "";
		      $this->orderCheckoutRelatedData["dlv_phone"]          				= "";
		      $this->orderCheckoutRelatedData["dlv_address_street"]              = "";
		      $this->orderCheckoutRelatedData["bll_address_street_ext"]              = "";
		      $this->orderCheckoutRelatedData["dlv_address_zip"]                 = "";
		      $this->orderCheckoutRelatedData["dlv_address_city"]                = "";
		      $this->orderCheckoutRelatedData["dlv_address_state"]             = "";	
		      $this->orderCheckoutRelatedData["dlv_address_country"]             = "";	
		      $this->orderCheckoutRelatedData["dlv_address_country_id"]             = "";	
		      
		      $billing_info = array();	
		      $shipping_info = array();	
		      
		      $this->basketInfo["deliveryCountryId"]  = "";

		
			  $getProfileRelatedData = "
					SELECT 
										COALESCE(delivery_same_as_bill,     '') AS delivery_same_as_bill,
										COALESCE(enteredStepData,           '') AS enteredStepData,
										COALESCE(payer_first_name,          '') AS payer_first_name,

										COALESCE(payer_last_name,           '') AS payer_last_name,
										COALESCE(payer_title,              	'') AS payer_title,
										COALESCE(payer_email,              	'') AS payer_email,
										COALESCE(payer_contact_phone,		'') AS payer_contact_phone,

										COALESCE(bll_name,					'') AS bll_name,
										COALESCE(bll_email,					'') AS bll_email,
										COALESCE(bll_phone,					'') AS bll_phone,								
										COALESCE(bll_address_street,        '') AS bll_address_street,
										COALESCE(bll_address_street_ext,	'') AS bll_address_street_ext,
										COALESCE(bll_address_zip,			'') AS bll_address_zip,
										COALESCE(bll_address_city,			'') AS bll_address_city,
										COALESCE(bll_address_state,			'') AS bll_address_state,
										COALESCE(bll_address_country,		'') AS bll_address_country,

										COALESCE(dlv_name,					'') AS dlv_name,
										COALESCE(dlv_email,					'') AS dlv_email,
										COALESCE(dlv_phone,					'') AS dlv_phone,
										COALESCE(dlv_address_street,		'') AS dlv_address_street,
										COALESCE(dlv_address_street_ext,	'') AS dlv_address_street_ext,
										COALESCE(dlv_address_zip,			'') AS dlv_address_zip,
										COALESCE(dlv_address_city,			'') AS dlv_address_city,
										COALESCE(dlv_address_state,			'') AS dlv_address_state,
										COALESCE(dlv_address_country,		'') AS dlv_address_country,
										COALESCE(dlv_address_country_id,	'') AS dlv_address_country_id

					   FROM ".ESHOP_STORE_DB.".eshop__basket
					  WHERE orderID = '".$this->orderid."'";					

				$relatedData = WebApp::execQuery($getProfileRelatedData);	
				if(!$relatedData->EOF()) {
					
					$enteredStepData = $relatedData->Field("enteredStepData");
					
					$this->orderCheckoutRelatedData["enteredStepData"] 			= $relatedData->Field("enteredStepData");
					$this->orderCheckoutRelatedData["delivery_same_as_bill"] 	= $relatedData->Field("delivery_same_as_bill");
					$this->orderCheckoutRelatedData["payer_first_name"] 		= $relatedData->Field("payer_first_name");
					$this->orderCheckoutRelatedData["payer_last_name"]     		= $relatedData->Field("payer_last_name");
					$this->orderCheckoutRelatedData["payer_title"]         		= $relatedData->Field("payer_title");
					$this->orderCheckoutRelatedData["payer_email"]         		= $relatedData->Field("payer_email");
					$this->orderCheckoutRelatedData["payer_contact_phone"] 		= $relatedData->Field("payer_contact_phone");

					if ($enteredStepData>=2) {
						$billing_info["data"][0]["full_name"] 			= $relatedData->Field("bll_name");
						$billing_info["data"][0]["address_phone"] 		= $relatedData->Field("bll_phone");
						$billing_info["data"][0]["address_email"] 		= $relatedData->Field("bll_email");	

						$billing_info["data"][0]["address_street"] 		= $relatedData->Field("bll_address_street");
						$billing_info["data"][0]["address_zip"] 		= $relatedData->Field("bll_address_zip");
						$billing_info["data"][0]["address_city"] 		= $relatedData->Field("bll_address_city");

						$billing_info["data"][0]["address_state"]		= $relatedData->Field("bll_address_state");
						$billing_info["data"][0]["address_country"] 		= $relatedData->Field("bll_address_country");
						
						$billing_info["AllRecs"] = count($billing_info["data"]);	
						WebApp::addVar("billing_info_Grid", $billing_info);	
						WebApp::addVar("billing_info_AllRecs", "".$billing_info["AllRecs"]);	
					}

					if ($enteredStepData>=3) {
						$shipping_info["data"][0]["full_name"] 			= $relatedData->Field("dlv_name");
						$shipping_info["data"][0]["address_phone"] 		= $relatedData->Field("dlv_phone");
						$shipping_info["data"][0]["address_email"] 		= $relatedData->Field("dlv_email");	

						$shipping_info["data"][0]["address_street"] 	= $relatedData->Field("dlv_address_street");
						$shipping_info["data"][0]["address_zip"] 		= $relatedData->Field("dlv_address_zip");
						$shipping_info["data"][0]["address_city"] 		= $relatedData->Field("dlv_address_city");

						$shipping_info["data"][0]["address_state"]		= $relatedData->Field("dlv_address_state");
						$shipping_info["data"][0]["address_country"] 	= $relatedData->Field("dlv_address_country");						

						$this->basketInfo["deliveryCountryId"]  					= $relatedData->Field("dlv_address_country_id");
						
						$shipping_info["AllRecs"] = count($shipping_info["data"]);	
						WebApp::addVar("shipping_info_Grid", $shipping_info);			
						WebApp::addVar("shipping_info_AllRecs", "".$shipping_info["AllRecs"]);			
					}
				}
		
		$this->profileData = $this->orderCheckoutRelatedData;
	}
	function prepareSecureCheckoutNeddedInfo()
	{
		$this->getCoundryCode();
		//per userin e loguar popullojme vlerat nga profili i tij --------------------------------------------------------------
		$existCheckoutRecord = "no";
		$this->InitProfileUser();
	
		$getOrderCheckoutRelatedDAta = "
			 SELECT COALESCE(enteredStepData,             '1') AS enteredStepData, 
			 		COALESCE(orderKey, 'empty') as OrderKey
			   FROM ".ESHOP_STORE_DB.".eshop__basket
			  WHERE orderID = '".$this->orderid."'";	
			
		$relatedData = WebApp::execQuery($getOrderCheckoutRelatedDAta);
		IF (!$relatedData->EOF()) {		// AND mysql_errno() == 0
			$OrderKey = $relatedData->Field("OrderKey");
			$this->OrderKey =$OrderKey;
			if ($OrderKey=="empty" || $OrderKey=="") {
					$OrderKey = strtoupper($this->genpassword(2))."-".strtoupper($this->genpassword(2)).$this->genpassword_nr(2).strtoupper($this->genpassword(1))."-".strtoupper($this->genpassword(2)).$this->genpassword_nr(2)."-".strtoupper($this->genpassword(2)).$this->genpassword_nr(1);
					
					$nrA = strlen($this->orderid);
					$rmng = 8-$nrA;
					//model 8_5_5
					$this->OrderKey = strtoupper($this->genpassword($rmng)).$this->orderid."-".strtoupper($this->genpassword(2)).$this->genpassword_nr(2).strtoupper($this->genpassword(1))."-".strtoupper($this->genpassword(2)).$this->genpassword_nr(2).strtoupper($this->genpassword(1));
					
					$updateStep= "
						UPDATE ".ESHOP_STORE_DB.".eshop__basket
						   SET orderKey   = '".$this->OrderKey."'
						 WHERE orderID    = '".$this->orderid."'
						   AND order_type = 'basket'";
					WebApp::execQuery($updateStep);	
					
					


						$getInfo ="
								SELECT   COALESCE(FirstName,				'') AS FirstName,
										 COALESCE(SecondName,				'') AS SecondName,
										 COALESCE(usr_title,				'') AS usr_title,
										 COALESCE(usr_email,				'') AS usr_email,
										 COALESCE(usr_phone,				'') AS usr_phone,
										 
										 COALESCE(usr_street,				'') AS address_street,
										 COALESCE(usr_postcode,				'') AS address_zip,
										 COALESCE(usr_city,				'') AS address_city
										 
								FROM users 				  				 
							   WHERE UserId ='".$this->userSystemID."'";	
							   
		
							   
							   
							   
							   

					$rs_info = WebApp::execQuery($getInfo);
					if (!$rs_info->EOF()) {

						 $FirstName 			= $rs_info->Field("FirstName");
						 $SecondName 			= $rs_info->Field("SecondName");
						 $full_name 			= $FirstName." ".$SecondName;
						 $usr_title 			= $rs_info->Field("usr_title");
						 $usr_email 			= $rs_info->Field("usr_email");
						 $usr_phone 			= $rs_info->Field("usr_phone");
						 
						 
						 $address_street 		= $rs_info->Field("address_street");
						 $address_zip 			= $rs_info->Field("address_zip");
						 $address_city 			= $rs_info->Field("address_city");
						 $address_state 			= "";
						 $address_country 			= "";
						 
						 $updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET	bll_name 		= '".ValidateVarFun::f_real_escape_string($full_name)."',
										bll_email 		= '".ValidateVarFun::f_real_escape_string($usr_email)."',
										bll_phone 		= '".ValidateVarFun::f_real_escape_string($usr_phone)."',

										bll_address_street 		= '".ValidateVarFun::f_real_escape_string($address_street)."',
										bll_address_zip 		= '".ValidateVarFun::f_real_escape_string($address_zip)."',
										bll_address_city 		= '".ValidateVarFun::f_real_escape_string($address_city)."',
										bll_address_state 		= '".ValidateVarFun::f_real_escape_string($address_state)."',
										bll_address_country 	= '".ValidateVarFun::f_real_escape_string($address_country)."'

								  WHERE orderid = '".$this->orderid."'";
						 //echo $updateStepRelatedData."--";
						 WebApp::execQuery($updateStepRelatedData);							 
						 

						 $updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET	payer_first_name 		= '".ValidateVarFun::f_real_escape_string($FirstName)."',
										payer_last_name 		= '".ValidateVarFun::f_real_escape_string($SecondName)."',
										payer_title 		= '".ValidateVarFun::f_real_escape_string($usr_title)."',
										payer_email 		= '".ValidateVarFun::f_real_escape_string($usr_email)."',
										payer_contact_phone 		= '".ValidateVarFun::f_real_escape_string($usr_phone)."'

								  WHERE orderid = '".$this->orderid."'";
						 //echo $updateStepRelatedData."--";
						 WebApp::execQuery($updateStepRelatedData);	
					}



					
						$getInfo ="
								SELECT   COALESCE(address_name,				'') AS address_name,
										 COALESCE(address_street,				'') AS address_street,
										 COALESCE(address_city,				'') AS address_city,
										 COALESCE(address_state,				'') AS address_state,
										 COALESCE(address_zip,				'') AS address_zip,
										 COALESCE(address_country,			'') AS address_country,
										 COALESCE(address_phone,				'') AS address_phone,
										 COALESCE(address_email,				'') AS address_email,
										 COALESCE(address_used,				'') AS address_used,
										 COALESCE(address_country_id,		'') AS address_country_id,
										 COALESCE(country_name,				'') AS country_name
								FROM user_address 				  				 
						   LEFT JOIN ".ESHOP_STORE_DB.".z_countries_code ON z_countries_code.id = user_address.address_country_id
							   WHERE id_user ='".$this->userSystemID."'";	

					$rs_info = WebApp::execQuery($getInfo);
					if (!$rs_info->EOF()) {

						 $full_name 			= $rs_info->Field("address_name");
						 $address_email 			= $rs_info->Field("address_email");
						 $address_phone 			= $rs_info->Field("address_phone");

						 $address_street 		= $rs_info->Field("address_street");
						 $address_zip 			= $rs_info->Field("address_zip");
						 $address_city 			= $rs_info->Field("address_city");

						 $address_state			= $rs_info->Field("address_state");
						 $address_country 		= $rs_info->Field("country_name");
						 $address_country_id 	= $rs_info->Field("address_country_id");					

						 $updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__basket
								   SET	bll_name 		= '".ValidateVarFun::f_real_escape_string($full_name)."',
										bll_email 		= '".ValidateVarFun::f_real_escape_string($address_email)."',
										bll_phone 		= '".ValidateVarFun::f_real_escape_string($address_phone)."',

										bll_address_street 		= '".ValidateVarFun::f_real_escape_string($address_street)."',
										bll_address_zip 		= '".ValidateVarFun::f_real_escape_string($address_zip)."',
										bll_address_city 		= '".ValidateVarFun::f_real_escape_string($address_city)."',
										bll_address_state 		= '".ValidateVarFun::f_real_escape_string($address_state)."',
										bll_address_country 	= '".ValidateVarFun::f_real_escape_string($address_country)."',
										bll_address_country_id 	= '".ValidateVarFun::f_real_escape_string($address_country_id)."'

								  WHERE orderid = '".$this->orderid."'";
						 //echo $updateStepRelatedData."--";
						 WebApp::execQuery($updateStepRelatedData);	
					}					
					
										
					
					
					
					
					
			   }
				WebApp::addVar("OrderKeyPurchase", $OrderKey);				
				$this->orderCheckoutRelatedData["enteredStepData"]     = $relatedData->Field("enteredStepData");
		}
		
		$this->getDeliveryInfo();
		$this->controllBasketState();
		$this->displayBasketInfo();
	}
	function prepareSecureCheckout()
	{
		//$this->prepareSecureCheckoutNeddedInfo();
		if ($this->checkoutClient=="bkt") {
			$this->bktSecureCheckout();
		} elseif ($this->checkoutClient=="Raiffaisen") { 
			$this->RaiffaisenSecureCheckout();	
		} elseif ($this->checkoutClient=="Paypal") { 
			$this->PaypalSecureCheckout();
		}
	}
	function bktSecureCheckout()
	{
		
		$this->bktConstants["tranType"] 	= "PreAuth";	//Auth, PreAuth, PostAuth,		// Void, Credit
				
		$this->bktConstants["okUrl"] 	= APP_URL."?crd=285&st=1";
		$this->bktConstants["failUrl"] 	= APP_URL."?crd=280&st=1";
		
		$this->bktConstants["instalment"] = "";
		
		//$strLenRnd =  strlen($this->bktConstants["rnd"]);
		$this->bktConstants["hash"] = "";
		
		
		
		$this->bktConstants["currency"] = "008";
		//$this->bktConstants["currency"] = $this->basketInfo["currency_code"];
		
		
//	$this->exchange["data"][$ind]["currency_code"]	
		
		
		$this->bktConstants["lang"] 	= PortalIndentifikuesLangCode;
		$this->bktConstants["Fismi"] 	= PortalIndentifikues;
		
		$this->bktConstants["amount"]  = ($this->basketInfo["TotalPriceAndDelivery"]);
		$this->bktConstants["DeliveryPrice"]  = ($this->basketInfo["totals"]["DeliveryPrice"]);
		
		//SWI19-CR65N-NA04-BU8
		//$this->bktConstants["oid"]     	= $this->orderid; //per kete mund te shtohet	
		$this->bktConstants["oid"]     	= $this->OrderKey; //per kete mund te shtohet	
		
		//$this->bktConstants["rnd"] 		= $this->OrderKey;
		$this->bktConstants["rnd"] 		= base64_encode(sha1($this->OrderKey, true));	
		
		$plainTextToHash = $this->bktConstants["clientid"].$this->bktConstants["oid"].$this->bktConstants["amount"].$this->bktConstants["okUrl"].$this->bktConstants["failUrl"].$this->bktConstants["tranType"].$this->bktConstants["instalment"].$this->bktConstants["rnd"].$this->bktConstants["storekey"];
		
		

		$hashedKeysha1 = sha1($plainTextToHash, true);
		$hashedKey = base64_encode($hashedKeysha1);
		
		$this->bktConstants["plainTextToHash"] 	= $plainTextToHash;	//Set to "Auth" for sale, "PreAuth" for authorization.
		$this->bktConstants["hashedKeysha1"] 	= $hashedKeysha1;	//Set to "Auth" for sale, "PreAuth" for authorization.
		$this->bktConstants["hash"] 	= $hashedKey;	//Set to "Auth" for sale, "PreAuth" for authorization.
		

		
	/*	echo "<pre>";
		print_r($plainTextToHash);
		echo "</pre>";	*/	
		
		
		
		
		
		$this->orderCheckoutRelatedData["urlToSubmitBktToConstants"] = $this->bktConstants["urlToSubmitTo"];

		WebApp::addVar("urlToSubmitBktToConstants",$this->bktConstants["urlToSubmitTo"]);	
		$this->orderCheckoutRelatedData = array_merge($this->orderCheckoutRelatedData,$this->bktConstants);

		$dataArray["data"][0] = $this->orderCheckoutRelatedData;
		$dataArray["AllRecs"] = 1;
		WebApp::addVar("CheckoutDataGrid",$dataArray);	
		
		$this->CheckoutDataGrid = $dataArray;	
	}
	function PaypalSecureCheckout()
	{
		$this->RaiffaisenConstants["OrderID"]      = $this->orderid; //per kete mund te shtohet
		$this->RaiffaisenConstants["PurchaseTime"] = date("ymdHis");
		$this->RaiffaisenConstants["PurchaseDesc"] = addslashes($this->itemsDescriptionsToCheckout); //per kete mund te shtohet

		$this->RaiffaisenConstants["OrderKey"]     = $this->OrderKey; //per kete mund te shtohet

		$MerchantID 	= $this->RaiffaisenConstants["MerchantID"];
		$TerminalID 	= $this->RaiffaisenConstants["TerminalID"];
		$PurchaseTime 	= $this->RaiffaisenConstants["PurchaseTime"];
		$OrderID 		= $this->RaiffaisenConstants["OrderID"];
		$CurrencyID 	= $this->RaiffaisenConstants["Currency"];

		$this->RaiffaisenConstants["TotalAmount"]  = ($this->basketInfo["totals"]["totalBasketPrice"]); //*100 -MBAJE MEND NESE TE JEP GABIM
		$TotalAmount 	= $this->RaiffaisenConstants["TotalAmount"];

		$dataToBeConvertedInSignatureOut = "$MerchantID;$TerminalID;$PurchaseTime;$OrderKey;$CurrencyID;$TotalAmount;".$this->uniqueid.";";
		$fp = fopen(CertificateRaifassenFolder.$MerchantID.".pem", "r");

		$priv_key = fread($fp, 8192);
		fclose($fp);
		$pkeyid = openssl_pkey_get_private($priv_key);
		openssl_sign( $dataToBeConvertedInSignatureOut , $signature, $pkeyid);
		openssl_free_key($pkeyid);
		$b64sign = base64_encode($signature);

		$this->RaiffaisenConstants["b64sign_submit"] = $b64sign;		
		
		$this->orderCheckoutRelatedData["urlToSubmitTo"] = $this->RaiffaisenConstants["urlToSubmitTo"];

	    while (list($keyV,$dataV)=each($this->orderCheckoutRelatedData)) 
	          {
	           WebApp::addVar("$keyV","".$dataV."");
	          }

		$this->orderCheckoutRelatedData = $this->orderCheckoutRelatedData;

		$dataArray["data"][0] = $this->RaiffaisenConstants;
		$dataArray["AllRecs"] = 1;
		WebApp::addVar("CheckoutDataGrid",$dataArray);	
		
		$this->CheckoutDataGrid = $dataArray;
	}	
	function RaiffaisenSecureCheckout()
	{
	}	
	function callBackFromPaymentOutsorceOnline()
	{
		if ($this->checkoutClient=="bkt") {
			$this->callBackFromBkt();
		} elseif ($this->checkoutClient=="Raiffaisen") { 
		} elseif ($this->checkoutClient=="Paypal") { 
		}	
	}
	function callBackFromBkt()
	{
		
		global $vrBkPost;

		
		
		
		
		$post_variables   = print_r($_POST,   true);
		$get_variables    = print_r($_GET,    true);
		$server_variables = print_r($_SERVER, true);
		 
		//1. kontrolli pare eshte kontrolli i firmes qe na dergohet nga raifasen
		//per verifikim
		$this->PPBA["merchantID"] 				= $_POST["merchantID"];
		$this->PPBA["HASHPARAMS"] 				= $_POST["HASHPARAMS"];
		$this->PPBA["HASHPARAMSVAL"] 			= $_POST["HASHPARAMSVAL"];
		$this->PPBA["HASH"] 					= $_POST["HASH"];

		$this->PPBA["mdStatus"] 				= $_POST["mdStatus"];
			//Status code for the 3D transaction
			//1=authenticated transaction
			//2, 3, 4 = Card not participating or attempt
			//5,6,7,8 = Authentication not available or system error
			//0 = Authentication failed
		
		$this->PPBA["iReqCode"] 				= $_POST["iReqCode"];
			//Code provided by ACS indicating data that is formatted correctly, but which invalidates the request. This element is included when business processing cannot be performed for some reason.

		$this->PPBA["vendorCode"] 				= $_POST["vendorCode"];
			//Error message describing iReqDetail error.

		//per ruajte
		$this->PPBA["AuthCode"] 				= $_POST["AuthCode"];	//Transaction Verification/Approval/Authorization code: 6 characters
		$this->PPBA["PaymentStatus"] 			= $_POST["Response"];	//Possible values: "Approved", "Error", "Declined"
		$this->PPBA["HostRefNum"] 				= $_POST["HostRefNum"];	//Host reference number: 12 characters
		$this->PPBA["TransId"] 					= $_POST["TransId"];	//BKT VPOS Transaction Id:Maximum 64 characters

		$this->PPBA["md"] 						= $_POST["md"];	//BKT VPOS Transaction Id:Maximum 64 characters

		$this->PPBA["XID"] 						= $_POST["xid"];		//xid Unique internet transaction ID
		$this->PPBA["MaskedPan"] 				= $_POST["MaskedPan"];	//Masked credit card number 12 characters, XXXXXX***XXX	
		$this->PPBA["ClientIp"] 				= $_POST["ClientIp"];	//IP address of the customer

		if (isset($_POST["ReturnOid"]) && $_POST["ReturnOid"]!="")
				$this->PPBA["orderKey"] 				= $_POST["ReturnOid"];
		else	$this->PPBA["orderKey"] 				= $_POST["oid"];
		
		
		
	//	oid=NUBRIPH5-BO53C-CH71U
		
		
		$this->PPBA["TotalAmount"] 				= $_POST["amount"];
		$this->PPBA["Currency"] 				= $_POST["currency"];	
		$this->PPBA["PurchaseTime"] 			= $_POST["EXTRA.TRXDATE"];
		$this->PPBA["CARDBRAND"] 				= $_POST["EXTRA.CARDBRAND"];
	//	PurchaseTime
		
		$this->PPBA["eci"] 						= $_POST["eci"];	//Electronic Commerce Indicator:2 digits, empty for non-3D transactions
		$this->PPBA["cavv"] 					= $_POST["cavv"];	//Cardholder Authentication Verification Value, determined by ACS.
		$this->PPBA["md"] 						= $_POST["md"];		//MPI data replacing card number
		$this->PPBA["SchemaID"] 				= $_POST["sID"];	//"1" for Visa, "2" for Mastercard

		//emaili qe eshte plotesuar te faqja matane ?????????????????
		$this->PPBA["callback_email"] 			= ""; //$_POST["Email"]; 		
		
		$this->PPBA["callback_email"] 				= $this->PPBA["callback_email"];	
		$this->PPBA["callback_TranCode"] 			= $this->PPBA["TransId"];	
		$this->PPBA["callback_ApprovalCode"] 		= $this->PPBA["AuthCode"];	
		$this->PPBA["callback_XID"] 				= $this->PPBA["XID"];	
		$this->PPBA["callback_Rrn"] 				= "";	
		
		$this->PPBA["callback_ProxyPan"] 			= $this->PPBA["MaskedPan"];
		/* KONTROLLI I TE DHENAVE TE BKT */
		
		$this->PPBA["HASHPARAMS"] 				= $_POST["HASHPARAMS"];
		$this->PPBA["HASHPARAMSVAL"] 			= $_POST["HASHPARAMSVAL"];
		$this->PPBA["HASH"] 					= $_POST["HASH"];	
		
		$this->PPBA["TranCode"] 				= $_POST["ProcReturnCode"];
		$this->PPBA["ErrMsg"] 					= $_POST["ErrMsg"];


/*echo "<textarea>";	
print_r($this->PPBA);
echo "</textarea>";*/



		
		$this->error_code = 1;	//
		If (isset($this->PPBA["HASH"]) && $this->PPBA["HASH"]!="") {
			If (isset($this->PPBA["HASHPARAMSVAL"]) && $this->PPBA["HASHPARAMSVAL"]!="") {
				If (isset($this->PPBA["HASHPARAMS"]) && $this->PPBA["HASHPARAMS"]!="") {
					$hashOrderedData = explode(":",$this->PPBA["HASHPARAMS"]);
					$orderedDataToPlainTextValArr = array();
					while (list($key,$val)=each($hashOrderedData)) {
						if ($val!="") {
							if (isset($vrBkPost[$val])) {
								$orderedDataToPlainTextValArr[$val] = $vrBkPost[$val];
							} else {
							//	$this->error_code = 4;	//HASHPARAMS jo ne rregull
							//	$this->applicationErrors[$this->error_code][$val] = "nuk ka ardhur Variabli me poste ose ne e kemi pastruar";	//BAD
							}
						}
					}
				} else {
					$this->error_code = 3;	//
					$this->applicationErrors[$this->error_code][] = "HASHPARAMS IS EMPTY";	//BAD
				}
			} else {
					$this->error_code = 2;	//
					$this->applicationErrors[$this->error_code][] = "HASHPARAMSVAL IS EMPTY";	//BAD
			}	
		} else {
				$this->error_code = 1;	//
				$this->applicationErrors[$this->error_code][] = "HASH IS EMPTY";	//BAD
		}		
		
		if ($this->error_code==1) { //deri tani nuk kemi asnje gabim
			
			if(count($orderedDataToPlainTextValArr)>0) {
				$orderedDataToPlainTextVal = implode("",$orderedDataToPlainTextValArr);
				
				if ($orderedDataToPlainTextVal != $this->PPBA["HASHPARAMSVAL"]) {
					$this->error_code = 6;	//
					$this->applicationErrors[$this->error_code][] = "HASHPARAMSVAL != orderedDataToPlainTextVal";	//BAD
				} else {
					$orderedDataToPlainTextValToHash = $orderedDataToPlainTextVal.$this->bktConstants["storekey"];
					$hashToCheck = base64_encode(sha1($orderedDataToPlainTextValToHash, true));	
					if ($hashToCheck != $this->PPBA["HASH"]) {
					//	$this->error_code = 7;	
					//	$this->applicationErrors[$this->error_code][] = "HASH != hashToCheck \n ".$this->PPBA["HASH"]."\n$hashToCheck";	//BAD
					}
				}
			} else {
				//	$this->error_code = 5;	
				//	$this->applicationErrors[$this->error_code][] = "VAR orderedDataToPlainTextValArr IS EMPTY ";	//BAD
			}
		}
		
		
/*echo "<textarea>";	
print_r($this->applicationErrors);
echo "</textarea>";	*/
		
		if ($this->error_code==1) { //deri tani nuk kemi asnje gabim
				
				$controlIfPaymentResponseIsSaved	= "SELECT orderid
														FROM ".ESHOP_STORE_DB.".eshop__checkout_cart WHERE orderKey = '".$this->PPBA["orderKey"]."'"; 
				$resp_ResponseIsSaved	= WebApp::execQuery($controlIfPaymentResponseIsSaved);					
				
/*echo "<textarea>";	
print_r($resp_ResponseIsSaved);
echo "</textarea>";*/						
				
				
				
				IF (!$resp_ResponseIsSaved->EOF())  {
					$this->error_code = 100;	
					$this->applicationErrors[$this->error_code][] = "OK";	
				} else {
					$controlIfOrderExist		= "SELECT orderid, coalesce(TranCodeAllowedTimes,0) as AllowedErrorTimes, payer_id
													 FROM ".ESHOP_STORE_DB.".eshop__basket WHERE orderKey = '".$this->PPBA["orderKey"]."'"; 
					$resp_controlIfOrderExist	= WebApp::execQuery($controlIfOrderExist);		
					
					
					
/*echo "<textarea>";	
print_r($resp_controlIfOrderExist);
echo "</textarea>";	*/				
					
					
					
					IF (!$resp_controlIfOrderExist->EOF())  {
							
							
							$this->payer_id			 = $resp_controlIfOrderExist->Field("payer_id");
							$this->orderid			 = $resp_controlIfOrderExist->Field("orderid");
							$this->AllowedErrorTimes = $resp_controlIfOrderExist->Field("AllowedErrorTimes");
							//nese pagesa ka rezultuar e sukseshme siguro qe ka gjendje per porosine	
							$this->articleNotInStock = 0;	
							$this->totalPrice 		 = 0;	
							$this->ariclesInBasket	 = Array();	
							
							if ($this->PPBA["TranCode"] == "00" || $this->PPBA["TranCode"] == "99") { //DEBUG MODE
									//--------------------------------------------koment1--------------------------------------------
										$insertCart = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart (id_satelit, orderid,  uniqueid, orderKey,  payer_id,  payer_first_name, payer_last_name, payer_title, payer_email, payer_contact_phone, bll_name, bll_email, bll_phone, bll_address_street, bll_address_street_ext, bll_address_zip, bll_address_city, bll_address_state, bll_address_country, delivery_same_as_bill, dlv_name, dlv_email, dlv_phone, dlv_address_street, dlv_address_street_ext, dlv_address_zip, dlv_address_city, dlv_address_state, dlv_address_country, dlv_address_country_id, PriceDelivery, PriceProducts, TotalAmount,	callback_email,                       callback_TranCode,                      callback_ApprovalCode,                      callback_XID,                       callback_Rrn,                       callback_ProxyPan, 					total_price, 							PurchaseTime,currency_id,exchange_rate,LekPriceDelivery,LekPriceProducts,LekTotalAmount)
																						  SELECT id_satelit, orderid,  uniqueid, orderKey,  payer_id,  payer_first_name, payer_last_name, payer_title, payer_email, payer_contact_phone, bll_name, bll_email, bll_phone, bll_address_street, bll_address_street_ext, bll_address_zip, bll_address_city, bll_address_state, bll_address_country, delivery_same_as_bill, dlv_name, dlv_email, dlv_phone, dlv_address_street, dlv_address_street_ext, dlv_address_zip, dlv_address_city, dlv_address_state, dlv_address_country, dlv_address_country_id, PriceDelivery, PriceProducts, TotalAmount,	'".$this->PPBA["callback_email"]."',  '".$this->PPBA["callback_TranCode"]."', '".$this->PPBA["callback_ApprovalCode"]."', '".$this->PPBA["callback_XID"]."',  '".$this->PPBA["callback_Rrn"]."',  '".$this->PPBA["callback_ProxyPan"]."',  '".$this->PPBA["TotalAmount"]."',  NOW(),currency_id,exchange_rate,LekPriceDelivery,LekPriceProducts,LekTotalAmount
																		  FROM ".ESHOP_STORE_DB.".eshop__basket 
																		 WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCart);		
										$updateStepRelatedData= "
											UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
											   SET transaction_step = 'succes'
											 WHERE orderID 		    = '".$this->orderid."'";	

										$ts = WebApp::execQuery($updateStepRelatedData);
										//if (!mysql_query($updateStepRelatedData)) 	$this->applicationErrors[] = mysql_error();
										$this->confirmationStatus     = "OK";
										$this->confirmationErrorLabel = "OK";

										//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere
										$updateStepRelatedData= "
											UPDATE ".ESHOP_STORE_DB.".eshop__basket
											   SET order_type = 'order',
												   order_step = 'succes_transaction',
												   transaction_step = 'from_payment_gateway'
											 WHERE orderID    = '".$this->orderid."'";

										$ts = WebApp::execQuery($updateStepRelatedData);
										
								
										
										//KETU MUND TI FSHIJME TE GJITHE ARTIKUJT E PROCESUAR
										
										$insertCartART = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_articles (id_satelit,orderid,item_type,id_item,quantity,price,Lekprice,uniqueid,id_promocion,description_item,code_item)
																						  SELECT id_satelit,orderid,item_type,id_item,quantity,price,Lekprice,uniqueid,id_promocion,description_item,code_item
																		  FROM ".ESHOP_STORE_DB.".eshop__basket_articles 
																		 WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCartART);											


										$insertCartDeliveryTarif = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_extra_tarif (id_satelit,orderid,id_tarif,id_subTarif,price_type,delivery_to_type,tarif_type,tarif_type_extended,user_selection,price,Lekprice,description_row,description_delivery_time)
																						  SELECT id_satelit,orderid,id_tarif,id_subTarif,price_type,delivery_to_type,tarif_type,tarif_type_extended,user_selection,price,Lekprice,description_row,description_delivery_time
																		  FROM ".ESHOP_STORE_DB.".eshop__basket_extra_tarif 
																		 WHERE orderID = '".$this->orderid."' AND user_selection = 'y'";
										$ts = WebApp::execQuery($insertCartDeliveryTarif);		
								
										//if (!mysql_query($updateStepRelatedData))	$this->applicationErrors[] = mysql_error();
										//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere

										//--------------------------------------------koment3--------------------------------------------
							
										$this->sendNotification($this->orderid,"PreOrder");
										
										
										
										
										$insertCmeElearning = "
		REPLACE INTO z_my_cme_learning (	content_id,	UserId,					ci_type,		TotalAmount,	orderKey,						PurchaseTime)
								SELECT 	id_item,	'".$this->payer_id."',	item_type,		price,			'".$this->PPBA["orderKey"]."',	NOW()
								  FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
							     WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCmeElearning);	
										
										



										$insertCme_user_progress = "
		REPLACE INTO z_EccE_user_progress (	item_id,	user_id, date_owened)
								SELECT 	id_item, '".$this->payer_id."', NOW()
								  FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
							     WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCme_user_progress);	

/*

z_my_cme_learning
CREATE TABLE `z_EccE_user_progress` (

  `lecture_id` int(11) NOT NULL DEFAULT '0',
  `user_id` smallint(5) NOT NULL DEFAULT '0',
  `progress_state` enum('new','in_progress','finished') COLLATE utf8_unicode_ci DEFAULT 'new',
  `results_state` enum('empty','passed','not_passed') COLLATE utf8_unicode_ci DEFAULT 'empty',

  `lastContentId` int(11) NOT NULL,

  `cme_points_received` tinyint(4) DEFAULT NULL,
  `token` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,

  `date_owened` date NOT NULL DEFAULT '0000-00-00',
  `date_begin` date NOT NULL DEFAULT '0000-00-00',
  `date_end` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`lecture_id`,`user_id`)
) ENGINE=MyISAM;
*/									
										
										
										
										
							} else { //ka nje transaction me error qe nga raifasen, nuk eshte bere pagesa per aresye qe e shpjegon kodi i errorit

										$this->getError_ISO8583();
										
										/*echo $this->PPBA["TranCode"]."<textarea>";
										print_r($this->Error_ISO8583[$this->PPBA["TranCode"]]);
										echo "</textarea>";

										echo "PPBA<textarea>";
										print_r($this->PPBA);
										echo "</textarea>";*/
										
							//			ErrMsg=Expired card.

										if ($this->AllowedErrorTimes<=AllowedBktErrorTimes) {
										
											//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere
											$updateStepRelatedData= "
												UPDATE ".ESHOP_STORE_DB.".eshop__basket
												   SET 
													   TranCode   				= '".$this->PPBA["TranCode"]."',
													     TranCodeDescription   	= '".mysql_real_escape_string($this->PPBA["ErrMsg"])."',
													   TranCodeAllowedTimes =TranCodeAllowedTimes+1
												  WHERE orderID   = '".$this->orderid."'";
											echo 	 $updateStepRelatedData; 
											WebApp::execQuery($updateStepRelatedData);
										} else {
										
											//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere
											$updateStepRelatedData= "
												UPDATE ".ESHOP_STORE_DB.".eshop__basket
												   SET order_step = 'failed_transaction', 
													   TranCode   				= '".$this->PPBA["TranCode"]."',
													   TranCodeDescription   	= '".mysql_real_escape_string($this->PPBA["ErrMsg"])."',
													   TranCodeAllowedTimes =TranCodeAllowedTimes+1
												  WHERE orderID   = '".$this->orderid."'";
											echo 	 $updateStepRelatedData;
											WebApp::execQuery($updateStepRelatedData);	
										}
										
								echo		$InsertLogSql = "INSERT INTO ".ESHOP_STORE_DB.".eshop__basket_declined 
														(	id_satelit,			orderid,				orderKey,						TranCode,						TranCodeDescription) 
												VALUES (	'".SATELIT_ID."',	'".$this->orderid."',	'".$this->PPBA["orderKey"]."',	'".$this->PPBA["TranCode"]."',	'".mysql_real_escape_string($this->PPBA["ErrMsg"])."')";
										WebApp::execQuery($InsertLogSql);												
										
							}



					} else { //nuk u gjet orderi ne sistemin tone

						$this->confirmationStatus		= "Error-orderi nuk u gjet ne db";
						$this->confirmationStatus		= "NotOK";
						$this->confirmationErrorLabel	= "Error-orderi nuk u gjet ne db";
						$this->error_code				= 10;	
						$this->applicationErrors[$this->error_code][] = "Error-orderi nuk u gjet ne db";
					}
				}
		}	
		
/*		
echo "<textarea>";	
print_r($this->applicationErrors);
echo "</textarea>";			*/
		
		
		
		

		if ($this->error_code != 100) {
			$InsertLogSql = "INSERT INTO ".ESHOP_STORE_DB.".eshop__checkout_log_bkt 
							(
							 id_satelit,

							 orderKey,					
							 TotalAmount,					
							 Currency,							
							 PurchaseTime,	
							 XID,
							 HTTP_ORIGIN,
							 EmailPayementGateway,
							 ClientIp,
							 ProxyPan,
							 eci,
							 cavv,
							 md,
							 SchemaID,
							 mdStatus,
							 iReqCode,
							 vendorCode,
							 AuthCode,
							 PaymentStatus,
							 HostRefNum,
							 TransId,
							 post_variables,
							 get_variables,
							 server_variables
							 ) 
			VALUES 
			(			'".SATELIT_ID."',

						'".$this->PPBA["orderKey"]."',
						'".$this->PPBA["TotalAmount"]."', 
						'".$this->PPBA["Currency"]."',
						'".$this->PPBA["PurchaseTime"]."', 
						'".$this->PPBA["XID"]."', 
						'".$this->PPBA["HTTP_ORIGIN"]."', 
						'".$this->PPBA["EmailPayementGateway"]."',
						'".$this->PPBA["ClientIp"]."', 
						'".$this->PPBA["ProxyPan"]."', 
						'".$this->PPBA['eci']."', 
						'".$this->PPBA['cavv']."',	 
						'".$this->PPBA['md']."', 
						'".$this->PPBA['SchemaID']."', 
						'".$this->PPBA['mdStatus']."', 
						'".$this->PPBA['iReqCode']."',
						'".$this->PPBA['vendorCode']."', 
						'".$this->PPBA['AuthCode']."',
						'".$this->PPBA['PaymentStatus']."', 
						'".$this->PPBA['HostRefNum']."', 
						'".$this->PPBA['TransId']."', 
						'".mysql_real_escape_string($post_variables)."',
						'".mysql_real_escape_string($get_variables)."', 
						'".mysql_real_escape_string($server_variables)."'
						)";

			WebApp::execQuery($InsertLogSql);
		}
	}



	function controllCallBackFromPagOnline()
	{
	
		global $requestVarLog;
		
				$this->errorCodeBkt = "error";

				$this->orderCheckoutRelatedData = array();
				$this->PPBA = array();
				$this->PPBA["orderKey"] = $_POST["ReturnOid"];

				//ketu do shtohet dhe uni qe ka ardhur ne where
				$controlIfOrderExist = "SELECT orderid,uniqueid,
											   transaction_step,transaction_description,orderKey,

											   payer_id,payer_first_name,payer_last_name,payer_title,payer_email,payer_contact_phone,

											   bll_address_street,bll_address_street_ext,bll_address_zip,bll_address_city,bll_address_country,

											   delivery_same_as_bill,dlv_address_street,dlv_address_street_ext,dlv_address_zip,dlv_address_city,dlv_address_country_id,dlv_address_country,total_price,

											   callback_email,callback_TranCode,callback_ApprovalCode,callback_XID,callback_Rrn,callback_ProxyPan

										  FROM ".ESHOP_STORE_DB.".eshop__checkout_cart
										 WHERE orderKey = '".$this->PPBA["orderKey"]."'";

				//e gjitha kjo duhet te thirret edhe nga procesim ne callback url
				$resp_controlIfOrderExist = WebApp::execQuery($controlIfOrderExist);

				if (!$resp_controlIfOrderExist->EOF()) { //perdor orderid e paperfunduar

						$this->errorCodeBkt = "no_error";
						
						
						$this->orderid 											= $resp_controlIfOrderExist->Field("orderid");

						$this->PPBA["transaction_step"] 						= $resp_controlIfOrderExist->Field("transaction_step");
						$this->PPBA["transaction_description"] 					= $resp_controlIfOrderExist->Field("transaction_description");

						$this->PPBA["callback_email"] 							= $resp_controlIfOrderExist->Field("callback_email");
						$this->PPBA["callback_TranCode"] 						= $resp_controlIfOrderExist->Field("callback_TranCode");
						$this->PPBA["callback_ApprovalCode"] 					= $resp_controlIfOrderExist->Field("callback_ApprovalCode");
						$this->PPBA["callback_XID"] 							= $resp_controlIfOrderExist->Field("callback_XID");
						$this->PPBA["callback_Rrn"] 							= $resp_controlIfOrderExist->Field("callback_Rrn");
						$this->PPBA["callback_ProxyPan"] 						= $resp_controlIfOrderExist->Field("callback_ProxyPan");

						$this->orderCheckoutRelatedData["payer_first_name"] 		= $resp_controlIfOrderExist->Field("payer_first_name");
						$this->orderCheckoutRelatedData["payer_last_name"] 			= $resp_controlIfOrderExist->Field("payer_last_name");
						$this->orderCheckoutRelatedData["payer_title"] 				= $resp_controlIfOrderExist->Field("payer_title");
						$this->orderCheckoutRelatedData["payer_email"] 				= $resp_controlIfOrderExist->Field("payer_email");
						$this->orderCheckoutRelatedData["payer_contact_phone"] 		= $resp_controlIfOrderExist->Field("payer_contact_phone");

						$this->orderCheckoutRelatedData["bll_address_street"] 		= $resp_controlIfOrderExist->Field("bll_address_street");
						$this->orderCheckoutRelatedData["bll_address_street_ext"] 	= $resp_controlIfOrderExist->Field("bll_address_street_ext");
						$this->orderCheckoutRelatedData["bll_address_zip"] 			= $resp_controlIfOrderExist->Field("bll_address_zip");
						$this->orderCheckoutRelatedData["bll_address_city"] 		= $resp_controlIfOrderExist->Field("bll_address_city");
						$this->orderCheckoutRelatedData["bll_address_country"] 		= $resp_controlIfOrderExist->Field("bll_address_country");

						
							$billing_info["data"] = array();

								$billing_info["data"][0]["full_name"] 			= $resp_controlIfOrderExist->Field("bll_name");
								$billing_info["data"][0]["address_phone"] 		= $resp_controlIfOrderExist->Field("bll_email");
								$billing_info["data"][0]["address_email"] 		= $resp_controlIfOrderExist->Field("bll_phone");	

								$billing_info["data"][0]["address_street"] 		= $resp_controlIfOrderExist->Field("bll_address_street");
								$billing_info["data"][0]["address_zip"] 		= $resp_controlIfOrderExist->Field("bll_address_zip");
								$billing_info["data"][0]["address_city"] 		= $resp_controlIfOrderExist->Field("bll_address_city");

								$billing_info["data"][0]["address_state"]		= $resp_controlIfOrderExist->Field("bll_address_state");
								$billing_info["data"][0]["address_country"] 		= $resp_controlIfOrderExist->Field("bll_address_country");



								$billing_info["AllRecs"] = count($billing_info["data"]);	
								WebApp::addVar("billing_info_Grid", $billing_info);	
								WebApp::addVar("billing_info_AllRecs", "".$billing_info["AllRecs"]);	

							$shipping_info["data"] = array();
								$shipping_info["data"][0]["full_name"] 			= $resp_controlIfOrderExist->Field("dlv_name");
								$shipping_info["data"][0]["address_phone"] 		= $resp_controlIfOrderExist->Field("dlv_phone");
								$shipping_info["data"][0]["address_email"] 		= $resp_controlIfOrderExist->Field("dlv_email");	

								$shipping_info["data"][0]["address_street"] 	= $resp_controlIfOrderExist->Field("dlv_address_street");
								$shipping_info["data"][0]["address_zip"] 		= $resp_controlIfOrderExist->Field("dlv_address_zip");
								$shipping_info["data"][0]["address_city"] 		= $resp_controlIfOrderExist->Field("dlv_address_city");

								$shipping_info["data"][0]["address_state"]		= $resp_controlIfOrderExist->Field("dlv_address_state");
								$shipping_info["data"][0]["address_country"] 	= $resp_controlIfOrderExist->Field("dlv_address_country");						

								$this->basketInfo["deliveryCountryId"]  					= $resp_controlIfOrderExist->Field("dlv_address_country_id");

								$shipping_info["AllRecs"] = count($shipping_info["data"]);	
								WebApp::addVar("shipping_info_Grid", $shipping_info);			
								WebApp::addVar("shipping_info_AllRecs", "".$shipping_info["AllRecs"]);			
						
						
								$this->orderCheckoutRelatedData["delivery_same_as_bill"] = "";

								if ($resp_controlIfOrderExist->Field("delivery_same_as_bill")=='y') 
								{
										$this->orderCheckoutRelatedData["delivery_same_as_bill"] = "same";
								}

								if ($resp_controlIfOrderExist->Field("delivery_same_as_bill")=='n') 
								{
										$this->orderCheckoutRelatedData["delivery_same_as_bill"] = "other";
								}

								$this->orderCheckoutRelatedData["dlv_address_street"] 		= $resp_controlIfOrderExist->Field("dlv_address_street");
								$this->orderCheckoutRelatedData["dlv_address_street_ext"]	 	= $resp_controlIfOrderExist->Field("dlv_address_street_ext");
								$this->orderCheckoutRelatedData["dlv_address_zip"] 			= $resp_controlIfOrderExist->Field("dlv_address_zip");
								$this->orderCheckoutRelatedData["dlv_address_city"] 			= $resp_controlIfOrderExist->Field("dlv_address_city");
								$this->orderCheckoutRelatedData["dlv_address_country"] 		= $resp_controlIfOrderExist->Field("dlv_address_country");	
								$this->orderCheckoutRelatedData["dlv_address_country_id"] 	= $resp_controlIfOrderExist->Field("dlv_address_country_id");	
						
					while (list($keyV,$dataV)=each($this->PPBA)) 
					   {
						WebApp::addVar("$keyV","".$dataV."");
					   }

					while (list($keyV,$dataV)=each($this->orderCheckoutRelatedData)) 
					   {
						WebApp::addVar("$keyV","".$dataV."");
					   }				
				} 
	}


function genpassword($length) { 
    srand((double)microtime()*10000); 
    $vowels = array("a", "e", "i", "o", "u","A", "E", "I", "O", "U"); 
    $cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr", 
    "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl",
    "B", "C", "D", "G", "H", "J", "K", "L", "M", "N", "P", "R", "S", "T", "U", "V", "W", "TR", 
    "CR", "BR", "FR", "TH", "DR", "CH", "PH", "WR", "ST", "SP", "SW", "PR", "SL", "CL"
    ); 

    $num_vowels = count($vowels); 
    $num_cons = count($cons); 
    for($i = 0; $i < $length; $i++){ 
        $password .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)]; 
    } 
    return substr($password, 0, $length); 
} 
function genpassword_nr($length) { 
    srand((double)microtime()*10000); 
    $numbers = array("0", "1", "2", "3", "4","5", "6", "7", "8", "9"); 
    $num_numbers = count($numbers); 
    for($i = 0; $i < $length; $i++){ 
        $password .= $numbers[rand(0, $num_numbers - 1)]; 
    } 
    return substr($password, 0, $length); 
} 




	function sendNotification($orderid,$notif_step_type) 
	{
			//event_id	event_name
			//1				Porosi e re - orderi u be gati per ne bkt
			//2				Porosi e re - paguar me sukses
			//3				Porosi e re - konfirmim i gjendjes
			//4				Porosi e re - ska gjendje
			//5				Porosi e re - gjendje e pjesshme kontakto me userin
			//6				Porosi u nis - dergimi i tracking code

			//notif_step_type	PrePreOrder|PreOrder
			define("ESHOP_EMAIL_FROM",				"jonidacuko@arkit.info"); 
			define("ESHOP_EMAIL_FROM_LABEL",		"OOT - eShop"); 
			define("ESHOP_EMAIL_MNG",				"jonidacuko@arkit.ch");
			
			$frmstr    = ESHOP_EMAIL_FROM_LABEL."<".ESHOP_EMAIL_FROM.">";
			$mail_To      = ESHOP_EMAIL_MNG;
			$mail_Subject = "eShop - OOT";	

			$sentEmail = "y";
			
			$getNot = "SELECT notif_subject,notif_message
						  FROM ".ESHOP_STORE_DB.".eshop__checkout_notification
						 WHERE id_satelit='".SATELIT_ID."' 
						   AND orderid='".$orderid."'
						   AND notif_step_type='".$notif_step_type."'
						   AND is_send = 'n'";
			$rsNot = WebApp::execQuery($getNot);

			if (!$rsNot->EOF()) { //perdor orderid e paperfunduar

				$mail_Subject 		= $rsNot->Field("notif_subject");
				$message_content 	= $rsNot->Field("notif_message");	
				
				if ($notif_step_type=="PrePreOrder") {
					$invoice_step_type = "InvoiceToUser";
				
				} elseif ($notif_step_type=="PostOrder"  || $notif_step_type=="PreOrder") {
				
					$invoice_step_type = "InvoiceToUser";	
						
					$getEm = "SELECT coalesce(bll_email, ".ESHOP_EMAIL_MNG.") as bll_email_not
								  FROM ".ESHOP_STORE_DB.".eshop__basket
								 WHERE id_satelit='".SATELIT_ID."' 
								   AND orderid='".$orderid."'";
					$rsNotEm = WebApp::execQuery($getEm);
					if (!$rsNotEm->EOF()) { //perdor orderid e paperfunduar
						$mail_To 	= $rsNotEm->Field("bll_email_not");	
					} else {
						$sentEmail = "n";
					}
				}
				// dergohet emaili notifikimit librari albanias per nje order potencial qe eshte bere gati per tu paguar
				if ($sentEmail == "y") {
				
						$contentMail = new htmlMimeMail();
						$contentMail->setTextCharset(APP_ENCODING);
						$contentMail->setHtmlCharset(APP_ENCODING);
						$contentMail->setHeadCharset(APP_ENCODING);
						$contentMail->setFrom($frmstr);
						$contentMail->setSubject($mail_Subject);     
						$contentMail->setHtml($message_content, $text, './');		
						
                        include ADODB_PATH."adodb.inc.php";
                        $conn = &ADONewConnection('mysql');
                        $conn->PConnect(DBHOST, DBUSER, DBPASS, DBNAME);	
                      //  $conn->debug=true;
						$recordSet = $conn->Execute('SELECT invoice_data, invoice_name, invoice_type, invoice_size FROM '.ESHOP_STORE_DB.'.eshop__checkout_invoice WHERE id_satelit="'.SATELIT_ID.'" AND orderid ="'.$orderid.'" AND invoice_step_type = "'.$invoice_step_type.'"');
						IF ($recordSet) {
							$file   = $recordSet->fields[0];
							$nameF   = $recordSet->fields[1];
							$c_type = $recordSet->fields[2];

							INI_SET("memory_limit", "152M");
							
							//trigger_error("$nameF $c_type ");
							$contentMail->addAttachment($file, $nameF, $c_type);
						}
                           
						$mailresult = $contentMail->send(array($mail_To));
						
						If ($mailresult) $is_send_succesfully = "y";
						else			 $is_send_succesfully = "g";
						
						$updateStepRelatedData= "
								UPDATE ".ESHOP_STORE_DB.".eshop__checkout_notification
								   SET is_send = '".$is_send_succesfully."',
								   	   sent_from = '".$frmstr."',
								   	   sent_to = '".$mail_To."',
								   	   sent_cc = '',
								   	   sent_bcc = '',
								   	   
									   date_send = now(),
									   record_userid = '".$this->userSystemID."'
								 WHERE orderid='".$orderid."'
							   AND notif_step_type='".$notif_step_type."'";
						WebApp::execQuery($updateStepRelatedData);						
				}
		}
	}
	
	
		//		$tmpData = eShopOrder::getTransatedPrice($this->preferences["sessionMonedhe"],$this->dataBIstock[$content_id]["Lek_list_price"]);
	
	
	function getTransatedPrice  ($monedhIDtoTranslate, $priceToTranslate) {
		
		$returnedData = array();
		/*$returnedData["existE"] = "no";
		
		$getLastExchange = "SELECT LastDate, date_format(LastDate,'%d.%m.%Y') as LastDate1, LastTime
							  FROM ".ESHOP_STORE_DB.".z_kursi_kembimit_lastUpdate
						  ORDER BY LastDate desc
							 LIMIT 0,1";

		$rsLastExchange = WebApp::execQuery($getLastExchange);	
		$ind = 0;
		if (!$rsLastExchange->EOF()) { //perdor orderid e paperfunduar
		
			$LastDateId 	= $rsLastExchange->Field("LastDate");	
			
			$getlastexchangeV = "SELECT vlera, currency_name
								   FROM ".ESHOP_STORE_DB.".z_kursi_kembimit
								   JOIN eshop_currency 
									 ON eshop_currency.currency_id = z_kursi_kembimit.monedhID 	 
									AND z_kursi_kembimit_monedha.active = 'y'
								  WHERE LastDate = '".$LastDateId."'
								    AND z_kursi_kembimit.refMonedhID = 5
								    AND z_kursi_kembimit.monedhID    = '".$monedhIDtoTranslate."'
								    AND vlera > 0
							   ORDER BY currency_name";
							   
			$rs_info = WebApp::execQuery($getlastexchangeV);

			if (!$rs_info->EOF()) {

				$returnedData["existE"]			= "yes";
				$returnedData["exchangeVal"]	= $rs_info->Field("vlera");
				$returnedData["origjinalVal"]	= $priceToTranslate;
				
				$returnedData["translatedVal"]	= round($priceToTranslate/$returnedData["exchangeVal"],2);
				$returnedData["currency_name"]		= $rs_info->Field("currency_name");
			}		
		}*/
		
		return $returnedData;

	}	
	
	function getLastExchangeRate  () {

		/*$getLastExchange = "SELECT LastDate, date_format(LastDate,'%d.%m.%Y') as LastDate1, LastTime
							  FROM ".ESHOP_STORE_DB.".z_kursi_kembimit_lastUpdate
						  ORDER BY LastDate desc
							 LIMIT 0,1";

		$rsLastExchange = WebApp::execQuery($getLastExchange);	
		$ind = 0;
		
		
			$getRefMonedhe = "SELECT  monedhLng1, monedhLng2, currency_name, monedhID, currency_code
								   FROM eshop_currency 
								  WHERE active = 'y' and is_reference = 'y'";
							   
			$rsR = WebApp::execQuery($getRefMonedhe);		
			while (!$rsR->EOF()) {

				$this->exchange["data"][$ind]["currency_code"] 		= $rsR->Field("currency_code");
				$this->exchange["data"][$ind]["monedhID"] 			= $rsR->Field("monedhID");
				$this->exchange["data"][$ind]["refMonedhID"] 		= $rsR->Field("monedhID");
				$this->exchange["data"][$ind]["currency_name"] 			= $rsR->Field("monedhLng1");
				$this->exchange["data"][$ind]["monedhLng1"] 		= $rsR->Field("monedhLng1");
				$this->exchange["data"][$ind]["monedhLng2"] 		= $rsR->Field("monedhLng2");
				$this->exchange["data"][$ind]["vlera"] 				= 1;
				
				$this->exchange["active"][$rsR->Field("monedhID")] = $this->exchange["data"][$ind]["currency_name"];
				$this->exchange["rate"][$rsR->Field("monedhID")] = 1;
				$this->exchange["code"][$rsR->Field("monedhID")] = "008";

				$ind++;
				$rsR->MoveNext();
			}								   
					   
	
		
		if (!$rsLastExchange->EOF()) { //perdor orderid e paperfunduar

			$this->exchange["info"]["existE"] 		= "yes";	
			$this->exchange["info"]["LastDateId"] 	= $rsLastExchange->Field("LastDate");	
			$this->exchange["info"]["LastDate"] 	= $rsLastExchange->Field("LastDate1");	
			$this->exchange["info"]["LastTime"] 	= $rsLastExchange->Field("LastTime");
			
			
			$getlastexchangeV = "SELECT  vlera, monedhLng1, monedhLng2, currency_name, z_kursi_kembimit.refMonedhID,z_kursi_kembimit.monedhID, currency_code
								   FROM ".ESHOP_STORE_DB.".z_kursi_kembimit
								   JOIN eshop_currency 
									 ON eshop_currency.currency_id = z_kursi_kembimit.monedhID 	 
									AND z_kursi_kembimit_monedha.active = 'y'
								  WHERE LastDate = '".$this->exchange["info"]["LastDateId"]."'
							   ORDER BY currency_name";
							   
			$rs_info = WebApp::execQuery($getlastexchangeV);		
			while (!$rs_info->EOF()) {

				$this->exchange["data"][$ind]["currency_code"] 		= $rs_info->Field("currency_code");
				$this->exchange["data"][$ind]["monedhID"] 			= $rs_info->Field("monedhID");
				$this->exchange["data"][$ind]["refMonedhID"] 		= $rs_info->Field("refMonedhID");
				$this->exchange["data"][$ind]["currency_name"] 			= $rs_info->Field("currency_name");
				$this->exchange["data"][$ind]["monedhLng1"] 		= $rs_info->Field("monedhLng1");
				$this->exchange["data"][$ind]["monedhLng2"] 		= $rs_info->Field("monedhLng2");
				$this->exchange["data"][$ind]["vlera"] 				= $rs_info->Field("vlera");
				
				$this->exchange["active"][$rs_info->Field("monedhID")] = $this->exchange["data"][$ind]["currency_name"];
				$this->exchange["rate"][$rs_info->Field("monedhID")] = $this->exchange["data"][$ind]["vlera"];
				$this->exchange["code"][$rs_info->Field("monedhID")] = $this->exchange["data"][$ind]["currency_code"];

				$ind++;
				$rs_info->MoveNext();
			}

			if (isset($this->exchange["data"]) && count($this->exchange["data"])>0) {
				$tmp["data"] =$this->exchange["data"];
				$tmp["AllRecs"] = count($tmp["data"]);
				WebApp::addVar("ExchangeGrid",$tmp);
			}

		} else {
			$this->exchange["info"]["existE"] = "no";
		}


		while (list($key,$data)=each($this->exchange["info"])) {
			WebApp::addVar("Exchange_".$key,"".$data);
		}*/
	}


	function getError_ISO8583  () {

		$this->Error_ISO8583[9999]["name"] = "Non-Numeric Error";
		$this->Error_ISO8583[9999]["desc"] = "Undefined error, contact your acquirer help desk";

		$this->Error_ISO8583[9998]["name"] = "Unknown Error from Issuer.";
		$this->Error_ISO8583[9998]["desc"] = "Undefined error, contact your acquirer help desk";

		$this->Error_ISO8583[01]["name"] = "Referral - call bank for manual approval.";
		$this->Error_ISO8583[01]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[02]["name"] = "Fake Approval, but should not be used in a VPOS system, check with your bank.";
		$this->Error_ISO8583[02]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[03]["name"] = "Invalid merchant or service provider.";
		$this->Error_ISO8583[03]["desc"] = "Virtual POS might be deactivated. Contact your acquirer";

		$this->Error_ISO8583[04]["name"] = "Pick-up card.";
		$this->Error_ISO8583[04]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[05]["name"] = "Do not honour";
		$this->Error_ISO8583[05]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[06]["name"] = "Error (found only in file update responses).";
		$this->Error_ISO8583[06]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[07]["name"] = "Pick up card, special condition.";
		$this->Error_ISO8583[07]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[08]["name"] = "Fake Approval, but should not be used in a VPOS system, check with your bank.";
		$this->Error_ISO8583[08]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[11]["name"] = "Fake Approved (VIP), but should not be used in a VPOS system, check with your bank.";
		$this->Error_ISO8583[11]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[12]["name"] = "Transaction is not valid.";
		$this->Error_ISO8583[12]["desc"] = "Contact your acquirer about the transaction";

		$this->Error_ISO8583[13]["name"] = "Invalid amount.";
		$this->Error_ISO8583[13]["desc"] = "Amount is not in valid format";

		$this->Error_ISO8583[14]["name"] = "Invalid account number.";
		$this->Error_ISO8583[14]["desc"] = "Terminal number or merchant number is wrong. Contact your acquirer";

		$this->Error_ISO8583[15]["name"] = "No such issuer.";
		$this->Error_ISO8583[15]["desc"] = "Issuer is not defined";

		$this->Error_ISO8583[19]["name"] = "Reenter, try again.";
		$this->Error_ISO8583[19]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[20]["name"] = "Invalid amount.";
		$this->Error_ISO8583[20]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[21]["name"] = "Unable to back out transaction.";
		$this->Error_ISO8583[21]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[25]["name"] = "Unable to locate record on file.";
		$this->Error_ISO8583[25]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[28]["name"] = "Original is denied.";
		$this->Error_ISO8583[28]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[29]["name"] = "Original not found.";
		$this->Error_ISO8583[29]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[30]["name"] = "Format error (switch generated).";
		$this->Error_ISO8583[30]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[32]["name"] = "Referral (General).";
		$this->Error_ISO8583[32]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[33]["name"] = "Expired card, pick-up.";
		$this->Error_ISO8583[33]["desc"] = "Card is expired, acquirer reject the transaction";

		$this->Error_ISO8583[34]["name"] = "Suspected fraud, pick-up.";
		$this->Error_ISO8583[34]["desc"] = "Suspected fraud, acquirer reject the transaction";

		$this->Error_ISO8583[36]["name"] = "Restricted card, pick-up.";
		$this->Error_ISO8583[36]["desc"] = "Card owner can contact his/her issuer for detailed information";

		$this->Error_ISO8583[37]["name"] = "Pick up card. Issuer wants card returned.";
		$this->Error_ISO8583[37]["desc"] = "Card is stolen, card owner must return the card to his acquirer";

		$this->Error_ISO8583[38]["name"] = "Allowable PIN tries exceeded, pick-up.";
		$this->Error_ISO8583[38]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[41]["name"] = "Lost card, Pick-up.";
		$this->Error_ISO8583[43]["desc"] = "Card is reported as stolen, card owner cannot use this card";

		$this->Error_ISO8583[43]["name"] = "Stolen card, pick-up.";
		$this->Error_ISO8583[43]["desc"] = "Card is reported as stolen, card owner cannot use this card";

		$this->Error_ISO8583[51]["name"] = "Insufficient funds.";
		$this->Error_ISO8583[51]["desc"] = "Card limit is not sufficient";

		$this->Error_ISO8583[52]["name"] = "No checking account.";
		$this->Error_ISO8583[52]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[53]["name"] = "No savings account.";
		$this->Error_ISO8583[53]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[54]["name"] = "Expired card.";
		$this->Error_ISO8583[54]["desc"] = "Card is expired, card owner cannot use this card";

		$this->Error_ISO8583[55]["name"] = "Incorrect PIN.";
		$this->Error_ISO8583[55]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[56]["name"] = "No card record.";
		$this->Error_ISO8583[56]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[57]["name"] = "Transaction not permitted to cardholder.";
		$this->Error_ISO8583[57]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[58]["name"] = "Transaction not permitted to terminal.";
		$this->Error_ISO8583[58]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[61]["name"] = "Exceeds withdrawal amount limit.";
		$this->Error_ISO8583[61]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[62]["name"] = "Restricted card.";
		$this->Error_ISO8583[62]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[63]["name"] = "Security violation";
		$this->Error_ISO8583[63]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[65]["name"] = "Activity limit exceeded.";
		$this->Error_ISO8583[65]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[75]["name"] = "Allowable number of PIN tries exceeded.";
		$this->Error_ISO8583[75]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[76]["name"] = "Key synchronization error.";
		$this->Error_ISO8583[76]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[77]["name"] = "Inconsistent data.";
		$this->Error_ISO8583[77]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[80]["name"] = "Date is not valid.";
		$this->Error_ISO8583[80]["desc"] = "Card owner should check card details";

		$this->Error_ISO8583[81]["name"] = "Encryption Error.";
		$this->Error_ISO8583[81]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[82]["name"] = "CVV Failure or CVV Value supplied is not valid.";
		$this->Error_ISO8583[82]["desc"] = "Card owner should check card details";

		$this->Error_ISO8583[83]["name"] = "Cannot verify PIN.";
		$this->Error_ISO8583[83]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[85]["name"] = "Declined (General).";
		$this->Error_ISO8583[85]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[91]["name"] = "Issuer or switch is inoperative.";
		$this->Error_ISO8583[91]["desc"] = "Cannot communicate with the host, Contact your acquirer help desk";

		$this->Error_ISO8583[92]["name"] = "Timeout, reversal is trying.";
		$this->Error_ISO8583[92]["desc"] = "Cannot communicate with the host, Contact your acquirer help desk";

		$this->Error_ISO8583[93]["name"] = "Violation, cannot complete (installment, loyalty).";
		$this->Error_ISO8583[93]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[96]["name"] = "System malfunction.";
		$this->Error_ISO8583[96]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583[98]["name"] = "Duplicate Reversal.";
		$this->Error_ISO8583[98]["desc"] = "Contact your acquirer help desk";

		$this->Error_ISO8583["YK"]["name"] = "Card in black list.";
		$this->Error_ISO8583["YK"]["desc"] = "Contact your acquirer help desk";
		
		$this->Error_ISO8583[99]["name"] = "General Error";
		$this->Error_ISO8583[99]["desc"] = "General Error";
		
			

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function callBackFictivePayementFrom($orderKey)
	{
		
		global $vrBkPost,$session;

		
		$this->error_code=1;
		$this->PPBA["orderid"] = $orderid;
		$this->PPBA["orderKey"] = $orderKey;
		$this->PPBA["TranCode"]	= "00";
		
		require_once(INC_PATH.'oot.session.base.class.php');	
		$sessUserObj = new eLearningUserPlatform();
		$sessUserObj->initCiReference();
		$sessUserObj->isSetGlobalObj="yes";
		
		$sessUserObj->getOverallProgrammeTree(); 
		
		if ($this->error_code==1) { //deri tani nuk kemi asnje gabim
				
				$controlIfPaymentResponseIsSaved	= "SELECT orderid
														FROM ".ESHOP_STORE_DB.".eshop__checkout_cart WHERE orderKey = '".$this->PPBA["orderKey"]."'"; 
				$resp_ResponseIsSaved	= WebApp::execQuery($controlIfPaymentResponseIsSaved);					
				

				
				
				IF (!$resp_ResponseIsSaved->EOF())  {
					$this->error_code = 100;	
					$this->applicationErrors[$this->error_code][] = "OK";	
				} else {
					$controlIfOrderExist		= "SELECT orderid, coalesce(TranCodeAllowedTimes,0) as AllowedErrorTimes, payer_id
													 FROM ".ESHOP_STORE_DB.".eshop__basket WHERE orderKey = '".$this->PPBA["orderKey"]."'"; 
					$resp_controlIfOrderExist	= WebApp::execQuery($controlIfOrderExist);		
					
					
					

					
					
					IF (!$resp_controlIfOrderExist->EOF())  {
							
							
							$this->payer_id			 = $resp_controlIfOrderExist->Field("payer_id");
							$this->orderid			 = $resp_controlIfOrderExist->Field("orderid");
							$this->AllowedErrorTimes = $resp_controlIfOrderExist->Field("AllowedErrorTimes");
							//nese pagesa ka rezultuar e sukseshme siguro qe ka gjendje per porosine	
							$this->articleNotInStock = 0;	
							$this->totalPrice 		 = 0;	
							$this->ariclesInBasket	 = Array();	
							
							if ($this->PPBA["TranCode"] == "00" || $this->PPBA["TranCode"] == "99") { //DEBUG MODE
									//--------------------------------------------koment1--------------------------------------------
										$insertCart = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart (id_satelit, orderid,  uniqueid, orderKey,  payer_id,  payer_first_name, payer_last_name, payer_title, payer_email, payer_contact_phone, bll_name, bll_email, bll_phone, bll_address_street, bll_address_street_ext, bll_address_zip, bll_address_city, bll_address_state, bll_address_country, delivery_same_as_bill, dlv_name, dlv_email, dlv_phone, dlv_address_street, dlv_address_street_ext, dlv_address_zip, dlv_address_city, dlv_address_state, dlv_address_country, dlv_address_country_id, PriceDelivery, PriceProducts, TotalAmount,	callback_email,                       callback_TranCode,                      callback_ApprovalCode,                      callback_XID,                       callback_Rrn,                       callback_ProxyPan, 					total_price, 							PurchaseTime,currency_id,exchange_rate,LekPriceDelivery,LekPriceProducts,LekTotalAmount)
																						  SELECT id_satelit, orderid,  uniqueid, orderKey,  payer_id,  payer_first_name, payer_last_name, payer_title, payer_email, payer_contact_phone, bll_name, bll_email, bll_phone, bll_address_street, bll_address_street_ext, bll_address_zip, bll_address_city, bll_address_state, bll_address_country, delivery_same_as_bill, dlv_name, dlv_email, dlv_phone, dlv_address_street, dlv_address_street_ext, dlv_address_zip, dlv_address_city, dlv_address_state, dlv_address_country, dlv_address_country_id, PriceDelivery, PriceProducts, TotalAmount,	'".$this->PPBA["callback_email"]."',  '".$this->PPBA["callback_TranCode"]."', '".$this->PPBA["callback_ApprovalCode"]."', '".$this->PPBA["callback_XID"]."',  '".$this->PPBA["callback_Rrn"]."',  '".$this->PPBA["callback_ProxyPan"]."',  '".$this->PPBA["TotalAmount"]."',  NOW(),currency_id,exchange_rate,LekPriceDelivery,LekPriceProducts,LekTotalAmount
																		  FROM ".ESHOP_STORE_DB.".eshop__basket 
																		 WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCart);		
										$updateStepRelatedData= "
											UPDATE ".ESHOP_STORE_DB.".eshop__checkout_cart
											   SET transaction_step = 'succes'
											 WHERE orderID 		    = '".$this->orderid."'";	

										$ts = WebApp::execQuery($updateStepRelatedData);
										//if (!mysql_query($updateStepRelatedData)) 	$this->applicationErrors[] = mysql_error();
										$this->confirmationStatus     = "OK";
										$this->confirmationErrorLabel = "OK";

										//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere
										$updateStepRelatedData= "
											UPDATE ".ESHOP_STORE_DB.".eshop__basket
											   SET order_type = 'order',
												   order_step = 'succes_transaction',
												   transaction_step = 'from_payment_gateway'
											 WHERE orderID    = '".$this->orderid."'";

										$ts = WebApp::execQuery($updateStepRelatedData);
										
								
										
										//KETU MUND TI FSHIJME TE GJITHE ARTIKUJT E PROCESUAR
										
										$insertCartART = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_articles (id_satelit,orderid,item_type,id_item,quantity,price,Lekprice,uniqueid,id_promocion,description_item,code_item)
																						  SELECT id_satelit,orderid,item_type,id_item,quantity,price,Lekprice,uniqueid,id_promocion,description_item,code_item
																		  FROM ".ESHOP_STORE_DB.".eshop__basket_articles 
																		 WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCartART);	
										
										$updateBa = "
										UPDATE ".ESHOP_STORE_DB.".eshop__basket_articles 
										set uniqueid = '11111'
										where orderid = '".$this->orderid."'";
										$ts = WebApp::execQuery($updateBa);	

										/*$insertCartDeliveryTarif = "
											REPLACE INTO ".ESHOP_STORE_DB.".eshop__checkout_cart_extra_tarif (id_satelit,orderid,id_tarif,id_subTarif,price_type,delivery_to_type,tarif_type,tarif_type_extended,user_selection,price,Lekprice,description_row,description_delivery_time)
																						  SELECT id_satelit,orderid,id_tarif,id_subTarif,price_type,delivery_to_type,tarif_type,tarif_type_extended,user_selection,price,Lekprice,description_row,description_delivery_time
																		  FROM ".ESHOP_STORE_DB.".eshop__basket_extra_tarif 
																		 WHERE orderID = '".$this->orderid."' AND user_selection = 'y'";
										$ts = WebApp::execQuery($insertCartDeliveryTarif);	*/	
								
										//if (!mysql_query($updateStepRelatedData))	$this->applicationErrors[] = mysql_error();
										//ndryshohet statusi i basketit ne basket te mbaruar vetem nese transaksioni eshte komplet i suskesshem dhe useri duhet ti dergohen artikujt qe ka blere

										//--------------------------------------------koment3--------------------------------------------
							
										//$this->sendNotification($this->orderid,"PreOrder");
										
										$insertCmeElearning = "
											REPLACE INTO z_my_cme_learning (	content_id,	UserId,					ci_type,		TotalAmount,	orderKey,						PurchaseTime)
																	SELECT 		id_item,	'".$this->payer_id."',	item_type,		price,			'".$this->PPBA["orderKey"]."',	NOW()
																	  FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
																	 WHERE orderID = '".$this->orderid."'";
																
										$ts = WebApp::execQuery($insertCmeElearning);	
										
										$insertCme_user_progress = "
											REPLACE INTO z_EccE_user_progress (	item_id,item_type,	user_id, date_owened,token)
															SELECT 	id_item,	item_type, '".$this->payer_id."', NOW(), '".$this->PPBA["orderKey"]."'
															  FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_articles 
															 WHERE orderID = '".$this->orderid."'";
										$ts = WebApp::execQuery($insertCme_user_progress);																									

												$getD = "SELECT id_item, item_type FROM ".ESHOP_STORE_DB.".eshop__checkout_cart_articles WHERE orderID = '".$this->orderid."'";
												$rs_con = WebApp::execQuery($getD);
												while (!$rs_con->EOF()) {
													$content_id = $rs_con->Field("id_item");
													$item_type 	= $rs_con->Field("item_type");                   
													
													
													
													if (isset($sessUserObj->programeOverallTreeStructure["CI_TYPE"][$content_id])) {
														$item_type 	= $sessUserObj->programeOverallTreeStructure["CI_TYPE"][$content_id];
														if ($item_type=="PR") { //gjej te gjitha leksionet e programit
															$programKey = $sessUserObj->programeOverallTreeStructure["PR_coord"][$content_id];
															if (isset($sessUserObj->programeOverallTreeStructure["ELInPR"][$programKey]) && count($sessUserObj->programeOverallTreeStructure["ELInPR"][$programKey])>0) {
																while (list($id_item,$dd)=each($sessUserObj->programeOverallTreeStructure["ELInPR"][$programKey])) {					
																		$insertCmeElearningLE = "
																		REPLACE INTO z_my_cme_learning (	content_id,	UserId,					ci_type,			orderKey,						PurchaseTime,mainRecord)
																								VALUES (	".$id_item.",	'".$this->payer_id."',	'EL',					'".$this->PPBA["orderKey"]."',	NOW(),'no')";

																								 //kontrollo tipin e ci-se se blere
																		WebApp::execQuery($insertCmeElearningLE);	
																		
																		$insertCmeElearningProgressLE = "
																			REPLACE INTO z_EccE_user_progress (	item_id,item_type,	user_id, date_owened,token)
																									VALUES (	".$id_item.",	'EL',	'".$this->payer_id."',	NOW(), '".$this->PPBA["orderKey"]."')";
																		WebApp::execQuery($insertCmeElearningProgressLE);																																					
																}
															}

														} elseif ($item_type=="EC") { //gjej te gjitha leksionet e modulit
															$moduleKey = $sessUserObj->programeOverallTreeStructure["EC_coord"][$content_id];
															if (isset($sessUserObj->programeOverallTreeStructure["ELInEC"][$moduleKey]) && count($sessUserObj->programeOverallTreeStructure["ELInEC"][$moduleKey])>0) {
																while (list($id_item,$dd)=each($sessUserObj->programeOverallTreeStructure["ELInEC"][$moduleKey])) {					
																		$insertCmeElearningLE = "
																			REPLACE INTO z_my_cme_learning (	content_id,	UserId,					ci_type,		orderKey,						PurchaseTime,mainRecord)
																									VALUES (	".$id_item.",	'".$this->payer_id."',	'EL',				'".$this->PPBA["orderKey"]."',	NOW(),'no')";
																		WebApp::execQuery($insertCmeElearningLE);	
																		
																		$insertCmeElearningProgressLE = "
																			REPLACE INTO z_EccE_user_progress (	item_id,item_type,	user_id, date_owened,token)
																									VALUES (	".$id_item.",	'EL',	'".$this->payer_id."',	NOW(), '".$this->PPBA["orderKey"]."')";
																		WebApp::execQuery($insertCmeElearningProgressLE);																			
																}
															}		
														} elseif ($item_type=="EL") {
														
														
														
														}
													}                  
													$rs_con->MoveNext();
												}							     
											
										
			//remove actual basket	
			/*$delBasket = "DELETE FROM ".ESHOP_STORE_DB.".eshop__basket WHERE orderID = '".$this->orderid."' ";
			WebApp::execQuery($delBasket);																			
			$delBasketRel = "DELETE FROM ".ESHOP_STORE_DB.".eshop__basket_articles WHERE orderID = '".$this->orderid."' ";
			WebApp::execQuery($delBasketRel);	*/																		
										
										
										
							} 
					} 
				}
		}	
		
		
	}	
	
	
	
	
}
/*

    [preferences] => Array
        (
            [defaultMonedhe] => 4
            [sessionMonedhe] => 4
            [currencySelected] => CHF
        )


# behet pagesa per nje order si preAuthorization

# dergohet nje email tek personi qe duhet te paketoje librat, ndoshta do ishte mire qe per artikujt te jepet dhe stoku qe gjendet ne sistem

# personi mbasi merr emailin duhet te gjeje librat qe marrin pjese ne kete order, 
		ti paketoje 
		ti shpjere ne poste 
		dhe te konfirmoje veprimin me qellim qe pagesa e kryer ti kaloje librarise

# nese per ndonje nga librat nuk ka stok, dhe libraria nuk arrin ta gjeje librin-at qe mungojne atehere

	ose orderi cancelohet
	
	ose i dergohet nje email userit ky pyetet nese eshte i gatshem ta beje blerjen per aq artikuj sa i kane mbetur ne shport (updatohet edhe price per delivery)
			useri duhet te logohet ne sistem, dhe ose te pranoje shporten e re ose te canceloje orderin
			
*/	

?>