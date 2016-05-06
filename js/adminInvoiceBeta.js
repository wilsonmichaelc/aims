$(document).on('click', 'input[name="getUsersForSelection"]', function(){

	$('div.project').empty('');
	$('div.bookings').empty('');
	$('div.requests').empty('');
	$('div.training').empty('');
	$('div.createButton').empty('');
	
	$('div.userList').hide();
	$('select[name="userId"]').html('<option value=""></option>');
	
	$('div.projectList').hide();
	$('select[name="projectId"]').html('<option value=""></option>');
	
	var dateFrom = $('select[name="yearFrom"]').val() + '-' + $('select[name="monthFrom"]').val() + '-' + $('select[name="dayFrom"]').val();
	var dateTo = $('select[name="yearTo"]').val() + '-' + $('select[name="monthTo"]').val() + '-' + $('select[name="dayTo"]').val();
	
	$.ajax({
		type: 'post',
		url: 'php/classes/ajaxAdminInvoiceBeta.php',
		dataType: 'text',
		data: {
			getUsersForSelection: '',
			dateFrom: dateFrom,
			dateTo: dateTo 
		},
		success: function(response)
		{
			var json = JSON.parse(response);
			$(json).each(function(i){
				$('select[name="userId"]').append('<option value="'+json[i].id+'">'+json[i].first+' '+json[i].last+'</option>');

			});
		}
	});
	
	$('div.userList').show();
	
});

$(document).on('change',  'select[name="projectId"]',  function(){

	var id = $('select[name="projectId"]').val();

	if(id == ''){
		$('div.project').text('');
		$('div.bookings').text('');
		$('div.requests').text('');
		$('div.training').text('');
		$('div.createButton').text('');
	}else{

		$('#spinner').show();
		$.ajax(
		{
			type: 'post',
			url: 'php/classes/ajaxAdminInvoiceBeta.php',
			dataType: 'text',
			data: {
				getProjectAsJSON: id
			},
			success: function(response)
			{
				$('div.project').text('');
				$('div.bookings').text('');
				$('div.requests').text('');
				$('div.training').text('');
				$('div.createButton').text('');

				var json = JSON.parse(response); // JSON with the following structure
				//console.log(json);
				
				/*
				 * 
				 * Display the project status/info
				 *
				*/
				var projectHtml = '<b>Project Info</b>';
				projectHtml += '<div class="projectInfo">' + json.project.title + ' (' + json.project.id + ') ';

				if(json.project.status == 'active'){
					projectHtml += '<span style="color: green;">' + (json.project.status).toUpperCase() + '</span>';
				}else{
					projectHtml += '<span style="color: red;">' + (json.project.status).toUpperCase() + '</span>';
				}
				
				projectHtml += '<span>' + json.user.first + ' ' + json.user.last + ' (' + json.user.id + ')</span>';
				projectHtml += '<span>' + json.user.name + '</span>';
				
				projectHtml += '</div>';

				$('div.project').append(projectHtml);

				/*
				 * 
				 * Display the booking info
				 *
				*/
				var bookingInfo = '';
				
				if(json.bookings.length > 0){
					bookingInfo += '<b>Bookings </b><span style="font-size: 12px; font-weight: bold;"> (You cannot mix high and low accuracy instruments on the same invoice.)</span><div class="bookingHeader"><span id="check"></span><span id="bid">ID</span><span id="inst">Instrument</span><span id="from">From</span><span id="to">To</span><span id="hrs">Hours</span></div>';
				}
				
				$(json.bookings).each(function(idx, obj){
				
					var hours = 0;
					$.ajax({
						type: 'post',
							url: 'php/classes/ajax.php',
							dataType: 'text',
							async: false,
							data:
							{
								dateFrom: obj.dateFrom,
								timeFrom: obj.timeFrom,
								dateTo: obj.dateTo,
								timeTo: obj.timeTo,
								calculateHours: ''
							},
							success: function(response)
							{
								hours = response;
							}
					});
				
					// Create a div w/ class bookingInfo for each booking
					bookingInfo += '<div class="bookingInfo">';
					bookingInfo += '<span id="check"><input type="checkbox" name="booking" value="' + obj.id + '" accuracy="' + obj.accuracy + '"></span>';
					bookingInfo += '<span id="bid">' + obj.id + '</span>';
					bookingInfo += '<span id="inst" style="color: ' + obj.color + ';">' + obj.name;
					if(obj.accuracy == 'high'){
						bookingInfo +=  '(H)</span>';
					}else{
						bookingInfo += '(L)</span>';
					}
					bookingInfo += '<span id="from">' + obj.dateFrom + '@' + obj.timeFrom  + '</span>';
					bookingInfo += '<span id="to">' + obj.dateTo + '@' + obj.timeTo + '</span>';
					bookingInfo += '<span id="hrs">' + hours + '</span>';					
					bookingInfo += '</div>';
					
					// Append this newly created bookingInfo div to the bookings div
					
				});
				
				$('div.bookings').append(bookingInfo);
				
				/*
				 * 
				 * Display the service requests
				 *
				*/
				var serviceInfo = '';
				
				if(json.requests.length > 0){
					serviceInfo += '<b>Service Requests</b><div class="serviceHeader"><span id="check"></span><span id="sid">ID</span><span id="rid">Request</span><span id="name">Service</span><span id="samples">Samples</span><span id="replicates">Replicates</span><span id="prep">Prep</span></div>';
				}
				
				for(var i=0; i<json.requests.length; i++){
					for(var j=0; j<json.requests[i].length; j++){
						
						//console.log(json.requests[i][j]);
						serviceInfo += '<div class="serviceInfo">';
						serviceInfo += '<span id="check"><input type="checkbox" name="service" value="' + json.requests[i][j].id + '" /></span>';
						serviceInfo += '<span id="sid">' + json.requests[i][j].id + '</span>';
						serviceInfo += '<span id="rid">' + json.requests[i][j].requestId + '</span>';
						serviceInfo += '<span id="name">' + json.requests[i][j].name + '</span>';
						serviceInfo += '<span id="samples">' + json.requests[i][j].samples + '</span>';
						serviceInfo += '<span id="replicates">' + json.requests[i][j].replicates + '</span>';
						serviceInfo += '<span id="prep">' + json.requests[i][j].prep + '</span>';
						serviceInfo += '</div>';
						
					}	
				}
				
				$('div.requests').append(serviceInfo);
				
				/*
				 * 
				 * Display the training requests
				 *
				*/
				var trainingInfo = '';
				
				if(json.training.length > 0){
					trainingInfo += '<b>Training</b><div class="trainingHeader"><span id="check"></span><span id="tid">ID</span><span id="inst">Instrument</span><span id="from">From</span><span id="to">To</span></div>';
				}
				
				$(json.training).each(function(idx, obj){
					
					trainingInfo += '<div class="trainingInfo">';
					trainingInfo += '<span id="check"><input type="checkbox" name="service" value="' + obj.id + '"></span>';
					trainingInfo += '<span id="tid">' + obj.id + '</span>';
					trainingInfo += '<span id="inst" style="color: ' + obj.color + ';">' + obj.name + '</span>';
					trainingInfo += '<span id="from">' + obj.dateFrom + '@' + obj.timeFrom + '</span>';
					trainingInfo += '<span id="to">' + obj.dateTo + '@' + obj.timeTo + '</span>';
					trainingInfo += '</div>';
					
				});
				$('div.training').append(trainingInfo);
				$('div.createButton').append('<form action="javascript:void(0)"><input type="submit" name="createInvoice" value="Generate Invoice" /></form>');
				
				// Finally... if this query even took long enough to show the spinner... hide the spinner
				$('#spinner').hide();
				
			},

		});

	}

});

$(document).on('change', 'select[name="userId"]',  function(){

	$('div.project').empty('');
	$('div.bookings').empty('');
	$('div.requests').empty('');
	$('div.training').empty('');
	$('div.createButton').empty('');
	$('select[name="projectId"]').html('<option value=""></option>');

	var userId = $('select[name="userId"]').val();
	var dateFrom = $('select[name="yearFrom"]').val() + '-' + $('select[name="monthFrom"]').val() + '-' + $('select[name="dayFrom"]').val();
	var dateTo = $('select[name="yearTo"]').val() + '-' + $('select[name="monthTo"]').val() + '-' + $('select[name="dayTo"]').val();

	if(userId == ''){
		$('select[name="projectId"]').html('');
		$('div.projectList').hide();
	}else{

		$.ajax(
		{
			type: 'post',
			url: 'php/classes/ajaxAdminInvoiceBeta.php',
			dataType: 'text',
			data: {
				getProjectsForSelection: userId,
				dateFrom: dateFrom,
				dateTo: dateTo 
			},
			success: function(response)
			{
				var json = JSON.parse(response);
				if(json.length > 0){
					$(json).each(function(idx, obj){
						$('select[name="projectId"]').append($("<option></option>").attr("value",obj.id).text(obj.title));
					});
				}
			},

		});
		
		$('div.projectList').show();

	}

});

$(document).on('click', 'input[name="createInvoice"]', function(){
	
	var userId = $('select[name="userId"] option:selected').val();					// Get the user ID
	var projectId = $('select[name="projectId"] option:selected').val();			// Get the project ID
		
	var allData = {};																// Create an object to hold all this data
	
	allData.userId = userId;														// Store the user ID
	allData.projectId = projectId;													// Store the project ID
	
	/*
	 *	Bookings
	 */
	var selectedBookings = []; 														// Create an array to hold the selected bookings
	$("div.bookingInfo").each(function(index){										// Iterate over the bookings.
		if($(this).find('span#check input').is(':checked')){						// If the booking has been selected,
			selectedBookings.push($(this).find('span#check input').val());			// add its ID to the list
		}
	});
	allData.bookings = selectedBookings;											// Add the list of booking to the json object for processing

	
	/*
	 *	Servcice Requests
	 */
	var selectedServiceRequests = [];												// Create an array to hold the selected service requests
	$("div.serviceInfo").each(function(index){										// Iterate over the service requests.
		if($(this).find('span#check input').is(':checked')){						// If the service requests has been selected,
			selectedServiceRequests.push($(this).find('span#check input').val());	// add its ID to the list
		}
	});
	allData.serviceRequests = selectedServiceRequests;								// Add the list of booking to the json object for processing
	
	/*
	 *	Training
	 */
	var selectedTraining = [];														// Create an array to hold the selected training
	$("div.trainingInfo").each(function(index){										// Iterate over the trainings.
		if($(this).find('span#check input').is(':checked')){						// If the training has been selected,
			selectedTraining.push($(this).find('span#check input').val());			// add its ID to the list
		}
	});
	allData.trainings = selectedTraining;											// Add the list of booking to the json object for processing
	
	if(allData.bookings.length > 0 || allData.serviceRequests.length > 0 || allData.trainings.length > 0){
		$.ajax({
			type: 'post',
			url: 'php/classes/ajaxAdminInvoiceBeta.php',
			dataType: 'json',
			async: false,
			data:
			{
				generateInvoice: JSON.stringify(allData)
			},
			success: function(response){
				if(response){
					
					$("div.bookingInfo").each(function(index){										// Iterate over the service requests.
						if($(this).find('span#check input').is(':checked')){						// If the service requests has been selected,
							$(this).closest('.bookingInfo').remove();
						}
					});
					$("div.serviceInfo").each(function(index){										// Iterate over the service requests.
						if($(this).find('span#check input').is(':checked')){						// If the service requests has been selected,
							$(this).closest('.serviceInfo').remove();
						}
					});
					$("div.trainingInfo").each(function(index){										// Iterate over the service requests.
						if($(this).find('span#check input').is(':checked')){						// If the service requests has been selected,
							$(this).closest('.trainingInfo').remove();
						}
					});
				
					window.location.href = "tmp/invoice.xls";
				}else{
					alert('Excel file was not generated.');
				}
			}
		});
	}else{
		alert('You must select at least one service to generate an invoice.');
	}
	
});

var currentClass = "";

$(document).on('change', 'input[name="booking"]',  function(){

	// Is this item being checked?
	if(this.checked){
		// Get the recently selected class
		var instrumentClass = $(this).attr("accuracy");

		// If the class type is not set, set it.
		if(currentClass == ""){
			currentClass = instrumentClass;
		}else{
			// If the class type doesnt match what was just selected, uncheck the recently selected item.
			if(instrumentClass != currentClass){
				$(this).attr('checked', false);
			}
		}
	}else{

	}
	

	// If there are not items selected, reset the class type.
	var count = 0;
	$( 'div.bookings input[name="booking"]' ).each(function( idx ) {
		if(this.checked){
			count++;
		}
	});
	//console.log(count);
	if(count == 0){
		currentClass = "";
	}

});



