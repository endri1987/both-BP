<?
function MetatagTemplate_eventHandler($event) {
	global $session;


}

function MetatagTemplate_onRender() {
	 global $session;

	$all_meta="";
	$page_encoding=APP_ENCODING;

	$keywordMeta="";
	$keywordValues=array();
	$all_meta_array=array();


		$content_id = "";
		if (isset($session->Vars["doctype"]) && $session->Vars["doctype"]=='details')
			$content_id=$session->Vars["uid_abstract"];
		else
			$content_id=$session->Vars["contentId"];
		WebApp::addVar("cont_id", $content_id );
		$rs_def = WebApp::openRS("get_meta_el");
	
		$ind = 1;
		while (!$rs_def->EOF()) {

				$meta_id  		= $rs_def->Field("meta_id");
				$meta_name  	= $rs_def->Field("name");
				$value_default  = $rs_def->Field("value_default");
				$description 	= $rs_def->Field("description");
				$order_meta  	= $rs_def->Field("order_meta");
				$meta_value  	= $rs_def->Field("meta_value");
		

				
				$all_meta_def[$order_meta] = $description;
				
				$value_meta = "";
				if ($meta_value!="") {
					$value_meta .= $meta_value;
				} elseif ($value_default!="") {
					$value_meta = $value_default;
				}
				
				
				if ($meta_id==3) {
					$xml_lang = $value_meta;
				}	
				
				if ($meta_id=="2") 
					$page_encoding =$value_meta;				
				
				if ($value_meta!="") {
					$all_meta_values[$order_meta] = $value_meta;
				} 
				
				$ind++;

			$rs_def->MoveNext();
		
		}
/*
	echo "<div style=display:none>";
	print_r($all_meta_values);
	echo "</div>";
*/


		$all_meta = "";

		while (list($key,$meta_value)=each($all_meta_values)) {
			if ($meta_value!="") {
				$all_meta .= preg_replace("/%%value%%/",$meta_value,$all_meta_def[$key])."\n";	
			}
		
		}

	WebApp::addVar("all_meta", $all_meta );
	
	WebApp::addVar("xml_lang", $xml_lang );
	WebApp::addVar("page_encoding", $page_encoding );
	
	$session->Vars["xml_lang"] = "$xml_lang";
	if($_REQUEST["herapare"]=="po") {
		//WebApp::addVar("xhtt", "yes");
	}	
	
	
	
	WebApp::addVar("xml_lang", $xml_lang );
	WebApp::addVar("page_encoding", $page_encoding );
	
	$session->Vars["xml_lang"] = "$xml_lang";
	

 

	/*	kemi kater gjendje
	*

	*	1. fronti, i loguar ose jo, por ska autentifikim apache
	*
	*			thisMode == ""
	*

	*
	*	2. front_end me login autentifiaction apache

	*			

	*			thisMode == "", callMainW==callMainW,  toolbar==no

	*

	*	backoffice
	*
	*
	*	3. dritarja e madhe qe formon objektin e status barit, toolbarin, desktopin me ikonat dhe dritaren e aplikimit
	*
	*			thisMode == "_new", callMainW==callMainW,  toolbar==yes
	*
	*	4. aplikimi brenda dritares
	*
	*
	*			thisMode == "_new", callMainW==y,  toolbar==yes
	*
	**/
	

	

	$session->Vars["xhtt"] = "0";

	if($session->Vars["thisMode"]=="" && !isset($session->Vars["toolbar"])) 
		$session->Vars["xhtt"] = "1";

	elseif($session->Vars["thisMode"]=="" && !isset($session->Vars["callMainW"]) && (!isset($session->Vars["toolbar"]) || ($session->Vars["toolbar"]=="no"))) 
		$session->Vars["xhtt"] = "2";
	elseif ($session->Vars["thisMode"]=="_new"  && !isset($session->Vars["callMainW"]) && $session->Vars["toolbar"]=="yes") {
		$session->Vars["xhtt"] = "3";
		
		WebApp::addVar("all_meta", "<title>".APP_URL."</title>" ); 

	} elseif ($session->Vars["thisMode"]=="_new"  && $session->Vars["callMainW"] == "y" && $session->Vars["toolbar"]=="yes")
		$session->Vars["xhtt"] = "4";
	
	


/*

<If condition="'{{xhtt}}'=='1'">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
</If>
<If condition="'{{xhtt}}'=='2'">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
</If>
<If condition="'{{xhtt}}'=='3'">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
</If>
<If condition="'{{xhtt}}'=='4'">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
</If>



<If condition="'{{thisMode}}'!='_new' && '{{toolbar}}'=='toolbar'">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
</If>
<If condition="'{{thisMode}}'!='_new' && '{{toolbar}}'=='no'">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
</If>
<If condition="'{{thisMode}}'=='_new' && '{{toolbar}}'=='yes' && '{{xhtt}}' != 'yes'">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
</If>
<If condition="'{{thisMode}}'=='_new' && '{{toolbar}}'=='yes' && '{{xhtt}}' == 'yes'">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
</If>

*/



/*	if($_REQUEST["herapare"]=="po") {
		$session->Vars["xhtt"] = "yes";
	} else {
		$session->Vars["xhtt"] = "no";
	}*/	
	
	
	
	
}
?>