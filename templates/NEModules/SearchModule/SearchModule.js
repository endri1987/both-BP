function on_searchTerm(arg_idstemp) 
{
  var form_id = "f_"+arg_idstemp;
  if (document.getElementById(form_id))
     {
      var form        = document.getElementById(form_id);
      var search_term = form.search.value;

      search_term       = search_term.replace(/^\s+|\s+$|'|"|;|>|</g,'');
      form.search.value = search_term;

      if (search_term.length < 3 )
         {
          alert(js_search_kriteria_more_than_mesg);
          form.search.focus();
          return;
         }

      if (search_term != '') 
         {		
          if (search_mode == "cache_dynamic")
             {
	     	  form.action = tp[arg_idstemp];
	    	  form.submit();
             }
          else
             {
              GoTo('thisPage?event=none.search(search_for='+search_term+';'+tp[arg_idstemp]+')');
             }
         } 
     }
}

function resetField(obj, str)
{
  if (obj.value.length == 0)
     {
      obj.value = str;
      return;
     }

  if (obj.value == str)
     {
      obj.value="";
     }
	onblur_extend();
}

function clear_search(arg_idstemp)
{
  var form_id = "f_"+arg_idstemp;
  if (document.getElementById(form_id))
     {
      document.getElementById(form_id).search.value = "";
      document.getElementById(form_id).search.focus();
     }
}