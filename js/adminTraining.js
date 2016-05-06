/*
 *
 *	New Training Module Event Handlers
 *
 */
$('.newTrainingModule').on('click', 'input[name="status"]', function(){
	if($(this).val() == 'active'){
		$(this).css('background-color', '#a30202');
		$(this).css('color', 'white');
		$(this).val('inactive');
		$('input[name="statusVal"]').val('0');
	}else{
		$(this).css('background-color', '#026502');
		$(this).css('color', 'white');
		$(this).val('active');
		$('input[name="statusVal"]').val('1');
	}
});
			
$('.newTrainingModule').on('click', 'input[name="addMCQuestion"]', function(){
	
	//var numQuestions = $('#trainingQuestions > div').length;
	var numQuestions = $('.newTrainingModule input[name="totalNumberOfQuestions"]').val();
	numQuestions += 1;
	var code = '';
		code+='<div id="' + numQuestions + '">';
			code+='Q: <input type="text" name="mc_question_' + numQuestions + '" id="question" placeholder="Multiple Choice Question" required/>';
			code+='<input type="button" name="remove" value="Remove" />';
			code+='<br /><input type="checkbox" name="a_correct_' + numQuestions + '" value="a" /><span id="indent">a)</span><input type="text" name="a_answer_' + numQuestions + '" id="answer" placeholder="Answer" required/>';
			code+='<br /><input type="checkbox" name="b_correct_' + numQuestions + '" value="b" /><span id="indent">b)</span><input type="text" name="b_answer_' + numQuestions + '" id="answer" placeholder="Answer" required/>';
			code+='<br /><input type="checkbox" name="c_correct_' + numQuestions + '" value="c" /><span id="indent">c)</span><input type="text" name="c_answer_' + numQuestions + '" id="answer" placeholder="Answer" required/>';
			code+='<br /><input type="checkbox" name="d_correct_' + numQuestions + '" value="d" /><span id="indent">d)</span><input type="text" name="d_answer_' + numQuestions + '" id="answer" placeholder="Answer" required/>';
		code+='</div><p></p>';
		
	$(this).closest('.newTrainingModule').find('.trainingQuestions').append(code);
	$(this).closest('.newTrainingModule').find('input[name="totalNumberOfQuestions"]').val(numQuestions);
});

$('.newTrainingModule').on('click', 'input[name="addTFQuestion"]', function(){
	//var numQuestions = $('#trainingQuestions > div').length;
	var numQuestions = $('.newTrainingModule input[name="totalNumberOfQuestions"]').val();
	numQuestions += 1;
	var code = '';
		code+='<div id="' + numQuestions + '">';
		code+='Q: <input type="text" name="tf_question_' + numQuestions + '" id="question" placeholder="True/False Question" required/>';
		code+='<input type="button" name="remove" value="Remove" />';
		code+='<br /><input type="radio" name="tf_correct_' + numQuestions + '" value="t" />True';
		code+='<br /><input type="radio" name="tf_correct_' + numQuestions + '" value="f" />False';
		code+='</div>';
		
	$(this).closest('.newTrainingModule').find('.trainingQuestions').append(code);
	$(this).closest('.newTrainingModule').find('input[name="totalNumberOfQuestions"]').val(numQuestions);
});

$('.newTrainingModule').on('click', 'input[name="remove"]', function(){
	
	if(confirm("Are you sure you want to remove this question?")){
	
		$(this).parent().remove();
		var id = 1;
		$('#trainingQuestions > div').each(function(){
			$(this).attr('id', id);
			id += 1;
		});
		var numQuestions = $('input[name="totalNumberOfQuestions"]').val();
		$('input[name="totalNumberOfQuestions"]').val(numQuestions-1);
		
	}

});

$('input[name="files"]').change(function(){
	$('#documentList').children().remove();

	var fileList = document.getElementById('fileUpload').files;
	if(fileList.length > 1){
		for (var i=0, l=fileList.length; i<l; i++) {
	    	$('#documentList').append('<span>' + fileList[i].name + '</span><br />');
		}
	}
});

/*
 *
 *	Existing Training Module Event Handlers
 *
 */
$('input[name="existingFiles"]').change(function(){
	$('#existingDocumentList').children().remove();

	var fileList = $('#existingFileUpload').files;
	if(fileList.length > 1){
		for (var i=0, l=fileList.length; i<l; i++) {
	    	$('#existingDocumentList').append('<span>' + fileList[i].name + '</span><br />');
		}
	}
});

$('.existingTrainingModule').on('click', 'input[name="removeDocument"]', function(e){
	
	var id = $(this).attr('doc');
	var target = $(this).closest('.existingTrainingModule').find('span.response');
	var spinner = $(this).closest('.existingTrainingModule').find('img.spinner');

	if(confirm("Are you sure you want to remove this document?")){
		$.ajax({
			type: "POST",
			url: 'php/classes/ajax.php',
			data: {
				removeTrainingDocument: id
			},
			beforeSend: function(b){
				spinner.css('display', 'block');
			},
			success: function(response)
			{
				
				var time = 0;
				
				if(response == true){
					$('div#doc_' + id).remove();
					$(this).remove();
					spinner.css('display', 'none');
					target.html("&#x2713;").css('color', 'green');
					time = 2000;
				}else{
					spinner.css('display', 'none');
					target.html("Something went wrong. Contact the site admin.").css('color', 'red');
					time = 10000;
				}
				setTimeout( function(){ target.html(''); }, time );
			}
		});
	}
	
});

$('.existingTrainingModule').on('click', 'input[name="addMCQuestion"]', function(){
	
	// create an empty mcq for this module and get the id
	var id = $(this).attr('modId');
	var html = '';
	$.ajax({
			type: "POST",
			url: 'php/classes/ajax.php',
			data: {
				createBlankMCQ: id
			},
			dataType: 'text',
			async: false,
			success: function(jdata)
			{
				var data = JSON.parse(jdata);
				// generate the html using the new id	
				html+='<div class="mcq" id="' + data.qid + '">';
				html+='Q: <input type="text" name="mc_question_' + data.qid + '" id="question" placeholder="Multiple Choice Question" required/>';
				html+='<input type="button" name="remove" value="Remove" />';
				html+='<br /><input type="checkbox" name="a_correct_' + data.qid + '" value="a" /><span id="indent">a)</span><input type="text" name="a_answer_' + data.a + '" id="answer" placeholder="Answer" required/>';
				html+='<br /><input type="checkbox" name="b_correct_' + data.qid + '" value="b" /><span id="indent">b)</span><input type="text" name="b_answer_' + data.b + '" id="answer" placeholder="Answer" required/>';
				html+='<br /><input type="checkbox" name="c_correct_' + data.qid + '" value="c" /><span id="indent">c)</span><input type="text" name="c_answer_' + data.c + '" id="answer" placeholder="Answer" required/>';
				html+='<br /><input type="checkbox" name="d_correct_' + data.qid + '" value="d" /><span id="indent">d)</span><input type="text" name="d_answer_' + data.d + '" id="answer" placeholder="Answer" required/>';
				html+='</div><p></p>';
			}
	});
	
	$(this).closest('.existingTrainingModule').find('.trainingQuestions').append(html);
});

$('.existingTrainingModule').on('click', 'input[name="addTFQuestion"]', function(){

	// Create an empty tfq for this module and get the ID
	var id = $(this).attr('modId');
	var html = '';
	$.ajax({
			type: "POST",
			url: 'php/classes/ajax.php',
			data: {
				createBlankTFQ: id
			},
			dataType: 'text',
			async: false,
			success: function(qid)
			{
				// generate the html using the new id	
				html+='<div id="' + qid + '">';
				html+='Q: <input type="text" name="tf_question_' + qid + '" id="question" placeholder="True/False Question" required/>';
				html+='<input type="button" name="remove" value="Remove" />';
				html+='<br /><input type="radio" name="tf_correct_' + qid + '" value="t" />True';
				html+='<br /><input type="radio" name="tf_correct_' + qid + '" value="f" />False';
				html+='</div>';
			}
	});
		
	$(this).closest('.existingTrainingModule').find('.trainingQuestions').append(html);

});

$('.existingTrainingModule').on('click', 'input[name="remove"]', function(){
	
	if(confirm("Are you sure you want to remove this question?")){
	
		var id = $(this).parent().attr('id');
		var pieces = id.split('_');
		var qid = pieces[pieces.length-1];
		console.log(qid);
		$.ajax({
			type: "POST",
			url: 'php/classes/ajax.php',
			data: { removeQuestion: qid },
			dataType: 'text',
			async: false,
			success: function(d){
				if(d === 'true'){
					$('div#'+id).remove();
				}else{
					$(this).closest('.existingTrainingModule').find('span.response').html('Database connection error.');
				}
			}
		});
		
	}

});

$('.existingTrainingModule').on('click', 'input[name="status"]', function(){

	var mid = $(this).attr('mid');
	var status;
	var time;
	var btn = $(this);
	var target = $(this).closest('.existingTrainingModule').find('span.response');

	if($(this).val() == 'active'){
		text = 'inactive';
		color = 'white';
		background = '#a30202';
		status = 0;
	}else{
		text = 'active';
		color = 'white';
		background = '#026502';
		status = 1;
	}
	
	$.ajax({
		type: "POST",
		url: 'php/classes/ajax.php',
		data: { 
			updateModuleStatus: mid,
			newStatus: status
		},
		dataType: 'text',
		async: true,
		success: function(response){
			if(response == true){
				
				// Update the button
				btn.css('background-color', background);
				btn.css('color', color);
				btn.val(text);
				$('input[name="statusVal"]').val(status);
				
				// Notify the users of success
				target.html("&#x2713;").css('color', 'green');
				time = 2000;
				
			}else{
				target.html("&#x2717;").css('color', 'red');
				time = 10000;
			}
			setTimeout( function(){ target.html(''); }, time );
		}
	});
	
	
});

$(document).on('click', 'span.toggle', function(){
	$(this).closest('.existingTrainingModule').find('.toggleMe').slideToggle();
	if($(this).text() == '[+]'){
		$(this).html('[-]');
	}else{
		$(this).text('[+]');
	}
});
