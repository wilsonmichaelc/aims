$(document).ready(function(){
	$('button.getServices').click(function(){
				
		var dateFrom = 	$('select[name="yearFrom"]').val() + '-' + 
						$('select[name="monthFrom"]').val() + '-' + 
						$('select[name="dayFrom"]').val();
											
		var dateTo = 	$('select[name="yearTo"]').val() + '-' + 
						$('select[name="monthTo"]').val() + '-' + 
						$('select[name="dayTo"]').val();
						
		var orderBy = $('select[name="orderBy"]').val();
		var userId  = $('select[name="userId"]').val();
		
		$('div.bookings').empty();
		$('div.services').empty();
		$('div.trainings').empty();
	
		if($('select[name="requestType"]').val() == 'booking'){
			getBookings(dateFrom, dateTo, orderBy, userId);
		}
		if($('select[name="requestType"]').val() == 'feeForService'){
			getServiceRequests(dateFrom, dateTo, orderBy, userId);
		}
		if($('select[name="requestType"]').val() == 'training'){
			getTrainingRequests(dateFrom, dateTo, orderBy, userId);
		}
		if($('select[name="requestType"]').val() == 'all'){
			getBookings(dateFrom, dateTo, orderBy, userId);
			getServiceRequests(dateFrom, dateTo, orderBy, userId);
			getTrainingRequests(dateFrom, dateTo, orderBy, userId);
		}
		
		$('.spinner').toggle();
	});
	
	$(document).on('click', 'button.singleBookingInvoice', function()
	{
	   getBookingInvoice($(this).attr('value'));
	});
	
	$(document).on('click', 'button.singleServiceInvoice', function()
	{
	   getServiceInvoice($(this).attr('value'));
	});
	
	$(document).on('click', 'button.singleTrainingInvoice', function()
	{
	   getTrainingInvoice($(this).attr('value'));
	});
});