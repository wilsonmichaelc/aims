$(document).ready(function(){
	$('.project-info-tab').click(function(){
	
		var id = $(this).attr('id');
		var article	= $(this).closest('article');
		
		article.find('.project-services#' + id).hide('fast');
		article.find('.project-bookings#' + id).hide('fast');
		article.find('.project-info#' + id).show('medium');
		
		article.find('.project-info-tab').css('background-color', 'rgba(154, 154, 154, .5)');
		article.find('.project-services-tab').css('background-color', '');
		article.find('.project-bookings-tab').css('background-color', '');
		
	});
	
	$('.project-services-tab').click(function(){
	
		var id = $(this).attr('id');
		var article	= $(this).closest('article');
		
		article.find('.project-info#' + id).hide('fast');
		article.find('.project-bookings#' + id).hide('fast');
		article.find('.project-services#' + id).show('medium');
		
		article.find('.project-services-tab').css('background-color', 'rgba(154, 154, 154, .5)');
		article.find('.project-info-tab').css('background-color', '');
		article.find('.project-bookings-tab').css('background-color', '');
		
	});
	
	$('.project-bookings-tab').click(function(){
	
		var id = $(this).attr('id');
		var article	= $(this).closest('article');
		
		article.find('.project-services#' + id).hide('fast');
		article.find('.project-info#' + id).hide('fast');
		article.find('.project-bookings#' + id).show('medium');
		
		article.find('.project-bookings-tab').css('background-color', 'rgba(154, 154, 154, .5)');
		article.find('.project-info-tab').css('background-color', '');
		article.find('.project-services-tab').css('background-color', '');
		
	});
	
	$('button.cancelBooking').click(function(){
		var id=$(this).attr('id');
		
		$.ajax(
		{
			type: 'post',
			url: 'php/classes/ajax.php',
			dataType: 'text',
			data: 
			{
				bookingId: id.split('_')[1],
				cancelBooking: ""
			},
			success: function(output)
			{
				$('div#'+id).remove();
			},
			error: function(output)
			{
				$('div#'+id).append('<span style="color: red;">&#x2717; ' + output + '</span>');
			}
		});
	});
	
});