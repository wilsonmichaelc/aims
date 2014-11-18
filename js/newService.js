$(document).ready(function(){

	$('form#newServiceRequest').change(function(){
	
		var accountType = $(this).find('input[name="accountType"]').val();
		var requestedServices = new Array();
							
		$('div#allServices > #analysisService').each(function(){
			if( $(this).find('#service').is(':checked') ){
				var samp = $(this).find('#samples').val();
				if( samp > 0 ){
					var service = new Object();
					service.id = $(this).find('div#id').attr('value');
					var r = $(this).find('#replicates').val();
					var s = $(this).find('#samples').val();
					service.samples =  parseInt(r*s);
					if($(this).find('#prep').is(':checked')){
						service.prep = 'true';
					}else{
						service.prep = 'false';
					}
					requestedServices.push(service);
				}
			}else{
				//$(this).find('#samples').val('');
			}
			
		});
		
		if(requestedServices.length > 0){
			$.ajax(
			{
				type: 'post',
				url: 'php/classes/ajax.php',
				dataType: 'json',
				data: 
				{
					json: JSON.stringify(requestedServices),
					accountType: accountType,
					serviceEstimate: ""
				},
				success: function(output)
				{
					$('input#estimate').val('$' + output);
				}
			});
			//console.log(JSON.parse(JSON.stringify(requestedServices)));
		}else{
			$('input#estimate').val('');
		}
	});
	
});

$(document).on('change', 'input#service', function(){
	var service = $(this);
	if(!service.is(':checked')){
		service.closest('#analysisService').find('input#prep').prop("checked", false);
		service.closest('#analysisService').find('input#samples').val('');
		service.closest('#analysisService').find('select#replicates').val(1);
	}
});