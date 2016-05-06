$(document).on('change', 'form[name="updateProject"]', function(){
	var form = $(this);
	var id = form.find('input[name="updateProject"]').val();
	var time = 0;
	$.ajax({
		url: "php/classes/ajax.php",
		data: form.serialize(),
		type: 'POST',
		dataType: 'text',
		success: function(response){
			var target = $('div#project'+id+'.ProjectResponse ');
			if(response == true){
				target.html('Saved &#x2713;').css('color', 'green');
				time = 1500;
			}else{
				target.html('Error!').css('color', 'red');
				time = 5000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});