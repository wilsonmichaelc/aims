$(document).ready(function(){
	updateInstrumentByMonth();
	updateServiceRequestsByMonth();
});

$('form[name="InstrumentByMonth"]').submit(function(){
	updateInstrumentByMonth();
});

$('form[name="ServiceRequestsByMonth"]').submit(function(){
	updateServiceRequestsByMonth();
});

$('select[name="instrument"]').change(function(){
	if($('select[name="instrument"] :selected').val() != 'null'){
		$('select[name="user"]').val('null');
		$('select[name="month"]').hide();
		$('select[name="endYear"]').show();
	}
});

$('select[name="user"]').change(function(){
	if($('select[name="user"] :selected').val() != 'null'){
		$('select[name="instrument"]').val('null');
		$('select[name="month"]').show();
	}else{
		$('select[name="month"]').hide();
	}
});

$('select[name="month"]').change(function(){
	if($('select[name="month"] :selected').val() != 'null'){
		$('select[name="endYear"]').hide();
	}else{
		$('select[name="endYear"]').show();
	}
});



// Instrument Usage By Month
function updateInstrumentByMonth(){
	
	var form = $('form[name="InstrumentByMonth"]');
	
	var jsonData = $.ajax({
		url: "php/classes/ajax.php",
		type: "post",
		dataType: "json",
		data: { 
			InstrumentByMonth: "true",
			startYear: form.find('select[name="startYear"] :selected').val(),
			endYear: form.find('select[name="endYear"] :selected').val(),
			instrument: form.find('select[name="instrument"]').val(),
			user: form.find('select[name="user"]').val(),
			month: form.find('select[name="month"]').val()
		},
		async: false
	}).responseJSON;
				
	var max = Math.max.apply(Math, JSON.parse(jsonData)['datasets'][0]['data']);
	$(JSON.parse(jsonData)['datasets']).each(function(idx, obj){
		if(Math.max.apply(Math, obj.data) > max){
			max = Math.max.apply(Math, obj.data);
		}
	});
	var steps = max;
	
	var options = {
		scaleOverride : true,
		scaleSteps : steps,
		scaleStepWidth : Math.ceil(max/steps),
		scaleStartValue : 0
	};
	
	var html = '';
	
	$(JSON.parse(jsonData)['datasets']).each(function(idx, obj){
		html += '<div style="display: inline; width: 100px; height: 30px; margin-left: 50px;">';
		html += '	<span style="display: inline-block; vertical-align: middle; width: 18px; height: 18px; background-color: '+obj.fillColor+'; border: 2px solid '+obj.strokeColor+';"></span>';
		
		if(form.find('select[name="user"]').val() != 'null'){
			html +=	'	<span style="display: inline-block; height: 10px;">&nbsp;' + form.find('select[name="user"] :selected').text(); 
			if(form.find('select[name="month"]').val() != 'null'){
				html += ' (' + form.find('select[name="month"] :selected').text() + ')';
			}
			html += '</span>';
			
		}else if(form.find('select[name="instrument"]').val() != 'null'){
			html +=	'	<span style="display: inline-block; height: 10px;">&nbsp;' + form.find('select[name="instrument"] :selected').text() + '</span>';
		}else{
			html +=	'	<span style="display: inline-block; height: 10px;">&nbsp;' + obj.title + '</span>';
		}
		
		html += '</div>';
	});
	
	$('.InstrumentByMonth.legend').html(html);
	
	var instrumentUsage = new Chart(document.getElementById("InstrumentByMonth").getContext("2d")).Bar(JSON.parse(jsonData));

}

function updateServiceRequestsByMonth(){

	var form = $('form[name="ServiceRequestsByMonth"]');
	
	var jsonData = $.ajax({
		url: "php/classes/ajax.php",
		type: "post",
		dataType: "json",
		data: { 
			ServiceRequestsByMonth: "true",
			startYear: form.find('select[name="startYear"] :selected').val(),
			endYear: form.find('select[name="endYear"] :selected').val()
		},
		async: false
	}).responseJSON;
				
	var max = Math.max.apply(Math, JSON.parse(jsonData)['datasets'][0]['data']);
	$(JSON.parse(jsonData)['datasets']).each(function(idx, obj){
		if(Math.max.apply(Math, obj.data) > max){
			max = Math.max.apply(Math, obj.data);
		}
	});
	var steps = max;
	
	var options = {
		scaleOverride : true,
		scaleSteps : steps,
		scaleStepWidth : Math.ceil(max/steps),
		scaleShowLabels : true,
		scaleStartValue : 0
	};
	
	var html = '';
	
	$(JSON.parse(jsonData)['datasets']).each(function(idx, obj){
		html += '<div style="display: inline; width: 100px; height: 30px; margin-left: 50px;">';
		html += '	<span style="display: inline-block; vertical-align: middle; width: 18px; height: 18px; background-color: '+obj.fillColor+'; border: 2px solid '+obj.strokeColor+';"></span>';
		html +=	'	<span style="display: inline-block; width: 40px; height: 10px;">&nbsp;' + obj.title + '</span>';
		html += '</div>';
	});
	
	$('.ServiceRequestsByMonth.legend').html(html);
	
	var instrumentUsage = new Chart(document.getElementById("ServiceRequestsByMonth").getContext("2d")).Bar(JSON.parse(jsonData));

}