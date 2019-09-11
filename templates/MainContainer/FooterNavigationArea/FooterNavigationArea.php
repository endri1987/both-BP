<?
function FooterNavigationArea_onRender() {
	global $session,$obj_siInRegion;

	$zone_id = 18;
	$zone_mode = "single";
	include_once ASP_FRONT_PATH."php/find.region.SI.php";
	$obj_siInRegion = new siInRegionS($zone_id,$zone_mode);
}
?>