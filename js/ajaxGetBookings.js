function getBookings(dateFrom, dateTo, orderBy, userId){

	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'json',
		async: true,
		data: 
		{
			dateFrom: dateFrom,
			dateTo: dateTo,
			order: orderBy,
			user: userId,
			booking: ""
		},
		beforeSend: function(b){
			$('#bookingSpinner').show();
		},
		success: function(output)
		{
			$('#bookingSpinner').hide();
			if(output.length > 0){
						
				var header = '';
				header += '<div class="title"><b>Bookings</b>';
				header += '</div><div class="header">';
				header += 	'<div class="bookingId">ID</div>';
				header +=	'<div class="user">User (ID)</div>';
				header +=	'<div class="accountType">Type</div>'; 
				header +=	'<div class="project">Project (ID)</div>'; 
				header +=	'<div class="instrument">Instrument (ID)</div>';
				header +=	'<div class="from">From</div>';
				header +=	'<div class="to">To</div>';
				header += 	'<div class="hours">Hours</div>';
				header +=	'<div class="estimate">Estimate</div>';
				header +=	'<div class="invoise">Invoice</div>';
				header += '</div>';
				$('div.bookings').append(header);
				
				$.each(output, function(index) {
					
					var hours = 0;
					$.ajax({
						type: 'post',
							url: 'php/classes/ajax.php',
							dataType: 'text',
							async: false,
							data:
							{
								dateFrom: output[index].dateFrom,
								timeFrom: output[index].timeFrom,
								dateTo: output[index].dateTo,
								timeTo: output[index].timeTo,
								calculateHours: ''
							},
							success: function(response)
							{
								hours = response;
							}
					});
					
					
					$.ajax(
						{
							type: 'post',
							url: 'php/classes/ajax.php',
							dataType: 'text',
							data:
							{
								booking_metadata: "",
								userId: output[index].userId,
								projectId: output[index].projectId,
								instrumentId: output[index].instrumentId,
								accountType: output[index].accountType,
								hours: hours
							},
							success: function(metaData)
							{
								var metaArray = metaData.split(',');
								var html = '<div class="booking" id="bid' + output[index].id + '"><div class="bookingId">' + output[index].id + '</div>' + 
								'<div class="user">' + metaArray[0] + ' (' + output[index].userId + ')' + '</div>' +
								'<div class="accountType">' + metaArray[3] + '</div>' +
								'<div class="project">' + metaArray[1] + ' (' + output[index].projectId + ')' + '</div>' + 
								'<div class="instrument">' + metaArray[2] + ' (' + output[index].instrumentId + ')</div>' +
								'<div class="from">' + output[index].dateFrom + ' ' + output[index].timeFrom + '</div>' +
								'<div class="to">' + output[index].dateTo + ' ' + output[index].timeTo + '</div>' +
								'<div class="hours">' + hours + '</div>' +
								'<div class="estimate">' + metaArray[4] + '</div>' +								
								'<button class="singleBookingInvoice" value="' + output[index].id + '">Invoice</button>' +
								'</div>';
								
								$('div.bookings').append(html);
								
							}
						}
					)
					
				 });
			 }else{
				 $('div.bookings').append('<br><div class="title"><b>Bookings</b><div>There were no bookings that matched your query.</div></div>');
			 }
			 
		}
	});
}

function getBookingInvoice(bookingId){
	
	$.ajax({
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		async: 'true',
		data:
		{
			getBookingInvoice: bookingId
		},
		success: function(response)
		{
			if(response == true){
				window.location.href = "tmp/booking_invoice.xls";
			}else{
				alert('Excel file was not generated.');
			}			
		}
	});
	
}