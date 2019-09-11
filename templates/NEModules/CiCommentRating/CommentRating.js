function returnUTS() {
	var d = new Date();
	return d.getTime() + '' + Math.floor(1000 * Math.random());
}
function showCenter(w,h){

	var  left = "left="+parseInt((screen.availWidth/2) - (w/2))+"px, ";
	var  top = "top="+parseInt((screen.availHeight/2) - (h/2))+"px, ";
	return left+top;
}
var DoUrlC = APUL+'templates/NEModules/ajaxrespvfl.php';
var ajaxGenVarC = '&uni='+uniF+'&u='+returnUTS();

function del_cr(rid)
{
 	var pars = '?ap_process=del_cr&crid='+rid
	$j.ajax({
	   type: "post",url: DoUrlC+pars+ajaxGenVarC,
	   success: function(resp){
			onChangeListState_CR(1,recPages);
			$j('#messageBoxEditCR').dialog("close");
	   },
	   error: function(e){
	   }
	});
}
function add_cr()
{
 	var pars = '?ap_process=save_cr'
	$j.ajax({
	   type: "post",url: DoUrlC+pars+ajaxGenVarC,
	   data:$j('#updITem').serialize(), 
	   success: function(resp){
			onChangeListState_CR(1,recPages);
			$j('#messageBoxEditCR').dialog('close');
	   },
	   error: function(e){
	   }
	});
}
function openWinR_CR()
{
 	var pars = '?ap_process=add_cr&fln='+cln+'&fci='+cci+'&fu='+cu+md+"&st="+st
	$j.ajax({
	   type: "post",
	   url: DoUrlC+pars+ajaxGenVarC,
	   success: function(resp){
		 $j('#MainCntEditCR').html(resp)
		  addRateYll();
		 $j('#messageBoxEditCR').dialog("open");
		 $j('#messageBoxEditCR').dialog({
			width: 500,
			title:	_comment_title_add,
			modal: true
		 });
	   },
	   error: function(e){
	   }
	});
}
function openWinRe_CR(rid)
{
 	var pars = '?ap_process=add_cr&fln='+cln+'&fci='+cci+'&fu='+cu+md+"&st="+st+"&crid="+rid
	$j.ajax({
	   type: "post",url: DoUrlC+pars+ajaxGenVarC,
	   success: function(resp){
		 $j('#MainCntEditCR').html(resp)
		 addRateYll()
		 $j('#messageBoxEditCR').dialog({
			width: 500,
			title:	_comment_title_modify
		 });
	   },
	   error: function(e){
	   }
	});
}
function onChangeListState_CR(rpp,rp) {
	var pars = '?ap_process=cr_list_refresh&fln='+cln+'&fci='+cci+'&fu='+cu+md+"&st="+st+'&rpp='+rpp+'&rp='+rp
	 $j.ajax({  
		type: "post",  
		dataType: "html",
		url: DoUrlC+pars+ajaxGenVarC,  
		data:'&rpp='+rpp+'&rp='+rp+'&msvSrc='+$j('#msvSrc').val(), 
		success: function(resp){  
			st_arr=resp.split('#####'); 
			var list_filteredPart = st_arr[0]
			$j('#rfrList_cr').html(list_filteredPart);
		
		}
	 }); 	
} 