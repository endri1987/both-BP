<?
//http://192.168.1.114/hausarztmodell/01_copy_info.php

INCLUDE_ONCE (dirname(__FILE__)."/application.php");

//parametrat burim -----------------------------------------------------------------
  GLOBAL $dest_db_name, $burim_db_name;
  
  $dest_db_name     = DBNAME;
  $burim_db_name    = MC_DBNAME;
//parametrat burim -----------------------------------------------------------------

//tabelat qe do kopjohen -----------------------------------------------------------
  $users_tab_copy[] = "users";
  //$users_tab_copy[] = "profil";
  $users_tab_copy[] = "user_profile";
//tabelat qe do kopjohen -----------------------------------------------------------

//pastrojme tabelen ----------------------------------------------------------------
  $sql_del = "DELETE FROM ".$dest_db_name.".profil WHERE profil_id > 2";
  WebApp::execQuery($sql_del);

  //kopjojme tabelen profil --------------------------------------------------------
    $sql_ins = "INSERT INTO ".$dest_db_name.".profil (profil_id, profil_name, profil_parentId)
                                               SELECT profil_id, profil_name, profil_parentId
                                                 FROM ".$burim_db_name.".profil
                                                WHERE profil_id > 2
               ";
  
  WebApp::execQuery($sql_ins);
//----------------------------------------------------------------------------------

//users_tab_copy -------------------------------------------------------------------
  FOR ($i=0; $i < count($users_tab_copy); $i++)
      {
       $tab_copy_sel = $users_tab_copy[$i];
     
       //pastrojme tabelen ---------------------------------------------------------
         $sql_del = "DELETE FROM ".$dest_db_name.".".$tab_copy_sel;
         WebApp::execQuery($sql_del);
       //---------------------------------------------------------------------------
     
       //kopjojme te dhenat --------------------------------------------------------
         $kol_sel = columns_sel($tab_copy_sel);
     
         $sql_ins = "INSERT INTO ".$dest_db_name.".".$tab_copy_sel." (".SUBSTR($kol_sel[1], 2).")
                                                               SELECT ".SUBSTR($kol_sel[2], 2)."
                                                                 FROM ".$burim_db_name.".".$tab_copy_sel."
                    ";
       
         WebApp::execQuery($sql_ins);
         IF (mysql_errno() != 0)
            {
             PRINT "<br>Error: ".$sql_ins;
             EXIT;
            }
       //kopjojme te dhenat --------------------------------------------------------
      }
//users_tab_copy -------------------------------------------------------------------

PRINT "OK";

function columns_sel($tab_name) 
   {
    GLOBAL $dest_db_name, $burim_db_name;

    //meren emrat e kolonave te tabeles korente per te qene te pavarur nga radhitja e tyre ne db te ndryshme
      $kol[1] = "";
      $kol[2] = "";
      
      $sql = "SHOW COLUMNS FROM ".$burim_db_name.".".$tab_name;
      $rs  = WebApp::execQuery($sql);
      $rs->MoveFirst();
      WHILE (!$rs->EOF())
            {
             $kol_name = $rs->Field('Field');

             $kol[1] .= ", ".$kol_name;
             $kol[2] .= ", CONVERT(".$kol_name." USING utf8)";
             //$kol[2] .= ", ".$kol_name;

             $rs->MoveNext();
            }
    
      RETURN $kol;
    //meren emrat e kolonave te tabeles korente per te qene te pavarur nga radhitja e tyre ne db te ndryshme
   }
?>