<?
require_once(INC_PATH.'collector.Data.List.Ext.class.php');
function ListItemCollector_onRender() {
	global $session,$event, $ootUserObj,$global_cache_dynamic,$cacheDyn;

//	$starts = WebApp::get_formatted_microtime();

	$termSearch	= "";
	WebApp::addVar("backToListIcon","");
	$statusNem = '0';	//0-list of searchin with term(, 1- abstract, nqse abstracti do trajtohet nga ky nem )
	$targeted_page = "";

	$ILC_x = new collectorDataListExtClass();
	$ILC_x->InitClass($session->Vars["idstemp"]);
	$ILC_x->ConstructDataList();
	//$ILC_x->controlPersonalizationConfiguration();
	
	//echo $ILC_x->templateFileName.":templateFileName";
	WebApp::addVar("include_default","<Include SRC=\"{{NEMODULES_PATH}}ListItemCollector/".$ILC_x->templateFileName."\"/>");

/*	if (isset($ILC_x->inicializeCollectorProp)) {
		WebApp::addVar("iconDependingOfConfiguration","yes");
		if (isset($ILC_x->inicializeCollectorProp["how_to_collect"])) 
			WebApp::addVar("how_to_collect",$ILC_x->inicializeCollectorProp["how_to_collect"]);
		if (isset($ILC_x->inicializeCollectorProp["user_preferences"])) 
			WebApp::addVar("user_preferences",$ILC_x->inicializeCollectorProp["user_preferences"]);
		
	} else {
		WebApp::addVar("iconDependingOfConfiguration","no");
	}


	WebApp::addVar("nr_of_items_cat",$ILC_x->CountItems);
	WebApp::addVar("enable_filter","no");
	if (isset($ILC_x->arrayConf["enableSearch"]) && $ILC_x->arrayConf["enableSearch"]=="0") {

		WebApp::addVar("enable_filter","yes");
		$nr_of_items = 5;	
		if (isset($ILC_x->arrayConf["nr_of_items"]) && $ILC_x->arrayConf["nr_of_items"]>0) {
			$nr_of_items = $ILC_x->arrayConf["nr_of_items"];
		}
		WebApp::addVar("nr_of_items_cat","$nr_of_items");
	}
*/	


		
// echo "<textarea>";
// print_r($Grid_AuthorNav);
// echo "</textarea>";
	
//echo "<textarea>";
//print_r($ILC_x);
//echo "</textarea>";

		
}






/*


<pre>



Configuring: Main Configuring: General Properties

	check to control atributes that are configured to be displayed

		DC_thumbnail_display		=>	{{DC_thumbnail_display}}
		DC_abstract_display			=>	{{DC_abstract_display}}
		DC_date_display				=>	{{DC_date_display}}
		DC_time_display				=>	{{DC_time_display}}
		DC_source_display			=>	{{DC_source_display}}
		DC_sourceauthor_display	=>	{{DC_sourceauthor_display}}
		DC_content_display			=>	{{DC_content_display}}

	label for atributes
		_DC_datetime_label	=>	{{_DC_datetime_label}}
		_DC_source_label	=>	{{_DC_source_label}}
		_DC_sourceauthor_label	=>	{{_DC_sourceauthor_label}}



Configuring: Main document attached id e filet attach eshte te var asset_id=>{{asset_id}}	[ecc_doc_id]

	atributet e filet attach kapen me griden e meposhtem DocCachedInfo_{{asset_id}}
	<Grid gridId="DocCachedInfo_{{asset_id}}">
		te gjitha atributet dhe propertite me poshte jane te lidhura me griden qe jep infomarcionin per documentin attach
	</Grid>

	check to control atributes that are configured to be displayed

		DA_icotype_display	=>	{{DA_icotype_display}}

		DA_filename_display	=>	{{DA_filename_display}}
		DA_mimetype_display	=>	{{DA_mimetype_display}}
		DA_filesize_display	=>	{{DA_filesize_display}}
		DA_duration_display	=>	{{DA_duration_display}}
		DA_dimension_display	=>	{{DA_dimension_display}}

	label for atributes
		_DA_filename_label	=>	{{_DA_filename_label}}
		_DA_mimetype_label	=>	{{_DA_mimetype_label}}
		_DA_filesize_label	=>	{{_DA_filesize_label}}
		_DA_duration_label	=>	{{_DA_duration_label}}
		_DA_dimension_label	=>	{{_DA_dimension_label}}


	check to control icons

		display_download	=>	{{display_download}}
		display_info		=>	{{display_info}}
		display_preview		=>	{{display_preview}}
		display_favorit		=>	{{display_favorit}}

		_DA_download_iconlabel	=>	{{_DA_download_iconlabel}}
		_DA_finfo_iconlabel		=>	{{_DA_info_iconlabel}}
		_DA_preview_iconlabel	=>	{{_DA_preview_iconlabel}}



Configuring: Extra properties - EVENT ITEM



	flage to control interelation - te gjitha atributet e eventit
	
            [event_timing_text] => 10.06.2017 bis 11.06.2017
            [eventStartDate] => 10.06.2017
            [eventEndDate] => 
            [eventStartTime] => 09:00
            [eventEndTime] => 12:00
            
			[flagToCntPeriod] => [noEndDate|sameDate|diffdate]

            
            [addressBlockExist] => yes
            
            [address_name] => 
            [street] => Hofmattstrasse 9
            [dp_street] => yes
            [zip] => 5223 
            [dp_zip] => yes
            [location] => Riniken
            [dp_location] => yes
            [country] => Schweiz
            [dp_country] => yes
            
            [addressKoordExist] => yes
            [latitudeEvent] => 47.4969225000
            [dp_latitudeEvent] => yes
            [longitudeEvent] => 8.1888468000
            [dp_longitudeEvent] => yes
            
            [ContactBlockExist] => yes
            [organizational_contact] => 
            [eventTel] => 0692070726
            [dp_eventTel] => yes
            [eventFax] => 0692070726
            [dp_eventFax] => yes
            [eventEmail] => albanruci@gmail.com
            [dp_eventEmail] => yes



	check to control atributes that are configured to be displayed

		display_eventStartDate	=>	{{display_eventStartDate}}
		display_eventEndDate	=>	{{display_eventEndDate}}
		display_eventTime		=>	{{display_eventTime}}
		eventPeriodText			=>	{{eventPeriodText}}
		

		display_address			=>	{{display_address}}
		display_country			=>	{{display_country}}
		
		
		
		
		display_organizational_contact		=>	{{display_organizational_contact}}
		display_eventTel		=>	{{display_eventTel}}
		display_eventFax		=>	{{display_eventFax}}
		display_eventEmail		=>	{{display_eventEmail}}

	label for atributes

		extrasLabels_dateblock		=>	{{extrasLabels_dateblock}}
		extrasLabels_locationblock	=>	{{extrasLabels_locationblock}}
		extrasLabels_eventTel		=>	{{extrasLabels_eventTel}}
		extrasLabels_eventFax		=>	{{extrasLabels_eventFax}}
		extrasLabels_eventEmail		=>	{{extrasLabels_eventEmail}}
		extrasLabels_contactblock	=>	{{extrasLabels_contactblock}}




</pre>




    <If condition="'{{dp_labelUserDefinedLink}}' == 'yes'">
        <a href="{{hrefToDoc}}" class="btn btn-primary btn-sm legitRipple" role="button" tabindex="0">{{labelUserDefinedLink}}</a>
    </If>
    <If condition="'{{dp_labelUserDefinedDocAttachedLink}}' == 'yes'">
		<Grid gridId="DocCachedInfo_{{asset_id}}">
		<a title="{{titleToAlt}}" data-title="{{titleToAlt}}" target="_blank"
			class="line-clamping view-{{ico_type}} item-title" data-key="{{identifier_type}}"
			data-width="{{dt_width}}" data-height="{{dt_height}}" data-id="{{CID_REF}}"
			data-url="{{identifier_key}}" href="{{stream_url}}" target="_blank">
			{{labelUserDefinedDocAttachedLink}}
		</a>
		</Grid>
	</If>



*/

?>