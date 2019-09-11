<?
function SearchModule_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

function SearchModule_onRender() 
{
	global $session,$event;
	extract($event->args);

    INCLUDE(ASP_FRONT_PATH."nems/SearchModule/SearchModule.php");
}

?>