/*function onLoginFrm() {
	var form = document.loginFrm;
	var usrname = form.usrname.value;
	var usrpassd = form.usrpassd.value;
	remember='no';
	if (form.remember) {
		if (form.remember.length)
		for (var i =0;i <form.remember.length; i++){
			if (form.remember[i].checked) {
				remember=form.remember[i].value;
			}
		} else {
				remember=form.remember.value;
		}
	}
	if (usrname!='' && usrpassd!=''){
		document.loginFrm.action = apUl;
		form.evn.value = 'lg';
		form.idstmp.value = idstempLogin;
		document.loginFrm.submit();
	} else {
		alert (_fill_required_data);
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
*/