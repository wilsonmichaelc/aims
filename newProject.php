<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/NewProject.php");
require_once("php/classes/States.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}else{
	$newProject = new NewProject();
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
		<link rel="stylesheet" href="css/newProject.css" />
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
									<span class="fa fa-folder-open"></span> 
								</span>
								<ul class="stats">
									<li class="current-page-start"><a href="newProject.php">Project</a></li>
									<li><a href="newService.php">Services</a></li>
									<li><a href="newTraining.php">Training</a></li>
									<li><a href="newBooking.php">Bookings</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">New Project</a></h2>
								<span class="byline">Create a project to manage your fee-for-service and booking requests</span>
								<span class="byline">
									<?php 
										// show negative messages
										if ($newProject->errors) { foreach ($newProject->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										// show positive messages
										if ($newProject->messages) { foreach ($newProject->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
									?>
								</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
							
								<!-- Create a new project -->
								<section class="is-form">
									<form action="newProject.php" method="post" accept-charset="UTF-8">
									
										<strong>Project Information</strong><br>
										<div class="column-container">
											<input type="text" class="text" name="title" placeholder="Project Title " required="true"/>
											<input type="text" class="text" name="primaryInvestigator" placeholder="Primary Investigator " required="true"/>
											<textarea type="textarea" class="text" name="abstract" placeholder="Abstract ... just a few sentences ..." required="true"></textarea>
										</div>
										
										<p></p>
										<strong>Contact Information</strong><br>
										<div class="column-container">
											<input type="text" name="addressOne" placeholder="Address" required="true"/>
											<input type="text" name="addressTwo" placeholder="Address" />
											<div class="inline-block">
												<input type="text" class="text" name="city" placeholder="City" required="true"/>
												<select type="text" class="text" name="state" required="true">
													<?php foreach($states as $key => $val): ?>
														<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
													<?php endforeach; ?>
												</select>
												<input type="text" class="text" name="zip" placeholder="Zip Code" required="true"/>
											</div>
											<div class="inline-block">
												<input type="text" class="text" name="phone" placeholder="Phone Number" required="true" />
												<input type="text" class="text" name="fax" placeholder="Fax Number" />
											</div>
										</div>
										
										<p></p>
										<strong>Billing Information</strong><br>
										<div class="column-container" style="overflow: hidden !important;">
											University of Maryland Chart String
											<div class="inline-block">
												<input type="text" class="text" name="projectCostingBusinessUnit" placeholder="PCBU" />
												<input type="text" class="text" name="projectId" placeholder="Project ID" />
												<input type="text" class="text" name="departmentId" placeholder="Department ID" />
											<div class="inline-block">
										</div>
										
										<div class="column-container">
											External Users Purchase Order Number
											<input type="text" class="text" name="purchaseOrder" placeholder="PO Number" />
										</div>
										
										<div class="column-container">
											<input type="submit" name="createProject" value="Submit"/>
										</div>
										
										<p></p>
										<p>Billing information is not required to submit this form but will be required prior to sample submission or instrument booking.<br>
										Your UMB Primary Investigator should provide you with a chart string.<br>
										Contact <a href="mailto:ygoo@rx.umaryland.edu">Young Ah Goo</a> to obtain a purchase order number.<br></p>
										
									</form>
								</section>							
							</div>
							<!-- Page Content -->
							
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
						<p>
							<strong>AIMS</strong> is an open source solution.  Fork it <a href="https://github.com/">here</a>!
						</p>
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
		<!-- /Scripts -->
		
	</body>
</html>