<?php
function ContactModule_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

function ContactModule_onLoad()
 {
  global $session,$event;
  extract($event->args);

  INCLUDE(ASP_FRONT_PATH."nems/ContactModule/ContactModule_onLoad.php");
 }

function ContactModule_onRender() 
{
  global $session,$event;
  extract($event->args);

  INCLUDE(ASP_FRONT_PATH."nems/ContactModule/ContactModule_onRender.php");
}

?>
