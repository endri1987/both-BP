<?
    //selektohet template ----------------------------------------------------------------------------------------------------
      $AuthorNavigation_TEMPLATE = "";
      $node_family_id_sel     = "";
      $template_id_sel        = "";
      $template_id_mob_sel    = "";
	  $force_expanded		  = "N";
	  $profile_step         	= "1";

      $objNem = rand(100,100);
      
      IF (ISSET($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") 
         {
		//$prop_arr = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));
		$prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);


			$objNemArr = explode("-",$session->Vars["idstemp"]);
			$objNem =$objNemArr[4];


          IF (ISSET($prop_arr["zone_id"]) AND ($prop_arr["zone_id"] != ""))
             {
              $zone_id_sel = $prop_arr["zone_id"];
             }
             
 


          IF (ISSET($prop_arr["zone_id"]) AND ($prop_arr["zone_id"] != ""))
             {
              $zone_id_sel = $prop_arr["zone_id"];
             }
                         
          $slogan_title = "Program Structure";
          IF (ISSET($prop_arr["slogan_title"]) AND ($prop_arr["slogan_title"] != ""))
             {
              $slogan_title = $prop_arr["slogan_title"];
             }
                                 
             


          IF (ISSET($prop_arr["node_family_id"]) AND ($prop_arr["node_family_id"] != ""))
             {
              $node_family_id_sel = $prop_arr["node_family_id"];
             }

          IF (ISSET($prop_arr["template_id"]) AND ($prop_arr["template_id"] != ""))
             {
              $template_id_sel = $prop_arr["template_id"];
             }

          IF (ISSET($prop_arr["templateID"]) AND ($prop_arr["templateID"] != ""))
             {
              $template_id_sel = $prop_arr["templateID"];
             }

          IF (ISSET($prop_arr["template_id_mob"]) AND ($prop_arr["template_id_mob"] != ""))
             {
              $template_id_mob_sel = $prop_arr["template_id_mob"];
             }
			
		   IF (ISSET($prop_arr["force_expanded"]) AND ($prop_arr["force_expanded"] != ""))
             {
              $force_expanded = $prop_arr["force_expanded"];
             }			
			 
			
		   IF (ISSET($prop_arr["profile_step_id"]) AND ($prop_arr["profile_step_id"] != ""))
             {
              $profile_step = $prop_arr["profile_step_id"];
             }			
         }



		WebApp::addVar("slogan_title", $slogan_title);
		


	$slogan_description_show	= "no";
	$slogan_description 		= "";
	$slogan_icon_show 			= "no";
	$slogan_icon 				= "";
	$help_show 					= "no";
	$help_title 				= "";
	$help_description 			= "";
	

	if (isset($prop_arr["slogan_description_show"]) && $prop_arr["slogan_description_show"] != "")
		$slogan_description_show = $prop_arr["slogan_description_show"];
	if (isset($prop_arr["slogan_description"]) && $prop_arr["slogan_description"] != "")
		$slogan_description = $prop_arr["slogan_description"];
	if (isset($prop_arr["slogan_icon_show"]) && $prop_arr["slogan_icon_show"] != "")
		$slogan_icon_show = $prop_arr["slogan_icon_show"];
	if (isset($prop_arr["slogan_icon"]) && $prop_arr["slogan_icon"] != "")
		$slogan_icon = $prop_arr["slogan_icon"];
	if (isset($prop_arr["help_show"]) && $prop_arr["help_show"] != "")
		$help_show = $prop_arr["help_show"];
	if (isset($prop_arr["help_title"]) && $prop_arr["help_title"] != "")
		$help_title = $prop_arr["help_title"];
	if (isset($prop_arr["help_description"]) && $prop_arr["help_description"] != "")
		$help_description = $prop_arr["help_description"];


	WebApp::addVar("slogan_description_show",	"$slogan_description_show");
	WebApp::addVar("slogan_description",		"$slogan_description");
	WebApp::addVar("slogan_icon_show",			"$slogan_icon_show");
	WebApp::addVar("slogan_icon",				"$slogan_icon");
	WebApp::addVar("help_show",					"$help_show");
	WebApp::addVar("help_title",				"$help_title");
	WebApp::addVar("help_description",			"$help_description");



     
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
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id."'";
			$rs = WebApp::execQuery($sql_select);
          IF (!$rs->EOF() AND mysql_errno() == 0)
             {
		//	echo $rs->Field("template_box");
			  $AuthorNavigation_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."AuthorNavigation/".$rs->Field("template_box")."\"/>";

              IF ($node_family_id_sel != "")
                 {
                  $Grid_AuthorNav = AuthorNavigation_Grid($node_family_id_sel,$force_expanded,$profile_step,$zone_id_sel);	
                   WebApp::addVar("Grid_AuthorNav_$objNem", $Grid_AuthorNav);
				  
                 }
             }
			
         }
      ELSE 
         {



          //formohet indeksi per TopNavigation --------------------------------------------------------------------------------------------
            IF (DEFINED("AUTHOR_TEMPLATE_TopNavigation") AND (AUTHOR_TEMPLATE_TopNavigation > 0))
               {
                //$node_family_id_sel         = '1';
                $Grid_AuthorNav = AuthorNavigation_Grid($node_family_id_sel,$force_expanded,$profile_step,$zone_id_sel);	
                WebApp::addVar("Grid_AuthorNav_$objNem", $Grid_AuthorNav);

				$AuthorNavigation_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."AuthorNavigation/".AUTHOR_TEMPLATE_TopNavigation.".html\"/>";
               }
          //formohet indeksi per TopNavigation --------------------------------------------------------------------------------------------
          
          //formohet indeksi per MainNavigation -------------------------------------------------------------------------------------------
            IF (DEFINED("AUTHOR_TEMPLATE_MainNavigation") AND (AUTHOR_TEMPLATE_MainNavigation > 0))
               {
               // $node_family_id_sel          = '0';
                $Grid_AuthorNav = AuthorNavigation_Grid($node_family_id_sel,$force_expanded,$profile_step,$zone_id_sel);	
                 WebApp::addVar("Grid_AuthorNav_$objNem", $Grid_AuthorNav);

				$AuthorNavigation_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."AuthorNavigation/".AUTHOR_TEMPLATE_MainNavigation.".html\"/>";
               }
          //formohet indeksi per MainNavigation -------------------------------------------------------------------------------------------
        
        
          //formohet indeksi per FooterNavigation -----------------------------------------------------------------------------------------
            IF (DEFINED("AUTHOR_TEMPLATE_FooterNavigation") AND (AUTHOR_TEMPLATE_FooterNavigation > 0))
               {
             //   $node_family_id_sel            = '2';
                $Grid_AuthorNav = AuthorNavigation_Grid($node_family_id_sel,$force_expanded,$profile_step,$zone_id_sel);	
                 WebApp::addVar("Grid_AuthorNav_$objNem", $Grid_AuthorNav);

				$AuthorNavigation_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."AuthorNavigation/".AUTHOR_TEMPLATE_FooterNavigation.".html\"/>";
               }
          //formohet indeksi per FooterNavigation -----------------------------------------------------------------------------------------
        
         }

          /* echo " $template_id-$node_family_id_sel,$force_expanded,$profile_step,$zone_id_sel-<textarea>";
				print_r($prop_arr);
				print_r($node_family_id_sel);
				print_r($kushti_node_family);
            echo "</textarea>";*/
      

         
        WebApp::addVar("node_family_id_sel", $node_family_id_sel);
       
      WebApp::addVar("AuthorNavigation_TEMPLATE", $AuthorNavigation_TEMPLATE);

      WebApp::addVar("objNem", $objNem);
   //selektohet template ----------------------------------------------------------------------------------------------------

	
?>
