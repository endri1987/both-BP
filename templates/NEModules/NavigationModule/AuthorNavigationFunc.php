<?

global $session;

$force_expanded = "0";
$AuthorNavigation_TEMPLATE = 'default_template.html';

$objNem = rand(100, 100);

IF (ISSET($session->Vars["idstemp"]) && $session->Vars["idstemp"] != "") {

    $prop_arr = WebApp::clearNemAtributes($session->Vars["idstemp"]);

    $objNemArr = explode("-", $session->Vars["idstemp"]);
    $objNem = $objNemArr[4];

    IF (ISSET($prop_arr["familiesRelated"]) AND ($prop_arr["familiesRelated"] != "")) {
        $node_family_id_sel = $prop_arr["familiesRelated"];
    }

    IF (ISSET($prop_arr["templateID"]) AND ($prop_arr["templateID"] != "")) {
        $template_id = $prop_arr["templateID"];
    }

    IF (ISSET($prop_arr["force_expanded"]) AND ($prop_arr["force_expanded"] != "")) {
        $force_expanded = $prop_arr["force_expanded"];
    }

    if(count($prop_arr) > 0)
        foreach($prop_arr as $k => $v)
            if(!is_array($v))
                WebApp::addVar($k, $v);

	require_once(INC_PHP_AJAX."NemsManager.class.php");
	$grn = NemsManager::getFrontEndGeneralProperties($prop_arr);

/*echo "<textarea>";
print_r($prop_arr);
echo "</textarea>";*/


    if(isset($prop_arr['properties_show']))
	reset($prop_arr['properties_show']);
        foreach($prop_arr['properties_show'] as $val){
            switch($val){
                case 'nodeLabel':
                    WebApp::addVar("show_nodeLabel", "yes");
                    break;
                case 'mainContentTitle':
                    WebApp::addVar("show_mainContentTitle", "yes");
                    break;
                case 'mainContentAbstract':
                    WebApp::addVar("show_mainContentAbstract", "yes");
                    break;
                case 'mainContentThumbnail':
                    WebApp::addVar("show_mainContentThumbnail", "yes");
                    break;
                case 'nodeIcon':
                    WebApp::addVar("show_nodeIcon", "yes");
                    break;                
                case 'nodeImage':
                    WebApp::addVar("show_nodeImage", "yes");
                    break;
            }
        }

    if(isset($prop_arr['makeLink']))
	reset($prop_arr['makeLink']);
        foreach($prop_arr['makeLink'] as $val){
            switch($val){
                case 'nodeLabel':
                    WebApp::addVar("makeLink_nodeLabel", "yes");
                    break;
                case 'nodeImage':
                    WebApp::addVar("makeLink_nodeImage", "yes");
                    break;
                case 'mainContentTitle':
                    WebApp::addVar("makeLink_mainContentTitle", "yes");
                    break;
                case 'mainContentThumbnail':
                    WebApp::addVar("makeLink_mainContentThumbnail", "yes");
                    break;
            }
        }

    IF ($template_id != "") {
        $sql_select = "SELECT template_box FROM template_list WHERE template_id = '" . $template_id . "'";
        $rs = WebApp::execQuery($sql_select);
        IF (!$rs->EOF()) {

            $AuthorNavigation_TEMPLATE = $rs->Field("template_box");
            
            IF ($node_family_id_sel != "") {
                $Grid_AuthorNav = NavigationModule_Grid($node_family_id_sel, $force_expanded, $prop_arr);
                WebApp::addVar("Grid_AuthorNav_$objNem", $Grid_AuthorNav);
            }
        }
    }
}


WebApp::addVar("NavigationModule_TEMPLATE", "<Include SRC=\"{{NEMODULES_PATH}}NavigationModule/".$AuthorNavigation_TEMPLATE."\"/>");

WebApp::addVar("objNem", $objNem);


?>
