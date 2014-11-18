<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/InstrumentInfo.php");


$login = new Login();

if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}else{
	$instrumentInfo = new InstrumentInfo();
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
									<span id="start-arrow" class="fa fa-arrow-circle-down"></span> 
								</span>
								<ul class="stats">
									<li><a href="newProject.php">Project</a></li>
									<li><a href="newService.php">Services</a></li>
									<li><a href="newTraining.php">Training</a></li>
									<li><a href="newBooking.php">Bookings</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">Analytical Instrument Management System</a></h2>
								<span class="byline">University of Maryland, Baltimore | School of Pharmacy</span>
							</header>

							<!-- Page Content -->
							<div>
								<table style="width: 500px;">
								<tr style="font-weight: bold;">
									<td>Instrument</td>
									<td>Status</td>
									<td>Location</td>
								</tr>
								<?php 
									$instruments;
									if($_SESSION['isAdmin']){
										$instruments = $instrumentInfo->getInstruments();
									}else{
										$instruments = $instrumentInfo->getAllUsersBookableInstruments($_SESSION['id']);
									} 
								?>
								<?php foreach($instruments as $instrument): ?>
									
									<tr class="instrument">
										<td><?php echo $instrument['name']; ?></td>
										<td><?php if($instrument['bookable'] == 1){echo "<span style='color: green;'>Active</span>";}else{echo "<span style='color: red;'>Offline</span>";} ?></td>
										<td><?php echo $instrument['location']; ?></td>
									</tr>
									
								<?php endforeach; ?>
								</table>
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
		<script src="js/skel.min.js"></script>
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		
		<!-- /Scripts -->
		
	</body>
</html>