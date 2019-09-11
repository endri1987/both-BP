
function include_js(script_filename) {
	document.write('<' + 'script');
	document.write(' language="javascript"');
	document.write(' type="text/javascript"');
	document.write(' src="' + script_filename + '">');
	document.write('</' + 'script' + '>');
}

function include_css(css_filename) {
	document.write('<' + 'link');
	document.write(' rel="stylesheet"');
	document.write(' type="text/css"');
	document.write(' href="' + css_filename + '"/>');
}


function call_includes() {
	//include_css(APP_URL+'include_css/default.css');
	include_css(APP_URL+'include_css/global.css');

	include_js(APP_URL+'include_js/mediaModule_js/swfobject.js');
	include_js(APP_URL+'include_js/mediaModule_js/swfembed.js');


}
call_includes();