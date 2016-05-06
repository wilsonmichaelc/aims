function getTrainingRequests(dateFrom, dateTo, orderBy, userId){
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'json',
		data: 
		{
			dateFrom: dateFrom,
			dateTo: dateTo,
			order: orderBy,
			trainingRequests: userId
		},
		beforeSend: function(b){
			$('#trainingSpinner').show();
		},
		success: function(response)
		{
			$('#trainingSpinner').hide();
			var target = $('div.trainings');
			if(response.length > 0){
			
				var header = '';
				var html = '';
				
				header += '<br><div class="title"><b>Training</b></div>';
				header += '<div class="header">';
				header += 	'<div class="trainingId">ID</div>';
				header +=	'<div class="user">User (ID)</div>';
				header +=	'<div class="instrument">Instrument</div>'; 
				header +=	'<div class="from">From</div>'; 
				header +=	'<div class="to">To</div>';
				header +=	'<div class="hours">Hours</div>';
				header +=	'<div class="estimate">Estaimte</div>'; 
				header += '</div>';
				target.append(header);
				
				$.each(response, function(idx) {
					html += '<div class="training">';
					html += '	<div class="trainingId">'+response[idx].id+'</div>';
					html += '	<div class="user">'+response[idx].userName+' ('+response[idx].id+')</div>';
					html += '	<div class="instrument">'+response[idx].instrumentName+' ('+response[idx].instrumentId+')</div>';
					html += '	<div class="from">' + response[idx].dateFrom + ' ' + response[idx].timeFrom + '</div>';
					html += '	<div class="to">' + response[idx].dateTo + ' ' + response[idx].timeTo + '</div>';
					html += '	<div class="hours">' + response[idx].hours + '</div>';
					html += '	<div class="estimate">$' + response[idx].estimate + '</div>';	
					html += '	<button class="singleTrainingInvoice" value="' + response[idx].id + '">Invoice</button>';
					html += '</div>';
				});
				target.append(html);
				
			}else{
				target.append('<br><div class="title"><b>Training</b><div>There were no training sessions that matched your query.</div></div>');
			}
		}
	});
}

function getTrainingInvoice(invoiceId){
	
	$.ajax({
		type: 'post',
			url: 'php/classes/ajax.php',
			dataType: 'text',
			async: 'true',
			data:
			{
				getTrainingInvoice: invoiceId
			},
			success: function(response)
		{
			if(response == true){
				window.location.href = "tmp/training_invoice.xls";
			}else{
				alert('Excel file was not generated.');
			}			
		}
	});
	
}