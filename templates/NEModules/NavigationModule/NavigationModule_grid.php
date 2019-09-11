<?
function NavigationModule_Grid($node_family_id_sel, $force_expandedV, $prop_arr) {
    global $session, $global_cache_dynamic, $cacheDyn;

    $lng_id = SUBSTR($session->Vars["lang"], -1);


    $nav_image_array = "";

    $force_expanded = $force_expandedV;//if Y show all node and subnode, N only on click show sub node
    $filter = "id1";
    if($session->Vars["level_1"] == 0){
        $filter = "id2";
    }else if($session->Vars["level_2"] == 0){
        $filter = "id3";
    }else if($session->Vars["level_3"] == 0){
        $filter = "id4";
    }


    //class name ---------------------------------------
    $style_name_n1 = "nav_n1";
    $style_name_n1_sel = " nav_n1_sel";
    $style_name_n1_cur = " nav_n1_current";

    $style_name_n2 = "nav_n2";
    $style_name_n2_sel = " nav_n2_sel";
    $style_name_n2_cur = " nav_n2_current";

    $style_name_n3 = "nav_n3";
    $style_name_n3_sel = " nav_n3_sel";
    $style_name_n3_cur = " nav_n3_current";

    $style_name_n4 = "nav_n4";
    $style_name_n4_sel = " nav_n4_sel";
    $style_name_n4_cur = " nav_n4_current";

    $style_name_noclick = " nav_noclickable";
    //--------------------------------------------------
    
    $id0 = $prop_arr["workGroup"];
    $nivel_end = $prop_arr["nivel_end"];
    $nivel_start = $prop_arr["nivel_start"];
    $collectionMode = $prop_arr["collectionMode"];

    $sqlN = "";
    INCLUDE(APP_PATH . "templates/NEModules/NavigationModule/AuthorNavigation_sql.php");
    unset($Grid_AuthorNav);
    $Grid_AuthorNav = array("data" => array(), "AllRecs" => "0");


    //niveli I --------------------------------------------------------------------------------------------------------------------
    $nr = -1;
    $nr1 = 0;
    $nr2 = 0;
    $nr3 = 0;
    $nr4 = 0;

    $k1 = 0;
    $key1 = 1;


    $sql_nivel1 = $sqlN;
    
    
    if (isset($node_family_id_sel) && $node_family_id_sel >= 0 && $collectionMode == '0') {
        $kushti_node_family = " AND nivel.node_family_id = '" . $node_family_id_sel . "'";
    } else {
        $kushti_node_family = "";
    }


    $sql_nivel1 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel1);
    
   // echo "$collectionMode:collectionMode";

	if($collectionMode == '4'){
            
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " >= 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " = 0 ", $sql_nivel1);     
 

 	} else if($collectionMode == '0' || $collectionMode == '2'){
     
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " >= 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " = 0 ", $sql_nivel1);     
     
     
     /*  $nivel_end_sel_2 = "";
        $nivel_end_sel_3 = "";
        $nivel_end_sel_4 = "";

        IF ($nivel_end >= 2)
        {$nivel_end_sel_2 = ">";}

        IF ($nivel_end >= 3)
        {$nivel_end_sel_3 = ">";}

        IF ($nivel_end == 4) {
            $nivel_end_sel_4 = ">";
        }

        IF ($nivel_start == 1) {
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " " . $nivel_end_sel_2 . "= 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " " . $nivel_end_sel_3 . "= 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " " . $nivel_end_sel_4 . "= 0 ", $sql_nivel1);
        }
        IF ($nivel_start == 2) {
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " " . $nivel_end_sel_3 . "= 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " " . $nivel_end_sel_4 . "= 0 ", $sql_nivel1);
        }
        IF ($nivel_start == 3) {
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " > 0 ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " " . $nivel_end_sel_4 . "= 0 ", $sql_nivel1);
        }
        IF ($nivel_start == 4) {
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " > 0 ", $sql_nivel1);
        }*/
    } else if ($collectionMode == '1') {
        //
        if($session->Vars["level_1"] == 0){
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " = 0 ", $sql_nivel1);
        }else if($session->Vars["level_2"] == 0){
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " = ".$session->Vars["level_1"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " = 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " = 0 ", $sql_nivel1);
        }else if($session->Vars["level_3"] == 0){
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " = ".$session->Vars["level_1"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " = ".$session->Vars["level_2"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " > 0  ", $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " = 0 ", $sql_nivel1);
        }else if($session->Vars["level_4"] == 0){
            $sql_nivel1 = str_ireplace("{{id_firstNivel}}",  " = ".$session->Vars["level_1"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_secondNivel}}", " = ".$session->Vars["level_2"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_thirdNivel}}",  " = ".$session->Vars["level_3"], $sql_nivel1);
            $sql_nivel1 = str_ireplace("{{id_fourthNivel}}", " > 0 ", $sql_nivel1);
        }else if($session->Vars["level_4"] > 0){
            return array();
        }

    }







//10,1,2,3,4
    $showColor = "n";
    //if the user logged has the write right he can see the navigation status color


    $rs1 = WebApp::execQuery($sql_nivel1);
		/*	echo "<textarea>NIVELI IPARE";
			print_r($rs1);
			echo '</textarea>'; */   
    
    
    
    WHILE (!$rs1->EOF()) {
        $id1 = $rs1->Field("id1");
        $id2 = $rs1->Field("id2");
        $id3 = $rs1->Field("id3");
        $id4 = $rs1->Field("id4");
        $label = $rs1->Field("label");
        $isExpanded = $rs1->Field("isExpanded");
        $clickable = $rs1->Field("clickable");
        $with_https = $rs1->Field("with_https");


        $class_style_sel = $style_name_n1;
        IF ($session->Vars["level_1"] == $id1) {
            $class_style_sel .= $style_name_n1_sel;

            IF ($session->Vars["level_2"] == 0) {
                $class_style_sel .= $style_name_n1_cur;
            }
        }

        $imageSm_id = $rs1->Field("imageSm_id");
        $imageSm_id_name = $rs1->Field("imageSm_id_name");
        $imageSm_id_node = $rs1->Field("imageSm_id_node");
        $imageSm_id_node_name = $rs1->Field("imageSm_id_name_node");

        $con_id = $rs1->Field("con_id");
        $ci_title = $rs1->Field("ci_title");
        $ci_description = $rs1->Field("ci_description");

        $title = $rs1->Field("title");
        $filename = $rs1->Field("filename");

        $ci_type = $rs1->Field("ci_type");

        $nr = $nr + 1;
        $nr1 = $nr1 + 1;

        $Grid_AuthorNav["data"][$k1]["key1"] = $key1;
        $Grid_AuthorNav["data"][$k1]["nivel"] = 1;
        $Grid_AuthorNav["data"][$k1]["id0"] = $id0;
        $Grid_AuthorNav["data"][$k1]["id1"] = $id1;
        $Grid_AuthorNav["data"][$k1]["id2"] = $id2;
        $Grid_AuthorNav["data"][$k1]["id3"] = $id3;
        $Grid_AuthorNav["data"][$k1]["id4"] = $id4;
        $Grid_AuthorNav["data"][$k1]["label"] = $label;
        $Grid_AuthorNav["data"][$k1]["con_id"] = $con_id;
        $Grid_AuthorNav["data"][$k1]["isExpanded"] = $isExpanded;
        $Grid_AuthorNav["data"][$k1]["clickable"] = $clickable;
        $Grid_AuthorNav["data"][$k1]["ci_type"] = $ci_type;
        $Grid_AuthorNav["data"][$k1]["ci_description"] = $ci_description;
        $Grid_AuthorNav["data"][$k1]["ci_title"] = $ci_title;

        $Grid_AuthorNav["data"][$k1]["keyIden"] = $id0 . "_" . $id1 . "_" . $id2 . "_" . $id3 . "_" . $id4;
        //imazhi --------------------------------------------------------------------------------------------------------------
        $Grid_AuthorNav["data"][$k1]["image_id"] = "";
        $Grid_AuthorNav["data"][$k1]["image_tag"] = "";
        $Grid_AuthorNav["data"][$k1]["image_src"] = "";
        $Grid_AuthorNav["data"][$k1]["image_id_node"] = "";
        $Grid_AuthorNav["data"][$k1]["image_tag_node"] = "";
        $Grid_AuthorNav["data"][$k1]["image_src_node"] = "";
        $Grid_AuthorNav["data"][$k1]["image_w"] = "";
        $Grid_AuthorNav["data"][$k1]["image_h"] = "";

        if (defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP == "Y") {
            $Grid_AuthorNav["data"][$k1]["boostrap_class"] = $rs1->Field("boostrap_class");
            $Grid_AuthorNav["data"][$k1]["boostrap_ico"] = $rs1->Field("boostrap_ico");
        }

        IF ($imageSm_id > 0) {
            $nav_image_array[$k1] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij

            IF ($global_cache_dynamic == "Y") {
                $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
            } ELSE {
                $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id;
            }

            $Grid_AuthorNav["data"][$k1]["image_id"] = $imageSm_id;
            $Grid_AuthorNav["data"][$k1]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
            $Grid_AuthorNav["data"][$k1]["image_src"] = $image_src_sel;
            CiManagerFe::get_SL_CACHE_INDEX($imageSm_id, "","");
        }
        IF ($imageSm_id_node > 0) {
            $nav_image_array[$k1] = $imageSm_id_node; //mbajme id e imazhit per te selektuar me vone atributet e tij

            IF ($global_cache_dynamic == "Y") {
                $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id_node, $imageSm_id_node_name);
            } ELSE {
                $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id_node;
            }

            $Grid_AuthorNav["data"][$k1]["image_id_node"] = $imageSm_id_node;
            $Grid_AuthorNav["data"][$k1]["image_tag_node"] = '<img src="' . $image_src_sel . '" border="0" />';
            $Grid_AuthorNav["data"][$k1]["image_src_node"] = $image_src_sel;
            CiManagerFe::get_SL_CACHE_INDEX($imageSm_id_node, "","");
        }
        //imazhi --------------------------------------------------------------------------------------------------------------

        $Grid_AuthorNav["data"][$k1]["index1"] = $nr1;
        $Grid_AuthorNav["data"][$k1]["index2"] = 0;
        $Grid_AuthorNav["data"][$k1]["index3"] = 0;
        $Grid_AuthorNav["data"][$k1]["index4"] = 0;

        //per stilin ----------------------------------------------------------------------------------------------------------
        $Grid_AuthorNav["data"][$k1]["class_style"] = $class_style_sel;
        //---------------------------------------------------------------------------------------------------------------------
        //clickable -----------------------------------------------------------------------------------------------------------
        IF ($clickable == "Y") {
            IF ($global_cache_dynamic == "Y") {
                $Grid_AuthorNav["data"][$k1]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
            } ELSE {
                $Grid_AuthorNav["data"][$k1]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=" . $id0 . "," . $id1 . "," . $id2 . "," . $id3 . "," . $id4 . ")')";
            }
        } ELSE {
            $Grid_AuthorNav["data"][$k1]["link"] = "javascript:void(0)";
            $Grid_AuthorNav["data"][$k1]["class_style"] .= $style_name_noclick;
        }
        //---------------------------------------------------------------------------------------------------------------------
        //shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
        $show_nivel2 = "N";
        $Grid_AuthorNav["data"][$k1]["display"] = " display:none;";
        IF ($session->Vars["level_1"] == $id1 && $$filter == 0) {
            $show_nivel2 = "Y";
            $Grid_AuthorNav["data"][$k1]["display"] = " display:block;";
        }
        IF ($force_expanded == "1" && $session->Vars["level_1"] == $id1) {
            $show_nivel2 = "Y";
            $Grid_AuthorNav["data"][$k1]["display"] = " display:block;";
        }
        //---------------------------------------------------------------------------------------------------------------------			
        $k2 = 0;
        $key2 = 1;
        //percaktohet sub Grida per nivelin e dyte te navigimit
        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"] = array();


        //niveli II ----------------------------------------------------------------------------------------------------------
        $nr2 = 0;

        $kushti_node_family = "";
        $sql_nivel2 = $sqlN;

//2,12,0,0,0
        $sql_nivel2 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel2);
        $sql_nivel2 = str_ireplace("{{id_firstNivel}}", " = '" . $id1 . "' ", $sql_nivel2);
        $sql_nivel2 = str_ireplace("{{id_secondNivel}}", " > '0' ", $sql_nivel2);
        $sql_nivel2 = str_ireplace("{{id_thirdNivel}}", " = '0' ", $sql_nivel2);
        $sql_nivel2 = str_ireplace("{{id_fourthNivel}}", " = '0' ", $sql_nivel2);


        $rs2 = WebApp::execQuery($sql_nivel2);
       
        WHILE (!$rs2->EOF()) {
            $id1 = $rs2->Field("id1");
            $id2 = $rs2->Field("id2");
            $id3 = $rs2->Field("id3");
            $id4 = $rs2->Field("id4");
            $label = $rs2->Field("label");
            $isExpanded = $rs2->Field("isExpanded");
            $clickable = $rs2->Field("clickable");
            $with_https = $rs2->Field("with_https");

            $class_style_sel2 = $style_name_n2;
            IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2)) {
                $class_style_sel2 .= $style_name_n2_sel;

                IF ($session->Vars["level_3"] == 0) {
                    $class_style_sel2 .= $style_name_n2_cur;
                }
            }


            $imageSm_id = $rs2->Field("imageSm_id");
            $imageSm_id_name = $rs2->Field("imageSm_id_name");
            $imageSm_id_node = $rs2->Field("imageSm_id_node");
            $imageSm_id_node_name = $rs2->Field("imageSm_id_name_node");

            $ci_title = $rs2->Field("ci_title");
            $ci_description = $rs2->Field("ci_description");
            $con_id = $rs2->Field("con_id");
            $ci_title = $rs2->Field("ci_title");
            $ci_description = $rs2->Field("ci_description");

            $title = $rs2->Field("title");
            $filename = $rs2->Field("filename");

            //get input ,proccesing, review
            $input = $rs2->Field("input");
            $proccesing = $rs2->Field("proccesing");
            $review = $rs2->Field("review");
            $ci_type = $rs2->Field("ci_type");


            $nr = $nr + 1;
            $nr2 = $nr2 + 1;

            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel"] = 2;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["key2"] = $key2;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id0"] = $id0;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id1"] = $id1;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id2"] = $id2;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id3"] = $id3;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["id4"] = $id4;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["label"] = $label;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["con_id"] = $con_id;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["isExpanded"] = $isExpanded;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["clickable"] = $clickable;

            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_description"] = $ci_description;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_title"] = $ci_title;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["ci_type"] = $ci_type;

            //display sub nivel

            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"] = $id0 . "_" . $id1 . "_" . $id2 . "_" . $id3 . "_" . $id4;


            //	echo $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"]."<br>";

            //imazhi --------------------------------------------------------------------------------------------------------------
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id_node"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag_node"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src_node"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_w"] = "";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_h"] = "";

            if (defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP == "Y") {
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["boostrap_class"] = $rs2->Field("boostrap_class");
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["boostrap_ico"] = $rs2->Field("boostrap_ico");
            }


            IF ($imageSm_id > 0) {
                $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij

                IF ($global_cache_dynamic == "Y") {
                    $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                } ELSE {
                    $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id;
                }
                CiManagerFe::get_SL_CACHE_INDEX($imageSm_id, "","");	

                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id"] = $imageSm_id;
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src"] = $image_src_sel;
            }
            IF ($imageSm_id_node > 0) {
                $nav_image_array[$nr] = $imageSm_id_node; //mbajme id e imazhit per te selektuar me vone atributet e tij

                IF ($global_cache_dynamic == "Y") {
                    $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id_node, $imageSm_id_node_name);
                } ELSE {
                    $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id_node;
                }
                
                CiManagerFe::get_SL_CACHE_INDEX($imageSm_id_node, "","");

                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_id"] = $imageSm_id_node;
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["image_src"] = $image_src_sel;
            }
            //imazhi --------------------------------------------------------------------------------------------------------------

            IF ($imageSm_id > 0) {
                $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
            }

            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index1"] = $nr1;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index2"] = $nr2;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index3"] = 0;
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["index4"] = 0;

            //per stilin ----------------------------------------------------------------------------------------------------------
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["class_style"] = $class_style_sel2;

            //---------------------------------------------------------------------------------------------------------------------
            //clickable -----------------------------------------------------------------------------------------------------------
            IF ($clickable == "Y") {
                IF ($global_cache_dynamic == "Y") {
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                } ELSE {
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=" . $id0 . "," . $id1 . "," . $id2 . "," . $id3 . "," . $id4 . ")')";
                }
            } ELSE {
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["link"] = "javascript:void(0)";
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["class_style"] .= $style_name_noclick;
            }
            //---------------------------------------------------------------------------------------------------------------------
            //shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
            $show_nivel3 = "N";
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"] = " display:none;";
            IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2)) {
                $show_nivel3 = "Y";
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"] = " display:block;";
            }
            IF ($force_expanded == "1" && ($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2)) {
                $show_nivel3 = "Y";
                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["display"] = " display:block;";
            }

            //---------------------------------------------------------------------------------------------------------------------
            $k3 = 0;
            $key3 = 1;

            //percaktohet sub Grida per nivelin e dyte te navigimit
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"] = array();

            //IF ($show_nivel3 == "Y")
            //{	
            //niveli III ------------------------------------------------------------------------------------------------
            $nr3 = 0;

            $kushti_node_family = "";
            $sql_nivel3 = $sqlN;
            $sql_nivel3 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel3);
            $sql_nivel3 = str_ireplace("{{id_firstNivel}}", " = '" . $id1 . "' ", $sql_nivel3);
            $sql_nivel3 = str_ireplace("{{id_secondNivel}}", " = '" . $id2 . "' ", $sql_nivel3);
            $sql_nivel3 = str_ireplace("{{id_thirdNivel}}", " > '0' ", $sql_nivel3);
            $sql_nivel3 = str_ireplace("{{id_fourthNivel}}", " = '0' ", $sql_nivel3);

//2,12,0,0,0

            $rs3 = WebApp::execQuery($sql_nivel3);

         /*   echo "<textarea>$id1-$id2";
            print_r($rs3);
            echo '</textarea>'; */  

            WHILE (!$rs3->EOF()) {
                $id1 = $rs3->Field("id1");
                $id2 = $rs3->Field("id2");
                $id3 = $rs3->Field("id3");
                $id4 = $rs3->Field("id4");
                $label = $rs3->Field("label");
                $isExpanded = $rs3->Field("isExpanded");
                $clickable = $rs3->Field("clickable");
                $with_https = $rs3->Field("with_https");

                //get input ,proccesing, review
                $input = $rs3->Field("input");
                $proccesing = $rs3->Field("proccesing");
                $review = $rs3->Field("review");
                $ci_type = $rs3->Field("ci_type");

                $class_style_sel3 = $style_name_n3;
                IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3)) {
                    $class_style_sel3 .= $style_name_n3_sel;

                    IF ($session->Vars["level_4"] == 0) {
                        $class_style_sel3 .= $style_name_n3_cur;
                    }
                }

                $imageSm_id = $rs3->Field("imageSm_id");
                $imageSm_id_name = $rs3->Field("imageSm_id_name");
                $imageSm_id_node = $rs3->Field("imageSm_id_node");
                $imageSm_id_node_name = $rs3->Field("imageSm_id_name_node");

                $con_id = $rs3->Field("con_id");
                $ci_title = $rs3->Field("ci_title");
                $ci_description = $rs3->Field("ci_description");

                $title = $rs3->Field("title");
                $filename = $rs3->Field("filename");


                $displayRecord = "yes";

                if ($displayRecord == "yes") {

                    $nr = $nr + 1;
                    $nr3 = $nr3 + 1;

                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel"] = 3;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["key3"] = $key3;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id0"] = $id0;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id1"] = $id1;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id2"] = $id2;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id3"] = $id3;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["id4"] = $id4;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["label"] = $label;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["con_id"] = $con_id;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["isExpanded"] = $isExpanded;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["clickable"] = $clickable;

                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_description"] = $ci_description;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_title"] = $ci_title;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["ci_type"] = $ci_type;

                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["keyIden"] = $id0 . "_" . $id1 . "_" . $id2 . "_" . $id3 . "_" . $id4;
                    //imazhi ---------------------------------------------------------------------------------------------------------------
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id_node"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag_node"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src_node"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_w"] = "";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_h"] = "";


                    if (defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP == "Y") {
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["boostrap_class"] = $rs3->Field("boostrap_class");
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["boostrap_ico"] = $rs3->Field("boostrap_ico");
                    }

                    IF ($imageSm_id > 0) {
                        $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij

                        IF ($global_cache_dynamic == "Y") {
                            $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                        } ELSE {
                            $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id;
                        }
                        CiManagerFe::get_SL_CACHE_INDEX($imageSm_id, "","");

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id"] = $imageSm_id;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src"] = $image_src_sel;
                    }
                    IF ($imageSm_id_node > 0) {
                        $nav_image_array[$nr] = $imageSm_id_node; //mbajme id e imazhit per te selektuar me vone atributet e tij

                        IF ($global_cache_dynamic == "Y") {
                            $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id_node, $imageSm_id_node_name);
                        } ELSE {
                            $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id_node;
                        }

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_id"] = $imageSm_id_node;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["image_src"] = $image_src_sel;
                        CiManagerFe::get_SL_CACHE_INDEX($imageSm_id_node, "","");
                    }
                    //imazhi --------------------------------------------------------------------------------------------------------------

                    IF ($imageSm_id > 0) {
                        $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                    }

                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index1"] = $nr1;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index2"] = $nr2;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index3"] = $nr3;
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["index4"] = 0;

                    //per stilin ----------------------------------------------------------------------------------------------------------
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["class_style"] = $class_style_sel3;
                    //---------------------------------------------------------------------------------------------------------------------
                    //clickable -----------------------------------------------------------------------------------------------------------
                    IF ($clickable == "Y") {
                        IF ($global_cache_dynamic == "Y") {
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                        } ELSE {
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=" . $id0 . "," . $id1 . "," . $id2 . "," . $id3 . "," . $id4 . ")')";
                        }
                    } ELSE {
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["link"] = "javascript:void(0)";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["class_style"] .= $style_name_noclick;
                    }
                    //---------------------------------------------------------------------------------------------------------------------
                    //shfaq nivelin tjeter ------------------------------------------------------------------------------------------------
                    $show_nivel4 = "N";
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"] = " display:none;";
                    IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3)) {
                        $show_nivel4 = "Y";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"] = " display:block;";
                    }
                    IF ($force_expanded == "1" && ($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3)) {
                        $show_nivel4 = "Y";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["display"] = " display:block;";

                    }


                    //---------------------------------------------------------------------------------------------------------------------

                    $k4 = 0;
                    $key4 = 1;
                    //percaktohet sub Grida per nivelin e dyte te navigimit
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"] = array();


                    //niveli IV ----------------------------------------------------------------------------------------
                    $nr4 = 0;

                    $kushti_node_family = "";
                    $sql_nivel4 = $sqlN;
                    $sql_nivel4 = str_ireplace("{{kushti_node_family}}", $kushti_node_family, $sql_nivel4);
                    $sql_nivel4 = str_ireplace("{{id_firstNivel}}", " = '" . $id1 . "' ", $sql_nivel4);
                    $sql_nivel4 = str_ireplace("{{id_secondNivel}}", " = '" . $id2 . "' ", $sql_nivel4);
                    $sql_nivel4 = str_ireplace("{{id_thirdNivel}}", " = '" . $id3 . "' ", $sql_nivel4);
                    $sql_nivel4 = str_ireplace("{{id_fourthNivel}}", " > '0' ", $sql_nivel4);

                    $rs4 = WebApp::execQuery($sql_nivel4);


                    WHILE (!$rs4->EOF()) {
                        $id1 = $rs4->Field("id1");
                        $id2 = $rs4->Field("id2");
                        $id3 = $rs4->Field("id3");
                        $id4 = $rs4->Field("id4");
                        $label = $rs4->Field("label");
                        $isExpanded = $rs4->Field("isExpanded");
                        $clickable = $rs4->Field("clickable");
                        $with_https = $rs4->Field("with_https");
                        $ci_type = $rs4->Field("ci_type");
                        //get input ,proccesing, review
                        $input = $rs4->Field("input");
                        $proccesing = $rs4->Field("proccesing");
                        $review = $rs4->Field("review");

                        $class_style_sel4 = $style_name_n4;
                        IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3) AND ($session->Vars["level_4"] == $id4)) {
                            $class_style_sel4 .= $style_name_n4_sel;

                            IF ($session->Vars["level_4"] == 0) {
                                $class_style_sel4 .= $style_name_n4_cur;
                            }
                        }


                        $imageSm_id = $rs4->Field("imageSm_id");
                        $imageSm_id_name = $rs4->Field("imageSm_id_name");
                        $imageSm_id_node = $rs4->Field("imageSm_id");
                        $imageSm_id_node_name = $rs4->Field("imageSm_id_name");

                        $con_id = $rs4->Field("con_id");
                        $ci_title = $rs4->Field("ci_title");
                        $ci_description = $rs4->Field("ci_description");


                        $title = $rs4->Field("title");
                        $filename = $rs4->Field("filename");

                        $nr = $nr + 1;
                        $nr4 = $nr4 + 1;

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["nivel"] = 4;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id0"] = $id0;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id1"] = $id1;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id2"] = $id2;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id3"] = $id3;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["id4"] = $id4;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["label"] = $label;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["con_id"] = $con_id;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["isExpanded"] = $isExpanded;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["clickable"] = $clickable;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_type"] = $ci_type;

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_description"] = $ci_description;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["ci_title"] = $ci_title;


                        //imazhi --------------------------------------------------------------------------------------------------------------
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id_node"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag_node"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src_node"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_w"] = "";
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_h"] = "";


                        if (defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP == "Y") {
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["boostrap_class"] = $rs4->Field("boostrap_class");
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["boostrap_ico"] = $rs4->Field("boostrap_ico");
                        }


                        IF ($imageSm_id > 0) {
                            $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij

                            IF ($global_cache_dynamic == "Y") {
                                $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id, $imageSm_id_name);
                            } ELSE {
                                $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id;
                            }

                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id"] = $imageSm_id;
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src"] = $image_src_sel;
                        
                        	CiManagerFe::get_SL_CACHE_INDEX($imageSm_id, "","");
                        }
                        IF ($imageSm_id_node > 0) {
                            $nav_image_array[$nr] = $imageSm_id_node; //mbajme id e imazhit per te selektuar me vone atributet e tij

                            IF ($global_cache_dynamic == "Y") {
                                $image_src_sel = $cacheDyn->get_SlDocTitleToUrl($imageSm_id_node, $imageSm_id_node_name);
                            } ELSE {
                                $image_src_sel = APP_URL . 'show_image.php?file_id=' . $imageSm_id_node;
                            }

                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_id"] = $imageSm_id_node;
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_tag"] = '<img src="' . $image_src_sel . '" border="0" />';
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["image_src"] = $image_src_sel;
                        
                        	CiManagerFe::get_SL_CACHE_INDEX($imageSm_id_node, "","");
                        }
                        //imazhi --------------------------------------------------------------------------------------------------------------

                        IF ($imageSm_id > 0) {
                            $nav_image_array[$nr] = $imageSm_id; //mbajme id e imazhit per te selektuar me vone atributet e tij
                        }

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index1"] = $nr1;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index2"] = $nr2;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index3"] = $nr3;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["index4"] = $nr4;

                        //per stilin ----------------------------------------------------------------------------------------------------------


                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["class_style"] = $class_style_sel4;


                        //---------------------------------------------------------------------------------------------------------------------

                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["input"] = $input;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["proccesing"] = $proccesing;
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["review"] = $review;
                        //clickable -----------------------------------------------------------------------------------------------------------
                        IF ($clickable == "Y") {
                            IF ($global_cache_dynamic == "Y") {
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"] = $cacheDyn->get_CiTitleToUrl($con_id, $lng_id, $title, $filename, "", $with_https);
                            } ELSE {
                                $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"] = "javascript:GoTo('thisPage?event=none.ch_state(k=" . $id0 . "," . $id1 . "," . $id2 . "," . $id3 . "," . $id4 . ")')";
                            }
                        } ELSE {
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["link"] = "javascript:void(0)";
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["class_style"] .= $style_name_noclick;
                        }
                        $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["display"] = "";
                        IF (($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3) AND $session->Vars["level_4"] == $id4) {
                            $show_nivel4 = "Y";
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["display"] = " display:block;";
                        }
                        IF ($force_expanded == "1" && ($session->Vars["level_1"] == $id1) AND ($session->Vars["level_2"] == $id2) AND ($session->Vars["level_3"] == $id3) AND $session->Vars["level_4"] == $id4) {
                            $show_nivel4 = "Y";
                            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["data"][$k4]["display"] = " display:block;";

                        }
                        //---------------------------------------------------------------------------------------------------------------------

                        $key4++;
                        $k4++;

                        $rs4->MoveNext();
                    }


                    //niveli IV ------------------------------------------------------------------------------------------------------------------------------------------

                    //ketu bej kontrollin e statuseve te EL --niveli i 3-----------------------------------------------------------------------------------------------------

                    //thirret funksioni qe merr array me childed e El dhe tipin e vet EL  dhe kthen nje pergjigje				
   

                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]['data']);

					$idKey 		= $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["keyIden"];
					$gridData 	= $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"];
					WebApp::addVar("Grid_AuthorNav_Nivel4".$idKey,$gridData);
					WebApp::addVar("Grid_AuthorNav_".$idKey,$gridData);
 
		//echo "<br>$idKey-4<br>";
		/*	echo "<textarea>$idKey-4";
            print_r($gridData);
            echo '</textarea>';*/
                    //sa femije ka niveli i 3 
                    $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nr_child"] = $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["data"][$k3]["nivel4"]["AllRecs"];


                    $key3++;
                    $k3++;
                }

                $rs3->MoveNext();
            }
            //niveli III ------------------------------------------------------------------------------------------------


							$Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]['data']);
							
							$idKey 		= $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["keyIden"];
							$gridData 	= $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"];
							WebApp::addVar("Grid_AuthorNav_".$idKey,$gridData);
							WebApp::addVar("Grid_AuthorNav_Nivel3".$idKey,$gridData);
							



		///echo "<br>$idKey-3<br>";
		/*	echo "<textarea>";
            print_r($gridData);
            echo '</textarea>';*/

//            if($session->Vars['uni'] == '20170615102835192168118172864228'){
//                echo '<textarea>nivel3';
//                print_r($Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]);
//                echo '</textarea>';
//            }

            //sa femije ka niveli i 2 
            $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nr_child"] = $Grid_AuthorNav["data"][$k1]["nivel2"]["data"][$k2]["nivel3"]["AllRecs"];






            $key2++;
            $k2++;

            $rs2->MoveNext();
        }


        //niveli II --------------------------------------------------------------------------------------------------------------------

       	    $Grid_AuthorNav["data"][$k1]["nivel2"]["AllRecs"] = COUNT($Grid_AuthorNav["data"][$k1]["nivel2"]['data']);
			
			$idKey 		= $Grid_AuthorNav["data"][$k1]["keyIden"];
			$gridData 	= $Grid_AuthorNav["data"][$k1]["nivel2"];
			WebApp::addVar("Grid_AuthorNav_".$idKey,$gridData);
			WebApp::addVar("Grid_AuthorNav_Nivel2".$idKey,$gridData);

           /* echo $Grid_AuthorNav["data"][$k1]["keyIden"].'<textarea>';
            print_r($Grid_AuthorNav["data"][$k1]["nivel2"]);
            echo '</textarea>';*/
            
       /*    echo "<textarea>$idKey-2";
            print_r($gridData);
            echo '</textarea>'; */           
            
            
            
        //sa femije ka niveli i 1 
        $Grid_AuthorNav["data"][$k1]["nr_child"] = $Grid_AuthorNav["data"][$k1]["nivel2"]["AllRecs"];


        $key1++;
        $k1++;

        $rs1->MoveNext();
    }
    //niveli I --------------------------------------------------------------------------------------------------------------------


    $Grid_AuthorNav["AllRecs"] = COUNT($Grid_AuthorNav["data"]);
    
    
    
    WebApp::addVar("showColor", $showColor);



	$actualLevelKey4 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_".$session->Vars["level_2"]."_".$session->Vars["level_3"]."_0";
	$actualLevelKey3 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_".$session->Vars["level_2"]."_0_0";
	$actualLevelKey2 = $session->Vars["level_0"]."_".$session->Vars["level_1"]."_0_0_0";
	$actualLevelKey1 = $session->Vars["level_0"]."_0_0_0_0";
    $idKeyHome = $actualLevelKey1;
    
	WebApp::addVar("Grid_AuthorNav_".$idKeyHome,$Grid_AuthorNav);
    
            
         /*  echo "<textarea>$idKeyHome-1";
            print_r($Grid_AuthorNav);
            echo '</textarea>';  */
    
    if ($session->Vars["level_4"] > 0) {
    	$dpLevel = 4;
    	WebApp::addVar("childKey", $actualLevelKey4);
    	WebApp::addVar("parentKey", $actualLevelKey3);
    } elseif ($session->Vars["level_3"] > 0) {	
   	 	$dpLevel = 3;
    	 WebApp::addVar("childKey", $actualLevelKey4);
    	WebApp::addVar("parentKey", $actualLevelKey3);
    } elseif ($session->Vars["level_2"] > 0) {	
    	$dpLevel = 2;
    	 WebApp::addVar("childKey", $actualLevelKey3);
    	WebApp::addVar("parentKey", $actualLevelKey2);
    } elseif ($session->Vars["level_1"] > 0) {	
    	$dpLevel = 1;
    	 WebApp::addVar("childKey", $actualLevelKey2);
    	WebApp::addVar("parentKey", $actualLevelKey1);
    } else {
    	 WebApp::addVar("childKey", $actualLevelKey1);
    
    }






    RETURN $Grid_AuthorNav;
}

?>
