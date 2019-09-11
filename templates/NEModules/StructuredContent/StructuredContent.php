<?php
function StructuredContent_onRender() 
{
  global $session,$event;
  extract($event->args);

  INCLUDE(ASP_FRONT_PATH."nems/StructuredContent/StructuredContent_onRender.php");
  /*echo "<textarea>";
	print_r($Grid_StructuredContent);
	echo "</textarea>";*/
/*	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

  		$objectNemProperties = unserialize(base64_decode(WebApp::findNemProp($session->Vars["idstemp"])));


		echo "<textarea>";
	print_r($objectNemProperties);
	echo "</textarea>";

	//[store_list_input] => [{"itemID":5,"title":"Item 3","description":"Description for item 4550210655092736","content":"adfasdfasdfasdfsadfsafsa","active":false},{"itemID":1,"title":"Item 2","description":"Description for item 6215899277164544","active":false},{"itemID":6,"title":"Über uns","description":"","active":false},{"itemID":3,"title":"Aktuell","description":"","active":false},{"itemID":2,"title":"Item 1784326859522048","description":"Description for item 1784326859522048","active":false}]

        
 
		$template_id_sel=$objectNemProperties["templateID"];
		
		if ($template_id_sel!="" && $template_id_sel>0) {
			//selektohet template ----------------------------------------------------------------------------------------------------
			$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id_sel."'";
			$rs = WebApp::execQuery($sql_select);
			IF (!$rs->EOF() AND mysql_errno() == 0)
				$inc_modul_template = $rs->Field("template_box");
		}
	}*/  
	//  echo "<textarea>";
	// print_r($Grid_StructuredContent);
	// echo "</textarea>";

 }
?>