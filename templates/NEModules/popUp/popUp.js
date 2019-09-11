
function callLoaderEmailFunction()
{
	var d = new Date();
	var unique = d.getTime() + '' + Math.floor(1000 * Math.random());

	if (window.opener) {
		_$("xhtmlBodyCont").value = window.opener.document.getElementById('MainContainer').innerHTML
		var params  = _$("xhtmlBody").serialize();
		//var pars = 'uni='+uniqueid+'&u='+unique+'&mode='+mode;
		var pars = 'convert_to.php?uni='+uniqueid+'&u='+unique+'&mode='+mode+'&APP_ENCODING='+app_encoding;
		
		var myAjax = new Ajax.Request(
		url+pars,
		{
			method: 'post',
			parameters: '',
			postBody: params,
			onComplete: showResponseRefreshEmail
		});
	}
}
function showResponseRefreshEmail(originalRequest)
{
	form_el = _$("WebAppForm");
	var inp9 = document.createElement("textarea");
	inp9.setAttribute("name","cont_v");
	
	if(navigator.appName.indexOf('Microsoft')!=-1) //explorer
	inp9.setAttribute("value",originalRequest.responseText);
	else
	inp9.innerHTML=originalRequest.responseText;
	
	inp9.style.display="none";
	inp9.value=originalRequest.responseText;
	
	form_el.appendChild(inp9);
	
	var inp = document.createElement("input");
	inp.setAttribute("name","url_v");
	inp.setAttribute("type","hidden");
	inp.setAttribute("value",window.opener.location.href);
	form_el.appendChild(inp);	
	
	
	
	
}
function callLoaderFunction()
{
	var unique = Math.floor(1000 * Math.random());
	if (window.opener) {

		_$("xhtmlBodyCont").value = window.opener.document.getElementById('MainContainer').innerHTML
		var params  = _$("xhtmlBody").serialize();

		//var pars = 'uni='+uniqueid+'&u='+unique+'&mode='+mode;
		var pars = 'convert_to.php?uni='+uniqueid+'&u='+unique+'&mode='+mode+'&APP_ENCODING='+app_encoding;
		var myAjax = new Ajax.Request(
		url+pars,
		{
			method: 'post',
			parameters: '',
			postBody: params,
			onComplete: showResponseRefresh
		});
	}
	
}
function showResponseRefresh(originalRequest)
{
	_$("CmlP").innerHTML = originalRequest.responseText;
	//alert("showResponseRefresh"+_$("filterDiv"))

	if(_$("filterDiv"))
	_$("filterDiv").innerHTML = "";
	
	if(_$("filterBoth"))
	_$("filterBoth").style.display = "none";

}

