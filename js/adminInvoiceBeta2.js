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