<?
function UserLogin_onRender() {
	global $session;

	$zone_id = 28;
	$zone_mode = "single";
	include_once ASP_FRONT_PATH."php/find.region.SI.php";
	$obj_siInRegion = new siInRegionS($zone_id,$zone_mode,"no");

}
?>