//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} 
	else 
	{
	}
}

//Our XmlHttpRequest object to get the auto suggest
var searchReq = getXmlHttpRequestObject();

var g_idstemp = "";
var navSearch;
//Called from keyup on the search textbox.
//Starts the AJAX request.
function searchSuggest(arg_idstemp) 
{  
    if(!navSearch){
        navSearch = $("#nav-search");
    }
    navSearch.addClass("search_active");
	g_idstemp = arg_idstemp;
	
	if (searchReq.readyState == 4 || searchReq.readyState == 0) 
	{
		var input_id = "i_"+arg_idstemp;
		var str      = escape(document.getElementById(input_id).value);
		searchReq.open("GET", APP_URL_JS+'SearchSuggest.php?search='+str+'&idstemp='+arg_idstemp+'&k='+lev0+'&thisMode='+thisMode, true);
		if(mob_web == 'web'){
			searchReq.onreadystatechange = handleSearchSuggest; 
		}else if(mob_web == 'mob'){
			searchReq.onreadystatechange = handleSearchSuggestMobile; 
		}
		searchReq.send(null);
		
        if (str.length > 0)
           {
            var id_sel = "id_"+arg_idstemp;
            var x = document.getElementById(id_sel);
            jQuery(x).slideDown('fast');
           }
        else
           {
            var id_sel = "id_"+arg_idstemp;
            var x = document.getElementById(id_sel);
            jQuery(x).hide();
           }
	}	
}





//Called when the AJAX response is returned.
function handleSearchSuggest() 
{
	if (searchReq.readyState == 4) 
	{
		var currWrapper = "";
		var currItem = "";
		var id_body = "body_"+g_idstemp;
		var ss      = jQuery('#'+id_body);
		var head	= jQuery('#head_'+g_idstemp);
		var cat_title_count = 0;
		ss.empty();
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) 
		{
		 if (str[i] != "")
			{
			 //Build our element string.  This is cleaner using the DOM, but
			 //IE doesn't support dynamically added attributes.
			 //var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			 //suggest += 'onmouseout="javascript:suggestOut(this);" ';
			 //suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			 //suggest += 'class="suggest_link">' + str[i] + '</div>';
			 //ss.innerHTML += suggest;
			
			 currItem = jQuery(str[i]);
			 if (i == 0)
			    {
			 	 head.html(currItem);
			    }
			 else
			    {
				 if (currItem.hasClass('cat_title'))
				    {
				 	 currWrapper = jQuery('<li class="cat_title_wrap"></li>');
					 currWrapper.append(currItem);
					 if (cat_title_count == 0)
					    {
					 	 currWrapper.addClass('cat_title_first');
						 cat_title_count = 1;
					    }
				    }
				 else
				    {
					 currWrapper = jQuery('<li class="node_title_wrap"></li>');
					 currWrapper.append(currItem);
				    }
                                navSearch.removeClass("search_active");
				 ss.append(currWrapper);
			    }
		    }
		}
	}
}

//Called when the AJAX response is returned.
function handleSearchSuggestMobile() 
{
	if (searchReq.readyState == 4) 
	{
		var currWrapper = "";
		var currItem = "";
		var id_body = "body_"+g_idstemp;
		var ss      = jQuery('#'+id_body);
		var head	= jQuery('#head_'+g_idstemp);
		var cat_title_count = 0;
		ss.empty();
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) 
		{
		 if (str[i] != "")
			{
			 //Build our element string.  This is cleaner using the DOM, but
			 //IE doesn't support dynamically added attributes.
			 //var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			 //suggest += 'onmouseout="javascript:suggestOut(this);" ';
			 //suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			 //suggest += 'class="suggest_link">' + str[i] + '</div>';
			 //ss.innerHTML += suggest;
			
			 currItem = jQuery(str[i]);
			 if (i == 0)
			    {
			 	 head.html(currItem);
			    }
			 else
			    {
				 if (currItem.hasClass('cat_title'))
				    {
				 	 currWrapper = jQuery('<li class="cat_title_wrap"></li>');
					 currWrapper.append(currItem);
					 if (cat_title_count == 0)
					    {
					 	 currWrapper.addClass('cat_title_first');
						 cat_title_count = 1;
					    }
				    }
				 else
				    {
					currItem.find('.title_label:first').addClass('li-text').removeClass('title_label');
					currItem.append('<span class="arrow li-icon"></span>');
					 currWrapper = jQuery('<li></li>');
					 currWrapper.append(currItem);
				    }
				 ss.append(currWrapper);
			    }
		    }
		}
	}
}

//Mouse over function
function suggestOver(div_value) {
	div_value.className = 'suggest_link_over';
}
//Mouse out function
function suggestOut(div_value) {
	div_value.className = 'suggest_link';
}
//Click function
function setSearch(value) {
	//document.getElementById('search').value = value;
	//document.getElementById('search_suggest').innerHTML = '';
}

function onblur_extend() 
{
  //var x = document.getElementById("search_suggest");
  //jQuery(x).hide();
  setTimeout(function(){jQuery('.search_suggest_wraper').slideUp('fast');if(navSearch){navSearch.removeClass("search_active");}}, 200);
}
