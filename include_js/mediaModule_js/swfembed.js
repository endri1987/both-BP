var player;
var currentItem = -1; 
var previousItem = -1; 

function returnUTS() {
	var d = new Date();
	return d.getTime() + '' + Math.floor(1000 * Math.random());
}

function playerReady(obj) {
	player = window.document[obj.id];
	//addListeners(player)
}

function addListeners(player) {
	if (player) {
		player.addControllerListener("ITEM", "itemListener");
	} else {
		setTimeout("addListeners(player)",100);
	}
}
function itemListener(obj) {
	//title_plyCI62551511457_0
	if (obj.index != currentItem) {
		previousItem = currentItem;
		currentItem = obj.index;
		$("#title_"+obj.id+"_"+previousItem).removeClass('selected')
		$("#title_"+obj.id+"_"+currentItem).addClass('selected')
	}

}

function init(idf,key,item) {
	player = window.document['ply'+idf];
	$("#title_ply"+idf+"_"+item).removeClass('selected')
	addListeners(player)
	player.sendEvent('STOP');
	player.sendEvent('ITEM',key);
}






