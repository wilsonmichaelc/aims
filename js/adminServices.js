$(document).ready(function(){
	$('input[name="prepCheckbox"]').change(function(){
		if($('input[name="prepCheckbox"]').prop('checked')){
			$('table#prepCheckbox').show("medium");
		}else{
			$('table#prepCheckbox').hide("medium");
		}
	})
	// Update button pressed
	$(document).on('change', '.updateService', function(e){
		var form = $(this);
		e.preventDefault();
		$.ajax(
		{
			type: 'post',
			url: 'php/classes/ajax.php',
			dataType: 'text',
			data: form.serialize(),
			success: function(response)
			{
				var target = form.find('.updateServiceResponse');
				var time = 0;
				if(response == true){

					target.html('Saved &#x2713;').css('color', 'green');
					time = 2000;

				}else{
				
					target.html('Error &#x2717;').css('color', 'red');
					time = 10000;
					
				}
				setTimeout(function(){ target.html(''); },time);
			},

		});
	});
	// END UPDATE BUTTON
});