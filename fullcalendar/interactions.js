var bgc = "";

function myMouseOver(){
	bgc = $(this).css('background-color');
	$(this).css('background-color', 'rgba(11, 33, 97, 0.2)');
}

function myMouseOut(){
	$(this).css('background-color', bgc);
}

function myClick(){
		
	var selectedDate = new Date($(this).attr('data-date'));
	selectedDate.setDate(selectedDate.getDate() + 1);
	var currentDate = new Date();

	//console.log('Selected: ' + selectedDate);
	//console.log('Current: ' + currentDate);
	
	if(selectedDate.getMonth() == currentDate.getMonth() && selectedDate.getDay() == currentDate.getDay() && selectedDate.getFullYear() == currentDate.getFullYear()){
		//console.log("Today was selected, make sure the times match so we can handle booking only for future times");
		selectedDate.setHours(currentDate.getHours());
		selectedDate.setMinutes(currentDate.getMinutes());
		selectedDate.setSeconds(currentDate.getSeconds());
		selectedDate.setMilliseconds(currentDate.getMilliseconds());
		//console.log('Update Selected Date: ' + selectedDate);
	}


	if(selectedDate >= currentDate){
		$('input[name="dateFrom"], input[name="dateTo"]').val(selectedDate.toString('M/d/yyyy'));
		$('a#showModal')[0].click();
		$("html, body").animate({ scrollTop: 0 }, "medium");
	}else{
		alert("You may not book an instrument for a past date.")
	}
	
}

function closeNewBooking(){
	$('.newbooking').hide();
}

$(document).ready(function() {

	$(document).on("mouseover", ".fc-day", myMouseOver);
	$(document).on("mouseout", ".fc-day", myMouseOut);
		
	$(document).on('click', ".fc-day", myClick);

    $("#dateFrom, #dateTo").datepicker({
    	minDate: 0,
        beforeShow: function() {
            $('#ui-datepicker-div').css('z-index', 300);
        },
        dateFormat: 'mm/dd/yy'
    });

    
});