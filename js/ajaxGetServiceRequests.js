function getServiceRequests(dateFrom, dateTo, orderBy, userId){
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
			user: userId,
			serviceRequests: ""
		},
		beforeSend: function(b){
			$('#serviceSpinner').show();
		},
		success: function(output)
		{
			$('#serviceSpinner').hide();
			
			if(output.length > 0){
				var header = '';
				header += '<div class="title"><br /><b>Fee-For-Service</b></div><div class="service header">';
				header += 	'<div class="serviceId">ID</div>';
				header +=	'<div class="user">User (ID)</div>';
				header +=	'<div class="accountType">Type</div>'; 
				header +=	'<div class="project">Project (ID)</div>'; 
				header +=	'<div class="createdAt">Created</div>';
				header +=	'<div class="estimate">Estimate</div>';
				header += '</div>';
				$('div.services').append(header);
				
				$.each(output, function(index) {
					
					$.ajax(
					{
						type: 'post',
						url: 'php/classes/ajax.php',
						dataType: 'text',
						async: false,
						data:
						{
							serviceRequests_metadata: "",
							userId: output[index].userId,
							projectId: output[index].projectId,
							requestId: output[index].id
						},
						success: function(metaData) // name,projectTitle,AccountType,estimate
						{
							
							$.ajax(
							{
								type: 'post',
								url: 'php/classes/ajax.php',
								dataType: 'json',
								async: false,
								data:
								{
									servicesSelected_metadata: "",
									requestId: output[index].id
								},
								success: function(serviceRequests)
								{
									var metaArray = metaData.split(',');
									var html = '<div class="service" id="bid' + output[index].id + '">' +
									'<div class="serviceId">' + output[index].id + '</div>' + 
									'<div class="user">' + metaArray[0].substring(0, 17) + ' (' + output[index].userId + ')' + '</div>' +
									'<div class="accountType">' + metaArray[2] + '</div>' +
									'<div class="project">' + metaArray[1].substring(0, 20) + ' (' + output[index].projectId + ')' + '</div>' + 
									'<div class="createdAt">' + output[index].createdAt + '</div>' +
									'<div class="estimate">' + metaArray[3] + '</div>' +
									'<button class="singleServiceInvoice" value="' + output[index].id + '">Invoice</button>';
									$.each(serviceRequests, function(idx) {
										 
										$.ajax(
										{
											type: 'post',
											url: 'php/classes/ajax.php',
											dataType: 'text',
											async: false,
											data: 
											{
												serviceName_metadata: serviceRequests[idx].serviceId
											},
											success: function(serviceName)
											{
												$('#serviceSpinner').hide();
												html += '<br /><div class="spacer"></div>';
												html += '<div class="serviceName">' + serviceName + '&nbsp;&nbsp;--</div>';
												html += '<div class="samples">Samples:&nbsp;' + serviceRequests[idx].samples + '</div>';
												html += '<div class="replicates">Replicates:&nbsp;';
												if(serviceRequests[idx].replicates == 1){
													html += 'no</div>';
												}else{
													html += serviceRequests[idx].replicates+'</div>';
												}
												
												html += '<div class="prep">Prep:&nbsp;';
												if(serviceRequests[idx].prep == '0'){
													html += 'no</div>';
												}else{
													html += 'yes</div>';
												}
											}
										});
										
									});
										
									html += '</div>';
									$('div.services').append(html);
								
								}
							});
						}
							
					});
					
				 });
			 }else{
				 $('div.services').append('<br><div class="title"><b>Services</b><div>There were no services requests that matched your query.</div></div>');
			 }
		}
	});
}

function getServiceInvoice(invoiceId){
	
	$.ajax({
		type: 'post',
			url: 'php/classes/ajax.php',
			dataType: 'text',
			async: 'true',
			data:
			{
				getServiceInvoice: invoiceId
			},
			success: function(response)
			{
				if(response == true){
					window.location.href = "tmp/service_invoice.xls";
				}else{
					alert('Excel file was not generated.');
				}			
			}
	});
	
}
