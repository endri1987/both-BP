function go_to_page(changethis) {
	//alert(changethis)
	
	var var_s = 'nr_fillim='+changethis+';search_for='+sf+';rec_page='+recp;
	alert(var_s)
	GoTo('thisPage?event=SearchResultModule.change_state('+var_s+')');
}

function change_rec_page(change_this) {

	var var_s = 'nr_fillim=0;search_for='+sf+';rec_page='+change_this;
	alert(var_s)
	GoTo('thisPage?event=SearchResultModule.change_state('+var_s+')');
}

function addFeedback() {
 
	document.formFeedback.texti.value = reg_exp(document.formFeedback.texti.value, 2);

	var form  		= document.formFeedback; 

	var email 		= form.email.value;
	var text		= form.texti.value;
	var Vorname		= form.Vorname.value;
	var Name		= form.Name.value;
	var Telefon		= form.Telefon.value;
	var Thema		= form.Thema.value;

	if ( text == '' || email == ''  || Name == ''  || Vorname == '') {
	   		alert(_please_fill_required_field)
		 	return;
	 } else {
	
	//	alert(isEmailAddress( document.formFeedback.email, 'Email')+' ketu')
		
		if(!isEmailAddress( document.formFeedback.email, 'Email')){
			//alert(_email_adress_is_not_exact);
			//return;
		}		

		str_var='email='+email+';text='+text+';Vorname='+Vorname+';Name='+Name+';Telefon='+Telefon+';Thema='+Thema;
		GoTo('thisPage?event=feedback.addFB('+str_var+')');
	}
}

function isEmailAddress(theElement, theElementName) {
   
    var s = theElement;
	re1 = / /gi;
	s=s.replace(re1, "");

    var filter=/(^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-|_)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$)/;
    if (filter.test(s)) {  	
    	
    	//alert('ne rregull')
    	return true;
    } else { 	
    	theElementName.focus(); 				
    	return false;
    }
}

function on_searchTerm() {

	var form = document.searchForm;
	var search_term = form.search.value;

	search_term = search_term.replace("/'/g",'%po%');
	search_term = search_term.replace('/"/g','%jo%');
	search_term = search_term.replace(';','');	
	
	if (search_term.length < 3 ) {
		alert(_more_than_two_char);
		return false;
	}

	if(search_term!='') {		
		GoTo('thisPage?event=MainContent.searchRes(search_for='+search_term+';k=0,0,0,0,0,6)');
	} else {
		alert(_please_fill_search);
	}
}

 
function on_send() {


	var form = document.emailForm;

	var recipientsOthers = form.recipientsOthers.value;
	var full_name= form.full_name.value;
	var senderemail= form.senderemail.value;
	var comment = form.comment.value;

	if (full_name == '' || recipientsOthers == '' || senderemail == '') {
			alert(_please_fill_required_field);
		  return;
	  } else if ( check_email() )	{
		var variab = 'recipientsOthers='+recipientsOthers+';senderemail='+senderemail+';comment='+comment+';full_name='+full_name;

			GoTo('thisPage?event=send_email_form.send('+variab+')');
			var getcat1=document.getElementById("send_off");
			getcat1.disabled="disabled";

	  } else {

			 return;
	  }
}

function on_send_ecards() {

	var form = document.emailForm;

	var recipientsOthers = form.recipientsOthers.value;
	var id = form.id.value;
	var full_name= form.full_name.value;
	var senderemail= form.senderemail.value;
	var comment = form.comment.value;
	if (full_name == '' || recipientsOthers == '' || senderemail == '') {
			alert(_please_fill_required_field);
		  	return;
	  } else if ( check_email() )	{
			var variab = 'recipientsOthers='+recipientsOthers+';senderemail='+senderemail+';id='+id+';comment='+comment+';full_name='+full_name;
			GoTo('thisPage?event=ecards.send('+variab+')');
			var getcat1=document.getElementById("send_off");
			getcat1.disabled="disabled";
	  } else {
			 return;
	  }
}

function check_email()
//return false if nothing is specified in e-mail address part
{

	var form = document.emailForm;
	var chkEmail = form.senderemail.value;
	var txtEmail = form.recipientsOthers.value;

	var myArray1 =txtEmail.split(',');
	for (var i=0; i<myArray1.length; i++){
    		//alert('1'+i+'*'+myArray1[i])
    		if (!isEmailAddress(myArray1[i], form.recipientsOthers)) {
				alert(mesgalert2);
			var ok='BAD';
		        return;
		}
	}
 	
	/*var myArray2 =chkEmail.split(',');
	for (var i=0; i<myArray2.length; i++){
        		//alert('2'+i+'*'+isEmailAddress(myArray1[i]))*/
    	if (!isEmailAddress(form.senderemail.value, form.senderemail)) {
			alert(mesgalert1);
			var ok='BAD';
		        return;
		}
	//}

	if (ok!='BAD') {
		return true;
	}
}


function reg_exp(fild_value, string_validim) {
	
	str_val_dec  = new Array(/[^0-9]/ig,/ /ig, /"/ig, /'/ig, /;/ig,/:/ig, /\//ig, /\\/ig); 	//0
	str_val  = new Array(/[^A-Za-z]/ig, /"/ig, /'/ig, /;/ig, /\//ig, /\\/ig); 				//1
	str_val_with_some_spe_char  = new Array(/;/ig, /\//ig, /\\/ig, /[^A-Za-z0-9 /"/']/ig);	//2
	var regex_type = ""
	
	
	if (string_validim == 0) 
		regex_type = str_val_dec
	else if (string_validim == 1)
		regex_type = str_val
	else if (string_validim == 2)
		regex_type = str_val_with_some_spe_char
	else
		fild_value = ""
		
	if (regex_type.length > 1) 
	   {
	    for(var i=0; i<regex_type.length; i++) 
	       {
	       if (regex_type[i]=='/\'/g')
	       fild_value=fild_value.replace(regex_type[i], "\'");
	       else
	       fild_value=fild_value.replace(regex_type[i], "");
	       }
	    }
	return fild_value;
}

function clearAll() {

	var form = document.emailForm;

	form.recipientsOthers.value = '';
	form.full_name.value = '';
	form.senderemail.value = '';
	form.comment.value = '';
	
}