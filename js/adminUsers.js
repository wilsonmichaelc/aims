/*
*
*	Update the user being modified
*
*/
var analysisServices = null;
$('select[name="user"]').change(function(){

	// The selected user
	var user = $('select[name="user"] option:selected').val();

	// Get the users info and account type
	if(user != ''){
		$.ajax({ url: "php/classes/ajax.php",
			data: {request: $(this).val()},
			type: 'post',
			success: function(output){
				$("span#name").html('<a href="mailto:' + output[0].email + '">' + output[0].first + ' ' + output[0].last + '</a>');
				$("span#username").text("Username: " + output[0].username);
				$("span#userId").text("User ID: " + output[0].id);
				console.log(output[0].accountDisabled);
				if(output[0].accountDisabled == 1){
					var accountStatus = 'Account Status: <input style="height: 25px; padding: 0; background-color: #a30202; color: white;" type="button" id="accountStatus" value="Disabled">';
				}else{
					var accountStatus = 'Account Status: <input style="height: 25px; padding: 0; background-color: #026502; color: white;" type="button" id="accountStatus" value="Enabled">';
				}
				$("div#accountStatus").html(accountStatus);
				$("input[name='id']").val(output[0].id);
				$("select[name='accountType'] option[value='" + output[0].accountType + "']").attr('selected', 'true');
				$('#user_details').show();
			}
		});
	}else{
		$('#user_details').hide();
	}
	// Sort out which training modules the user has passed
	if(user != ''){
		$("span#name").text($('#user option:selected').text());
		$("input[name='userId']").val($(this).val());
		$('input[name="status"][value="0"]').prop('checked', true);
		$.ajax({ url: "php/classes/ajax.php",
	         data: {trainingPassed: $(this).val()},
	         type: 'post',
	         success: function(output){
				 $.each(output, function(index) {
				 	$('#id' + output[index].moduleId + ' input[name="status"][value="1"]').prop('checked', true);
				 });
				 $('#training_details').show();
	         }
		});
	}else{
		$('#training_details').hide();
	}
	// Get the instruments for which this user is trained
	if(user != ''){
		$(".instrumentAccess input[name='userId']").val($(this).val());
		$('.instrumentAccess input[name="accessStatus"][value="0"]').prop('checked', true);
		$.ajax({ url: "php/classes/ajax.php",
	         data: {access: $(this).val()},
	         type: 'post',
	         success: function(output){
				 $.each(output, function(index) {
				 	$('#id' + output[index].instrumentId + ' input[name="accessStatus"][value="' + output[index].access + '"]').prop('checked', true);
				 });
				 $('#instrument_access').show();
	         }
		});
	}else{
		$('#instrument_access').hide();
	}

	// Get the conference rooms for which this user has access
	if(user != ''){
		$(".conferenceAccess input[name='userId']").val($(this).val());
		$('.conferenceAccess input[name="accessStatus"][value="0"]').prop('checked', true);
		$.ajax({ url: "php/classes/ajax.php",
	         data: {conferenceAccess: $(this).val()},
	         type: 'post',
	         success: function(response){
				 $.each(response, function(index) {
				 /*
				 	THIS IS NOT UPDATING CORRECTLY... THERE IS NOT ACCESS''''
				 */
				 	$('#id' + response[index].conferenceId + ' input[name="accessStatus"][value="' + response[index].access + '"]').prop('checked', true);
				 });
				 $('#conference_access').show();
	         }
		});
	}else{
		$('#conference_access').hide();
	}

	// Get this users project and create the forms to modify each one
	if(user != ''){
		$("input[name='userId']").val($(this).val());
		$.ajax({ url: "php/classes/ajax.php",
	         data: {
	         	projects: $(this).val()
	         },
	         type: 'post',
	         success: function(output){

	         	 var proj = '';
	         	 var pmnt = '';

				 $.each(output, function(i) {

				 	var pmntInfo;

					$.ajax({ url: "php/classes/ajax.php",
						data: { jsonGetPmntInfo: output[i].paymentId },
						type: 'post',
						async: false,
						success: function(pmnt){
							pmntInfo = pmnt;
						}
					});

				 	var status = '<br><div class="label">Status</div><div class="input"><input type="radio" name="status" value="active"';
				 	if(output[i].status == 'active'){ status += ' checked="true"'; }
				 	status += '>Active';
					status += '<input type="radio" name="status" value="inactive"';
					if(output[i].status == 'inactive'){ status += ' checked="true"'; }
					status += '>Inactive</div>';

					var address = '<br><div class="label">Address</div><div class="input"><input type="text" name="addressOne" value="' + output[i].addressOne + '" />';
					address += '<input type="text" name="addressTwo" value="' + output[i].addressTwo + '" />';
					address += '<input type="text" name="city" value="' + output[i].city + '" />';
					address += '<input type="text" name="state" value="' + output[i].state + '" />';
					address += '<input type="text" name="zip" value="' + output[i].zip + '" /></div>';

					proj += '<br><div><span class="title">' + output[i].title + ' (' + output[i].id + ')</span></div>';
				 	proj += '<div style="width: 980px; overflow: auto;">';

				 	proj += '<form method="post" action="javascript:void(0)" class="userProject" id="proj'+output[i].id+'">';
				 	proj += '	<div style="float: left; width: 650px;">';
				 	proj += '		<input type="hidden" name="updateUserProject" value="' + output[i].id + '" />';
				 	proj += '		<div class="label">P.I.</div><div class="input"><input type="text" name="primaryInvestigator" value="' + output[i].primaryInvestigator + '" /></div>';
				 	proj += 		address;
				 	proj += 		status;
				 	proj += '		<br><div class="label">Phone</div><div class="input"><input type="text" name="phone" value="' + output[i].phone + '" /></div>';
				 	proj += '		<br><div class="label">Fax</div><div class="input"><input type="text" name="fax" value="' + output[i].fax + '" /></div>';
				 	proj += '		<br><div class="label"></div><div class="input projectUpdateResponse"></div>';
				 	proj += '	</div>';

				 	proj += '	<div style="float: right; width: 330px;">';
				 	proj += '		Purchase Order<br>';
				 	proj += '		<input type="text" name="purchaseOrder" value="'+pmntInfo.purchaseOrder+'" /><br>';
				 	proj += '		Chart String<br>';
				 	proj += '		<input type="text" name="projectCostingBusinessUnit" value="'+pmntInfo.projectCostingBusinessUnit+'" /><br>';
				 	proj += '		<input type="text" name="projectId" value="'+pmntInfo.projectId+'" /><br>';
				 	proj += '		<input type="text" name="departmentId" value="'+pmntInfo.departmentId+'" /><br>';
				 	proj += '		<input type="hidden" name="pmntId" value="'+output[i].paymentId+'" />';
				 	proj += '	</div>';
				 	proj += '	<div style="width: 980px;">';
				 	proj += '		<textarea name="abstract">'+output[i].abstract+'</textarea>';
				 	proj += '	</div>';
				 	proj += '</form>';
				 	proj += '</div>';

				 });
				 $('div#user_projects span.content').html(proj);
				 $('#user_projects').show();
	         }
		});
	}else{
		$('#user_projects').hide();
	}

	// Get this users bookings and create the forms to modify each one
	if(user != ''){
		$("input[name='userId']").val($(this).val());
		$.ajax({ url: "php/classes/ajax.php",
	         data: {
	         	getUserBookings: $(this).val()
	         },
	         type: 'post',
	         success: function(output){
	         	 var html = '<br>Archived Bookings: <input style="height: 25px; padding: 0;" type="button" name="showHideArchive" value="Show" />';
	         	 var uhtml = '';
	         	 var name = '';
	         	 var instrumentId = '';
				 $.each(output, function(i) {

				 	$.ajax({
					 	url: "php/classes/ajax.php",
					 	data: { jsonGetInstrumentInfo: output[i].instrumentId },
					 	type: 'post',
					 	async: false,
					 	success: function(instrument){
						 	name = instrument.name;
						 	instrumentId = instrument.id;
					 	}
				 	});

				 	var disabled="";
				 	if(output[i].invoiced == 0){disabled="";}else{disabled="disabled";}

				 	if(output[i].archiveStatus == 0){

					 	html += '<div class="active"><br>';
					 	html += '	<span class="title">Booking #' + output[i].id + ' &nbsp; -- ' + name + ' (' + instrumentId + ') -- Project #' + output[i].projectId + '</span>';
					 	html += '	<form method="post" action="javascript:void(0)" class="userBooking" id="b'+output[i].id+'">';

					 	html += '		<input type="hidden" name="updateUserBooking" value="' + output[i].id + '" />';

					 	html += '		<div class="label">Date</div>';
					 	html += '		<div class="input"><input type="date" name="dateFrom" value="' + output[i].dateFrom + '" ' + disabled + ' /></div>';
					 	html += '		-<div class="input"><input type="date" name="dateTo" value="' + output[i].dateTo + '" ' + disabled + ' /></div>';

					 	html += '		<div class="label">Time</div><div class="input">';
					 	html += '		<input type="time" name="timeFrom" value="' + output[i].timeFrom + '" ' + disabled + ' /></div>';
					 	html += '		-<div class="input"><input type="time" name="timeTo" value="' + output[i].timeTo + '" ' + disabled + ' /></div>';

					 	html += '			<div class="input"><input type="button" id="archiveTrigger" class="archiveBooking" value="Archive"/></div>';

					 	if(output[i].invoiced == 0){
					 		html += '			<div class="input"><input type="button" class="cancel" id="' + output[i].id + '" value="Cancel"/></div>';
					 	}

					 	html += '			<div class="input" id="archiveResponse" style="width: 30px; text-align: right;"></div>';

					 	html += '			<div class="label updateBookingResponse" style="width: 30px; text-align: right;"></div>';

					 	html += '	</form>';
					 	html += '</div>';

				 	}else{

					 	uhtml += '<div class="archived"><br>';
					 	uhtml += '	<span class="title">' + name + '</span>';
					 	uhtml += '	<form method="post" action="javascript:void(0)" class="userBooking" id="b'+output[i].id+'">';

					 	uhtml += '		<input type="hidden" name="updateUserBooking" value="' + output[i].id + '" />';

					 	uhtml += '		<div class="label">Date</div>';
					 	uhtml += '		<div class="input"><input type="date" name="dateFrom" value="' + output[i].dateFrom + '" ' + disabled + ' /></div>';
					 	uhtml += '		-<div class="input"><input type="date" name="dateTo" value="' + output[i].dateTo + '" ' + disabled + ' /></div>';

					 	uhtml += '		<div class="label">Time</div>';
					 	uhtml += '		<div class="input"><input type="time" name="timeFrom" value="' + output[i].timeFrom + '" ' + disabled + ' /></div>';
					 	uhtml += '		-<div class="input"><input type="time" name="timeTo" value="' + output[i].timeTo + '" ' + disabled + ' /></div>';

					 	uhtml += '			<div class="input"><input type="button" id="archiveTrigger" class="unArchiveBooking" value="Un-Archive"/></div>';

						if(output[i].invoiced == 0){
					 		uhtml += '			<div class="input"><input type="button" class="cancel" id="' + output[i].id + '" value="Cancel"/></div>';
					 	}

					 	uhtml += '			<div class="input" id="archiveResponse" style="width: 30px; text-align: right;"></div>';

					 	uhtml += '			<div class="label updateBookingResponse" style="width: 30px; text-align: right;"></div>';

					 	uhtml += '	</form>';
					 	uhtml += '</div>';

				 	}





				 });
				 $('div#user_bookings span.content').html(html + uhtml);
				 $('#user_bookings').show();
	         }
		});
	}else{
		$('#user_bookings').hide();
	}

	// Get this users training sessions and create the forms to modify each one
	if(user != ''){
		$("input[name='userId']").val($(this).val());
		$.ajax({
			url: "php/classes/ajax.php",
			data: {
				getUserTrainingBookings: $(this).val()
			},
			type: 'post',
			success: function(output){

				var html = '';

				$.each(output, function(i) {

					var disabled="";
				 	if(output[i].invoiced == 0){disabled="";}else{disabled="disabled";}

					html += '<div class="active"><br>';
					html += '	<span class="title">' + output[i].instrumentId + ' Training' + '</span>';
					html += '	<form method="post" action="javascript:void(0)" class="userTrainingBooking" id="tb'+output[i].id+'">';

					html += '		<input type="hidden" name="updateUserTrainingBooking" value="' + output[i].id + '" />';

					html += '		<div class="label">Date</div>';
					html += '		<div class="input"><input type="date" name="dateFrom" value="' + output[i].dateFrom + '" ' + disabled + ' /></div>';
					html += '		-<div class="input"><input type="date" name="dateTo" value="' + output[i].dateTo + '" ' + disabled + ' /></div>';

					html += '		<div class="label">Time</div>';
					html += '		<div class="input"><input type="time" name="timeFrom" value="' + output[i].timeFrom + '" ' + disabled + ' /></div>';
					html += '		-<div class="input"><input type="time" name="timeTo" value="' + output[i].timeTo + '" ' + disabled + ' /></div>';

					if(output[i].invoiced == 0){
						html += '		<div class="input"><input type="button" class="cancel" id="' + output[i].id + '" value="Cancel"/></div>';
					}

					html += '		<div class="label updateBookingResponse" style="width: 30px; text-align: right;"></div>';

					html += '	</form>';
					html += '</div>';

					$('div#user_trainingBookings span.content').html(html);
					$('#user_trainingBookings').show();

				});
			}
		});
	}else{
		$('#user_trainingBookings').hide();
	}

	// Get this users service requests and create the forms to modify each one
	if(user != ''){
		$("input[name='userId']").val($(this).val());
		$.ajax({
			 url: "php/classes/ajax.php",
	         data: {
	         	getServiceRequests: $(this).val()
	         },
	         type: 'post',
	         success: function(output){

	         	//console.log(output);

	         	 var ffs = '';

				 $.each(output, function(i) {

				 	//var d = new Date(output[i].servicesSelected[0].createdAt);
				 	//console.log(output[i].servicesSelected[0].createdAt);
				 	// for Safari/IE
				 	//var dd = output[i].servicesSelected[0].createdAt.split(" ")[0].split("-");
				 	//var displayDate = new Date(dd[0],dd[1],dd[2]);
				 	//console.log(displayDate[0] + '-' + displayDate[1]-1 + '-' + displayDate[2]);
				 	ffs += '<div class="serviceContainer">';
				 	ffs += '<div style="height: 30px;">';
				 	ffs += '<h3 style="float: left;">';
				 	ffs += '	<span class="requestToggle" id="'+output[i].id+'">[+]</span>';
				 	//ffs += '	Request: ' + displayDate.getMonth() + '-' + displayDate.getDate() + '-' + displayDate.getFullYear();
				 	ffs += '	Request: ' + output[i].createdAt;
				 	ffs += '</h3>';
				 	ffs += '<div  style="display: inline; float: right;" class="response" id="response'+output[i].id+'"></div>';
				 	ffs += '</div>';
				 	ffs += '<div class="request" id="'+output[i].id+'">';

				 	ffs += '<form method="post" action="javascript:void(0)" name="updateServiceRequest">';
				 	ffs += '	<div style="height: 30px;width: ">';

				 	ffs += '		<div style="font-weight: bold; float: left;">Project ID: '+output[i].projectId+'</div>';

				 	ffs += '		<div style="display: inline; float: right;">';
				 	ffs += '			<select name="status">';
				 	ffs += '				<option value="pending" ';
				 							if(output[i].status == 'pending'){ffs+= 'selected="true">';}else{ffs+= '>';}
				 	ffs += '				Pending</option>';
				 	ffs += '				<option value="approved" ';
				 							if(output[i].status == 'approved'){ffs+= 'selected="true">';}else{ffs+= '>';}
				 	ffs += '				Approved</option>';
				 	ffs += '				<option value="closed" ';
				 							if(output[i].status == 'closed'){ffs+= 'selected="true">';}else{ffs+= '>';}
				 	ffs += '				Closed</option>';
				 	ffs += '				<option value="archived" ';
				 							if(output[i].status == 'archived'){ffs+= 'selected="true">';}else{ffs+= '>';}
				 	ffs += '				Archived</option>';
				 	ffs += '			</select>';
				 	ffs += '		</div>';

				 	ffs += '	</div>';
				 	ffs += '	<div class="ffs_left">';
				 	ffs += '		<span class="label">Service ID:</span><input type="text" name="updateServiceRequest" value="'+output[i].id+'" readonly="true"/>';
				 	ffs += '		<span class="label">Label:</span><input type="text" name="label" value="'+output[i].label+'" />';
				 	ffs += '		<span class="label">Concentration:</span><input type="text" name="concentration" value="'+output[i].concentration+'" />';
				 	ffs += '		<span class="label">State:</span><input type="text" name="state" value="'+output[i].state+'" />';
				 	ffs += '		<span class="label">Buffer Composition:</span><input type="text" name="composition" value="'+output[i].composition+'" />';
				 	ffs += '		<span class="label">Digestion Enzyme:</span><input type="text" name="digestionEnzyme" value="'+output[i].digestionEnzyme+'" />';
				 	ffs += '	</div>';

				 	ffs += '	<div class="ffs_right">';
				 	ffs += '		<span class="label">Purification:</span><input type="text" name="purification" value="'+output[i].purification+'" />';
				 	ffs += '		<span class="label">Reduction/Alkylation:</span><input type="text" name="redoxChemicals" value="'+output[i].redoxChemicals+'" />';
				 	ffs += '		<span class="label">Molecular Weight:</span><input type="text" name="molecularWeight" value="'+output[i].molecularWeight+'" />';
				 	ffs += '		<span class="label">Suspected Mods:</span><input type="text" name="suspectedModifications" value="'+output[i].suspectedModifications+'" />';
				 	ffs += '		<span class="label">Amino Acid Mods:</span><input type="text" name="aaModifications" value="'+output[i].aaModifications+'" />';
				 	ffs += '		<span class="label">Species:</span><input type="text" name="species" value="'+output[i].species+'" />';
				 	ffs += '	</div>';

				 	ffs += '	<div>';
				 	ffs += '		<span class="label">Sequence:</span><textarea name="sequence">'+output[i].sequence+'</textarea>';
				 	ffs += '	</div>';

				 	ffs += '	<div>';
				 	ffs += '		<span class="label">Comments:</span><textarea name="comments">'+output[i].comments+'</textarea>';
				 	ffs += '	</div>';

				 	//ffs += '	<input type="submit" name="updateServiceRequest" value="Update"/>';
				 	ffs += '</form>';



				 	ffs += '	<div class="ffs_Selected">';
				 	ffs += '		<div class="labels">';
				 	ffs += '			<div>Name</div>';
				 	ffs += '			<div>Samples</div>';
				 	ffs += '			<div>Replicates</div>';
				 	ffs += '			<div>Prep</div>';
				 	ffs += '		</div>';

				 	$.each(output[i].servicesSelected, function(j) {

				 		var disabled="";
				 		if(output[i].servicesSelected[j].invoiced == 0){disabled="";}else{disabled="disabled";}

					 	ffs += '<div class="inputs">';
					 	ffs += '	<form method="post" action="javascript:void(0)" name="updateSelectedService" >';

					 	ffs += '		<input type="hidden" name="updateSelectedService" value="'+output[i].servicesSelected[j].id+'"/>';
					 	ffs += '		<div>'+output[i].servicesSelected[j].serviceName+'</div>';
					 	ffs += '		<div><input type="number" name="samples" value="'+output[i].servicesSelected[j].samples+'" '+disabled+' /></div>';

					 	var r = output[i].servicesSelected[j].replicates;
					 	ffs += '		<div>';
					 	ffs += '		<select name="replicates" '+disabled+'>';

					 	ffs += '			<option value="1"';
					 	if(r == 0){ ffs+='selected="true"'; }
					 	ffs += '			>None</option>';

					 	ffs += '			<option value="2"';
					 	if(r == 2){ ffs+='selected="true"'; }
					 	ffs += '			>Duplicate</option>';

					 	ffs += '			<option value="3"';
					 	if(r == 3){ ffs+='selected="true"'; }
					 	ffs += '			>Triplicate</option>';

					 	ffs += '		</select>';
					 	ffs += '		</div>';

					 	var p = output[i].servicesSelected[j].prep;
					 	ffs += '		<div>';
					 	ffs += '		<select name="prep" '+disabled+'>';

					 	ffs += '			<option value="0"';
					 	if(p == 0){ ffs+='selected="true"'; }
					 	ffs += '			>No</option>';

					 	ffs += '			<option value="1"';
					 	if(p == 1){ ffs+='selected="true"'; }
					 	ffs += '			>Yes</option>';

					 	ffs += '		</select>';

					 	ffs += '		</div>';

					 	if(output[i].servicesSelected[j].invoiced == 0){
						 	ffs += '		<div id="delete">';
						 	ffs += '			<input style="line-height: 10px; text-align: center; border-radius: 1.6em; -webkit-appearance: none; border: solid 1px #ddd; padding: 0.5em; height: 30px; width: 30px; font-family: "Comic Sans MS", sans-serif; font-size: 12pt; font-weight: 400; color: #565656;" type="button" name="delete" id="'+output[i].servicesSelected[j].id+'" value="X"/>';
						 	ffs += '			<span class="ServiceResponse"></span>';
						 	ffs += '		</div>';
						}

					 	ffs += '	</form>';
					 	ffs += '</div>';

				 	});

				 	ffs += '	</div>';
				 	ffs += '	<input style="width: 90px;" type="button" class="call-modal" value="Add Service" id="'+output[i].id+'" >';
				 	ffs += '</div>';
				 	ffs += '</div>';
				 });

				 $('div#user_ffs div.content').html(ffs);
				 $('#user_ffs').show();
	         }
		});
	}else{
		$('#user_ffs').hide();
	}

});

/*
*
*	Async account type update
*
*/
$(document).on('change', '#updateUserAccountType', function(e){
	e.preventDefault();
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		data:
		{
			updateUserAccountType: $(this).find('input[name="id"]').val(),
			accountType: $(this).find('select[name="accountType"]').val()
		},
		success: function(output)
		{
			if(output == true){
				$('#updateUserAccountType .messages').html('<span style="color: green;">&#x2713;</span>');
				setTimeout(function(){
		        	$('#updateUserAccountType .messages').html('');
		        },2000);
			}else{
				$('#updateUserAccountType .messages').html('<span style="color: red;">&#x2717; ' + output + '</span>')
				setTimeout(function(){
		        	$('#updateUserAccountType .messages').html('');
		        },10000);
			}
		}
	});
});

/*
*
*	Async training status update
*
*/
$(document).on('change', '.trainingStatus', function(e){
	e.preventDefault();
	var form = $(this);
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		data:
		{
			updateTrainingStatus: $(this).find('input[name="moduleId"]').val(),
			userId: $(this).find('input[name="userId"]').val(),
			status: $(this).find('input[name="status"]:checked').val()
		},
		success: function(output)
		{
			//console.log(e.target.id);
			var target = form.find('span.trainingStatusResponse');
			var time = 0;
			if(output == 'Success!'){
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + output + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

/*
*
*	Async instrument access update
*
*/
$(document).on('change', '.instrumentAccess', function(e){
	e.preventDefault();
	var form = $(this);
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		dataType: 'text',
		data:
		{
			instrumentId: $(this).find('input[name="instrumentId"]').val(),
			userId: $(this).find('input[name="userId"]').val(),
			accessStatus: $(this).find('input[name="accessStatus"]:checked').val(),
			updateInstrumentAccess: ""
		},
		success: function(response)
		{
			var target = form.find('span.instrumentAccessResponse');
			var time = 0;
			if(response == 'Success!'){
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

$(document).on('change', '.conferenceAccess', function(e){
	e.preventDefault();
	var form = $(this);
	$.ajax(
	{
		type: 'post',
		url: 'php/classes/ajax.php',
		data:
		{
			conferenceId: $(this).find('input[name="conferenceId"]').val(),
			userId: $(this).find('input[name="userId"]').val(),
			accessStatus: $(this).find('input[name="accessStatus"]:checked').val(),
			updateConferenceAccess: ""
		},
		success: function(response)
		{
			var target = form.find('span.conferenceAccessResponse');
			var time = 0;
			if(response == true){
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

/*
*
*	Update the users project details
*
*/
$(document).on('change', ".userProject", function(e){
	e.preventDefault();
	// Get the form so we can serialize it
	var form = $(this);
	// Start the ajax call
	$.ajax({ url: "php/classes/ajax.php",
		data: form.serialize(),
		type: 'post',
		success: function(response){

			var target = form.find('div.projectUpdateResponse');
			var time = 0;
			if(response == true){
				target.html('<span style="color: green;">Saved &#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);

		},
	});

});

/*
*
*	Update the users booking times
*
*/
$(document).on('change', ".userBooking", function(e){
	e.preventDefault();
	// Get the form so we can serialize it
	var form = $(this);
	setTimeout(function(){
		$.ajax({ url: "php/classes/ajax.php",
			data: form.serialize(),
			type: 'post',
			success: function(response){

				var target = form.find('div.updateBookingResponse');
				var time = 0;
				if(response == true){
					target.html('<span style="color: green;">&#x2713;</span>');
					time = 2000;
				}else{
					target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
					time = 10000;
				}
				setTimeout(function(){ target.html(''); },time);

			},
		});
	}, 1000);
});

$(document).on('change', ".userTrainingBooking", function(e){
	e.preventDefault();
	// Get the form so we can serialize it
	var form = $(this);

	$.ajax({ url: "php/classes/ajax.php",
		data: form.serialize(),
		type: 'post',
		success: function(response){

			var target = form.find('div.updateBookingResponse');
			var time = 0;
			if(response == true){
				target.html('<span style="color: green;">&#x2713;</span>');
				time = 2000;
			}else{
				target.html('<span style="color: red;">&#x2717; ' + response + '</span>')
				time = 10000;
			}
			setTimeout(function(){ target.html(''); },time);

		},
	});

});

$(document).on('click', 'input#accountStatus', function(e){
	if(e.target.value == "Disabled"){
		var newStatus = 0;
		var html = 'Account Status: <input style="height: 25px; padding: 0; background-color: #026502; color: white;" type="button" id="accountStatus" value="Enabled">';
	}else{
		var newStatus = 1;
		var html = 'Account Status: <input style="height: 25px; padding: 0; background-color: #a30202; color: white;" type="button" id="accountStatus" value="Disabled">';
	}

	$.ajax({
		url: "php/classes/ajax.php",
		data: {updateUserAccountStatus: $('input[name="id"]').val(), status: newStatus},
		type: 'POST',
		dataType: 'text',
		success: function(response){
			if(response == true){
				$('div#accountStatus.input').html(html);
			}else{
				alert("Failed to update account status.");
			}
		}
	});

});

$(document).on('click', 'input.archiveBooking', function(e){
	e.preventDefault();
	var form = $(this).closest("form");

	if(confirm('Are you sure you want to archive this booking?')){
		$.ajax({ url: "php/classes/ajax.php",
			data: { archiveBooking: form.find('input[name="updateUserBooking"]').val() },
			type: 'post',
			success: function(response){

				if(response == true){
					form.find('#archiveTrigger').val('Un-Archive');
					form.find('#archiveTrigger').addClass('unArchiveBooking');
					form.find('#archiveTrigger').removeClass('archiveBooking');

					form.find('#yes').addClass('unArchiveBookingYes');
					form.find('#yes').removeClass('archiveBookingYes');

					form.find('#no').addClass('unArchiveBookingNo');
					form.find('#no').removeClass('archiveBookingNo');

					form.parent().addClass('archived').removeClass('active');

					if($('input[name="showHideArchive"]').val() == "Show"){
						form.parent().hide('slow');
					}

					form.find('#ajaxResponse').css('color', 'green');
					form.find('#ajaxResponse').html('&#x2713;');
						setTimeout(function(){
				        	form.find('#ajaxResponse').html('');
				        },2000);
			    }else{
				    form.find('#ajaxResponse').css('color', 'red');
					form.find('#archiveResponse').html('&#x2717;');
						setTimeout(function(){
				        	form.find('#ajaxResponse').html('');
				        },2000);
			    }

			},
		});
	}

});



/*
*
*	Confirmation and ajax for UNARCHIVE booking
*
*/

$(document).on('click', 'input.unArchiveBooking', function(e){
	e.preventDefault();
	var form = $(this).closest("form");

	if(confirm('Are you sure you want to un-archive this booking?')){
		$.ajax({
			url: "php/classes/ajax.php",
			data: { unArchiveBooking: form.find('input[name="updateUserBooking"]').val() },
			type: 'post',
			success: function(response){

				if(response == true){
					form.find('#archiveTrigger').val('Archive');
					form.find('#archiveTrigger').addClass('archiveBooking');
					form.find('#archiveTrigger').removeClass('unArchiveBooking');

					form.find('#yes').addClass('archiveBookingYes');
					form.find('#yes').removeClass('unArchiveBookingYes');

					form.find('#no').addClass('archiveBookingNo');
					form.find('#no').removeClass('unArchiveBookingNo');

					form.parent().addClass('active').removeClass('archived').show();

					form.find('#ajaxResponse').css('color', 'green');
					form.find('#ajaxResponse').html('&#x2713;');
						setTimeout(function(){
				        	form.find('#ajaxResponse').html('');
				        },2000);
			    }else{
				    form.find('#ajaxResponse').css('color', 'red');
					form.find('#ajaxResponse').html('&#x2717;');
						setTimeout(function(){
				        	form.find('#ajaxResponse').html('');
				        },2000);
			    }

			},
		});
	}

});


/*
 *
 *	Update the service request on 'change' via ajax
 *
 */
$(document).on('change', 'form[name="updateServiceRequest"]', function(){
	var form = $(this);
	var id = form.find('input[name="updateServiceRequest"]').val();
	var time = 0;
	$.ajax({
		url: "php/classes/ajax.php",
		data: $(this).serialize(),
		type: 'POST',
		dataType: 'text',
		success: function(response){
			var target = $('div#response'+id);
			if(response == true){
				target.html('Saved! &#x2713;').css('color', 'green');
				time = 1500;
			}else{
				target.html('Database connection error!').css('color', 'red');
				time = 5000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

/*
 *
 *	Update the service selected on change via ajax
 *
 */
$(document).on('change', 'form[name="updateSelectedService"]', function(){
	var form = $(this);
	var time = 0;
	$.ajax({
		url: "php/classes/ajax.php",
		data: form.serialize(),
		type: 'POST',
		dataType: 'text',
		success: function(response){
			var target = form.find('span.ServiceResponse');
			if(response == true){
				target.html('&#x2713;').css('color', 'green');
				time = 1500;
			}else{
				target.html('Error!').css('color', 'red');
				time = 5000;
			}
			setTimeout(function(){ target.html(''); },time);
		}
	});
});

/*
*
*	Cancel this booking
*
*/
$(document).on('click', '.theBookings input.cancel', function(e){

	e.preventDefault();
	var id = $(this).attr('id');
	var form = $('form#b'+id);

	if(confirm('Are you sure you want to CANCEL this booking?')){
		$.ajax({
			url: "php/classes/ajax.php",
			data: { cancelBooking: 'true', bookingId: id },
			type: 'post',
			dataType: 'text',
			success: function(response){

				if(response == 'success'){

					form.closest('div.active').remove();

			    }else{
				    var target = form.find('.updateBookingResponse');
					target.html('&#x2717;').css('color', 'red');
					setTimeout(function(){ target.html(''); },10000);
			    }



			},
		});
	}


});

/*
*
*	Cancel this training session
*
*/
$(document).on('click', '.theTrainingBookings input.cancel', function(e){

	e.preventDefault();
	var id = $(this).attr('id');
	var form = $('form#tb'+id);

	if(confirm('Are you sure you want to CANCEL this training session?')){
		$.ajax({
			url: "php/classes/ajax.php",
			data: { cancelTrainingBooking: 'true', trainingId: id },
			type: 'post',
			dataType: 'text',
			success: function(response){

				if(response == true){

					form.closest('div.active').remove();

			    }else{
				    var target = form.find('.updateBookingResponse');
					target.html('&#x2717;').css('color', 'red');
					setTimeout(function(){ target.html(''); },10000);
			    }



			},
		});
	}

});

/*
*
*	Toggle the Instrument access pannel for this person
*
*/
$('.toggleAccess').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theAccess .content').toggle();
});

$('.toggleConferenceAccess').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theConferenceAccess .content').toggle();
});

/*
*
*	Toggle the Training pannel for this person
*
*/
$('.toggleTrainingModules').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theTrainingModules .content').toggle();
});

$('.toggleTrainingBookings').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theTrainingBookings .content').toggle();
});

/*
*
*	Toggle the Projects pannel for this person
*
*/
$('.toggleProjects').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theProjects .content').toggle();
});

/*
*
*	Toggle the Booking pannel for this person
*
*/
$('.toggleBookings').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theBookings .content').toggle();
});

/*
*
*	Toggle the Service Request pannel for this person
*
*/
$(document).on('click', 'span.requestToggle', function(){

	var id = $(this).attr('id');
	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('div#'+id+'.request').toggle();
});

/*
*
*	Toggle the Individual Services pannel for this person
*
*/
$('.toggleFFS').click(function(){

	if($(this).text() == '[+]'){
		$(this).text('[-]');
	}else{
		$(this).text('[+]');
	}

	$('.theServiceRequests .content').toggle();
});


$(document).on('click', 'input[name="showHideArchive"]', function(){
	if($(this).val() == 'Show'){
		$(this).val('Hide');
	}else{
		$(this).val('Show');
	}
	$('div.archived').toggleClass("show");
});

function getAnalysisServices(){

	if(analysisServices != null){
		return analysisSerivices;
	}else{
		$.ajax({
			url: "php/classes/ajax.php",
			data: {
				getAnalysisServices: true
			},
			type: 'post',
			success: function(output){
				return output;
			}
		});
	}

}
