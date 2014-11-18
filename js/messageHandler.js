$(document).ready(function(){
	if($.trim($('div.success').text()) === ""){
		$('div.success').hide();
	}else{
		$('div.success').delay(25000).fadeOut('slow');
	}
});