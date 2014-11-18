<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	
	require_once("php/config/config.php");
	require_once("php/classes/Login.php");
	require_once("php/classes/NewBooking.php");
	require_once("php/classes/ProjectInfo.php");
	require_once("php/classes/InstrumentInfo.php");
	require_once("php/classes/ConferenceRoomInfo.php");
	require_once("php/classes/MetaData.php");
	require_once("php/classes/TrainingInfo.php");
	
	$login = new Login();
	
	date_default_timezone_set('America/New_York');
	
	// ... ask if we are logged in here:
	if ($login->isUserLoggedIn() == false) {
	    header('Location: login.php');
	}
	$newBooking = new NewBooking();
	$projectInfo = new ProjectInfo();
	$instrumentInfo = new InstrumentInfo();
	$conferenceRoomInfo = new ConferenceRoomInfo();
	$metaData = new MetaData();
	$trainingInfo = new TrainingInfo();
	if($_SESSION['isAdmin']){
		$instruments = $instrumentInfo->getInstruments();
	}else{
		$instruments = $instrumentInfo->getUsersBookableInstruments($_SESSION['id']);
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
		
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		
		<link rel="stylesheet" href="fullcalendar/fullcalendar.css" />
		<link rel="stylesheet" media="print" href="fullcalendar/fullcalendar.print.css" />
		<link rel="stylesheet" href="css/jquery-ui-1.10.3.custom.min.css"/>
		<link rel="stylesheet" href="css/modal.css">
		
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="css/ie7.css" /><![endif]-->
	</head>

	<body class="left-sidebar menu">

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
									<span class="fa fa-calendar"></span>
								</span>
								<ul class="stats">
									<li><a href="newProject.php">Project</a></li>
									<li><a href="newService.php">Services</a></li>
									<li><a href="newTraining.php">Training</a></li>
									<li class="current-page-start"><a href="newBooking.php">Bookings</a></li>
									<br>
									<?php foreach($instruments as $instrument): ?>
									<li style="font-size: .8em; width: 150px; overflow: visible; color: <?php echo $instrument['color']; ?>"><?php echo $instrument['name']; ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
							
								
							<!-- /Inner Menu -->
							
							<header>
								<h2><a id="showModal" href="#modal-text" class="call-modal" title="Clicking this link shows the modal">Book an Instrument</a></h2>
								<span class="byline">
									<?php 
										// show negative messages
										if ($newBooking->errors) { foreach ($newBooking->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($newBooking->messages) { foreach ($newBooking->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							</header>
							
							<!-- Page Content -->
							<div class="page-content-min-height">
							
								<div id='calendar'></div>

								<!-- New Booking Form -->
								<section class="semantic-content" id="modal-text" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
									
									<div class="modal-inner">
									
										<form method="POST" action="newBooking.php" accept-charset="UTF-8" id="newBooking">
											
											Instrument
											<select name="instrumentId" required>
												<option value=""></option>
												
												<?php foreach($instruments as $instrument): ?>
														<option value="i<?php echo $instrument['id']; ?>" minUnit="<?php echo $instrument['minBookableUnit']; ?>"><span style="background-color: <?php echo $instrument['color']; ?>;"><?php echo $instrument['name']; ?></span></option>
												<?php endforeach; ?>
												
												<?php $rooms = $conferenceRoomInfo->getUsersBookableConferenceRooms($_SESSION['id']); ?>
												<?php foreach($rooms as $room): ?>
														<option value="c<?php echo $room['id']; ?>"><?php echo $room['name']; ?></option>
												<?php endforeach; ?>
											</select>

											<span id="projectId-showHide">
												Project
												<select name="projectId" required>
													<option value=""></option>
													<?php if($_SESSION['isAdmin']): ?>
													<?php $training = $trainingInfo->getPendingTrainingRequests(); ?>
													<?php if(sizeOf($training) > 0): ?>
														<option value="training">***Training***</option>
													<?php endif; ?>
													<?php endif; ?>
													<?php $projects = $projectInfo->getActiveProjects($_SESSION['id']); ?>
													<?php foreach($projects as $project): ?>
														<option value="<?php echo $project['id']; ?>"><?php echo $project['title']; ?></option>
													<?php endforeach; ?>
												</select>
											</span>
											
											<span id="trainingId-showHide" style="display: none;">
												Training
												<select name="trainingId" >
													
													<?php if(sizeof($training) > 0): ?>
														<?php foreach($training as $t): ?>
															<option value="<?php echo $t['id'] . '-' . $t['userId']; ?>">
																<?php
																	$name = $metaData->getUserFirstName($t['userId']);
																	$module = $metaData->getTrainingModuleName($t['moduleId']);
																	echo $name . ' - ' . $module;
																?>
															</option>
														<?php endforeach; ?>
													<?php else: ?>
														<option value="">No Pending requests.</option>
													<?php endif; ?>
												</select>
											</span>
											
											Date
											<br>
											<input style="height: 36px; width: 95px; float: left;" type="text" name="dateFrom" class="datepicker" id="dateFrom" required>
											<span style="height: 36px; width: 10px; float: left; text-align: center;">&nbsp;-</span>
											<input style="height: 36px; width: 95px; float: right;" type="text" name="dateTo" class="datepicker" id="dateTo" required>
										
											Time
											<br>
											<select style="height: 36px; width: 95px; float: left;" name="timeFrom" id="startTime" required>
												<option value=""></option>
											</select>
											<span  style="height: 36px; width: 10px; float: left;">&nbsp;-</span>
											<select  style="height: 36px; width: 95px; float: right;" name="timeTo" id="endTime" required>
												<option value=""></option>
											</select>
											
											<br>
											Hours
											<input id="hours" type="text" disabled="true"/>
										
											<span id="estimate-showHide">
												Estimate
												<input id="estimate" type="text" disabled="true"/>
											</span>
											<input id="accountType" type="hidden" value="<?php echo $_SESSION['accountType']; ?>"/>
											
											<input type="submit" name="createBooking" value="Submit">
										
										</form>
									
									</div>
									
									<!-- Use Hash-Bang to maintain scroll position when closing modal -->
									<a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal">x</a>
									
								</section><!-- / New Booking Form-->
							
							</div>
							<!-- End Page Content -->
							
							
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
						<li class="current_page_item"><a href="index.php">Home</a></li>
						<li><a href="myProjects.php">My Projects</a></li>
						<li><a href="settings.php">Settings</a></li>
						<?php if($_SESSION['isAdmin'] == 1): ?>
						<li><a href="adminStats.php">Admin</a></li>
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
						Maintainer: <a href="mailto:mwilson@rx.umaryland.edu">Michael Wilson</a>
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
		<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		
		<script src="js/date.js"></script>
		<script src="fullcalendar/fullcalendar.js"></script>
		<script src="fullcalendar/interactions.js"></script>
		<script src="fullcalendar/populatecalendar.js"></script>
		<script>
			$(document).ready().delay(20).queue( function(fullCalendar){$('#calendar').fullCalendar('render');} );
		</script>
		<script src="js/bookings.js"></script>
		<!-- /Scripts -->

	</body>
</html>