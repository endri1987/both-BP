<?
function FooterAreaPopup_onRender() {
	global $session;

	$zone_id = 20;
	$zone_mode = "single";
	include_once INCLUDE_PATH."find.region.SI.php";
	$obj_siInRegion = new siInRegionS($zone_id,$zone_mode);
}
?>