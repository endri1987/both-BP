<?php
function send_email_form_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}


function send_email_form_onRender() 
{
  global $session,$event;
  extract($event->args);

  INCLUDE(ASP_FRONT_PATH."nems/popUp/send_email_form/send_email_form.php");
}

?>
