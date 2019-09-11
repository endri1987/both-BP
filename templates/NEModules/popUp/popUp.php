<?
function popUp_onLoad() 
{
	global $session,$event;
	extract($event->args);

    INCLUDE(ASP_FRONT_PATH."nems/popUp/popUp_onLoad.php");
}

function popUp_onRender() 
{
	global $session,$event;
	extract($event->args);

    INCLUDE(ASP_FRONT_PATH."nems/popUp/popUp_onRender.php");
    
 /************************   
 
    //SHENIME
    
    mode = simple, ne kete rast parsohet vetem contenti i kerkuar, pa header dhe footer
    		kjo menyre thirrje eshte perdorur dikur per te nxjerr imazhe te medha, me popup, ose swf
    
    mode = wb, po thirret nga toolset ne backoffice eshte perdorur te comment and rating, te plotesohet dokumentacioni
    
    mode = alone, ne kete rast parsohet vetem contenti i kerkuar me header dhe footer, print si funkionalitet dhe close, 
    		te vendoset nese do te nxirret contenti brenda divit me id=MainContainer te parenti, apo do krijohet gjendja dinamikisht
    		
    		kjo menyre thirrje perdoret kur krijohet nje link external me mode=alone
    		
    		
     mode = print		
     mode = email
    
    ne dy rastet e fundit kapet innerhtml e parentit, dhe ne rastin e printimit, hiqen te gjitha linqet, behen hidden me ane te styleshetet
    	navigimet neper lista, next previws
    		
    
    
    behen hidden te gjitha stilet e meposhtme, dhe clasat qe e percaktojne kete gjenden tek include_css\print.css
   
	#filterTable{
		display: none;
	}
	#expandImg{
		display: none;
	}
	input{
		display: none;
	}

	#printH{
		display: none;
	}

    
    
    keto me poshte jane blloqet e searchit, dhe te navigimeve kudo ku ka liste
	.search_again{
		display: none;
	}

	.navListLbl{
		display: none;
	}

	.nav_block_2{
		display: none;
	}

      
    
    
 ************************/
    
    
}

?>