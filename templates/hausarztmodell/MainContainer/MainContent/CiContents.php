<?
require_once(INCLUDE_AJAX_PATH."/CiManagerFe.class.php");
function CiContents_onRender() {
	global $session,$event;
	$workingCi = new CiManagerFe($session->Vars["contentId"],$session->Vars["lang"]);
	$workingCi->getMainDocumentCiToGrid();		
}
?>