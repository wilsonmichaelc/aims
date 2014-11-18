<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");

$login = new Login();

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

	<body class="left-sidebar">

		<!-- Wrapper -->
		<div id="wrapper">

			<!-- Content -->
			<div id="content">
			
				<!-- Inner Content -->
				<div id="content-inner">
			
						<!-- Post -->
						<article class="is-post is-post-excerpt">

							<!-- Inner Menu -->
							<div class="info">
								<span class="date">
									<span class="fa fa-pencil"></span> 
								</span>
							</div>
							<!-- /Inner Menu -->
							
							<header>
								<h2><a href="#">End User License Agreement</a></h2>
								<!-- <span class="byline">Mass Spectrometry Center Instrument Management System</span> -->
							</header>

							<!-- Page Content -->
							<div style="max-width: 800px;">
								<h3 style="font-size: 1.4em; margin-bottom: 5px;">Terms and Conditions</h3>
								<p style="text-align: justify;">
									The Mass Spectrometry Center (MSC) Analytical Instrument Management System (AIMS) is an internet application that enables users to have secure web-based access to information from their submitted scientific projects maintained by the School of Pharmacy MSC at the University of Maryland, Baltimore.
								</p>
								<ul style="list-style-type: disc; padding-left: 20px;">
									<li>MSC is offering this service to users to submit service requests, review projects, arrange training, and book instruments. </li>
									<li>Information from a user’s project record that is accessible through AIMS is intended for the user’s access only. The user will have a unique username and password which will provide access to their submitted projects including service requests, project updates, training, and instrument scheduling. The unique username and password are crucial to protecting the security of projects and billing information. Therefore, such information should not be shared with anyone. In the event a user believes their password has been compromised, one may change it anytime through the AIMS. Protecting the security and privacy of such information are the responsibility of the users.</li>
									<li>The MSC is operated as a Cost Center through which institutional users (UM, non-profit and for-profit) contribute to the upkeep of the resource by means of a recharge system. Rates are based on the operating costs and individual instrument usage. All rates are reviewed and approved by the University of Maryland Administration and are subject to change without prior notice.</li>
									<li>All users are expected to abide by instrument usage guidelines and best practices. All instrument problems and issues should be reported to MSC staff immediately.</li>
									<li>Should the MSC determine that a user has violated these Terms and Conditions, as may be modified from time to time, MSC may discontinue one’s usage of AIMS immediately, without providing prior notice. </li>
									<li>In addition, AIMS may not be available at all times due to system maintenance, outages, or other issues beyond the control of MSC.</li>
								</ul>
								<p>
									By using this service or by clicking “accept” below, you signify your agreement to these terms and conditions. If you do not agree to these terms and conditions, do not use this service.
								</p>
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
		
				<!-- Nav -->
				<nav id="nav">
					<ul>
						<li><a href="login.php">Login</a></li>
						<li><a href="register.php">Register</a></li>
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