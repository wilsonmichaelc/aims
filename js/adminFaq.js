$(document).on('submit', '.updateFaq', function(e){
	e.preventDefault();
	var form = $(this);
	$.ajax({
		type: 'post',
		url: 'php/classes/ajax.php',
		async: true,
		dataType: 'text',
		data: form.serialize(),
		success: function(response)
		{
			var target = form.find('.ajaxResponse');
			var time = 0;
			if(response == true){
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

$(document).on('submit', '.deleteFaq', function(e){
	e.preventDefault();
	var form = $(this);
	$.ajax({
		type: 'post',
		url: 'php/classes/ajax.php',
		async: true,
		dataType: 'text',
		data: form.serialize(),
		success: function(response)
		{
			var target = form.find('.ajaxResponse');
			var time = 0;
			if(response == true){
				var id = form.find('input[name="id"]').val();
				$('.updateFaq#'+id).remove();
				form.remove();
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});