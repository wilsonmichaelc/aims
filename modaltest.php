<html>

	<head>
		<link rel="stylesheet" href="css/modal.css">
	</head>
	
	<body>
		<input style="line-height: 10px; text-align: center; border-radius: 1.6em; -webkit-appearance: none; border: solid 1px #ddd; padding: 0.5em; height: 30px; width: 30px; font-family: 'Comic Sans MS', sans-serif; font-size: 12pt; font-weight: 400; color: #565656;" type="button" name="delete" id="" value="X"/>
		
		<a style="display: none;" href="#addService" class="call-modal"></a>
		
		<!-- The button we want the user to click which will pass our id to the modal -->
		<input type="button" name="addService" class="call-modal" value="Add Service" />
		
		<section class="semantic-content" id="addService" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
			<div class="modal-inner">
				<form method="POST" action="javascript:void(0)" accept-charset="UTF-8">
					Blah<input type="text" name="blah">
					<input type="submit" name="submit" value="Submit">
				</form>
			</div>
			<!-- Use Hash-Bang to maintain scroll position when closing modal -->
			<a href="#" class="modal-close" data-dismiss="modal">x</a>
		</section>
		
		
	</body>

	<script src="js/jquery.min.js"></script>
	<script>
		$(document).on('click', 'input.call-modal', function(){
			$('a.call-modal')[0].click();
			$("html, body").animate({ scrollTop: 0 }, "medium");
		});
	</script>

</html>