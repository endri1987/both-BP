<?

function PrintEmailModule_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

function PrintEmailModule_onRender() 
{
    global $session,$event;
    extract($event->args);
    
    INCLUDE(ASP_FRONT_PATH."nems/PrintEmailModule/PrintEmailModule.php");

}

?>