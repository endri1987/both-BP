<?php
function NavigationModule_eventHandler($event) 
{
	global $session,$event;
	extract($event->args);
}

INCLUDE(APP_PATH."templates/NEModules/NavigationModule/NavigationModule_grid.php");

function NavigationModule_onRender() 
{
	global $session;

    INCLUDE(APP_PATH."templates/NEModules/NavigationModule/AuthorNavigationFunc.php");



    
//    WebApp::addVar("parentKey", $actualLevelKey3);
//    WebApp::addVar("childKey", $actualLevelKey4);

	//home - shildet 1

}

?>
