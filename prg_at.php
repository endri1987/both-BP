<?php

IF (ISSET($_GET["uni"]) AND ($_GET["uni"] != "")) {

	INCLUDE (dirname(__FILE__)."/config/const.Paths.php");
	INCLUDE (dirname(__FILE__)."/config/const.DB.php");
	INCLUDE (dirname(__FILE__)."/config/const.Config.php");

	INCLUDE (EASY_PATH."web_app/database/class.Connection.php");
	
	$cnn = new Connection;
	$last_state_Sql = "SELECT ses_userid FROM transition WHERE ID_S = '" . $_GET["uni"] . "' AND tool_id in (0) AND process_id in (0)";
	$result = mysql_query($last_state_Sql);
	IF (!$result) {
		RETURN;
	} else {	
		
		$row = mysql_fetch_assoc($result);		
		$ses_userid	= $row["ses_userid"];

		$cis		= $_GET["cis"]*1;
		if (isset($_GET["cid"])) {
					$cid = $_GET["cid"]*1;
		}  else 	$cid = $cis;
		
		
		if (isset($_GET["idElC"])) {
					$idElCkey = $_GET["idElC"]*1;	
		}  else 	$idElCkey = $cis;
		
		
		
		$firstTime	= $_GET["frst"];
		$insetStat  = 'INSERT INTO z_analytics_progress (ses_userid,	lecture_id,	contentId,	cirel,uni,nr_tick,nr_count,nr_download,date_created) 
						 VALUES ("'.$ses_userid.'","'.$idElCkey.'","'.$cis.'","'.$cid.'","'.mysql_real_escape_string($_GET["uni"]).'",1,0,0,now()) ON DUPLICATE KEY UPDATE nr_tick=nr_tick+1;';
		mysql_query($insetStat);
		

		if ($firstTime=='1') {
			$updDuration = "UPDATE z_analytics_progress SET nr_count = nr_count+1 WHERE ses_userid = '".$ses_userid."' AND contentId = '".$cis."' AND cirel = '".$cid."' AND uni = '".mysql_real_escape_string($_GET["uni"])."'";
			mysql_query($updDuration);		
		}
		
		if ($firstTime=='2') {
			$updDuration = "UPDATE z_analytics_progress SET nr_download = nr_download+1,nr_count = nr_count+1 WHERE ses_userid = '".$ses_userid."' AND contentId = '".$cis."' AND cirel = '".$cid."' AND uni = '".mysql_real_escape_string($_GET["uni"])."'";
			mysql_query($updDuration);		
		}
				
		$updDuration = "UPDATE z_analytics_progress SET duration = nr_tick*".ANALYTICS_TIMER." WHERE ses_userid = '".$ses_userid."' AND contentId = '".$cis."' AND cirel = '".$cid."' AND uni = '".mysql_real_escape_string($_GET["uni"])."'";
		mysql_query($updDuration);
	}
}

?>