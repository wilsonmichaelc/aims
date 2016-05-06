$(document).on('change', '.updateInstrument', function(e){
	e.preventDefault();
	var t = $('.updateInstrument span#id' + $(this).find('input[name="id"]').val());
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		data: 
		{
			id: $(this).find('input[name="id"]').val(),
			name: $(this).find('input[name="name"]').val(),
			model: $(this).find('input[name="model"]').val(),
			asset: $(this).find('input[name="asset"]').val(),
			accuracy: $(this).find('select[name="accuracy"] option:selected').val(),
			minBookableUnit: $(this).find('select[name="minBookableUnit"] option:selected').val(),
			color: $(this).find('input[name="color"]').val(),
			bookable: $(this).find('input[name="bookable"]:checked').val(),
			location: $(this).find('input[name="location"]').val(),
			updateInstrument: ""
		},
		success: function(output)
		{
			if(output == 'Success!'){
				t.html('<span style="color: green;">&nbsp;&nbsp;&nbsp;&#x2713;</span>');
				setTimeout(function(){
		        	t.html('');
		        },2000);
			}else{
				t.html('<span style="color: red;">&nbsp;&nbsp;&nbsp;&#x2717; ' + output + '</span>')
				setTimeout(function(){
		        	t.html('');
		        },10000);
			}
		}
	});
});