<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once("php/config/config.php");
require_once("php/classes/Login.php");
require_once("php/classes/BookingRates.php");

$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false) {
    header('Location: login.php');
}
if ($_SESSION['isAdmin'] == 0){
	header('Location: index.php');
}else{
	$bookingRates = new BookingRates();
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
		<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,700|Open+Sans+Condensed:300,700" rel="stylesheet" type="text/css"/>

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
									<span class="fa fa-bar-chart-o"></span> 
								</span>
								<ul class="stats">
									<li><a href="adminStats.php">Stats</a></li>
									<li><a href="adminInvoice.php">Invoices</a></li>
									<li><a href="adminServices.php">Services</a></li>
									<li><a href="adminInstruments.php">Instruments</a></li>
									<li><a href="adminUsers.php">Users</a></li>
									<li><a href="adminTraining.php">Training</a></li>
									<li><a href="adminFAQ.php">FAQ</a></li>
									<li><a href="adminAccountTypes.php">Accounts</a></li>
									<li class="current-page-start"><a href="adminBookingRates.php">Rates</a></li>
									<li><a href="adminInstrumentAccess.php">Access</a></li>
									<li><a href="adminInvoiceSearch.php">Invoice-Search</a></li>
								</ul>
							</div>
							<!-- /Inner Menu -->

							<header>
								<span class="byline">Booking Rates</span>
								<span class="byline">
									<?php 
										// show negative messages
										if ($bookingRates->errors) { foreach ($bookingRates->errors as $error) { echo '<div class="error">' . $error . '</div>'; } }
										if (isset($_GET['fail'])){ echo '<div class="error">Failed to update rates.</div>'; }
										// show positive messages
										if ($bookingRates->messages) { foreach ($bookingRates->messages as $messages) { echo '<div class="success">' . $messages . '</div>'; } }
										if (isset($_GET['success'])){ echo '<div class="success">Rates updated successfully!</div>'; }
									?>
								</span>
							</header>
				            
							<div>
								
								<div><b>Booking Rates Internal</b></div>
								<?php $internalRates = $bookingRates->getInternalRates(); ?>
								<?php foreach($internalRates as $r): ?>
									<b><?php echo $r['accountName']; ?></b><br>
									<form method="post" action="adminBookingRates.php" name="updateInternalRates">
										Staff Rate: <input style="width: 50px;" type="text" name="staffRate" value="<?php echo $r['staffRate']; ?>" required/>
										1 Hour: <input style="width: 50px;" type="text" name="oneHour" value="<?php echo $r['oneHour']; ?>" required/>
										4 Hours: <input style="width: 50px;" type="text" name="fourHours" value="<?php echo $r['fourHours']; ?>" required/>
										8 Hours: <input style="width: 50px;" type="text" name="eightHours" value="<?php echo $r['eightHours']; ?>" required/>
										16 Hours: <input style="width: 50px;" type="text" name="sixteenHours" value="<?php echo $r['sixteenHours']; ?>" required/>
										24 Hours: <input style="width: 50px;" type="text" name="twentyFourHours" value="<?php echo $r['twentyFourHours']; ?>" required/>
										<input type="hidden" name="accountTypeId" value="<?php echo $r['accountTypeId']; ?>" />
										<input style="width: 100px;" type="submit" name="updateInternalRates" value="Update" />
									</form>
								
								<?php endforeach; ?>
								
								<br><br>
								<div><b>Booking Rates External</b></div>
								<?php $externalRates = $bookingRates->getExternalRates(); ?>
								<?php foreach($externalRates as $r): ?>
									<b><?php echo $r['accountName']; ?></b><br>
									<form method="post" action="adminBookingRates.php" name="updateExternalRates">
										Staff Rate: <input style="width: 50px;" type="text" name="staffRate" value="<?php echo $r['staffRate']; ?>" required/>
										High Accuracy: <input style="width: 50px;" type="text" name="highAccuracyRate" value="<?php echo $r['highAccuracyRate']; ?>" required/>
										Low Accuracy: <input style="width: 50px;" type="text" name="lowAccuracyRate" value="<?php echo $r['lowAccuracyRate']; ?>" required/>
										<input type="hidden" name="accountTypeId" value="<?php echo $r['accountTypeId']; ?>" />
										<input style="width: 100px;" type="submit" name="updateExternalRates" value="Update" />
									</form>
								
								<?php endforeach; ?>
								
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
		<script src="js/adminFaq.js"></script>
		<!-- /Scripts -->

	</body>
</html>
