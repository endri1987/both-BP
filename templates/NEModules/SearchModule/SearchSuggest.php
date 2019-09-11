<?php
IF (
    ISSET($_GET['search'])
    AND ($_GET['search'] != '') 
    AND (strlen($_GET['search']) <= 50)

    AND ISSET($_GET['idstemp']) 
    AND ($_GET['idstemp'] != '')
    AND (strlen($_GET['idstemp']) <= 20)

    AND ISSET($_GET['k']) 
    AND ($_GET['k'] != '')
    AND (strlen($_GET['k']) <= 2)
   )
   {
    INCLUDE_ONCE dirname(__FILE__)."/application.php";
    INCLUDE ASP_FRONT_PATH."php/SearchSuggest.php";
   }
?>