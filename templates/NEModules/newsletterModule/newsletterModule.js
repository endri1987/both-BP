function addNewsletterUser() {
 
	str_val  = new Array(/'/ig, /;/ig, /\//ig, /\\/ig);
	str_val1 = new Array(/"/ig, /'/ig, /;/ig, /\//ig, /\\/ig);
	str_val2 = new Array(/;/ig);
	//str_val3 = new Array(/"/ig, /'/ig, /;/ig);

	document.formNewsletter.email.value = reg_exp(document.formNewsletter.email.value, str_val);

	var form  		= document.formNewsletter; 
	var emri		= form.emri.value;
	var mbiemri 	= form.mbiemri.value;
	var profesioni	= form.profesioni.value;
	var email		= form.email.value;

	if (emri == '' || mbiemri == '' || email == '') {
	   		alert(_fill_required_data);
		 	return;
	 } else {
	 
		if (!isEmailAddress(document.formNewsletter.email, 'Email')) {
			alert(_right_format_email);
			return;
		}		 
	 
		str_var='email='+email+';emri='+emri+';mbiemri='+mbiemri+';profesioni='+profesioni+';idstempNl='+idstempNl;
		GoTo('thisPage?event=none.newsletternem('+str_var+')');
	}
}

function isEmailAddress(theElement, theElementName) {
    
    var s = theElement.value;
	var email_filter=/^[A-Za-z][\.\w-]+@[A-Za-z0-9][\.\w-]+\.[A-za-z]+$/;
    if (email_filter.test(s))  
			return true;

      return false;
}
function reg_exp(fild_value, string_validim) 
   {
	if (string_validim.length > 1) 
	   {
	    for(var i=0; i<string_validim.length; i++) 
	       {
	       if (string_validim[i]=='/\'/g')
	       fild_value=fild_value.replace(string_validim[i], "\'");
	       else
	       fild_value=fild_value.replace(string_validim[i], "");
	       }
	    }
	return fild_value;
 }