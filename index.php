
<?
$start_time = microtime(true);


$cachetime = 60*60;

$cachefile='';



if ($event->target=="none") { 
	WebApp::callFreeEvent($event);
}

	
if (isset($session->Vars["lang"])) {
    $lang_sel=strtoupper($session->Vars["lang"])."_Name";
    $name_lang=constant($lang_sel);
    if ($name_lang!="")
       $messg_file= TPL_PATH.$name_lang.".mesg";
    else
       $messg_file= TPL_PATH."messages.mesg";
}


if ($global_cache_dynamic=="Y") {
	WebApp::collectHtmlPage();
	WebApp::constructMasterTemplateHtmlPage($tpl_file,$head_file,$messg_file);
	echo $contentForm = WebApp::getHtmlPage();
} else {
	WebApp::constructMasterTemplateHtmlPage($tpl_file,$head_file,$messg_file);
}


        IF($cachefile!='' && $global_cache_dynamic == "Y")
        {
                $fp = fopen($cachefile, 'w');
                fwrite($fp, ob_get_contents());
                fclose($fp);
                ob_end_flush();
                
        }


?>