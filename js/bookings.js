$(document).ready(function(){
	
	$(document).ready(function(){
		$('span.fc-button.fc-button-agendaWeek, span.fc-button.fc-button-agendaDay').bind('click', function(){
			if( $('.fc-header-title #noteaboutmonthview').length == 0 ){
				$('.fc-header-title').append('<h4 id="noteaboutmonthview">Please use the month view to book instruments.</h4>');
			}
		});
		$('span.fc-button.fc-button-month').bind('click', function(){
			if( $('.fc-header-title #noteaboutmonthview').length == 1 ){
				$('.fc-header-title #noteaboutmonthview').remove();
			}
		});
	});
	
	$('form#newBooking').change(function(){
					
		var instrumentId = $('select[name="instrumentId"]').val();
		var dateFrom = $('input[name="dateFrom"]').val().split("/");
		var dateTo = $('input[name="dateTo"]').val().split("/");
		var timeFrom = $('select[name="timeFrom"]').val().split(":");
		var timeTo = $('select[name="timeTo"]').val().split(":");
		var training = false;
		if($('select[name="projectId"]').val() == 'training'){
			training = true;
		}
		
		if(instrumentId != '' && dateFrom != '' && dateTo != '' && timeFrom != '' && timeTo != ''){
			var from = new Date(dateFrom[2], dateFrom[0], dateFrom[1], timeFrom[0], timeFrom[1], timeFrom[2]);
			//console.log(from);
			
			var to = new Date(dateTo[2], dateTo[0], dateTo[1], timeTo[0], timeTo[1], timeTo[2]);
			//console.log(to);
			
			var diff = to.getTime() - from.getTime();
			//console.log(diff);
			//console.log(diff / 3600000); // convert from milliseconds to hours
			var totalHours = diff / 3600000;
			$('input#hours').val(totalHours);
			
			if(training){
			
				
				$.ajax(
				{
					type: 'post',
					url: 'php/classes/ajax.php',
					dataType: 'text',
					data: 
					{
						accountType: $('input#accountType').val(),
						hours: totalHours,
						trainingEstimate: ""
					},
					success: function(output)
					{
						$('input#estimate').val('$' + output);
					}
				});
				
				//var total = totalHours * 100;
				//$('input#estimate').val('$' + total);
			
			}else{
			
				$.ajax(
				{
					type: 'post',
					url: 'php/classes/ajax.php',
					dataType: 'text',
					data: 
					{
						accountType: $('input#accountType').val(),
						instrument: instrumentId, 
						hours: totalHours,
						bookingEstimate: ""
					},
					success: function(output)
					{
						$('input#estimate').val('$' + output);
					}
				});
				
			}
			
		}
		
	});
	
	$('select[name="instrumentId"]').change(function(){
		var minUnit = $('option:selected', this).attr('minUnit');
		
		$('select[name="timeFrom"]').html('<option value=""></option>');
		$('select[name="timeTo"]').html('<option value=""></option>');
		
		//console.log(minUnit);
		for(var hour=0; hour<=24; hour++){
							
			for(var minute=0; minute<60;){
																							
				var option = '<option value="';
				if(hour<=9){option += '0' + hour + ':';}else{option += hour + ':';}
				if(minute == 0){option += '0' + minute + ':00">';}else{option += minute + ':00">';}
				
				if(hour<=9){option += '0' + hour + ':';}else{option += hour + ':';}
				if(minute == 0){option += '0' + minute + '</option>';}else{option += minute + '</option>';}
				
				$('select[name="timeFrom"]').append(option);
				$('select[name="timeTo"]').append(option);
				minute += parseInt(minUnit);
			}
				
		}
						
	});
	
	$('select[name="projectId"]').change(function(){
		if( $('option:selected', this).val() == "training" ){
			$('#trainingId-showHide').css('display', 'block');
			$('select[name="projectId"]').prop('required', true);
		}else{
			$('#trainingId-showHide').css('display', 'none');
			$('select[name="projectId"]').prop('required', false);
		}
	});
	
});



			
/*			
$(document).on('change', 'select[name="projectId"]', function(){
	if($(this).val() == 'training'){
		alert('changed');
		$('#trainingId-showHide').toggle();
		$('select[name="projectId"]').prop('required', true);
	}else{
		$('#trainingId-showHide').toggle();
		$('select[name="projectId"]').prop('required', false);
	}
});
*/

$('select[name="instrumentId"]').change(function(){
	if($(this).val()[0] == 'c'){
		$('#projectId-showHide').hide();
		$('#estimate-showHide').hide();
		$('select[name="projectId"]').prop('required', false);
	}else{
		$('#projectId-showHide').show();
		$('#estimate-showHide').show();
		$('select[name="projectId"]').prop('required', true);
	}
});