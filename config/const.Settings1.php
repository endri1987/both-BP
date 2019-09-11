<?
//The constants defined here change the behaviour of the application

define("DEBUG", false);


//if this constant is true, the framework displays an alert
//each time that the function GoTo() is called	(for debug)
define("DEBUG_GOTO", false);
//define("DEBUG_GOTO", true);

//if this constant is true, the framework outputs the session
//variables as an HTML table (for debug)
//define("DEBUG_SESSION", false);
define("DEBUG_SESSION", false);

//if this constant is true, the framework outputs all the
//recordsets of the page and their contents (for debug)
//define("DEBUG_RECORDSETS", false);
define("DEBUG_RECORDSETS", false);

//if this constant is true, the framework outputs the tree
//structure of the templates of the page (for debug)
//define("DEBUG_TEMPLATES", false);
define("DEBUG_TEMPLATES", true);

//if this constant is true, the framework outputs information
//about the execution time of several processes (for debug)
define("EXECUTION_TIME_INFO", false);

//Global var $MENU_TYPE determines which kind of menu will be used.
//There 2 menu types, one that used JavaScript arrays to build the
//menus (hierMenus), and another menu that builds the menu directly
//in HTML code.
define("ARR_MENUS", 1);
define("HTML_MENUS", 2);
$MENU_TYPE = HTML_MENUS;

define("LNG1", "English");
// define("LNG2", "French");
//define("LNG3", "Deutsch");
//etc.

?>