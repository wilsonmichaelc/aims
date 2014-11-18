$(document).ready(function() {
	
	$('#calendar').fullCalendar({
				
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		
		editable: false,

		eventSources: [
			{
				url: 'fullcalendar/mscInstrumentEvents-SQL.php',
				type: 'POST',
				error: function() {
	      			alert('There was an error while fetching booked instruments!');
		  		}
			},
			{
				url: 'fullcalendar/mscConferenceRoomEvents-SQL.php',
				type: 'POST',
				error: function() {
	      			alert('There was an error while fetching booked conference rooms!');
		  		}
			},
			{
				url: 'fullcalendar/mscTrainingBookings-SQL.php',
				type: 'POST',
				error: function() {
	      			alert('There was an error while fetching booked training requests!');
		  		}
			}
		],
		
		timeFormat: 'h(:mm)t {- h(:mm)t}',
		
		loading: function(bool) {
			if (bool) $('#loading').show();
			else $('#loading').hide();
		}
		
	});
	$('#calendar').fullCalendar('option', 'aspectRatio', 1.6);
			
});