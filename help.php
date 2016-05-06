<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");

$login = new Login();

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

							<div class="info">
								<span class="date">
									<span class="fa fa-question-circle"></span>
								</span>
							</div>

							<header>
								<h2><a href="#">Help</a></h2>
								<span class="byline">How to use this site...</span>
							</header>

							<!-- Page Content -->
							<div class="page-content-min-height">
								<h3>Fee For Service</h3>
								<ul>
									<li>Create a Project by selecting "New Project" at the top of the page - Be sure to include your payment information</li>
									<ul>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If you already have a project for which you would like to submit samples, feel free to skip this step</li>
									</ul>
									<li>Click on the "Home" button. Then click "Services" on the submenu located under the blinking down arrow. Complete the form to begin the process - We will contact you if we have any questions</li>
									<ul>
										<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If this is your first time using our services feel free to contact us with any questions you may have (see below for contact info)</li>
									</ul>
								</ul>

								<h3>Training</h3>
								<ul>
									<li>Select "Training" from the home page menu</li>
									<li>Choose an instrument for which you would like to be trained and read through the training material</li>
									<li>Take and pass the quiz</li>
									<li>Click "Hands On Training" to request a training session. Choose the project you wish to be used for billing.</li>
									<li>You can check the "Hands on Training" section for updates. When your session is scheduled it will appear here.</li>
								</ul>

								<h3>Instrument Booking</h3>
								<ul>
									<li>If you have not already done so, complete the training for the instrument you would like to use</li>
									<li>Select "Book an Instrument" from the top menu</li>
									<li>Click on the day you would like to book and complete the form</li>
									<li>Check your booking carefully and remember you must cancel at least 24 hours prior to the event to avoid being charged</li>

									<li>Click "Submit" when you are ready to book</li>
								</ul>

								<h3>Create A Project</h3>
								<ul>
									<li>Click on the  "Home" link</li>
									<li>Then click "Project" from the newly exposed menu</li>
									<li>Note: Its just under the blinking arrow</li>
									<li>Or just click <a href="newProject.php">here</a></li>
								</ul>

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
						<li><a href="index.php">Home</a></li>
						<li><a href="myProjects.php">My Projects</a></li>
						<li><a href="settings.php">Settings</a></li>
						<?php if($_SESSION['isAdmin'] == 1): ?>
						<li><a href="adminStats.php">Admin</a></li>
						<?php endif; ?>
						<li class="current_page_item"><a href="help.php">Help</a></li>
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
		<!-- /Scripts -->

	</body>
</html>
