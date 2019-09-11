function onLoginFrm() {
	
	var frmLg 			= document.loginFrm;
	var find_submit_el 	= 0
	var usrname 		= frmLg.usrname.value;
	var usrpassd 		= frmLg.usrpassd.value;
	if (usrname!='' && usrpassd!=''){
		GoTo("thisPage?event=none.login(usrname="+usrname+";usrpassd="+usrpassd+";idstempLogin="+idstempLogin+")");
	} else {
		//alert (_fill_required_data);
		return false;
	}
}

function resetField(obj,str){
 if(obj.value.length==0){
 obj.value=str;
 return;
 }
 if(obj.value==str){
 obj.value="";
 }
 } 