<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/Users.php");
require_once("php/classes/TrainingModules.php");
require_once("php/classes/InstrumentInfo.php");
require_once("php/classes/ConferenceRoomInfo.php");
require_once("php/classes/MSCServices.php");


$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	$user = new Users();
	$trainingModules = new TrainingModules();
	$instrumentInfo = new InstrumentInfo();
	$conferenceRoomInfo = new ConferenceRoomInfo();
	$mscServices = new MSCServices();
}
?>
<!DOCTYPE HTML>
<!--
	Striped 2.5 by HTML5 Up!
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Analytical Instrument Management System</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Mass Spectrometry Center, Instrument Management, Mass Spec, Goodlett" />
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,700|Open+Sans+Condensed:300,700" rel="stylesheet" />
		<link rel="stylesheet" href="css/adminUsersStyle.css" />
		<link rel="stylesheet" href="css/skel-noscript.css" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/style-desktop.css" />
		<link rel="stylesheet" href="css/style-wide.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="css/ie7.css" /><![endif]-->
		<link rel="stylesheet" href="css/modal.css">
	</head>

	<body class="left-sidebar menu">

		<a style="display: none;" href="#addService" class="call-modal"></a>
		<section class="semantic-content" id="addService" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
			<div class="modal-inner">
				<form name="modalAddService" id="resetForm" method="POST" action="javascript:void(0)" accept-charset="UTF-8">
					<?php $analysisServices = $mscServices->getAnalysisServices(); ?>

					Service ID
					<input type="number" name="modalServiceId" value="" readonly="true" required>

					Service:
					<select name="modalServiceName" required>
						<option value="" prep=""></option>
					<?php foreach($analysisServices as $service): ?>
						<option value="<?php echo $service['id']; ?>" prep="<?php if($service['samplePrepId'] != ''){echo 'y'; }else{echo 'n';} ?>"><?php echo $service['name']; ?></option>
					<?php endforeach; ?>
					</select>

					Samples:
					<input type="number" min="1" name="modalSamples" required>

					Replicates:
					<select name="modalReplicates" required>
						<option value="1"></option>
						<option value="1">none</option>
						<option value="2">duplicate</option>
						<option value="3">triplicate</option>
					</select>

					Prep:
					<select name="modalPrep" readonly>
						<option value=""></option>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>

					<div><br /></div>
					<input type="submit" name="modalSubmit" value="Add Service">
				</form>
			</div>
			<a href="#" class="modal-close" data-dismiss="modal">x</a>
		</section>

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Content -->
			<div id="content">

				<!-- Inner Content -->
				<div id="content-inner">

						<!-- Post -->
						<article class="is-post is-post-excerpt">

							<!-- Inner Menu -->
							<div class="info menu">
								<span class="date">
									<span class="fa fa-group"></span>
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li class="current-page-start"><a href="adminUsers.php">Users</a></li>
									<li><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
									<li><a href="adminBookingRates.php">Rates</a></li>
									<li><a href="adminInstrumentAccess.php">Access</a></li>
									<li><a href="adminInvoiceSearch.php">Invoice-Search</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->

							<header>
								<span class="byline">User Management</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
								Please select a user to edit:
								<select id="user" name="user">
									<option value=""></option>
									<?php
										$users = $user->getAllUsers();

										foreach($users as $u){
											echo '<option value="' . $u['id'] . '">' . $u['first'] . ' ' . $u['last'] . '</option>';
										}
									?>
								</select>

								<div class="theBasics" id="user_details" style="display: none;">
									<b>The Basics...</b><br>
									<div style="display: inline-block; font-weight: bold;">
										<span id="name"></span>
									</div>
                  <div>
                    <span id="username"></span>
                  </div>
                  <div>
                    <span id="userId"></span>
                  </div>
                  <div class="input" id="accountStatus">
                  </div>

									<form method="post" action="javascript:void(0)" id="updateUserAccountType">
										<input type="hidden" name="id" value="" />
										<select name="accountType" style="max-width: 220px;">
											<option value="1">Member</option>
											<option value="2">Collaborator</option>
											<option value="3">Affiliate</option>
											<option value="4">UMB</option>
											<option value="5">Non-Profit</option>
											<option value="6">For-Profit</option>
										</select>
										<span class="messages"></span>
									</form>
								</div>


								<div class="theTrainingModules" id="training_details" style="display: none;">
									<b><span class="toggleTrainingModules">[+]</span> Training Modules...</b>
									<span class="content" style="display: none;">
										<?php $modules = $trainingModules->getTrainingModules(); ?>
										<?php foreach($modules as $module): ?>
											<form method="post" action="javascript:void(0)" class="trainingStatus" id="id<?php echo $module['id']; ?>">
												<div style="display: inline-block; width: 200px !important; margin-left: 10px; margin-right: 120px;"><?php echo $module['name']; ?></div>
												<input type="hidden" name="moduleId" value="<?php echo $module['id']; ?>">
												<input type="hidden" name="userId" value="">
												<input type="radio" name="status" value="1">Complete
												<input type="radio" name="status" value="0" checked="true">Incomplete
												<span class="trainingStatusResponse"></span>
											</form>
										<?php endforeach; ?>
									</span>
								</div>

								<div class="theAccess" id="instrument_access" style="display: none;">
									<b><span class="toggleAccess">[+]</span> Instrument Access...</b>
									<span class="content" style="display: none;">
										<?php $instruments = $instrumentInfo->getInstruments(); ?>
										<?php foreach($instruments as $instrument): ?>
											<form method="post" action="javascript:void(0)" class="instrumentAccess" id="id<?php echo $instrument['id']; ?>">
												<div style="display: inline-block; width: 200px !important; margin-left: 10px; margin-right: 146px;"><?php echo $instrument['name']; ?></div>
												<input type="hidden" name="instrumentId" value="<?php echo $instrument['id']; ?>">
												<input type="hidden" name="userId" value="">
												<input type="radio" name="accessStatus" value="1">Granted
												<input type="radio" name="accessStatus" value="0" checked="true">Revoked
												<span class="instrumentAccessResponse"></span>
											</form>
										<?php endforeach; ?>
									</span>
								</div>

								<div class="theConferenceAccess" id="conference_access" style="display: none;">
									<b><span class="toggleConferenceAccess">[+]</span> Conference Room Access...</b>
									<span class="content" style="display: none;">
										<?php $conferenceRooms = $conferenceRoomInfo->getConferenceRooms(); ?>
										<?php foreach($conferenceRooms as $rooms): ?>
											<form method="post" action="javascript:void(0)" class="conferenceAccess" id="id<?php echo $rooms['id']; ?>">
												<div style="display: inline-block; width: 200px !important; margin-left: 10px; margin-right: 146px;"><?php echo $rooms['name']; ?></div>
												<input type="hidden" name="conferenceId" value="<?php echo $rooms['id']; ?>">
												<input type="hidden" name="userId" value="">
												<input type="radio" name="accessStatus" value="1">Granted
												<input type="radio" name="accessStatus" value="0" checked="true">Revoked
												<span class="conferenceAccessResponse"></span>
											</form>
										<?php endforeach; ?>
									</span>
								</div>

								<div class="theProjects" id="user_projects" style="display: none;">
									<b><span class="toggleProjects">[+]</span> Projects...</b>
									<span class="content" style="display: none;">

									</span>
								</div>

								<div class="theBookings" id="user_bookings" style="display: none;">
									<b><span class="toggleBookings">[+]</span> Bookings...</b>
									<span class="content" style="display: none;">

									</span>
								</div>

								<div class="theTrainingBookings" id="user_trainingBookings" style="display: none;">
									<b><span class="toggleTrainingBookings">[+]</span> Training Sessions...</b>
									<span class="content" style="display: none;">

									</span>
								</div>

								<div class="theServiceRequests" id="user_ffs" style="display: none;">
									<b><span class="toggleFFS">[+]</span> Service Requests...</b>
									<div class="content" style="display: none;">

									</div>
								</div>

							</div>

						</article>
						<!-- End Post -->

				</div>
				<!-- /Inner Content -->

			</div>
			<!-- /Content -->

			<!-- Sidebar -->
			<div id="sidebar">

				<!-- Logo -->
				<div id="logo">
					<h1>AIMS</h1>
				</div>
				<!-- /Logo -->

				<!-- Logout -->
				<section>
					<div class="inner">
						Welcome <?php echo $_SESSION['first']; ?>!&nbsp;&nbsp;&nbsp;
						<strong><a href="index.php?logout">Logout</a></strong>
					</div>
				</section>
				<!-- /Logout -->

				<!-- Nav -->
				<nav id="nav">
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="myProjects.php">My Projects</a></li>
						<li><a href="settings.php">Settings</a></li>
						<?php if($_SESSION['isAdmin'] == 1): ?>
						<li class="current_page_item"><a href="adminStats.php">Admin</a></li>
						<?php endif; ?>
						<li><a href="help.php">Help</a></li>
						<li><a href="faq.php">FAQ</a></li>
					</ul>
				</nav>
				<!-- /Nav -->

				<!-- Search -->
					<?php include("php/includes/search.php"); ?>
				<!-- /Search -->

				<!-- Text -->
				<section class="is-text-style1">
					<div class="inner">
						<?php echo $login->getSideBarMessage(); ?>
					</div>
				</section>
				<!-- /Text -->


				<!-- Copyright -->
				<div id="copyright">
					<p>
						&copy; 2014 Mass Spectrometry Center.<br />
						Maintainer: <a href="mailto:<?php echo MAINTAINER_EMAIL; ?>"><?php echo MAINTAINER_NAME; ?></a>
						Aesthetics: <a href="http://html5up.net/">HTML5 UP</a>
					</p>
				</div>
				<!-- /Copyright -->

			</div>
			<!-- /Sidebar -->

		</div>
		<!-- /Wrapper -->

		<!-- Scripts -->
		<script src="js/jquery.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script src="js/messageHandler.js"></script>
		<script src="js/adminUsers.js"></script>
		<script>
			var id;
			// When the user clicks add service, get/pass the id to the modal, and simulate modal click. Also scroll up
			$(document).on('click', 'input.call-modal', function(){

				id=$(this).attr('id');
				$('input[name="modalServiceId"]').val(id);
				$("html, body").animate({ scrollTop: 0 }, "medium");
				$('a.call-modal')[0].click();

			});

			// Clear the form when the modal is closed
			$("a.modal-close").click(function() {
			    $('#resetForm').trigger('reset');
			});

			$(document).on('click', 'input[name="delete"]', function(){
				var target = $(this).parents('.inputs');
				if(confirm("Are you sure you want to delete this service?")){
					$.ajax({
					url: "php/classes/ajax.php",
			        data: {deleteServiceFromRequest: $(this).attr('id')},
			        type: 'post',
			        async: true,
			        success: function(response){

						if(response == 1){
							target.hide('medium').remove();
						}else{
							alert("Could not delete service.");
						}

					}
					});
				}

			});

			// When the modal is submitted, use ajax to add the new service to the existing request
			$(document).on('click', 'input[name="modalSubmit"]', function(){

				var target = 'div#'+id+'.request .ffs_Selected';
				var service = $('select[name="modalServiceName"] option:selected').val();
				var serviceName = $('select[name="modalServiceName"] option:selected').text();
				var samples = $('input[name="modalSamples"]').val();
				var replicates = $('select[name="modalReplicates"] option:selected').val();
				var prep = $('select[name="modalPrep"] option:selected').val();

				$('a.modal-close')[0].click(); /// Remember... this action clears the form.

				$.ajax({
					url: "php/classes/ajax.php",
			        data: {
			        	addServiceToRequest: id,
			        	service: service,
			        	samples: samples,
			        	replicates: replicates,
			        	prep: prep
			        },
			        type: 'post',
			        async: true,
			        success: function(resultId){

			        	if(resultId != false){
							var html = '<div class="inputs">';
							html += '<form method="post" action="javascript:void(0)" name="updateSelectedService">';
							html += '	<input type="hidden" name="updateSelectedService" value="'+resultId+'">';
							html += '	<div>'+serviceName+'</div>';
							html += '	<div>';
							html += '		<input type="number" name="samples" value="'+samples+'">';
							html += '	</div>';
							html += '	<div>';
							html += '		<select name="replicates">';

							html += '			<option value="1" ';
							if(replicates == 1){html += 'selected';}
							html += '>None</option>';

							html += '			<option value="2" ';
							if(replicates == 2){html += 'selected';}
							html += '>Duplicate</option>';

							html += '			<option value="3" ';
							if(replicates == 3){html += 'selected';}
							html += '>Triplicate</option>';

							html += '		</select>';
							html += '	</div>';
							html += '	<div>';
							html += '		<select name="prep">';

							html += '			<option value="0" ';
							if(prep == 0){html += 'selected';}
							html += '>No</option>';

							html += '			<option value="1" ';
							if(prep == 1){html += 'selected';}
							html += '>Yes</option>';

							html +=	'		</select>';
							html += '	</div>';
							html += '	<div id="delete">';
						 	html += '		<input style="line-height: 10px; text-align: center; border-radius: 1.6em; -webkit-appearance: none; border: solid 1px #ddd; padding: 0.5em; height: 30px; width: 30px; font-family: "Comic Sans MS", sans-serif; font-size: 12pt; font-weight: 400; color: #565656;" type="button" name="delete" id="'+resultId+'" value="X"/>';
						 	html += '		<span class="ServiceResponse"></span>';
						 	html += '	</div>';
							html += '</form>';
							html += '</div>';
							$(target).append(html);
						}else{
							alert("Failed to add new service.");
						}

			        }
				});


			});

		</script>

		<!-- /Scripts -->

	</body>
</html>
