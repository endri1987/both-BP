<?


//SEARCHRESULTMODULE ----------------------------------------------------------------------
  define("LI_MODULE_TEMPLATE", "standard"); 
 //--------------------------------------------------------------------------------------


/*
Standard List	-default
Standard List	-standard
Bullet List
Event Item Template
Image Three Columns
Text scroller
Title  List
Video Three Columns
*/

//LanguageSwitch -----------------------------------------------------------------------------------------------------
  //define("LANGUAGESWITCH_TEMPLATE", "0");           //kur nuk do te perdoresh nje nga templatet
  define("LANGUAGESWITCH_TEMPLATE", "3");             //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0

  define("LANGUAGESWITCH_SHOW_CURRENT_LNG", "N");     //vlerat Y,N; Ta shfaq apo jo gjuhen korente ?
  define("LANGUAGESWITCH_ONLY_ICO_ENABLE",  "N");     //vlerat Y,N; Te punoje apo jo vetem me iconat enable ?
  define("LANGUAGESWITCH_LNG_ID",           "3"); //ID e gjuheve te aplikimit te ndara me presje te renditura sipas radhes se shfaqej: '1,2' ose '2,1,3' ose '2,1'
  define("LANGUAGESWITCH_SHOW_LNG_ALWAYS",  "Y");     //vlerat Y,N; Ti shfaq gjuhet pa link kur ato nuk jane aktive?
//--------------------------------------------------------------------------------------------------------------------


//SEARCHRESULTMODULE ----------------------------------------------------------------------
  //define("SEARCHRESULTMODULE_TEMPLATE", "0"); //defaulti
  define("SEARCHRESULTMODULE_TEMPLATE", "1"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0
 //--------------------------------------------------------------------------------------

//SearchModule ---------------------------------------------------------------------------
  //define("SEARCHMODULE_TEMPLATE", "0"); //defaulti
  define("SEARCHMODULE_TEMPLATE", "1"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0

  define("SEARCHMODULE_ONBLUR_ONFOCUS", "Y"); //vlerat Y/N; aktivizon funksionet ONBLUR, ONFOCUS tek inputi i searchit per te baere searchin me trendi :)
//--------------------------------------------------------------------------------------


 //LOCATIONMODULE ----------------------------------------------------------------------
  //define("LOCATIONMODULE_TEMPLATE", "0"); //kur nuk do te perdoresh nje nga templatet
  define("LOCATIONMODULE_TEMPLATE", "1"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0
 //--------------------------------------------------------------------------------------

  //PrintEmailModule ----------------------------------------------------------------------
  //define("PRINTEMAILMODULE_TEMPLATE", "0"); //kur nuk do te perdoresh nje nga templatet
  define("PRINTEMAILMODULE_TEMPLATE", "3"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0

  define("PRINTEMAILMODULE_P_RESIZABLE", "no");  //Dritarja per printim RESIZABLE = yes/no
  define("PRINTEMAILMODULE_P_WIDTH",     "740"); //Gjeresia e dritares per printim
  define("PRINTEMAILMODULE_P_HEIGHT",    "600"); //Lartesia e dritares per printim

  define("PRINTEMAILMODULE_E_RESIZABLE", "no");  //Dritarja per email RESIZABLE = yes/no
  define("PRINTEMAILMODULE_E_WIDTH",     "740"); //Gjeresia e dritares per email
  define("PRINTEMAILMODULE_E_HEIGHT",    "600"); //Lartesia e dritares per email
//--------------------------------------------------------------------------------------


//LastUpdate ---------------------------------------------------------------------------
  //define("LASTUPDATE_TEMPLATE", "0"); //kur nuk do te perdoresh nje nga templatet
  define("LASTUPDATE_TEMPLATE", "1"); //kur nuk do te perdoresh templaten 1,2,...
//--------------------------------------------------------------------------------------


//POPUP --------------------------------------------------------------------------------
  define("POPUP_INCLUDE_FOOTERCONTAINER", "N"); //vlerat Y/N; ta inkludoje FOOTERCONTAINER apo te punoje me mesazhet copyright_mesg, copyright_name_mesg

  //define("SEND_EMAIL_FORM_TEMPLATE", "0"); //kur nuk do te perdoresh nje nga templatet
  define("SEND_EMAIL_FORM_TEMPLATE", "1"); //kur nuk do te perdoresh templaten 1,2,...
  define("SEND_EMAIL_MODE", "HTML"); //MENYRA E DERGIMIT TE EMAILIT; HTML|TXT;
  define("SEND_EMAIL_INCLUDE_LOGO", "1"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0
  define("SEND_EMAIL_INCLUDE_COPYRIGHT", "1"); //kur nuk do te perdoresh templaten 1,2,...; kur nuk do te perdoresh vendos 0
//--------------------------------------------------------------------------------------

//ContactModule ---------------------------------------------------------------------------
  //CONTACT_TEMPLATE 	//ky variabel definohet tek propertite e nemit
  //CONTACT_SLEEP 		//ky variabel ka te beje me ritjen e sigurise ne dergimin e emailit. 
  						//eshte numri i sekondave qe caktivizon butonin send. 
  						//ne rast se nuk doni ta perdorni vijeni kete variabel 0
  						//definohet tek propertite e nemit
  //CONTACT_VALID_IP  	//validon qe mos te dergohen email shume te shpeshta nga e njejta IP
  						//definohet tek propertite e nemit
//--------------------------------------------------------------------------------------

//siteMapModule --------------------------------------------------------------------------------------------------------------------
  define("SITEMAP_TEMPLATE_TopNavigation",    "0"); //kur nuk do te printosh ne sitemap dhe familjen TopNavigation
  //define("SITEMAP_TEMPLATE_TopNavigation",    "1"); //kur do te printosh ne sitemap dhe familjen TopNavigation vlerat 1,2,3...
  
  //define("SITEMAP_TEMPLATE_MainNavigation",    "0"); //kur nuk do te printosh ne sitemap dhe familjen MainNavigation
  define("SITEMAP_TEMPLATE_MainNavigation",    "4"); //kur do te printosh ne sitemap dhe familjen MainNavigation vlerat 1,2,3...

  define("SITEMAP_TEMPLATE_FooterNavigation",    "0"); //kur nuk do te printosh ne sitemap dhe familjen FooterNavigation
  //define("SITEMAP_TEMPLATE_FooterNavigation",    "1"); //kur do te printosh ne sitemap dhe familjen FooterNavigation vlerat 1,2,3...
//---------------------------------------------------------------------------------------------------------------------------------


?>