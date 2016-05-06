$(document).on('submit', '.updateUser', function(){
	$.ajax({
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		data: $(this).serialize(),
		success: function(response)
		{
			var target = $('span#messages');
			var time;
			if(response == true){
				target.html('<div class="success">Record updated successfully.</div>');
				time=2000;
			}else{
				target.html('<div class="error">Failed to update record.</div>');
				time=10000;
			}
			setTimeout( function(){ target.html(''); }, time );
		}
	});
});