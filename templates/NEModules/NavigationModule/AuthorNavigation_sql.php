<?
//KUSHTI PER NYJEN AKTIVE, JOAKTIVE --------------------------------------------------------------------------------------
IF ($session->Vars["thisMode"] == "_new")
{$kusht_aktiv_joaktiv = "";}
ELSE
{
    $kusht_aktiv_joaktiv  = " AND nivel.active".$session->Vars["lang"]."     != '1' ";
    $kusht_aktiv_joaktiv .= " AND c.published".$session->Vars["lang"]." = 'Y' ";
}
//------------------------------------------------------------------------------------------------------------------------
$sessLANG = $session->Vars["lang"];
if (isset($sessLANG) && $sessLANG!="") {
    //$lngId	= eregi_replace ("Lng","",$sessLANG);
    $lngId	= str_replace("Lng","",$sessLANG);
}else
    $lngId	= 1;




if(defined('NODE_BOOTSTRAP') && NODE_BOOTSTRAP=="Y")
    $otherFields = ", coalesce(boostrap_class,'') as boostrap_class, coalesce(boostrap_ico,'') as boostrap_ico";

/*if ($profile_step != 1) {
	$id0 = $session->Vars["level_0"];
	if ($id0==ZONE_ECCELEARNING || $id0==ZONE_MYCME)
		$id0 = ZONE_AUTHORING;
}	*/




//niveli -----------------------------------------------------------------------------------------------------------------

$sqlN  = "SELECT DISTINCT nivel.id_firstNivel                                as id1, 
                nivel.id_secondNivel                               as id2, 
                nivel.id_thirdNivel                                as id3, 
                nivel.id_fourthNivel                               as id4, 
                nivel.isExpanded                                   as isExpanded, 
                nivel.clickable                                    as clickable, 
                
                COALESCE(nivel.imageSm_id,      '')                as imageSm_id_node, 
                COALESCE(nivel.imageSm_id_name, '')                as imageSm_id_name_node, 

                coalesce(nivel.description".$session->Vars["lang"].$session->Vars["thisMode"].",'') as label,
				
				c.ci_type                           as ci_type,
                c.content_id                         as con_id,
				COALESCE(c.title".$session->Vars["lang"].", '') as ci_title,
                COALESCE(c.description".$session->Vars["lang"].$session->Vars["thisMode"].", '') as ci_description,

                c.title".$session->Vars["lang"]."    as title,
                c.filename".$session->Vars["lang"]." as filename,

                COALESCE(c.imageSm_id,      '')			as imageSm_id, 
                COALESCE(c.imageSm_id_name, '')		as imageSm_id_name,                                 
                COALESCE(c.with_https, 'n')          as with_https
				".$otherFields."
								
               
            FROM (nivel_4 as nivel,
                content as c,
                profil_rights)

			".$joinConditions."
				

                WHERE nivel.id_zeroNivel   = c.id_zeroNivel   AND
                      nivel.id_firstNivel  = c.id_firstNivel  AND
                      nivel.id_secondNivel = c.id_secondNivel AND
                      nivel.id_thirdNivel  = c.id_thirdNivel  AND
                      nivel.id_fourthNivel = c.id_fourthNivel AND

                      nivel.id_zeroNivel   = profil_rights.id_zeroNivel   AND
                      nivel.id_firstNivel  = profil_rights.id_firstNivel  AND
                      nivel.id_secondNivel = profil_rights.id_secondNivel AND
                      nivel.id_thirdNivel  = profil_rights.id_thirdNivel  AND
                      nivel.id_fourthNivel = profil_rights.id_fourthNivel AND

                      nivel.id_zeroNivel   = '".$id0."' AND
                      nivel.id_firstNivel  {{id_firstNivel}}  AND
                      nivel.id_secondNivel {{id_secondNivel}} AND
                      nivel.id_thirdNivel  {{id_thirdNivel}}  AND
                      nivel.id_fourthNivel {{id_fourthNivel}} AND

                      c.orderContent = '0' AND

                      nivel.state".$session->Vars["lang"]." != 7  AND
                      profil_rights.profil_id      IN (".$session->Vars["tip"].") 
                      
                      {{kushti_node_family}}
                      ".$kusht_aktiv_joaktiv."
             ORDER BY nivel.orderMenu
            ";
//niveli -----------------------------------------------------------------------------------------------------------------
?>
