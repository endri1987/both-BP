function nlunreg() {
 
	str_val  = new Array(/'/ig, /;/ig, /\//ig, /\\/ig);
	document.nlunregf.email.value = reg_exp(document.nlunregf.email.value, str_val);
	var email = document.nlunregf.email.value;
	
	if (email == '') {
	   		alert(_fill_required_data);
		 	return;
	 } else {
		if (!isEmailAddress(document.nlunregf.email, 'Email')) {
			alert(_right_format_email);
			return;
		}	 
	 
		str_var='email='+email+';idstempUNl='+idstempUNl;
		GoTo('thisPage?event=none.nlunreg('+str_var+')');
	}
}

function isEmailAddress(theElement, theElementName) {
    
    var s = theElement.value;
   // var filter=/^[A-Za-z][A-Za-z0-9_\-.]*@[A-Za-z0-9\-]+\.[A-Za-z0-9_.]+[A-za-z]$/;
    var filter=/((^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-|_)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$)([, ]))*/;
    
    if (s.length == 0 ) return true;
    
    if (filter.test(s))  
			return true;
    else  
			alert(mesgfed2);
      
      theElement.focus(); 
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