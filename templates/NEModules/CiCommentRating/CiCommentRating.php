<?

require_once(INCLUDE_PATH.'intServices/rating.class.php');

function CiCommentRating_onRender() {
	global $session,$event;


	$session->Vars["rIDset"] = $session->Vars["tip"];
	$session->Vars["uIDset"] = $session->Vars["ses_userid"];
	
	WebApp::addVar("uIDset",$session->Vars["uIDset"]);
	WebApp::addVar("uIDset",$session->Vars["ses_userid"]);


	if(isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="")
			WebApp::addVar("comments_idstemp",$session->Vars["idstemp"]);
	else 	WebApp::addVar("comments_idstemp","");

	WebApp::addVar("comments_ratingci",$session->Vars["contentId"]);
	WebApp::addVar("ln",eregi_replace("Lng","",$session->Vars["lang"]));
	

	$rateNeddedData = array();

	$rateNeddedData["uid"] = $session->Vars["ses_userid"];
	$rateNeddedData["tip"] = $session->Vars["tip"];
	$rateNeddedData["cid"] = $session->Vars["contentId"];


	$contribute_comments_rating = "no";

	if ($session->Vars["mode"]=="wb") {
		$obj_rating = new rating("",$session->Vars["contentId"]);
	} else {
		
		
		$CID = WebApp::getVar("CID");	
		IF (isset($CID) && ($CID!= 'undefined') && isset($session->Vars["parsed_istemp"]) && ($session->Vars["parsed_istemp"]!="")) 
		{
			$parsedIistempInfo = explode("-",$session->Vars["parsed_istemp"]);
			$idstempInfo = explode("-",$session->Vars["idstemp"]);


			if ($parsedIistempInfo[0]=="CI" AND $parsedIistempInfo[1] ==$CID && $session->Vars["contentId"] !=$CID) 
			{
				$idstemp_tmp = $session->Vars["idstemp"];
				$session->UnsetVar["parsed_istemp"];
			} 
			elseif ($parsedIistempInfo[0]=="CI" AND $parsedIistempInfo[1] ==$CID) 
			{
				$idstemp_tmp = $session->Vars["parsed_istemp"];
				$session->UnsetVar["parsed_istemp"];
			} 
			else 
			{
				$idstemp_tmp = $session->Vars["idstemp"];
				$session->UnsetVar["idstemp"];
			}	
		} 
		else 
		{
			$idstemp_tmp = $session->Vars["idstemp"];
			$CID =$session->Vars["contentId"];
		}	






		
		
		$obj_rating = new rating($idstemp_tmp,$CID);
		

		
	}
	


	

	

	

	

	$obj_rating->validateInstance();


	if ($session->Vars["thisMode"]=="_new") {
		WebApp::addVar("md","&md=111");
	} else {
	
		WebApp::addVar("md","");
	}	


	if ($obj_rating->rights[CI]=="yes") {//	$fill_rating_info = $obj_rating->fill_rating_info();

		
		if (isset($obj_rating->nemProp["headline"]) && $obj_rating->nemProp["headline"]!="") 
				WebApp::addVar("__headline_rates",$obj_rating->nemProp["headline"]);
		else 	WebApp::addVar("__headline_rates","{{_headline_rates}}");
		
		if (isset($obj_rating->nemProp["rates"]) && $obj_rating->nemProp["rates"]!="") 
				WebApp::addVar("__rates",$obj_rating->nemProp["rates"]);
		else 	WebApp::addVar("__rates","{{_rates}}");
		
		if (isset($obj_rating->nemProp["articles_pro"]) && $obj_rating->nemProp["articles_pro"]!="") 
				WebApp::addVar("__items",$obj_rating->nemProp["articles_pro"]);
		else 	WebApp::addVar("__items","{{_items}}");


		

		
		
		if (isset($obj_rating->nemProp["sites"]) && $obj_rating->nemProp["sites"]!="") 
				WebApp::addVar("__page",$obj_rating->nemProp["sites"]);
		else 	WebApp::addVar("__page","{{_Showing}}");
		
		if (isset($obj_rating->nemProp["von"]) && $obj_rating->nemProp["von"]!="") 
				WebApp::addVar("__of",$obj_rating->nemProp["von"]);
		else 	WebApp::addVar("__of","{{_of}}");
		
		if (isset($obj_rating->nemProp["edit"]) && $obj_rating->nemProp["edit"]!="") 
				WebApp::addVar("__contribute_edit",$obj_rating->nemProp["edit"]);
		else	WebApp::addVar("__contribute_edit","{{_contribute_edit}}");

		if (isset($obj_rating->nemProp["delete"]) && $obj_rating->nemProp["delete"]!="") 
				WebApp::addVar("__contribute_delete",$obj_rating->nemProp["delete"]);
		else 	WebApp::addVar("__contribute_delete","{{_contribute_delete}}");

		if (isset($obj_rating->nemProp["rate_this_doc"]) && $obj_rating->nemProp["rate_this_doc"]!="") 
				WebApp::addVar("__contribute",$obj_rating->nemProp["rate_this_doc"]);
		else 	WebApp::addVar("__contribute","{{_contribute_add}}");


		WebApp::addVar("template_type","".$obj_rating->nemProp["template_type"]."");

		
		if($obj_rating->publish_comments == "yes") 
				WebApp::addVar("publish_block","yes");
		 else	WebApp::addVar("publish_block","no");


		if ($event->name == "srm" ) {
			$obj_rating->recPages = $event->args["rp"];
			$obj_rating->NrPage =	$event->args["rpp"];
		}


		if ($_GET["evn"]=="srm") {
			$obj_rating->recPages = $_GET["rp"];
			$obj_rating->NrPage =	$_GET["rpp"];
		}
		
		if ($event->name == "rmr") 
			$obj_rating->remove_contribute($event->args["rid"]);
		
		if ($_GET["evn"]=="rmr") 
			$obj_rating->remove_contribute($_GET["rid"]);
		
		





		$gridContainerList["data"] =array();
		$ignore_list_items_confguration = "no";
		if (!isset($obj_rating->nemProp["list_item"]) or (isset($obj_rating->nemProp["list_item"]) && $obj_rating->nemProp["list_item"] == "")) 
			$ignore_list_items_confguration = "yes";

		$publish_title = "no";
		$publish_comment = "no";
		$publish_date = "no";
		$publish_user = "no";
		$publish_ind_rate = "no";
		$publish_av_rate = "no";

		$listItemDisplay = explode (",",substr($obj_rating->nemProp["list_item"],1,-1));
		





			if ($ignore_list_items_confguration == "yes" or in_array(0,$listItemDisplay)) 
				$publish_title = "yes";
			if ($ignore_list_items_confguration == "yes" or in_array(1,$listItemDisplay)) 
				$publish_comment = "yes";
			if ($ignore_list_items_confguration == "yes" or in_array(2,$listItemDisplay)) 
				$publish_date = "yes";
			if ($ignore_list_items_confguration == "yes" or in_array(3,$listItemDisplay)) 
				$publish_user = "yes";
			if ($ignore_list_items_confguration == "yes" or in_array(4,$listItemDisplay)) 
				$publish_ind_rate = "yes";
			if ($ignore_list_items_confguration == "yes" or in_array(5,$listItemDisplay)) 
				$publish_av_rate = "yes";		
		
		if ($obj_rating->rights[READ]=="yes") {	//ka te drejten per te lexuar
			$obj_rating->get_all_info_data();
			
			$data_to_display = $obj_rating->data_to_display;
			$rate_average = 0;
			$rate_cnt = 0;
			$rate_index = 0;
			
			
			$rates_sum = 0;
			
			$gridContainerRatingAverage["data"] =array();
			
			if ($obj_rating->publish_rating=="yes") {
	
				reset($obj_rating->rating_property);
				while (list($key,$value)=each($obj_rating->rating_property)) {
					
					/*if ($value["id"]>0) {	
						$rate_perq = $obj_rating->getIndividualRatingPerqindjeWidth (60,$value["rates"],count($obj_rating->data_to_display));

						if (count($obj_rating->data_to_display)>0) 
						$rate_average += $value["rates"]/count($obj_rating->data_to_display);
						$rate_cnt++;
					}	*/				
			
					if ($value["publish"] == "yes" && $value["id"]>0) {	
						
						
						$rate_perq = $obj_rating->getIndividualRatingPerqindjeWidth (60,$value["rates"],count($obj_rating->data_to_display));

						if (count($obj_rating->data_to_display)>0) 
						$rate_average += $value["rates"]/count($obj_rating->data_to_display);
						
						
						
						$rates_sum +=$value["rates"];
						
						$rate_cnt++;						
						
						
						$gridContainerRatingAverage["data"][$rate_index]["id"] = $value["id"];
						$gridContainerRatingAverage["data"][$rate_index]["rate_desc"] = $value["desc"];
						$gridContainerRatingAverage["data"][$rate_index]["rate_perq"] = $rate_perq;
						$gridContainerRatingAverage["data"][$rate_index]["rates_sum"] = $value["rates"];
						$gridContainerRatingAverage["data"][$rate_index]["rate_average"] = $rate_average;
						$gridContainerRatingAverage["data"][$rate_index]["rate_cnt"] = count($obj_rating->data_to_display);
						
						
						if (count($obj_rating->data_to_display)>0) 
								$gridContainerRatingAverage["data"][$rate_index]["rate_raport"] = $value["rates"]/count($obj_rating->data_to_display);
						else	$gridContainerRatingAverage["data"][$rate_index]["rate_raport"] = "0";
						
						
						$gridContainerRatingAverage["data"][$rate_index]["rate_raport_rounded"] = round($gridContainerRatingAverage["data"][$rate_index]["rate_raport"]);
						
						$rate_index++;
					}				
				}	
			
				if ($obj_rating->rating_property[0]["publish"] == "yes" ) {
					$gridContainerRatingAverage["data"][$rate_cnt]["id"] = "0";
					
					
					$gridContainerRatingAverage["data"][$rate_cnt]["rate_desc"] = $obj_rating->rating_property[0]["desc"];
					
					
					$rate_average_perq = $obj_rating->getIndividualRatingPerqindjeWidth (60,$rate_average,$rate_cnt);
					
					
					$gridContainerRatingAverage["data"][$rate_cnt]["rate_perq"] = $rate_average_perq;
					$gridContainerRatingAverage["data"][$rate_cnt]["rate_average"] = $rate_average;
					$gridContainerRatingAverage["data"][$rate_cnt]["rate_cnt"] = $rate_cnt;
					
						if ($rate_cnt>0) 
								$gridContainerRatingAverage["data"][$rate_cnt]["rate_raport"] = $rates_sum/$rate_cnt;
						else	$gridContainerRatingAverage["data"][$rate_cnt]["rate_raport"] = "0";
						
						
						$gridContainerRatingAverage["data"][$rate_cnt]["rate_raport_rounded"] = round($gridContainerRatingAverage["data"][$rate_index]["rate_raport"]);
											
					
					
				}
			}

			$gridContainerRatingAverage["AllRecs"] = count($gridContainerRatingAverage["data"]);
			
			/*echo "<textarea>";
			print_r($gridContainerRatingAverage);
			echo "</textarea>";*/
			
			WebApp::addVar("gridContainerRatingAverage",$gridContainerRatingAverage);
			
				
			WebApp::addVar("recPages","".$obj_rating->recPages."");
			WebApp::addVar("NrPage","".$obj_rating->NrPage."");
			WebApp::addVar("CountItems","".$obj_rating->CountItems."");
			WebApp::addVar("FromRecs","".$obj_rating->FromRecs."");
			WebApp::addVar("ToRecs","".$obj_rating->ToRecs."");
			WebApp::addVar("TotPage","".$obj_rating->TotPage."");
			WebApp::addVar("previewsPage","".$obj_rating->previewsPage."");
			WebApp::addVar("nextPage","".$obj_rating->nextPage."");
			
			
			
			
	
			
			
			

			if ($obj_rating->CountItems > 0) {

				$index = 1;
				
					for ($key=$obj_rating->FromRecs;$key<=$obj_rating->ToRecs;$key++) {
									
						$value = $data_to_display[$key];

						$gridContainerList["data"][$index]["CurrentRowNr"] = $index;
						$gridContainerList["data"][$index]["rid"] = $value["rid"];
						
						$gridContainerList["data"][$index]["cr_user_id"] = $value["user_id"];
						

						if ($publish_title=="yes") 
								$gridContainerList["data"][$index]["titleC"] = $value["title"];
						else 	$gridContainerList["data"][$index]["titleC"] = "";

						if ($publish_comment=="yes") 
								$gridContainerList["data"][$index]["textC"] = $value["text"];
						else 	$gridContainerList["data"][$index]["textC"] = "";

						$user_date = array();
						if ($publish_date=="yes") {
							$user_date["fillData"] = $value["dateModified"];
							$gridContainerList["data"][$index]["dateModified"] = $value["dateModified"];
						}
						if ($publish_user=="yes") {
							$user_date["userData"] = $value["name_to_display"];
							$gridContainerList["data"][$index]["name_to_display"] = $value["name_to_display"];
						}
							
						//$gridContainerList["data"][$index]["user_date"] = implode(", ",$user_date);	

						if (count($user_date)>0) 
								$gridContainerList["data"][$index]["user_date"] = implode(", ",$user_date);
						else 	$gridContainerList["data"][$index]["user_date"] = "";

						$rate_contribute = 0;
						$rate_contribute_cnt = 0;

						if(isset($value["rate"]) && $value["rate"]!=""){
							while (list($keyX,$valueX)=each($value["rate"])) {
								if ($publish_ind_rate=="yes" && $obj_rating->rating_property[$keyX]["publish"] == "yes" &&  $obj_rating->rating_property[$keyX]["id"]>0)  {
									
									$rate_contribute += $valueX;
									$rate_contribute_cnt++;									
									
									$rate_perq = $obj_rating->getIndividualRatingPerqindjeWidth (60,$valueX,1);
									$gridContainerList["data"][$index]["individual_rating_ori_".$keyX] = $valueX;
									$gridContainerList["data"][$index]["individual_rating_".$keyX] = $rate_perq;
									//$gridContainerList["data"][$index]["individual_rating_".$keyX] = $obj_rating->getIndividualRating($valueX);
								}
							}
						}
						if ($publish_av_rate=="yes")  {
							$gridContainerList["data"][$index]["individual_rating_0"] = 
								$obj_rating->getIndividualRatingPerqindjeWidth (60,$rate_contribute,$rate_contribute_cnt);
							}

						$roles_array = array_count_values (explode(",",$obj_rating->tip));
						if ($obj_rating->rights[FULL_WRITES]=="yes") { //ka te drejte te fshije, cdo administrator
							$gridContainerList["data"][$index]["edit_delete"] = "yes";
							//echo "administrator";
						} elseif ($session->Vars["ses_userid"]!=2 && $session->Vars["ses_userid"] == $value["user_id"]) {
							$gridContainerList["data"][$index]["edit_delete"] = "yes";
							//echo "user i njohur";
						} elseif ($session->Vars["uni"] == $value["uni"]) {
							$gridContainerList["data"][$index]["edit_delete"] = "yes";
							//echo "uni i njohur per userat web kemi qe userat e webit, mund te editojen, rekordet brenda unit, por meqenese uni ruhet ne cookies, ekzsiton mundesia qe te editohen komentet e te tjereve nese aplikimi hapet nga nje user tjeter ne te njejtin kompjuter";
						} else {
							$gridContainerList["data"][$index]["edit_delete"] = "no";
							//echo "user i panjohur, keto rekorde preken vetem nga administratori";
						}
						
						$index++;
					} 
					
					$evarege_data_temp =array();
					$gridContainerRating["data"] =array();
					reset($obj_rating->rating_property);
					$indexIndividualRating = 0;
					
					while (list($key,$value)=each($obj_rating->rating_property)) {

						if ($publish_ind_rate=="yes" && $value["publish"] == "yes" && $value["id"]>0)  {
								$gridContainerRating["data"][$indexIndividualRating]["id"] = $value["id"];
								$gridContainerRating["data"][$indexIndividualRating]["rate_desc"] = $value["desc"];
								$gridContainerRating["data"][$indexIndividualRating]["rate_value"] = "{{individual_rating_".$value["id"]."}}";
								$gridContainerRating["data"][$indexIndividualRating]["rate_ori_value"] = "{{individual_rating_ori_".$value["id"]."}}";
								$indexIndividualRating++;
						}

						if ($publish_av_rate=="yes" && $value["id"]==0) {
								$evarege_data_temp["id"] = $value["id"];
								$evarege_data_temp["rate_desc"] = $value["desc"];
								$evarege_data_temp["rate_value"] = "{{individual_rating_".$value["id"]."}}";
								
								WebApp::addVar("titleW","80%");
								
								if ($value["publish"] =="yes")
									WebApp::addVar("rate_value","".$evarege_data_temp["rate_value"]."");
								else {
									WebApp::addVar("rate_value","0");
									WebApp::addVar("titleW","100%");
								}
						} 
					}
			}
			
			$gridContainerRating["AllRecs"] = count($gridContainerRating["data"]);
			
			
			WebApp::addVar("recPages","".$obj_rating->recPages."");
			WebApp::addVar("NrPage","".$obj_rating->NrPage."");
			WebApp::addVar("CountItems","".$obj_rating->CountItems."");
			WebApp::addVar("FromRecs","".$obj_rating->FromRecs."");
			WebApp::addVar("ToRecs","".$obj_rating->ToRecs."");
			WebApp::addVar("TotPage","".$obj_rating->TotPage."");
			WebApp::addVar("previewsPage","".$obj_rating->previewsPage."");
			WebApp::addVar("nextPage","".$obj_rating->nextPage."");
			
			
			
			
			
	
						
			
			
			
			
			
			
			
			
			WebApp::addVar("gridContainerRating",$gridContainerRating);					
			
			$gridContainerList["AllRecs"] = count($gridContainerList["data"]);
			
			$gridContainerList["dataNAV"]["CntI"] =$obj_rating->CountItems;
			$gridContainerList["dataNAV"]["fromI"]=$obj_rating->FromRecs;
			$gridContainerList["dataNAV"]["toI"] 	=$obj_rating->ToRecs;
			$gridContainerList["dataNAV"]["pg"] 	=$obj_rating->NrPage;
			$gridContainerList["dataNAV"]["pgCnt"]=$obj_rating->TotPage;
			$gridContainerList["dataNAV"]["pgRec"]=$obj_rating->recPages;					
			
			
			
			WebApp::addVar("gridContainerList",$gridContainerList);
			
			
			
			
			
			
			
			$show_comments_rating = "yes";
			if ($obj_rating->rights[WRITE]=="yes") { //mund te contribuoje
				$contribute_comments_rating = "yes";
			}
			
			
			WebApp::addVar("comments_rating_full_writes","".$obj_rating->rights[FULL_WRITES]."");
			
			
		}
	}


	$propN = base64_encode(serialize($rateNeddedData));
	WebApp::addVar("propN",$propN);
	WebApp::addVar("show_comments_rating","$show_comments_rating");
	WebApp::addVar("contribute_comments_rating","$contribute_comments_rating");

	//ketu jep rating qe ka dhene ky user

	

	WebApp::addVar("rating_portal","0");

/*echo "<textarea>";
print_r($obj_rating);
echo "</textarea>";*/




}


?>