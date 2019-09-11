<?php
class generalFunctionality
{

	/*****************************************************
	*** CONSTRUCTOR OF THE CLASS ****************
	******************************************************/
	function generalFunctionality () {}
	

	function secondsToTime($seconds,$fullFormat = "ms")
	{
		// extract hours
		$obj = array(
			"h" => 0,
			"m" => 0,
			"s" => 0,
		);
		
		$hours = floor($seconds / (60 * 60));
		// extract minutes
		$divisor_for_minutes = $seconds % (60 * 60);
		$minutes = floor($divisor_for_minutes / 60);

		// extract the remaining seconds
		$divisor_for_seconds = $divisor_for_minutes % 60;
		$seconds = ceil($divisor_for_seconds);

		// return the final array
		$obj = array(
			"h" => (int) $hours,
			"m" => (int) $minutes,
			"s" => (int) $seconds,
		);
		
		if (strlen($obj["h"])==0)	$obj["h"] = "00";
		if (strlen($obj["m"])==0)	$obj["m"] = "00";
		if (strlen($obj["s"])==0)	$obj["s"] = "00";

		if (strlen($obj["h"])==1)	$obj["h"] = "0".$obj["h"];
		if (strlen($obj["m"])==1)	$obj["m"] = "0".$obj["m"];
		if (strlen($obj["s"])==1)	$obj["s"] = "0".$obj["s"];
		
		if ($fullFormat == "yes") {
			
			//if ($obj["h"]!="00")			
					return $obj["h"].":".$obj["m"].":".$obj["s"];
			//else	return $obj["m"].":".$obj["s"];
		} elseif ($fullFormat == "ms") {
			
			return $obj["h"].":".$obj["m"];
		} else {
			return $obj["m"].":".$obj["s"];
		}
		
	}
	
	
	function getPlaceholdersToReplace($htmlToParse) 
	{	

		$content_html = (string)$contentTemplateTI;
		$contentTemplateContainerTI =  $contentTemplateTI;
		$regex_var_templ ="#\[\[([^\]]*)\]\]#is";		//([\]]*)
		if (preg_match_all($regex_var_templ, $htmlToParse, $matches_var_templ)) {
			
			$i=0;
			while ($i<count($matches_var_templ[1])) {
				$tagToBeReplaced 	= "#\[\[".$matches_var_templ[1][$i]."\]\]#";
				$tagToReplace 	= "{{".$matches_var_templ[1][$i]."}}";

				$htmlToParse=preg_replace($tagToBeReplaced,$tagToReplace,$htmlToParse);
				$i++;
			}
		}
		return $htmlToParse;
	}	
}	
?>	