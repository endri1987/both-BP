<?
function AuthorNavigation_Grid($node_family_id_sel,$force_expandedV,$profile_step,$zone_id_sel="") 
{
	global $session,$event, $global_cache_dynamic,$cacheDyn;
	extract($event->args);

    $lng_id = SUBSTR($session->Vars["lang"], -1);


   
    $nav_image_array	 = "";

	$force_expanded 	 = $force_expandedV;//if Y show all node and subnode, N only on click show sub node
	  //class name ---------------------------------------
      $style_name_n1      = "nav_n1";
      $style_name_n1_sel  = " nav_n1_sel";
      $style_name_n1_cur  = " nav_n1_current";
    
      $style_name_n2      = "nav_n2";
      $style_name_n2_sel  = " nav_n2_sel";
      $style_name_n2_cur  = " nav_n2_current";
    
      $style_name_n3      = "nav_n3";
      $style_name_n3_sel  = " nav_n3_sel";
      $style_name_n3_cur  = " nav_n3_current";

      $style_name_n4      = "nav_n4";
      $style_name_n4_sel  = " nav_n4_sel";
      $style_name_n4_cur  = " nav_n4_current";
    
      $style_name_noclick = " nav_noclickable";
    //--------------------------------------------------

//$profile_step_id         	= "1";


$id0 = $session->Vars["level_0"];
if ($profile_step != 1) {
	if ($profile_step == 5 && isset($zone_id_sel) && $zone_id_sel!="" && $zone_id_sel>=0) {
			$id0 = $zone_id_sel;   
			//$node_family_id_sel = "";
	} else {
		if ($id0==ZONE_ECCELEARNING || $id0==ZONE_MYCME)
			$id0 = ZONE_AUTHORING;   
	}
} 


//$profile_step .= "<option value=\"1\" $selectedO>Related to Actual Zone</option>";
//$profile_step .= "<option value=\"2\" $selectedO>Related to Authoring Zone - All Sitemap</option>";
//$profile_step .= "<option value=\"3\" $selectedO>Related to Authoring Zone - Reading basket Case</option>";
//$profile_step .= "<option value=\"3\" $selectedO>Related to Authoring Zone - MyCme Home</option>";
//$profile_step .= "<option value=\"5\" $selectedO>Selected Zone</option>";

   
   
    INCLUDE(APP_PATH."templates/NEModules/AuthorNavigation/AuthorNavigation_sql.php");
    unset($Grid_AuthorNav);
    $Grid_AuthorNav = array("data" => array(), "AllRecs" => "0");	


    //niveli I --------------------------------------------------------------------------------------------------------------------
		$nr   = -1;
		$nr1  = 0; 	 
		$nr2  = 0;	
		$nr3  = 0;	
		$nr4  = 0;	
		
		$k1 	= 0; 
		$key1 	= 1;
		
      $sql_nivel1 = $sqlN;

      if (isset($node_family_id_sel) && $node_family_id_sel>=0) {
			$kushti_node_family = " AND nivel.node_family_id = '".$node_family_id_sel."'";
      } else {
			$kushti_node_family = "";
      }
           

      
      $sql_nivel1 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel1);
      $sql_nivel1 = str_ireplace("{{id_firstNivel}}",      " > '0' ",           $sql_nivel1);
      $sql_nivel1 = str_ireplace("{{id_secondNivel}}",     " = '0' ",           $sql_nivel1);
      $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",      " = '0' ",           $sql_nivel1);
      $sql_nivel1 = str_ireplace("{{id_fourthNivel}}",     " = '0' ",           $sql_nivel1);
	  
	  
	  $color_type	= "";
	  $showColor	= "n";
	  //if the user logged has the write right he can see the navigation status color
	global $session;

	

	

	

	/* $authoringObj;		
	if ($session->Vars["level_0"]==ZONE_AUTHORING) {

	
		if (isset($authoringObj->isSetGlobalObj) && $authoringObj->isSetGlobalObj=="yes") {
		} else {
				require_once(INC_PATH.'authoring.base.class.php');
				$authoringObj = new authoringZone();
				$authoringObj->isSetGlobalObj = "yes";	
				$authoringObj->authoringZone();
				$authoringObj->initCiReference();	
		}

		


		 $read_write =$authoringObj->appRelatedState["CiRights"][$authoringObj->lecture_id]["read_write"];

		 if (isset($read_write) && $read_write == "W") {			
				$showColor		= "n";
		  }else{

		  }
	  }	*/	
	  

      $rs1  = WebApp::execQuery($sql_nivel1);
      
// echo "<textarea>";
// print_r($rs1);
// echo "</textarea>";
      
      
      WHILE (!$rs1->EOF())
            {
             $id1       		= $rs1->Field("id1");
             $id2       		= $rs1->Field("id2");
             $id3       		= $rs1->Field("id3");
             $id4       		= $rs1->Field("id4");
             $label     		= $rs1->Field("label");
             $isExpanded		= $rs1->Field("isExpanded");
             $clickable 		= $rs1->Field("clickable");
             $with_https		= $rs1->Field("with_https");
             
             
             
             
             
             
             
			 
			$class_style_sel = $style_name_n1;
			IF ($session->Vars["level_1"] == $id1)
			  {
			   $class_style_sel .= $style_name_n1_sel;
	   
			   IF ($session->Vars["level_2"] == 0)
				  {$class_style_sel .= $style_name_n1_cur;}
			  }
             
             $imageSm_id     	= $rs1->Field("imageSm_id");
             $imageSm_id_name 	= $rs1->Field("imageSm_id_name");


             $con_id     		= $rs1->Field("con_id");
			 $ci_title     		= $rs1->Field("ci_title");
			 $ci_description    = $rs1->Field("ci_description");			  
							 
				$title      		= $rs1->Field("title");
				$filename   		= $rs1->Field("filename");

			//get input ,proccesing, review
				$input 			= $rs1->Field("input");
				$proccesing  	= $rs1->Field("proccesing");
				$review 		= $rs1->Field("review");
				$ci_type 		= $rs1->Field("ci_type");

             $nr  = $nr + 1;
             $nr1 = $nr1 + 1;

				$Grid_AuthorNav["data"][$k1]["key1"]     			= $key1;
				$Grid_AuthorNav["data"][$k1]["nivel"]     			= 1;
				$Grid_AuthorNav["data"][$k1]["id0"]       			= $id0;
				$Grid_AuthorNav["data"][$k1]["id1"]       			= $id1;
				$Grid_AuthorNav["data"][$k1]["id2"]       			= $id2;
				$Grid_AuthorNav["data"][$k1]["id3"]       			= $id3;
				$Grid_AuthorNav["data"][$k1]["id4"]       			= $id4;
				$Grid_AuthorNav["data"][$k1]["label"]     			= $label;
				$Grid_AuthorNav["data"][$k1]["con_id"]     			= $con_id;
				$Grid_AuthorNav["data"][$k1]["isExpanded"] 			= $isExpanded;
				$Grid_AuthorNav["data"][$k1]["clickable"]  			= $clickable;
				$Grid_AuthorNav["data"][$k1]["ci_type"]  	  		= $ci_type;
				$Grid_AuthorNav["data"][$k1]["ci_description"]  	= $ci_description;
				$Grid_AuthorNav["data"][$k1]["ci_title"]  	  		= $ci_title;
	
				$Grid_AuthorNav["data"][$k1]["keyIden"]     		= $id0."_".$id1."_".$id2."_".$id3."_".$id4;
             //imazhi --------------------------------------------------------------------------------------------------------------
               $Grid_AuthorNav["data"][$k1]["image_id"]  			= "";
               $Grid_AuthorNav["data"][$k1]["image_tag"] 			= "";
               $Grid_AuthorNav["data"][$k1]["image_src"] 			= "";
               $Grid_AuthorNav["data"][$k1]["image_w"]   			= "";
               $Grid_AuthorNav["data"][$k1]["image_h"]   			= "";
               
				if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
					$Grid_AuthorNav["data"][$k1]["boostrap_class"]	= $rs1->Field("boostrap_class");
					$Grid_AuthorNav["data"][$k1]["boostrap_ico"]	= $rs1->Field("boostrap_ico");
				}

               IF ($imageSm_id > 0)
                  {
                   $nav_image_array[$k1] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij

                   IF ($global_cache_dynamic == "Y")
                      {
                       $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                      }
                   ELSE
                      {
                       $image_src_sel = APP_URL.'show_image.php?file_id='.$imageSm_id;
                      }

                   $Grid_AuthorNav["data"][$k1]["image_id"]  = $imageSm_id;
                   $Grid_AuthorNav["data"][$k1]["image_tag"] = '<img src="'.$image_src_sel.'" border="0" />';
                   $Grid_AuthorNav["data"][$k1]["image_src"] = $image_src_sel;
                  }
             //imazhi --------------------------------------------------------------------------------------------------------------

             $Grid_AuthorNav["data"][$k1]["index1"]     = $nr1;
             $Grid_AuthorNav["data"][$k1]["index2"]     = 0;
             $Grid_AuthorNav["data"][$k1]["index3"]     = 0;
             $Grid_AuthorNav["data"][$k1]["index4"]     = 0;
             
             //per stilin ----------------------------------------------------------------------------------------------------------
               $Grid_AuthorNav["data"][$k1]["class_style"] = $class_style_sel;
             //---------------------------------------------------------------------------------------------------------------------
             //clickable -----------------------------------------------------------------------------------------------------------
               IF ($clickable == "Y")
                  {
                   IF ($global_cache_dynamic == "Y")
                      {
                       $Grid_AuthorNav["data"][$k1]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                      }
                   ELSE
                      {
                       $Grid_AuthorNav["data"][$k1]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$id0.",".$id1.",".$id2.",".$id3.",".$id4.")')";
                      }
                  }
               ELSE
                  {
                   $Grid_AuthorNav["data"][$k1]["link"]   = "javascript:void(0)";
                   $Grid_AuthorNav["data"][$k1]["class_style"] .= $style_name_noclick;
                  }
             //---------------------------------------------------------------------------------------------------------------------
			//shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
             $show_nivel2 = "N"; 
				$Grid_AuthorNav["data"][$k1]["display"]	=" display:none;";			 
                 IF ($session->Vars["level_1"] == $id1)
                    {
						$show_nivel2 = "Y";
						$Grid_AuthorNav["data"][$k1]["display"]	=" display:block;";
					}
				IF ($force_expanded=="Y")
					{
						$show_nivel2 = "Y";
						$Grid_AuthorNav["data"][$k1]["display"]	=" display:block;";
					}
           //---------------------------------------------------------------------------------------------------------------------			
			$k2 = 0; 
			$key2 = 1;
			//percaktohet sub Grida per nivelin e dyte te navigimit
			$Grid_AuthorNav["data"][$k1]["nivel2"]["data"]     			= array();
						
			//IF ($show_nivel2 == "Y" )
           // {	
			   
             //niveli II ----------------------------------------------------------------------------------------------------------
               $nr2 = 0;

               $kushti_node_family = "";
               $sql_nivel2 = $sqlN;
               $sql_nivel2 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel2);
               $sql_nivel2 = str_ireplace("{{id_firstNivel}}",      " = '".$id1."' ",    $sql_nivel2);
               $sql_nivel2 = str_ireplace("{{id_secondNivel}}",     " > '0' ",           $sql_nivel2);
               $sql_nivel2 = str_ireplace("{{id_thirdNivel}}",      " = '0' ",           $sql_nivel2);
               $sql_nivel2 = str_ireplace("{{id_fourthNivel}}",     " = '0' ",           $sql_nivel2);

               $rs2 = WebApp::execQuery($sql_nivel2);
        
            			   
               WHILE (!$rs2->EOF())
                     {
						$id1        		= $rs2->Field("id1");
						$id2        		= $rs2->Field("id2");
						$id3        		= $rs2->Field("id3");
						$id4        		= $rs2->Field("id4");
						$label      		= $rs2->Field("label");
						$isExpanded 		= $rs2->Field("isExpanded");
						$clickable  		= $rs2->Field("clickable");
						$with_https 		= $rs2->Field("with_https");

						$class_style_sel2 = $style_name_n2;
						IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2))
						  {
						   $class_style_sel2 .= $style_name_n2_sel;
							
						   IF ($session->Vars["level_3"] == 0)
							  {$class_style_sel2 .= $style_name_n2_cur;}
						  }
						
						
						$imageSm_id      	= $rs2->Field("imageSm_id");
						$imageSm_id_name 	= $rs2->Field("imageSm_id_name");

						$ci_title     		= $rs2->Field("ci_title");
						$ci_description    	= $rs2->Field("ci_description");
						$con_id    		 	= $rs2->Field("con_id");
						$ci_title     		= $rs2->Field("ci_title");
						$ci_description    	= $rs2->Field("ci_description");
					  
						$title      		= $rs2->Field("title");
						$filename  			= $rs2->Field("filename");
						
						//get input ,proccesing, review
						$input 				= $rs2->Field("input");
						$proccesing  		= $rs2->Field("proccesing");
						$review 			= $rs2->Field("review");
						$ci_type 			= $rs2->Field("ci_type");
						

                      $nr  	= $nr + 1;
                      $nr2 	= $nr2 + 1;
                      
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel"]   				= 2;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["key2"]   				= $key2;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id0"]        			= $id0;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id1"]        			= $id1;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id2"]        			= $id2;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id3"]        			= $id3;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id4"]        			= $id4;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["label"]      			= $label;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["con_id"]     			= $con_id;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["isExpanded"] 			= $isExpanded;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["clickable"]  			= $clickable;

						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_description"]  		= $ci_description;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_title"]  	  		= $ci_title;
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_type"]  	  		= $ci_type;
						
						//display sub nivel
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"]  	  		= " display:block;";

						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"]        		= $id0."_".$id1."_".$id2."_".$id3."_".$id4;
					
					
				//	echo $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"]."<br>";
					
					//imazhi --------------------------------------------------------------------------------------------------------------
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id"]  			= "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag"] 			= "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src"] 			= "";
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_w"]   			= "";
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_h"]   			= "";
						
				if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["boostrap_class"]	= $rs2->Field("boostrap_class");
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["boostrap_ico"]	= $rs2->Field("boostrap_ico");
				}						
						
						

                        IF ($imageSm_id > 0)
                           {
                            $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
         
                            IF ($global_cache_dynamic == "Y")
                               {
                                $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                               }
                            ELSE
                               {
                                $image_src_sel = APP_URL.'show_image.php?file_id='.$imageSm_id;
                               }
         
                           $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id"]  = $imageSm_id;
                           $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag"] = '<img src="'.$image_src_sel.'" border="0" />';
                           $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src"] = $image_src_sel;
                           }
                      //imazhi --------------------------------------------------------------------------------------------------------------
                      
                      IF ($imageSm_id > 0)
                         {
                          $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                         }
             
                      $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index1"]     = $nr1;
                      $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index2"]     = $nr2;
                      $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index3"]     = 0;
                      $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index4"]     = 0;
                      
                      //per stilin ----------------------------------------------------------------------------------------------------------
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["class_style"] = $class_style_sel2;
						
                      //---------------------------------------------------------------------------------------------------------------------
                      //clickable -----------------------------------------------------------------------------------------------------------
                        IF ($clickable == "Y")
                           {
                            IF ($global_cache_dynamic == "Y")
                               {
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                               }
                            ELSE
                               {
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$id0.",".$id1.",".$id2.",".$id3.",".$id4.")')";
                               }
                           }
                        ELSE
                           {
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"]   = "javascript:void(0)";
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["class_style"] .= $style_name_noclick;
                           }
                      //---------------------------------------------------------------------------------------------------------------------
					//shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
					 $show_nivel3 = "N";
					 $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"]	=" display:none;";				 
						  IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2))
							{
								$show_nivel3 = "Y";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"]	=" display:block;";
							}
						 IF ($force_expanded=="Y")
							{
								$show_nivel3 = "Y";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"]	=" display:block;";
							}

				   //---------------------------------------------------------------------------------------------------------------------
					$k3		= 0;
					$key3	= 1;

					//percaktohet sub Grida per nivelin e dyte te navigimit
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"]    			= array();
					
					//IF ($show_nivel3 == "Y")
					//{	
                      //niveli III ------------------------------------------------------------------------------------------------
                        $nr3 	= 0;

                        $kushti_node_family = "";
                        $sql_nivel3 = $sqlN;
                        $sql_nivel3 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel3);
                        $sql_nivel3 = str_ireplace("{{id_firstNivel}}",      " = '".$id1."' ",    $sql_nivel3);
                        $sql_nivel3 = str_ireplace("{{id_secondNivel}}",     " = '".$id2."' ",    $sql_nivel3);
                        $sql_nivel3 = str_ireplace("{{id_thirdNivel}}",      " > '0' ",           $sql_nivel3);
                        $sql_nivel3 = str_ireplace("{{id_fourthNivel}}",     " = '0' ",           $sql_nivel3);



if ($id0==ZONE_MYCME) {


//	$profile_step = 1 | Related to Actual Zone";
//	$profile_step = 2 | Related to Authoring Zone - All Sitemap";
//	$profile_step = 3 | Related to Authoring Zone - Reading basket Case";



}





                        $rs3 = WebApp::execQuery($sql_nivel3);
/*echo "<textarea>";
print_r($rs3);
echo "</textarea>";*/

                        WHILE (!$rs3->EOF())
                              {
								$id1        		= $rs3->Field("id1");
								$id2        		= $rs3->Field("id2");
								$id3        		= $rs3->Field("id3");
								$id4        		= $rs3->Field("id4");
								$label      		= $rs3->Field("label");
								$isExpanded 		= $rs3->Field("isExpanded");
								$clickable  		= $rs3->Field("clickable");
								$with_https 		= $rs3->Field("with_https");
								
								//get input ,proccesing, review
								$input 			= $rs3->Field("input");
								$proccesing  	= $rs3->Field("proccesing");
								$review 		= $rs3->Field("review");
								$ci_type 		= $rs3->Field("ci_type");

								$class_style_sel3 = $style_name_n3;
								IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3))
								{
									$class_style_sel3 .= $style_name_n3_sel;

									IF ($session->Vars["level_4"] == 0)
									{$class_style_sel3 .= $style_name_n3_cur;}
								}

								$imageSm_id     	= $rs3->Field("imageSm_id");
								$imageSm_id_name 	= $rs3->Field("imageSm_id_name");

								$con_id     		= $rs3->Field("con_id");
								$ci_title     		= $rs3->Field("ci_title");
								$ci_description    	= $rs3->Field("ci_description");
							   
                               $title      = $rs3->Field("title");
                               $filename   = $rs3->Field("filename");

                               
                               $displayRecord = "yes";
                               if ($profile_step==3) {
							   
									$isMeine   = $rs3->Field("isMeine");
									if ($isMeine=="notMine")
										 $displayRecord = "no";
							   
							   }

                               
                               
                               
                               if ($displayRecord == "yes") {
                               
                               $nr  = $nr + 1;
                               $nr3 = $nr3 + 1;
							
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel"]      			= 3;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["key3"]      			= $key3;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id0"]        			= $id0;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id1"]        			= $id1;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id2"]        			= $id2;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id3"]        			= $id3;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id4"]        			= $id4;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["label"]      			= $label;
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["con_id"]     			= $con_id;                                              
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["isExpanded"] 			= $isExpanded;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["clickable"]  			= $clickable;
                                                                                 
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_description"]  		= $ci_description;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_title"]  	  		= $ci_title;
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_type"]  	  			= $ci_type;

								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["keyIden"]        		= $id0."_".$id1."_".$id2."_".$id3."_".$id4;
                               //imazhi ---------------------------------------------------------------------------------------------------------------
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id"]   			= "";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag"]  			= "";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src"]  			= "";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_w"]    			= "";
								$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_h"]    			= "";


				if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["boostrap_class"]	= $rs3->Field("boostrap_class");
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["boostrap_ico"]	= $rs3->Field("boostrap_ico");
				}						
				
                                 IF ($imageSm_id > 0)
                                    {
                                     $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
         
                                     IF ($global_cache_dynamic == "Y")
                                        {
                                         $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                                        }
                                     ELSE
                                        {
                                         $image_src_sel = APP_URL.'show_image.php?file_id='.$imageSm_id;
                                        }
         
                                     $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id"]  = $imageSm_id;
                                     $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag"] = '<img src="'.$image_src_sel.'" border="0" />';
                                     $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src"] = $image_src_sel;
                                    }
                               //imazhi --------------------------------------------------------------------------------------------------------------
                               
                               IF ($imageSm_id > 0)
                                  {
                                   $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                                  }
             
                               $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index1"]     = $nr1;
                               $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index2"]     = $nr2;
                               $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index3"]     = $nr3;
                               $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index4"]     = 0;

                               //per stilin ----------------------------------------------------------------------------------------------------------
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["class_style"] = $class_style_sel3;
                               //---------------------------------------------------------------------------------------------------------------------
                               //clickable -----------------------------------------------------------------------------------------------------------
                                 IF ($clickable == "Y")
                                    {
                                     IF ($global_cache_dynamic == "Y")
                                        {
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                                        }
                                     ELSE
                                        {
                                         $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$id0.",".$id1.",".$id2.",".$id3.",".$id4.")')";
                                        }
                                    }
                                 ELSE
                                    {
                                     $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"]   = "javascript:void(0)";
                                     $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["class_style"] .= $style_name_noclick;
                                    }
                               //---------------------------------------------------------------------------------------------------------------------
								//shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
							 $show_nivel4 = "N";
							 $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"]=" display:none;";							 
								  IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3))
									{
										$show_nivel4 = "Y";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"]=" display:block;";
									}
								 IF ($force_expanded=="Y")
									{
										$show_nivel4 = "Y";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"]=" display:block;";
									
									}
									
								
							  //---------------------------------------------------------------------------------------------------------------------
							
							$k4		= 0;
							$key4	= 1;
							//percaktohet sub Grida per nivelin e dyte te navigimit
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"]  			= array();
								
								//IF ($show_nivel4 == "Y")
								//{	
                               //niveli IV ----------------------------------------------------------------------------------------
                                 $nr4 	= 0;

                                 $kushti_node_family = "";
                                 $sql_nivel4 = $sqlN;
                                 $sql_nivel4 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel4);
                                 $sql_nivel4 = str_ireplace("{{id_firstNivel}}",      " = '".$id1."' ",    $sql_nivel4);
                                 $sql_nivel4 = str_ireplace("{{id_secondNivel}}",     " = '".$id2."' ",    $sql_nivel4);
                                 $sql_nivel4 = str_ireplace("{{id_thirdNivel}}",      " = '".$id3."' ",    $sql_nivel4);
                                 $sql_nivel4 = str_ireplace("{{id_fourthNivel}}",     " > '0' ",           $sql_nivel4);
                                 
                                 $rs4 = WebApp::execQuery($sql_nivel4);
                                 

								 
                                 WHILE (!$rs4->EOF())
                                       {
											$id1        		= $rs4->Field("id1");
											$id2        		= $rs4->Field("id2");
											$id3        		= $rs4->Field("id3");
											$id4        		= $rs4->Field("id4");
											$label      		= $rs4->Field("label");
											$isExpanded 		= $rs4->Field("isExpanded");
											$clickable  		= $rs4->Field("clickable");
											$with_https 		= $rs4->Field("with_https");
											$ci_type  			= $rs4->Field("ci_type");
											//get input ,proccesing, review
											$input 				= $rs4->Field("input");
											$proccesing  		= $rs4->Field("proccesing");
											$review 			= $rs4->Field("review");

											$class_style_sel4 = $style_name_n4;
											IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3) AND ($session->Vars["level_4"] == $id4))
											{
												$class_style_sel4 .= $style_name_n4_sel;

												IF ($session->Vars["level_4"] == 0)
												{$class_style_sel4 .= $style_name_n4_cur;}
											}
											
											
											
											$imageSm_id      	= $rs4->Field("imageSm_id");
											$imageSm_id_name 	= $rs4->Field("imageSm_id_name");

											$con_id     		= $rs4->Field("con_id");
											$ci_title     		= $rs4->Field("ci_title");
											$ci_description    	= $rs4->Field("ci_description");
														
										
                                        $title      = $rs4->Field("title");
                                        $filename   = $rs4->Field("filename");

                                        $nr  = $nr + 1;
                                        $nr4 = $nr4 + 1;
                                        
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["nivel"]     			= 4;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id0"]       			= $id0;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id1"]       			= $id1;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id2"]       			= $id2;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id3"]       			= $id3;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id4"]       			= $id4;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["label"]     			= $label;
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["con_id"]     		= $con_id;                             
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["isExpanded"] 		= $isExpanded;
                                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["clickable"]  		= $clickable;
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_type"]  	  		= $ci_type;
										
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_description"]  	= $ci_description;
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_title"]  	  		= $ci_title;
										
										
                                        //imazhi --------------------------------------------------------------------------------------------------------------
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id"]   		= "";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag"]  		= "";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src"]  		= "";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_w"]    		= "";
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_h"]    		= "";

                                          
                                          
				if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y") {
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["boostrap_class"]	= $rs4->Field("boostrap_class");
					$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["boostrap_ico"]	= $rs4->Field("boostrap_ico");
				}                                          
                                          
                                          
                                          
                                          
                                          IF ($imageSm_id > 0)
                                             {
                                              $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                  
                                              IF ($global_cache_dynamic == "Y")
                                                 {
                                                  $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                                                 }
                                              ELSE
                                                 {
                                                  $image_src_sel = APP_URL.'show_image.php?file_id='.$imageSm_id;
                                                 }
                           
											  $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id"]  = $imageSm_id;
                                              $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag"] = '<img src="'.$image_src_sel.'" border="0" />';
                                              $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src"] = $image_src_sel;
                                             }
                                        //imazhi --------------------------------------------------------------------------------------------------------------

                                        IF ($imageSm_id > 0)
                                           {
                                            $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                                           }
											
											$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index1"]     = $nr1;
											$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index2"]     = $nr2;
											$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index3"]     = $nr3;
											$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index4"]     = $nr4;
                                        
                                        //per stilin ----------------------------------------------------------------------------------------------------------
                                       
                                       
											$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["class_style"] = $class_style_sel4;


									   //---------------------------------------------------------------------------------------------------------------------
										
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["input"]     		= $input;
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["proccesing"]     = $proccesing;
										$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["review"]    		= $review;
										//clickable -----------------------------------------------------------------------------------------------------------
                                          IF ($clickable == "Y")
                                             {
                                              IF ($global_cache_dynamic == "Y")
                                                 {
                                                  $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                                                 }
                                              ELSE
                                                 {
                                                  $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=".$id0.",".$id1.",".$id2.",".$id3.",".$id4.")')";
                                                 }
                                             }
                                          ELSE
                                             {
                                              $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"]   = "javascript:void(0)";
                                              $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["class_style"] .= $style_name_noclick;
                                             }
                                        //---------------------------------------------------------------------------------------------------------------------
                                        
										$key4++;
										$k4++;
										
										$rs4->MoveNext();
                                       }
  									   
                              // }//if show livel IV
							   //niveli IV ------------------------------------------------------------------------------------------------------------------------------------------
   
							//ketu bej kontrollin e statuseve te EL --niveli i 3-----------------------------------------------------------------------------------------------------

							//thirret funksioni qe merr array me childed e El dhe tipin e vet EL  dhe kthen nje pergjigje				
						/*	if ($session->Vars["level_0"]==ZONE_AUTHORING) {
								$resultArray	= $authoringObj->MetaStatusCheck($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_type"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["con_id"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]['data']);//347-EL,  378-PR
							
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["color_i"]   			= $resultArray['input']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["value_i"]  				= $resultArray['input']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["percentage_i"]  		= $resultArray['input']['percentage'];

							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["color_p"]   			= $resultArray['process']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["value_p"]  				= $resultArray['process']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["percentage_p"]  		= $resultArray['process']['percentage'];
							
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["color_r"]   			= $resultArray['review']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["value_r"]  				= $resultArray['review']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["percentage_r"]  		= $resultArray['review']['percentage'];

							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["input"]  				= $resultArray['input']['input'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["proccesing"]  			= $resultArray['process']['process'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["review"]  				= $resultArray['review']['review'];
							//------------------------------------------------------------------------------------------------------------------------------------------------------																			 
							}*/
							
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]['data']);
							//WebApp::addVar("Grid_AuthorNav_Nivel4".$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["con_id"].$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["key3"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]);
							WebApp::addVar("Grid_AuthorNav_Nivel4".$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["con_id"].$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["keyIden"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]);
							
							//sa femije ka niveli i 3 
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nr_child"]  =$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["AllRecs"];
					 
					 
							$key3++;
							$k3++;
							}
							
							$rs3->MoveNext();
                        }
                      
                      //}
					  //if show livel III
					  //niveli III ------------------------------------------------------------------------------------------------
					 	//ketu bej kontrollin e statuseve te EC --niveli i 2-----------------------------------------------------------------------------------------------------

							//thirret funksioni qe merr array me childed e EC dhe tipin e vet EC  dhe kthen nje pergjigje				
						/*	if ($session->Vars["level_0"]==ZONE_AUTHORING) {
							$resultArray2	= $authoringObj->MetaStatusCheck($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_type"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["con_id"],$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]['data']);//347-EL,  378-PR
						
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["color_i"]   		= $resultArray2['input']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["value_i"]  		= $resultArray2['input']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["percentage_i"]  	= $resultArray2['input']['percentage'];

							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["color_p"]   		= $resultArray2['process']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["value_p"]  		= $resultArray2['process']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["percentage_p"]  	= $resultArray2['process']['percentage'];

							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["color_r"]   		= $resultArray2['review']['color'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["value_r"]  		= $resultArray2['review']['value'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["percentage_r"]  	= $resultArray2['review']['percentage'];

							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["input"]  			= $resultArray2['input']['input'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["proccesing"]  		= $resultArray2['process']['process'];
							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["review"]  			= $resultArray2['review']['review'];
							}*/
							//------------------------------------------------------------------------------------------------------------------------------------------------------							

						
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]['data']);
						//WebApp::addVar("Grid_AuthorNav_Nivel3".$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["key2"]  ,$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]);
						WebApp::addVar("Grid_AuthorNav_Nivel3".$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"]  ,$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]);
					 
					 
						//sa femije ka niveli i 2 
						$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nr_child"]  =$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["AllRecs"];
					 
					 
					  $key2++;
					  $k2++;
					  
                      $rs2->MoveNext();
                     }
            
            // }//if show livel II
			//niveli II --------------------------------------------------------------------------------------------------------------------
				
			//ketu bej kontrollin e statuseve te EC --niveli i 1-----------------------------------------------------------------------------------------------------

				//thirret funksioni qe merr array me childed e PR dhe tipin e vet PR  dhe kthen nje pergjigje					
				/*if ($session->Vars["level_0"]==ZONE_AUTHORING) {
				$resultArray3	= $authoringObj->MetaStatusCheck($Grid_AuthorNav["data"][$k1]["ci_type"],$Grid_AuthorNav["data"][$k1]["con_id"],$Grid_AuthorNav["data"][$k1]["nivel2"]['data']);//347-EL,  378-PR
				
				$Grid_AuthorNav["data"][$k1]["color_i"]   			= $resultArray3['input']['color'];
				$Grid_AuthorNav["data"][$k1]["value_i"]  			= $resultArray3['input']['value'];
				$Grid_AuthorNav["data"][$k1]["percentage_i"]  		= $resultArray3['input']['percentage'];
				
				
				$Grid_AuthorNav["data"][$k1]["color_p"]   			= $resultArray3['process']['color'];
				$Grid_AuthorNav["data"][$k1]["value_p"]  			= $resultArray3['process']['value'];
				$Grid_AuthorNav["data"][$k1]["percentage_p"]  		= $resultArray3['process']['percentage'];

				$Grid_AuthorNav["data"][$k1]["color_r"]   			= $resultArray3['review']['color'];
				$Grid_AuthorNav["data"][$k1]["value_r"]  			= $resultArray3['review']['value'];
				$Grid_AuthorNav["data"][$k1]["percentage_r"]  		= $resultArray3['review']['percentage'];
				
				$Grid_AuthorNav["data"][$k1]["input"]  				= $resultArray3['input']['input'];
				$Grid_AuthorNav["data"][$k1]["proccesing"]  		= $resultArray3['process']['process'];
				$Grid_AuthorNav["data"][$k1]["review"]  			= $resultArray3['review']['review'];
				}*/
				
				//------------------------------------------------------------------------------------------------------------------------------------------------------							
				
				
				
			$Grid_AuthorNav["data"][$k1]["nivel2"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]['data']);
			//WebApp::addVar("Grid_AuthorNav_Nivel2".$Grid_AuthorNav["data"][$k1]["key1"] ,$Grid_AuthorNav["data"][$k1]["nivel2"]);
			WebApp::addVar("Grid_AuthorNav_Nivel2".$Grid_AuthorNav["data"][$k1]["keyIden"] ,$Grid_AuthorNav["data"][$k1]["nivel2"]);
			
			//sa femije ka niveli i 1 
			$Grid_AuthorNav["data"][$k1]["nr_child"]  =$Grid_AuthorNav["data"][$k1]["nivel2"]["AllRecs"];
			
			
			$key1++;
			$k1++;
			 
             $rs1->MoveNext();
            }
    //niveli I --------------------------------------------------------------------------------------------------------------------
 
    //kapim atributet e imazheve qe kemi ne array -------------------------------------------------------------------------------------------

    $Grid_AuthorNav["AllRecs"] = COUNT($Grid_AuthorNav["data"]);
	WebApp::addVar("showColor",$showColor);



// echo "<textarea>";
// print_r($Grid_AuthorNav);
// echo "</textarea>";

    RETURN $Grid_AuthorNav;
}

?>
