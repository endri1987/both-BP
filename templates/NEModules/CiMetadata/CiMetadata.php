<?
function CiMetadata_onRender() 
{
	global $session,$event;
	extract($event->args);

    $objIdList =  $session->Vars["idstemp"];
	$objectPropList = unserialize(base64_decode(WebApp::findNemProp($objIdList)));		
 
    require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");
	$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);    
    
    if (isset($objectPropList["big_image"]) && $objectPropList["big_image"]=='on')
    { WebApp::addVar("show_big_image",'yes');}  
  

    
	//if (isset($objectPropList["extended"]) && $objectPropList["extended"]=='on')
   // { 
           
           
           
           
           
           
           
            $workingCi->getDocProperties();  
            $tmpData= $workingCi->properties_structured["DC"];              
            $tmpDataEx= $workingCi->properties_structured["EX"];  
            
            if(COUNT($tmpDataEx)>0){
                 $CiDataEx = array("data" => array(), "AllRecs" => "0");	              
                 $CiDataEx["data"][0]=$tmpDataEx;  
                 $CiDataEx["AllRecs"] = COUNT($CiDataEx["data"]);  
                 WebApp::addVar("CiMetaDataEx",$CiDataEx); 
             }
           
            $MainGridData = array("data" => array(), "AllRecs" => "0");	           
            $MainGridDataTemp = array_merge($tmpData, $tmpDataEx);
            
            if(COUNT($MainGridDataTemp)>0){
                $MainGridData["data"][0]=$MainGridDataTemp;             
                $MainGridData["AllRecs"] = COUNT($MainGridData["data"]); 
                WebApp::addVar("MainGridData",$MainGridData);
            }



/*

		$workingCi->parseDocumentToDisplayPreviewMode();	
		$MainGridData = $workingCi->properties_structured["DC"];
		
			if (isset($workingCi->properties_structured["EX"]))
				$MainGridData = array_merge($MainGridData, $workingCi->properties_structured["EX"]);
			if (isset($workingCi->properties_structured["EX"]))
				$MainGridData = array_merge($MainGridData, $workingCi->properties_structured["EX"]);

			while(list($key_descriptor,$value_descriptor) = each ($MainGridData)) {
				WebApp::addVar($key_descriptor,$value_descriptor);
			}	
			
			
			if (isset($workingCi->properties_structured["TiContent"])  && $workingCi->properties_structured["TiContent"]!="") {
				$gridD["data"][0]['templateHtml'] = $workingCi->properties_structured["TiContent"];
				$gridD["AllRecs"] = count($gridD["data"]);	
				WebApp::addVar("TemplateGrid", $gridD);		
			}	
*/







  /*  }   
    else {        
         $tmpData = $workingCi->getGeneralProperties();  
    }*/
    
    
    
    
    
	    
    /*$CiData = array("data" => array(), "AllRecs" => "0");	
    if(COUNT($tmpData)>0){
        $CiData["data"][0]=$tmpData;  
        $CiData["AllRecs"] = COUNT($CiData["data"]);      
        WebApp::addVar("CiMetaDataBase",$CiData);
    }*/
    

    
    
   
   //ew_title ew_source_author ew_abstract ew_imgbig ew_imgsmall ci_content ew_source creation_date scheduling_from show_content
        
    if (isset($objectPropList["content"]) && $objectPropList["content"]=='on' 
        && $workingCi->properties_structured["DC"]["ci_content"]!='')
        {WebApp::addVar("show_content",'yes'); }
     else {WebApp::addVar("show_content",'no'); }


    if (isset($objectPropList["title"]) && $objectPropList["title"]=='on' 
        && $workingCi->properties_structured["DC"]["ew_title"]!='')
        {WebApp::addVar("show_title",'yes'); }
     else {WebApp::addVar("show_title",'no'); }
     
    if (isset($objectPropList["abstract"]) && $objectPropList["abstract"]=='on' 
        && $workingCi->properties_structured["DC"]["ew_abstract"]!='')
        {WebApp::addVar("show_abstract",'yes'); }
     else {WebApp::addVar("show_abstract",'no'); }
     
     if (isset($objectPropList["author"]) && $objectPropList["author"]=='on' 
        && $workingCi->properties_structured["DC"]["ew_source_author"]!='')
        {WebApp::addVar("show_author",'yes'); }
     else {WebApp::addVar("show_author",'no'); }
             
    if (isset($objectPropList["sorce"]) && $objectPropList["sorce"]=='on' 
        && $workingCi->properties_structured["DC"]["ew_source"]!='')
        {WebApp::addVar("show_source",'yes'); }
     else {WebApp::addVar("show_source",'no'); }  
   
    if (isset($objectPropList["small_image"]) && $objectPropList["small_image"]=='on' 
         && $workingCi->properties_structured["DC"]["ew_imgsmall"]!='')
        {WebApp::addVar("show_small_image",'yes'); }
     else {WebApp::addVar("show_small_image",'no'); }  
   
    if (isset($objectPropList["big_image"]) && $objectPropList["big_image"]=='on' 
    && $workingCi->properties_structured["DC"]["ew_imgbig"]!='')
     {WebApp::addVar("show_big_image",'yes'); }
     else {WebApp::addVar("show_big_image",'no'); }     
     
     if (isset($objectPropList["scheduleDate"]) && $objectPropList["scheduleDate"]=='on' 
        && $workingCi->properties_structured["DC"]["scheduling_from"]!='')
        {WebApp::addVar("show_scheduleDate",'yes'); }
     else {WebApp::addVar("show_scheduleDate",'no'); }  
     
     if (isset($objectPropList["creationDate"]) && $objectPropList["creationDate"]=='on' 
       && $workingCi->properties_structured["DC"]["creation_date"]!='')
        {WebApp::addVar("show_creationDate",'yes'); }
     else {WebApp::addVar("show_creationDate",'no'); }  
     

    
   /*echo("<textarea style=\"display:none\">");
    print_r($MainGridData);
    print_r($objectPropList);
    echo("</textarea>");   */
    
    
    $templateFileNameToInclude = "simple_template.html";
 
	//selektohet template -------------------------------------------------------------------------------------------------

	if (isset($objectPropList["templateType"]) && $objectPropList["templateType"] !='') 
		$template_id=$objectPropList["templateType"];

	if ($template_id!="") {

		$sql_select = "SELECT template_box FROM template_list WHERE template_id = '".$template_id."'";
		$rs = WebApp::execQuery($sql_select);
		IF (!$rs->EOF() AND mysql_errno() == 0) {
		   $templateFileNameToInclude = $rs->Field("template_box");
		   $templateToInclude = "yes";
		}
	}	
	//---------------------------------------------------------------------------------------------------------------------	
	$html_template_to_include = "<Include SRC=\"{{NEMODULES_PATH}}CiMetadata/".$templateFileNameToInclude."\"/>";
    
	WebApp::addVar("templateFileNameToInclude", $templateFileNameToInclude);		
	WebApp::addVar("html_template_to_include", $html_template_to_include);	    

	

    
    
}
?>