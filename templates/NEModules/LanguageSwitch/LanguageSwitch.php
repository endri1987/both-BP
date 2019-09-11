 <?
function LanguageSwitch_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

function LanguageSwitch_onRender() 
{
    global $session,$event;
    extract($event->args);
    
    INCLUDE(ASP_FRONT_PATH."nems/LanguageSwitch/LanguageSwitch.php");
}

?>