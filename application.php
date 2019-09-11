<?
  define("APPLICATION_STATE", "FE");	//BO;FE

  //KETU DEKLAROHEN VARIABLAT E SESIONIT TE PHP SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP --
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
  //define("PHP_VALID_VAR_SESSION", "unic,Y;unic,f_pozitive_numbers");	TO_HTML_ENTITIES
  //------------------------------------------------------------------------------------------------  

  //KETU DEKLAROHEN VARIABLAT E SESIONIT SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP --
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
    //define("APP_VALID_VAR_SESSION", "nr_rec_page,Y;nr_rec_page,ONLY_NUMBERS;xx,Y;xx,ONLY_NUMBERS");	
  //------------------------------------------------------------------------------------------------  

  //KETU DEKLAROHEN VARIABLAT REQUEST SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP -----
  //KETO JANE VARIABLAT QE VIJNE ME _GET OSE _POST
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
    //define("APP_VALID_VAR_REQUEST", "nr_rec_page,Y;nr_rec_page,ONLY_NUMBERS;xx,Y;xx,ONLY_NUMBERS");	
  //------------------------------------------------------------------------------------------------ 
      //APP_VALID_VAR_REQUEST

define("PHP_VALID_VAR_SESSION", "k_ini,Y;zRef,Y;EccEmode,Y;idRefM,Y;idRef,Y;idElC,Y;idElC,f_pozitive_numbers");
define("APP_VALID_VAR_SESSION", "k_ini,Y;zRef,Y;EccEmode,Y;idRefM,Y;idRef,Y;idElC,Y;md,Y;oid,Y;question_id,Y;stepElearn,Y;is_virtual_slide,Y;simpleModePreview,Y;simpleEditAuthoring,Y");	



$valid_var_request  = "";
 
  //variabla per rss feed
//MC ---------------------------------------------------  
  $valid_var_request .= "org_main,Y;";
  $valid_var_request .= "org_main,f_only_numbers;";
  
  

  $valid_var_request .= "id_sig_g,Y;";
  $valid_var_request .= "id_sig_g,f_only_numbers;";

  $valid_var_request .= "id_sig,Y;";
  $valid_var_request .= "id_sig,f_only_numbers;";

  $valid_var_request .= "id_sig_s,Y;";
  $valid_var_request .= "id_sig_s,f_only_numbers;";

  $valid_var_request .= "idb,Y;";
  $valid_var_request .= "nrpage,Y;";
  $valid_var_request .= "nrpage,f_only_numbers;";
  
  $valid_var_request .= "idkd,Y;";
  $valid_var_request .= "idkd,f_only_all_natural_numbers;";
  
  $valid_var_request .= "prm,Y;";
  $valid_var_request .= "prm,f_safe_unserialize;";
  
  
  $valid_var_request .= "search,Y;";
  $valid_var_request .= "search,f_full_escape_text;";

  $valid_var_request .= "email,Y;";
  $valid_var_request .= "email,f_full_escape_text;";
  $valid_var_request .= "firstname,Y;";
  $valid_var_request .= "firstname,f_full_escape_text;";
  $valid_var_request .= "lastname,Y;";
  $valid_var_request .= "lastname,f_full_escape_text;";
  $valid_var_request .= "salutation,Y;";
  $valid_var_request .= "salutation,f_full_escape_text;";
  $valid_var_request .= "cis,Y;";
  $valid_var_request .= "cis,f_only_numbers;";
  $valid_var_request .= "register_newsletter,Y;";
  $valid_var_request .= "register_newsletter,f_full_escape_text;";





//MC ---------------------------------------------------  


  //variabla per kalendarin e  eventeve
  
  $valid_var_request .= "apprcss,Y;"; //emri i procesit 
  $valid_var_request .= "act,Y;"; //action
 
 
  define("APP_VALID_VAR_REQUEST", $valid_var_request);	
	
  //set_time_limit(0);
  $app_path = dirname(__FILE__);
  DEFINE("APP_PATH",		$app_path."/");

  DEFINE("CONFIG_PATH",	APP_PATH."config/");
  DEFINE("DEBUG",			"0");

  INCLUDE CONFIG_PATH."const.Paths.php";
  INCLUDE CONFIG_PATH."const.Nems.php";

  //include configuration features and modules
  INCLUDE CONFIG_PATH."const.Config.php";

  //include configuration constants
  INCLUDE CONFIG_PATH."const.DB.php";
  INCLUDE CONFIG_PATH."const.Settings.php";

  INCLUDE EASY_PATH."inc/php/php_session_start.php";

  //include the WebApp framework
  INCLUDE WEBAPP_PATH."WebApp.php";

  //add some template variables that are commonly used
  INCLUDE INCLUDE_PATH."add_app_vars.php";
  INCLUDE INCLUDE_PATH."htmlMimeMail.php";
?>