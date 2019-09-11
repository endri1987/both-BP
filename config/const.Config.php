<?
//define("CONTROL_ENTRY_TEMPLATE", 					"YES");		//yes|no
define("ACTIVATE_ENTITY_TOOLSET_SPECIALIZATION",	"Y");	//specializohet WORKGROUPI
define("ACTIVATE_SOURCE_LIBRARY_CI",				"Y");	//konstante qe percakton nese do kete CI extended per assetet e librarise
define("ACTIVATE_TEMPLATE_EXTENDED",				"Y");	//konstante qe percakton nese Templatet do te behen extend me master template
define("ACTIVATE_METADATA_STRUCTURED",				"Y");	//konstante qe percakton nese Templatet do te behen extend me master template
define("ACTIVATE_FULL_TEXT_SEARCH_READABLE",	"Y");	//SAVE FULL TEXT TO BE USED FOR SEARCH RESULT

define("ACTIVATE_NEMS_CONTROL",					"Y");		//c

define("PLATFORM_MODE",       					"WEB");		//SIMPLE,INTRANET-INTERNET,DOCUMENT_MANAGEMENT,ELEARNING, MC
define("VERSION_ASP",							"4");		//c

define("BO_MODE",       						"Y");			//
define("WRAPER_ENABLED",						"Y");			//

define("ACTIVATE_ESHOP",       	"N");			//SAVOHET informacioni per shitje per cdo ci
define("NODE_BOOTSTRAP",		"Y"); 

define("EVENT_SOURCE_SETTINGS",	"Y");	//caschohet dhe indeksohet permbajtaj e filet ne library
define("ACTIVATE_SL_CACHE_INDEX",	"Y");	//caschohet dhe indeksohet permbajtaj e filet ne library
define("ACTIVATE_META_DESCRIPTORS",	"Y");	//SAVOHEN METADESCRIPTORET

//---------------------------------------------------------------
define("SESSION_TIMEOUT_CONFIGURATION",	"personalization");		//yes|no
define("LOCKSCREENHTML",				"app");					//asp|appdefine("AUTOMATIC_EXPIRING", 					"60");	//minutes X*60=seconds if needed*/
//---------------------------------------------------------------


define("SITEMAP_VERSION", "");					// Y:mobile version adaptive, N:mobile version responsice
define("MOBILE_VERSION_INCLUDED", "NO");		// YES:mobile version adaptive, NO:mobile version responsice

define("CACHE_DYNAMIC",	     	"Y");		// Y/N  kete variabel e perdor backofice per te paresuar kontentin me versionin e ri te keshimit
define("CACHE_DYNAMIC_FRONT", 	"Y");		// Y/N  kete variabel e perdor fronti per te punuar me linqe te keshuara dinamikisht(tek nemet) dhe me fushen ku kontenti eshte paresuar contentLng1/contentLng1_front
define("MOBILE_ACTIVE",       	"N");		// Y/N  variabel qe tregon nese faqja ka template edhe per mobile

//Easy-Web Configuration
//Features
define("ASP-Version", "Yes");			////avalable values (Yes/No)
define("Multiproduct", "No");			////avalable values (Yes/No)

define("COLLECTOR_RSS",					"YES");	//nese kjo eshte YES, do te perdoret RSS te cdo collector
define("COLLECTOR_PERSONALIZATION",		"YES");	//nese kjo eshte YES, do te perdoret te cdo collector, li_collector, search apo ndonje collector extend

define("KW_CI_CI",						"YES");	//nese kjo eshte YES del mundesia per te bere lidhjen ci me ci
define("CI_Favorite",					"YES");	//nese kjo eshte YES del mundesia per teperdorur coomments abd rating
define("CI_CommentRating",				"YES");	//nese kjo eshte YES del mundesia per teperdorur coomments abd rating
define("CI_override_rights",			"YES");	//nese kjo eshte YES del mundesia per te bere override te drejten e roleve mbi ci
define("EVENT_AUTOMATIC_TRIGGER_NOTIFICATIONS",	"NO");	//nese kjo eshte YES del mundesia per te derguar notifikime automatike

define("INSERTCISTATISTICS",       "N");//Y:ruhen ca statistika per ci	


define("Multizone", "Yes");			////avalable values (Yes/No)
//Contition if Multizone is Yes
define("IP-Policy", "Yes");			////avalable values (Yes/No)
define("Zone_share_content", "Yes");		////avalable values (Yes/No)
//Contition if Multizone is Yes Ends
define("Users_Av", "0");			////avalable values (Yes/No)

define("Multilanguage", "Yes");			////avalable values (Yes/No)

//Contition if Multilanguage is Yes
define("NumberLang", "2");
define("LNGs", 2);

///Language code for each of them
define("LNG1", 		"Shqip");			//////mund te perdoret ne backoffice te switchet e gjuhes te CI
define("LNG2", 		"English");		//////mund te perdoret ne backoffice te switchet e gjuhes te CI
//define("LNG3", 		"English");		//////mund te perdoret ne backoffice te switchet e gjuhes te CI

define("LNG1_Name", "Shqip");			////////////perdoret per emrat e filave te mesazheve te pjesa front
define("LNG2_Name", "English");		////////////perdoret per emrat e filave te mesazheve te pjesa front
//define("LNG3_Name", "English");		////////////perdoret per emrat e filave te mesazheve te pjesa front

define("LNG1_Code", "1");
define("LNG2_Code", "2");
//define("LNG3_Code", "3");

define("MetaCodeLNG1", "sq");		//kjo do te perdoret per te mbushur selektin ne editor
define("MetaCodeLNG2", "en");		//kjo do te perdoret per te mbushur selektin ne editor
//define("MetaCodeLNG3", "en");		//kjo do te perdoret per te mbushur selektin ne editor

///Language code for each of them Ends
define("Type", "Interrelated");   		////avalable values (Interrelated/Independent)
//Contition if Multilanguage is Yes Ends

define("Personalization", "Yes");		////avalable values (Yes/No)
define("Version_Control", "Yes");		////avalable values (Yes/No)
define("Caching_Metatags", "No");		////avalable values (Yes/No)
//if Caching_Metatags is Yes check below property
//define("Caching_Mode", "Zone");			////avalable values (Zone/ )

//format source code when caching Yes/No ?
define("Clean_Html", "No");			////avalable values (Yes/No)
define("Audit_Trails", "Yes");			////avalable values (Yes/No)

//if newsletter is available
define("BackgoundProces", "Yes");		////avalable values (Yes/No)

//if organigram is available
define("Organigram", "No");				////avalable values (Yes/No)

/// if "members" tool available
//define("MEMBERS_ORG_MAIN",	"1"); 		///for available values please check the configuration of Member tool
define("QXD-WIN", "Yes");
define("ADS", "Yes");
define("NEW_NEM_STR", "Yes");

define("CSS_VERSION", "Yes");

define("APP_ENCODING", "utf-8");
define("AUDIT_TRAIL_STATISTIC", "internet,intranet,backoffice");

define("EW_KONVERT", "convert");
define("HTML_TAG", "Yes");		////avalable values (Yes/No)

define("GoToBackDirectly", "Yes");			////avalable values (Yes/No)

//keto constante sherbejn per doctypin dhe XMLNS ne BO

define("APPDOCTYPE", "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
define("APPXMLNS", " xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"{{xml_lang}}\" xml:lang=\"{{xml_lang}}\"");


define("SL_MAX_UPLOAD_SIZE_IMG",	"524288000");	//500MB
define("SL_MAX_UPLOAD_SIZE_DOC",	"5368709120");	//5 GB

//email status---------------------------------------------------
define("STATUS_EMAIL_FROM",			"luertahoxha@arkit.ch");
define("STATUS_EMAIL_FROM_LABEL",	"eccE-Learning");
define("STATUS_EMAIL_FROM_TO",		"klevinahamzaj@arkit.ch");
define("STATUS_EMAIL_FROM_BCC",		"jonidacuko@arkit.ch");
//---------------------------------------------------------------


?>