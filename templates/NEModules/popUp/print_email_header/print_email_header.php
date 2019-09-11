<?
function print_email_header_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}


function print_email_header_onRender() 
{
	global $session,$event;
	extract($event->args);

    INCLUDE(ASP_FRONT_PATH."nems/popUp/print_email_header/print_email_header.php");
}

?>