<?
function LocationModule_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}


function LocationModule_onRender() 
{
    global $session,$event;
    extract($event->args);

    INCLUDE(ASP_FRONT_PATH."nems/LocationModule/LocationModule.php");
}
?>