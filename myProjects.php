<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/libraries/password_compatibility_library.php");
require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/ProjectInfo.php");
require_once("php/classes/ServiceRequestInfo.php");
require_once("php/classes/BookingInfo.php");

$login = new Login();
$projectInfo = new ProjectInfo();
$serviceInfo = new ServiceRequestInfo();
$bookingInfo = new BookingInfo();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
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
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><script src="js/html5shiv.js"></script><link rel="stylesheet" href="css/ie8.css" /><![endif]-->
		<!--[if lte IE 7]><link rel="stylesheet" href="css/ie7.css" /><![endif]-->
		<link rel="stylesheet" href="css/myProjects.css" />
	</head>

	<body class="left-sidebar menu">

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Content -->
			<div id="content">
			
				<!-- Inner Content -->
				<div id="content-inner">
			
					<!-- project -->
					<div>
					
						<!-- Print out the project information -->
						<?php $projects = $projectInfo->getActiveProjects($_SESSION['id']); ?>
						
						<?php if(sizeOf($projects) > 0): ?>
							<?php foreach($projects as $project): ?>
							
								<article class="is-post is-post-excerpt">
								
									<div class="info">
										<span class="date">
											<span class="fa fa-file"></span>
										</span>
										<span class="stats">
											&nbsp;&nbsp;&nbsp;&nbsp;ID: <?php echo $project['id']; ?>
										</span>
									</div>
								
									<header>
										<h2><a href="#"><?php echo $project['title']; ?></a></h2>
									</header>
											
									<div class="project-info-tab" id="id<?php echo $project['id']; ?>" style="background-color: rgba(154, 154, 154, .5);">Info</div>
									<div class="project-services-tab" id="id<?php echo $project['id']; ?>">Services</div>
									<div class="project-bookings-tab" id="id<?php echo $project['id']; ?>">Bookings</div>
									<div class="ProjectResponse" id="project<?php echo $project['id']; ?>"></div>
									
									
									<!-- Project Container -->
									<div class="project-container">
									
										<!-- Project Info -->
										<form method="post" action="javascript:void(0)" name="updateProject">
											<div class="project-info" id="id<?php echo $project['id']; ?>">
												<div class="project-column-container">
												
													<div class="project-column-2 left">
														
														<div id="project-id">
															<span class="label">Project ID:</span>
															<span class="input">
																<input type="hidden" name="updateProject" value="<?php echo $project['id']; ?>" />
																<input type="text" value="<?php echo $project['id']; ?>" disabled="true"/>
															</span>
														</div>
														
														<div id="project-title">
															<span class="label">Title:</span>
															<span class="input">
																<input type="text" name="title" value="<?php echo $project['title']; ?>" />
															</span>
														</div>
														
														
														<div id="project-pi">
															<span class="label">Primary Investigator:</span>
															<span class="input">
																<input class="input-disabled" type="text" value="<?php echo $project['primaryInvestigator']; ?>" disabled="true"/>
															</span>
														</div>
				
														<?php $payment = $projectInfo->getPaymentInfo($project['paymentId']); ?>
														<div id="project-payment">
															<span class="label">PCBU:</span>
															<span class="input">
																<input class="input-disabled" type="text" value="<?php echo $payment['projectCostingBusinessUnit']; ?>" disabled="true"/>
															</span>
														</div>
														
														<div id="project-payment">
															<span class="label">Project ID:</span>
															<span class="input">
																<input class="input-disabled" type="text" value="<?php echo $payment['projectId']; ?>" disabled="true"/>
															</span>
														</div>
														
														<div id="project-payment">
															<span class="label">Department ID:</span>
															<span class="input">
																<input class="input-disabled" type="text" value="<?php echo $payment['departmentId']; ?>" disabled="true"/>
															</span>
														</div>
														
														<div id="project-payment">
															<span class="label">Purchase Order:</span>
															<span class="input">
																<input class="input-disabled" type="text" value="<?php echo $payment['purchaseOrder']; ?>" disabled="true"/>
															</span>
														</div>
														
													</div>
													
													
													<div class="project-column-2 right">
														<div id="project-address1">
															<span class="label">Address:</span>
															<span class="input">
																<input type="text" name="addressOne" value="<?php echo $project['addressOne']; ?>" />
															</span>
														</div>
														
														<div id="project-address2">
															<span class="label">&nbsp;</span>
															<span class="input">
																<input type="text" name="addressTwo" value="<?php echo $project['addressTwo']; ?>" />
															</span>
														</div>
														
														<div id="project-city">
															<span class="label">City:</span>
															<span class="input">
																<input type="text" name="city" value="<?php echo $project['city']; ?>" />
															</span>
														</div>
														
														<div id="project-state">
															<span class="label">State:</span>
															<span class="input">
																<input type="text" name="state" value="<?php echo $project['state']; ?>" />
															</span>
														</div>
														
														<div id="project-zip">
															<span class="label">Zip:</span>
															<span class="input">
																<input type="text" maxlength="10" name="zip" value="<?php echo $project['zip']; ?>" />
															</span>
														</div>
														
														<div id="project-phone">
															<span class="label">Phone:</span>
															<span class="input">
																<input type="text" name="phone" value="<?php echo $project['phone']; ?>" />
															</span>
														</div>
														
														<div id="project-fax">
															<span class="label">Fax:</span>
															<span class="input">
																<input type="text" name="fax" value="<?php echo $project['fax']; ?>" />
															</span>
														</div>
													</div>
												</div>
																					
												<div class="project-column-abstract">
													<div id="project-abstract">Abstract:</div>
													<div id="project-abstract">
														<textarea name="abstract" ><?php echo $project['abstract']; ?></textarea>
													</div>
												</div>
											</div>
										</form>
										<!-- /Project Info -->
										
										<!-- Services-->
										<div class="project-services" id="id<?php echo $project['id']; ?>" style="display: none; overflow: auto;">
											<?php $services = $serviceInfo->getActiveServiceRequests($project['id']); ?>
											<?php if(sizeof($services) >0): ?>
												<?php foreach($services as $service): ?>
													<div class="project-column-container">
														
														<div>
															<span>ID: <b><?php echo $service['id']; ?></b></span>
															<?php if($service['status'] == 'closed'): ?>
																<span style="float: right; color: black;">Closed</span>
															<?php elseif($service['status'] == 'pending'): ?>
																<span style="float: right; color: blue;">Pending</span>
															<?php elseif($service['status'] == 'approved'): ?>
																<span style="float: right; color: green;">Approved</span>
															<?php else: ?>
																<span style="float: right; color: black;"><?php echo $service['status']; ?></span>
															<?php endif; ?>
														</div>
														
														<div class="project-column-2 left" style="padding: 2px; margin: 2px; margin-left: 3px;">
															
															<div id="project-id">
																<span class="label">Created:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['createdAt']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-title">
																<span class="label">Label:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['label']; ?>" disabled="true"/>
																</span>
															</div>
															
															
															<div id="project-pi">
																<span class="label">Concentration:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['concentration']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">State:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['state']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">Composition:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['composition']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">Digestion Enzyme:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['digestionEnzyme']; ?>" disabled="true"/>
																</span>
															</div>
															
														</div>
														
														<div class="project-column-2 right" style="padding: 2px; margin: 2px;">
															
															<div id="project-id">
																<span class="label">Purification:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['purification']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-title">
																<span class="label">Reduction/Alkylation:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['redoxChemicals']; ?>" disabled="true"/>
																</span>
															</div>
															
															
															<div id="project-pi">
																<span class="label">Molecular Weight:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['molecularWeight']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">Suspected Mods:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['suspectedModifications']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">Amino Acid Mods:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['aaModifications']; ?>" disabled="true"/>
																</span>
															</div>
															
															<div id="project-payment">
																<span class="label">Species:</span>
																<span class="input">
																	<input type="text" value="<?php echo $service['species']; ?>" disabled="true"/>
																</span>
															</div>
															
														</div>
														
													</div>
													
													<div class="project-column-1">
														<div id="project-abstract">Sequence:</div>
														<div id="project-abstract">
															<textarea disabled="true"><?php echo $service['sequence']; ?></textarea>
														</div>
													</div>
													<div class="project-column-1">
														<div id="project-abstract">Comments:</div>
														<div id="project-abstract">
															<textarea disabled="true"><?php echo $service['comments']; ?></textarea>
														</div>
													</div>
													
													
																	
													Services Selected:<br>
													<?php $servicesSelected = $serviceInfo->getServicesSelected($service['id']); ?>
													<?php foreach($servicesSelected as $service): ?>
														<div class="servicesSelected">
														<?php $serviceName = $serviceInfo->getServiceName($service['serviceId']); ?>
														<?php
															echo $serviceName['name'];
															echo ': ' . $service['samples'] . ' samples';
															if($service['prep']){echo ' with prep';}
															if($service['replicates'] > 1){
																echo ' -- ' . $service['replicates'] . ' replicates';
															}else{
																echo ' -- no replicates';
															}
														?>
														</div>
													<?php endforeach; ?>
																			
												<?php endforeach; ?>
											<?php else: ?>
												<div>This project has no associated fee-for-service requests.</div>
											<?php endif; ?>
										</div>
										<!-- /Services -->
										
										<!-- Bookings-->
										<div class="project-bookings" id="id<?php echo $project['id']; ?>" style="display: none;">
											<?php $bookings = $bookingInfo->getBookings($project['id'], $_SESSION['id']); ?>
											<?php if(sizeof($bookings) > 0): ?>
											<div><a href="newBooking.php">Add a new booking</a></div>
												<?php foreach($bookings as $booking): ?>
													<div id="bookingId_<?php echo $booking['id']; ?>">
													<?php $instrument = $bookingInfo->getInstrumentInfo($booking['instrumentId']); ?>
													ID:<b><?php echo $booking['id']; ?></b><br>
													&nbsp;&nbsp;&nbsp;&nbsp;<?php echo '<span style="color: ' . $instrument['color'] . '">' .  $instrument['name'] . '</span>: ' . $instrument['model']; ?><br>
													&nbsp;&nbsp;&nbsp;&nbsp;
													<?php 
														$from = $booking['dateFrom'] . ' ' . $booking['timeFrom'];
														$to = $booking['dateTo'] . ' ' . $booking['timeTo'];
														echo date("F j, Y, g:i a",strtotime($from));
														echo '<b> to </b>';
														echo date("F j, Y, g:i a",strtotime($to));
														$event = strtotime($booking['dateFrom'] . ' ' . $booking['timeFrom']);
														$now = time();
														if(($event - $now) > 86400){
															echo '<button type="button" class="cancelBooking" id="bookingId_' . $booking['id'] . '">Cancel</button>';
														}
													?>
													<br><br>
													</div>
												<?php endforeach; ?>
											<?php else: ?>
												<div>This project has no associated bookings. <a href="newBooking.php">Add a new booking</a></div>
											<?php endif; ?>
										</div>
										<!-- /Bookings -->
									
									</div>
									<!-- /Project Container -->

									<a href="newProject.php">Create a new project</a>
									
								</article>
							
							<?php endforeach; ?>
						<?php else: ?>
							<article class="is-post is-post-excerpt">
								
									<div class="info">
										<span class="date">
											<span class="fa fa-file"></span>
										</span>
										<span class="stats">
											&nbsp;&nbsp;&nbsp;&nbsp;
										</span>
									</div>
								
									<header>
										<h2><a href="#">Looks like you don't have any projects yet..</a></h2>
										<span class="byline"><a href="newProject.php">Click here to create one</a></span>
									</header>
									
							</article>
						<?php endif; ?>
					
					</div>
					<!--/project -->


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
						<li class="current_page_item"><a href="myProjects.php">My Projects</a></li>
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
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<script src="js/ajaxMyProjects.js"></script>
		<script src="js/myProjects.js"></script>
		<!-- /Scripts -->

	</body>
</html>